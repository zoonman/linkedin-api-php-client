<?php


namespace Pricat\Entities;


class Manufacturer
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $active;

    /**
     * Manufacturer constructor.
     * @param int $id
     * @param string $name
     * @param int $active
     */
    public function __construct($id, $name, $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
    }
}
