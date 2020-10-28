<?php


namespace Pricat\Services\Manufacturer;


use Pricat\Entities\Manufacturer;

class BagManufacturers
{
    /**
     * @var $arr []
     */
    private $bag;

    public function __construct()
    {
        $this->bag = [];
    }

    public function add(Manufacturer $manufacturer)
    {
        $this->bag[$manufacturer->getName()] = $manufacturer;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->bag;
    }

    /**
     * @param string|null $name
     * @return int
     */
    public function getIdManufacturer(?string $name): int
    {
        if (!array_key_exists($name, $this->bag)) {
            return -1;
        }
        /* @var $item Manufacturer */
        $item = $this->bag[$name];
        return $item->getId();
    }

    /**
     * @param string|null $name
     * @return bool
     */
    public function existsManufacturer(?string $name): bool
    {
        return array_key_exists($name, $this->bag);
    }
}
