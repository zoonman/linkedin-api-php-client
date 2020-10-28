<?php

namespace Pricat\Services\Product;

use Db;
use Pricat\Entities\Tire;
use Pricat\Services\Manufacturer\ActivateManufacturer;
use Pricat\Services\Manufacturer\BagManufacturers;
use Pricat\Services\Product\Photos\UpdatePhoto;
use Pricat\Services\Seo\FillSeo;
use Pricat\Utils\Helper as Utils;
use Pricat\Utils\Prestahop\Helper;

class UpdateProduct
{
    /**
     * @var BagManufacturers
     */
    private $bagManufacturers;

    public function __construct(BagManufacturers $bagManufacturers)
    {
        $this->bagManufacturers = $bagManufacturers;
    }

    public function run(\Pricat\Entities\Product $product, Tire $tire, bool $todo = false)
    {
        if (
            ($product->getDateUpd() == $product->getDateErp() && $product->getHashErp() == $tire->hash && $tire->stock == $product->getStock())
            ||
            ($tire->stock == 0 && $tire->stock == $product->getStock() and !$product->getHashErp() and $product->getDateUpd() == $product->getDateErp())
        ) {
            if ($product->getHashErp()) {
                return 0;
            }
        }

        //En el caso de stock=0 o solo cambio de stock solo actualizamos el stock, en caso contrario hacemos carga completa
        if ($product->getDateUpd() == $product->getDateErp() && $product->getHashErp() == $tire->hash and $product->getHashErp()) {
            $product->setQuantity((int)$tire->stock);
            if ($tire->stock) {
                $est = 2;
            } else {
                $est = 1;
            }
            Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product
                SET quantity = ' . $tire->stock . ',
                active = 1, visibility = \'both\', available_for_order=1,
                date_upd = now()
                WHERE id_product = ' . $product->getId());

            Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_shop
                SET active = 1, visibility = \'both\', available_for_order=1,
                date_upd = now()
                WHERE id_product = ' . $product->getId());
        } else {
            $product = new \Product($product->getId());
            if ($todo) {
                (new UpdateFeatures())->run($product->id, $tire);
                (new UpdatePhoto())->run($product, $tire);
            }

            $id_manufacturer = $this->bagManufacturers->getIdManufacturer($tire->marca);
            if ($id_manufacturer == -1) {
                Utils::printInfo(sprintf("[Error: actualizaProducto] No se encuentra el fabricante: %s\n", $tire->marca));
                return;
            }

            if (!(new ActivateManufacturer($this->bagManufacturers))->run($tire->marca)) {
                Utils::printInfo(sprintf("[Error: actualizaProducto] No se ha podido activar el fabricante: %s\n", $tire->marca));
            }

            Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product
                SET id_manufacturer = ' . $id_manufacturer . ',
                ean13 = \'' . $tire->ean13 . '\',
                ecotax = ' . $tire->ecotasa . ',
                quantity = ' . $tire->stock . ',
                price = ' . (float)$tire->precioneto . ',
                active = 1, visibility = \'both\', available_for_order=1,
                date_upd = now()
                WHERE id_product = ' . $product->id);

            Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_shop
                SET ecotax = ' . $tire->ecotasa . ',
                price = ' . (float)$tire->precioneto . ',
                active = 1, visibility = \'both\', available_for_order=1,
                date_upd = now()
                WHERE id_product = ' . $product->id);

            $tire->nombre = str_replace('\'', ' ', $tire->nombre);
            $tire->nombre = str_replace('"', ' ', $tire->nombre);

            // SEO
            (new FillSeo())->run($product, $tire);

            Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_lang
                SET link_rewrite = \'' . $product->link_rewrite[SHOP_LANG] . '\',
                meta_title = \'' . $product->meta_title[SHOP_LANG] . '\',
                meta_description = \'' . $product->meta_description[SHOP_LANG] . '\',
                meta_keywords = \'' . $product->meta_keywords[SHOP_LANG] . '\',
                name = \'' . $tire->nombre . '\'
                WHERE id_product = ' . $product->id . '
                AND id_lang = ' . SHOP_LANG);

            (new CheckFeaturesValues())->run($product, $tire);

            $categories = [2]; // Inicio
            if (!$tire->nieve && !$tire->estaciones) {
                $categories[] = 16;
            } elseif ($tire->nieve) {
                $categories[] = 15;
            } else {
                $categories[] = 17;
            }
            if ($tire->runflat) {
                $categories[] = 14;
            }
            switch ($tire->tipo) {
                case 'C0':
                    $categories[] = 10;
                    break;
                case 'L0':
                    $categories[] = 12;
                    break;
                case 'L4':
                    $categories[] = 11;
                    break;
                case 'T0':
                    $categories[] = 14;
                    break;
            }

            Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'category_product WHERE id_product=' . $product->id);
            $product->addToCategories($categories);
            (new AddReferenceOriginalToProduct())->run($product, $tire->reference_ori);
            $est = 3;
        }

        //Caches de stock prestashop

        \StockAvailable::setProductOutOfStock($product->id, 2);
        Helper::setQuantity($product->id, 0, $tire->stock);

        //Se guardan las variables del erp
        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product
            SET date_erp = date_upd, hash_erp=\'' . $tire->hash . '\'
            WHERE id_product = ' . $product->id);
        return $est;
    }
}
