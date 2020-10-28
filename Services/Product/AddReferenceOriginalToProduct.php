<?php

namespace Pricat\Services\Product;

use Db;
use Pricat\Utils\Helper as Utils;
use Product;

class AddReferenceOriginalToProduct
{
    /**
     * Meter referencia original en el nuevo campo
     * @param Product $productModelPrestahop
     * @param string $reference
     */
    public function run(Product $productModelPrestahop, string $reference)
    {
        try {
            Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product
                SET reference_ori = \'' . $reference . '\',
                reference = \'' . $reference . '\'
                WHERE id_product = ' . pSQL($productModelPrestahop->id));
        } catch (\Exception $e) {
            Utils::printInfo(sprintf("[Error: referenciaOri] No se ha podido actualizar la referencia del producto: %s\n", $productModelPrestahop->id));
        }
    }
}
