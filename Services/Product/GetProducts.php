<?php


namespace Pricat\Services\Product;


use Db;
use Pricat\Entities\Product;

class GetProducts
{
    /**
     * @var BagProducts
     */
    private $allProducts;

    /**
     * @var array
     */
    private $duplicated;
    /**
     * @var BagProducts
     */
    private $activeProducts;

    public function __construct()
    {
        $this->allProducts = new BagProducts();
        $this->activeProducts = new BagProducts();
        $this->duplicated = [];
        $this->run();
    }

    private function run()
    {
        //Se obtienen los datos de todos los productos de la base de datos incluidos datos para cache
        $sql = 'SELECT id_product, reference_ori, active, date_erp, hash_erp, date_upd, quantity FROM ' . _DB_PREFIX_ . 'product WHERE reference_ori IS NOT NULL';
        foreach (Db::getInstance()->ExecuteS($sql) as $item) {

            $reference_ori = (string)$item['reference_ori'];

            if ($this->allProducts->existsProductReference($reference_ori)) {
                /* @var $productModel Product */
                $productModel = $this->allProducts->getItems()[$reference_ori];
                $this->duplicated[] = $productModel->getId();
            }

            $product = new Product(
                (string)$item['id_product'],
                (string)$item['active'],
                $item['date_erp'],
                (string)$item['hash_erp'],
                $item['date_upd'],
                $item['quantity'],
                $reference_ori
            );
            $this->allProducts->add($product);
        }

        if (count($this->duplicated) > 0) {
            (new DeactivateProductWithEmptyHash())->run($this->duplicated);
        }
    }

    /**
     * @return array
     */
    public function getAllProducts()
    {
        return $this->allProducts->getItems();
    }

    /**
     * @return array
     */
    public function getActiveProducts()
    {
        $this->activeProducts = $this->allProducts;
        return array_filter($this->activeProducts->getItems(), function (Product $product) {
            return $product->getActive() == '1';
        });
    }
}
