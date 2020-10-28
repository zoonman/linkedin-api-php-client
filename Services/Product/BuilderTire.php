<?php


namespace Pricat\Services\Product;


use Pricat\Entities\Tire;

class BuilderTire
{
    /**
     * Mapea los campos del array en una entidad Tire (Neumatico)
     * @param array $fields
     * @return Tire
     */
    public function build(array $fields): Tire
    {
        $tire = new Tire($fields);
        $tire->createHash();
        $tire->applyStock();
        $tire->addCustomFields();
        return $tire;
    }
}
