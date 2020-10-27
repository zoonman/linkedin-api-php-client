<?php

namespace Pricat\Entities;

class Product
{
    private $id;
    private $active;
    private $dateErp;
    private $hashErp;
    private $dateUpd;
    private $stock;
    private $reference;

    public function __construct($id, $active, $dateErp, $hashErp, $dateUpd, $stock, $reference)
    {
        $this->id = $id;
        $this->active = $active;
        $this->dateErp = $dateErp;
        $this->hashErp = $hashErp;
        $this->dateUpd = $dateUpd;
        $this->stock = $stock;
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getDateErp()
    {
        return $this->dateErp;
    }

    /**
     * @return mixed
     */
    public function getHashErp()
    {
        return $this->hashErp;
    }

    /**
     * @return mixed
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }


}
