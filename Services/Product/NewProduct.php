<?php


namespace Pricat\Services\Product;


use Db;
use Encoding;
use Pricat\Entities\Tire;
use Pricat\Services\Manufacturer\ActivateManufacturer;
use Pricat\Services\Manufacturer\BagManufacturers;
use Pricat\Services\Product\Photos\UpdatePhoto;
use Pricat\Services\Seo\FillSeo;
use Pricat\Utils\Helper as Utils;
use Product;

class NewProduct
{
    /**
     * @var BagManufacturers
     */
    private $bagManufacturers;

    public function __construct(BagManufacturers $bagManufacturers)
    {
        $this->bagManufacturers = $bagManufacturers;
    }

    /**
     * Crear producto en la base de datos
     * @param Tire $tire
     * @return Product|void
     */
    public function run(Tire $tire)
    {
        $id_manufacturer = $this->bagManufacturers->getIdManufacturer($tire->marca);
        if ($id_manufacturer == -1) {
            Utils::printInfo(sprintf("[Error: nuevoProducto] No se encuentra el fabricante: %s\n", $tire->marca));
            return;
        }

        if (!(new ActivateManufacturer($this->bagManufacturers))->run($tire->marca)) {
            Utils::printInfo(sprintf("[Error: nuevoProducto] No se ha podido activar el fabricante: %s\n", $tire->marca));
        }

        $product = new Product();
        $product->reference = $tire->reference;

        // Valores por defecto
        $product->supplier_reference = '';
        $product->upc = '';
        $product->wholesale_price = 0;
        $product->weight = 0;
        $product->default_on = 0;
        $product->width = 0;
        $product->height = 0;
        $product->depth = 0;
        $product->unit_price = 0;
        $product->id_supplier = 0;
        $product->id_tax_rules_group = 0;
        $product->id_color_default = 0;
        $product->on_sale = 0;
        $product->online_only = 0;
        $product->minimal_quantity = 1;
        $product->unit_price_ratio = 0;
        $product->out_of_stock = 2;
        $product->active = 1;
        $product->available_for_order = 1;
        $product->condition = 'new';
        $product->show_price = 1;

        // Valores del producto
        $product->name[SHOP_LANG] = Encoding::toUTF8($tire->nombre);
        $product->id_category_default = 2;
        $product->id_manufacturer = $id_manufacturer;
        $product->ean13 = $tire->ean13;
        $product->quantity = (int)$tire->stock;
        $product->ecotax = $tire->ecotasa;
        $product->price = $tire->precioneto;

        // SEO
        (new FillSeo())->run($product, $tire);

        try {
            $product->add();
        } catch (\Exception $e) {
            $from = "info@yofindo.com";
            $to = EMAIL_ADMIN;
            $subject = "Error en Yofindo";
            $message = "Error al agregar producto con referencia " . $product->reference;
            $headers = "From:" . $from;
            mail($to, $subject, $message, $headers);
        }

        // Añadidas las IDs de las categorias de la nueva tienda (desarrollar multipleas categorias a partir de aqui)
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
                break;//Turismo
            case 'L4':
                $categories[] = 11;
                break;//4x4
            case 'L0':
                $categories[] = 12;
                break;//Furgoneta
            case 'T0':
                $categories[] = 13;
                break;//Camion
        }
        $product->addToCategories($categories);

        Utils::printDebug(sprintf("[new Product] id: %s\n", $product->id));

        (new UpdateFeatures())->run($product->id, $tire);
        (new UpdatePhoto())->run($product, $tire);
        (new AddReferenceOriginalToProduct())->run($product, $tire->reference_ori);
        (new CheckFeaturesValues())->run($product, $tire);
        (new InsertDescription())->short($product, $tire);
        (new InsertDescription())->long($product, $tire);

        if ($tire->stock > 4) {
            \StockAvailable::setProductOutOfStock($product->id, 1);
        } else {
            \StockAvailable::setProductOutOfStock($product->id, 2);
        }

        //Se guardan los flags para la sincronización con el ERP
        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product
            SET date_erp = now(),
            hash_erp= \'' . $tire->hash . '\'
            WHERE id_product = ' . $product->id);
        return $product;
    }
}
