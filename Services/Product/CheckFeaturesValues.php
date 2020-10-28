<?php

namespace Pricat\Services\Product;


use Pricat\Entities\Tire;
use Pricat\Utils\Helper as Utils;
use Product;

class CheckFeaturesValues
{
    /**
     * Comprueba si el producto cargado tiene características para recargarlas en cualquier caso
     * @param Product $product
     * @param Tire $tire
     */
    public function run(Product $product, Tire $tire)
    {
        try {
            $productFeatures = $product->getFeatures();
            if ($productFeatures && is_array($productFeatures) && count($productFeatures) <= 0) {
                (new UpdateFeatures())->run($product->id, $tire);
            }
        } catch (\Exception $e) {
            Utils::printInfo(sprintf("[Error: checkFeaturesValues] Error al comprobar las características del producto: %s\n", $product->id));
        }
    }
}
