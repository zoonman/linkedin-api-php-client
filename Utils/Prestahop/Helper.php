<?php

namespace Pricat\Utils\Prestahop;

use Cache;
use Configuration;
use Context;
use Db;
use Hook;
use Image;
use ImageManager;
use ImageType;
use Pricat\Utils\Helper as Utils;
use Shop;
use ShopGroup;
use StockAvailable;
use Tools;
use Validate;

class Helper
{
    /**
     * Método nativo Prestashop 1.6.0.9: AdminImportController::copyImg
     *
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->id_entity
     * $id_image is used if we need to add a watermark
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled
     * @param string $url path or url to use
     * @param string entity 'products' or 'categories'
     * @return boolean
     */
    static function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
    {
        Utils::printDebug(sprintf("copyImg: %s | %s | %s | %s\n", $id_entity, $id_image, $url, $entity));

        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int)$id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int)$id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int)$id_entity;
                break;
        }
        $url = str_replace(' ', '%20', trim($url));

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($url)) {
            return false;
        }

        // 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
        // Just hide the warning, the processing will be the same.
        if (Tools::copy($url, $tmpfile)) {
            ImageManager::resize($tmpfile, $path . '.jpg');
            $images_types = ImageType::getImagesTypes($entity);

            if ($regenerate) {
                foreach ($images_types as $image_type) {
                    ImageManager::resize($tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height']);
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                    }
                }
            }
        } else {
            unlink($tmpfile);
            return false;
        }
        unlink($tmpfile);
        return true;
    }

    /**
     * Método nativo Prestashop 1.6.0.9: StockAvailable::setQuantity
     *
     * For a given id_product and id_product_attribute sets the quantity available
     *
     * @param int $id_product
     * @param int $id_product_attribute Optional
     * @param int $delta_quantity The delta quantity to update
     * @param int $id_shop Optional
     */
    static function setQuantity($id_product, $id_product_attribute, $quantity, $id_shop = null)
    {

        if (!Validate::isUnsignedId($id_product)) {
            return false;
        }


        $context = Context::getContext();

        // if there is no $id_shop, gets the context one
        if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP) {
            $id_shop = (int)$context->shop->id;
        }


        $depends_on_stock = StockAvailable::dependsOnStock($id_product);

        //Try to set available quantity if product does not depend on physical stock
        if (!$depends_on_stock) {
            $id_stock_available = (int)StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);
            if ($id_stock_available) {
                Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'stock_available
                    SET quantity = ' . (int)$quantity . '
                    WHERE id_stock_available = ' . $id_stock_available);
            } else {
                $out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);
                $stock_available = new StockAvailable();
                $stock_available->out_of_stock = (int)$out_of_stock;
                $stock_available->id_product = (int)$id_product;
                $stock_available->id_product_attribute = (int)$id_product_attribute;
                $stock_available->quantity = (int)$quantity;

                if ($id_shop === null) {
                    $shop_group = Shop::getContextShopGroup();
                } else {
                    $shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));
                }

                // if quantities are shared between shops of the group
                if ($shop_group->share_stock) {
                    $stock_available->id_shop = 0;
                    $stock_available->id_shop_group = (int)$shop_group->id;
                } else {
                    $stock_available->id_shop = (int)$id_shop;
                    $stock_available->id_shop_group = 0;
                }
                $stock_available->add();
            }
        }

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_' . (int)$id_product . '*');
    }
}
