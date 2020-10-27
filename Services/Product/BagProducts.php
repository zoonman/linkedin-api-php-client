<?php


namespace Pricat\Services\Product;


use Pricat\Entities\Product;

class BagProducts
{
    /**
     * @var $arr []
     */
    private $bag;

    public function __construct()
    {
        $this->bag = [];
    }

    public function add(Product $product)
    {
        $this->bag[$product->getReference()] = $product;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->bag;
    }

    /**
     * @param $reference
     * @return bool
     */
    public function existsProductReference($reference)
    {
        return array_key_exists($reference, $this->bag);
    }
}
