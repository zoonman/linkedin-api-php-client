<?php

namespace Pricat\Services\Manufacturer;

use Pricat\Entities\Manufacturer;

class GetManufacturers
{
    /**
     * @var BagManufacturers
     */
    private $bag;

    public function __construct()
    {
        $this->bag = new BagManufacturers();
    }

    /**
     * @return BagManufacturers
     */
    public function run()
    {
        $sql = 'SELECT id_manufacturer, name, active FROM ' . _DB_PREFIX_ . 'manufacturer ORDER BY name, active DESC';
        foreach (\Db::getInstance()->ExecuteS($sql) as $item) {
            $manufacturer = new Manufacturer($item['id_manufacturer'], trim($item['name']), $item['active']);
            $this->bag->add($manufacturer);
        }
        return $this->bag;
    }
}
