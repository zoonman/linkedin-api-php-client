<?php


namespace Pricat\Services\Manufacturer;


use Pricat\Entities\Manufacturer;
use Pricat\Utils\Helper as Utils;

class AddManufacturer
{
    /**
     * @var BagManufacturers
     */
    private $bag;

    public function __construct(BagManufacturers $bagManufacturers)
    {
        $this->bag = $bagManufacturers;
    }


    public function run(string $name): string
    {
        $name = strtoupper(trim($name));
        Utils::printDebug(sprintf("addManufacturer: %s\n", $name));

        $manufacturer = new \Manufacturer();
        $manufacturer->name = $name;
        $manufacturer->description[SHOP_LANG] = $name;
        $manufacturer->meta_title[SHOP_LANG] = $name;
        $manufacturer->meta_description[SHOP_LANG] = $name;
        $manufacturer->active = true;
        $manufacturer->add();

        $manufacturer = new Manufacturer($manufacturer->id, $manufacturer->name, $manufacturer->active);
        $this->bag->add($manufacturer);

        return $name;
    }
}
