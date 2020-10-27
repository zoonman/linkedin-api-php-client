<?php


namespace Pricat\Services\Product;


use Db;
use Pricat\Entities\Product;

class GetProductReference
{
    /**
     * @var BagProducts
     */
    private $products;

    /**
     * @var array
     */
    private $duplicated;

    public function __construct()
    {
        $this->products = new BagProducts();
        $this->duplicated = [];
    }

    public function run()
    {
        //Se obtienen los datos de todos los productos de la base de datos incluidos datos para cache
        $sql = 'SELECT id_product, reference_ori, active, date_erp, hash_erp, date_upd, quantity FROM ' . _DB_PREFIX_ . 'product WHERE reference_ori IS NOT NULL';
        foreach (Db::getInstance()->ExecuteS($sql) as $item) {

            $reference_ori = (string)$item['reference_ori'];

            if ($this->products->existsProductReference($reference_ori)) {
                /* @var $item Product */
                $item = $this->products->getItems()[$reference_ori];
                $this->duplicated[] = $item->getId();
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
            $this->products->add($product);
        }

        if (count($this->duplicated) > 0) {
            (new DeactivateProductWithEmptyHash())->run($this->duplicated);
        }

        return $this->products;
    }
}
