<?php


namespace Pricat\Services\Manufacturer;


use Pricat\Entities\Manufacturer;

class ActivateManufacturer
{
    /**
     * @var BagManufacturers
     */
    private $manufacturers;

    public function __construct(BagManufacturers $bagManufacturers)
    {
        $this->manufacturers = $bagManufacturers;
    }

    public function run(string $name)
    {
        /* @var $item Manufacturer */
        $item = $this->manufacturers->getItems()[$name];
        if ($item->getActive() == 1) {
            return true;
        }
        $manufacturer = new \Manufacturer($item->getId());
        if (!$manufacturer) {
            return false;
        }
        $manufacturer->active = 1;
        return $manufacturer->save();
    }
}
