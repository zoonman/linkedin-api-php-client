<?php


namespace Pricat\Services\Product;


use Db;
use Search;

class DeactivateProduct
{

    /**
     * @param array $ids
     */
    public function run($ids)
    {
        Db::getInstance()->Execute(sprintf('UPDATE ' . _DB_PREFIX_ . 'product
            SET visibility = \'none\', available_for_order=0, hash_erp=\'\',
            date_upd = now()
            WHERE id_product IN (%s)', implode(', ', $ids)));

        Db::getInstance()->Execute(sprintf('UPDATE ' . _DB_PREFIX_ . 'product_shop
            SET visibility = \'none\', available_for_order=0,
            date_upd = now()
            WHERE id_product IN (%s)', implode(', ', $ids)));

        //Se desindexan los productos desactivados
        Search::removeProductsSearchIndex($ids);
    }
}
