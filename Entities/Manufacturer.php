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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

}
