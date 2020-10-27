<?php


namespace Pricat\Services\Manufacturer;


use Pricat\Entities\Manufacturer;

class BagManufacturers
{
    /**
     * @var $arr Manufacturer[]
     */
    private $bag;

    public function __construct()
    {
        $this->bag = [];
    }

    public function add(Manufacturer $manufacturer)
    {
        array_push($this->bag, $manufacturer);
    }

    /**
     * @return Manufacturer[]
     */
    public function getItems()
    {
        return $this->bag;
    }

}
