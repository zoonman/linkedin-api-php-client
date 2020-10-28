<?php


namespace Pricat\Entities;


use Pricat\Utils\Helper as Utils;

class Tire
{
    public $reference;
    public $reference_ori;
    public $nombre;
    public $hash;
    public $ean13;
    public $marca;
    public $ecotasa;
    public $precioneto;
    public $imagen;
    public $anchura;
    public $altura;
    public $diametro;
    public $carga;
    public $velocidad;
    public $eficienciaA;
    public $eficienciaB;
    public $eficienciaC;
    public $nieve;
    public $runflat;
    public $estaciones;
    public $percarretera;
    public $percampo;
    public $cubrellanta;
    public $modelo;
    public $temporada;
    public $tipo;
    public $consumo;
    public $oferta;
    //private $dot;
    public $recomendado;
    public $stock;

    /**
     * @var array
     */
    private $fields;

    public function __construct($fields)
    {
        $this->fields = $fields;
        $this->initBasicFields();
        // esta de stock me daba guerra y metia solo 0 a todo
        //$this->stock = Utils::getNumberFormatted($fields[71], 0);
    }

    private function initBasicFields()
    {
        $this->reference = $this->fields[4];
        $this->reference_ori = $this->fields[4];
        $this->nombre = "";
    }

    public function createHash()
    {
        $this->hash = substr(sha1(GESTIONCSV_HASH_VERSION . json_encode($this)), 0, 32);
    }

    public function applyStock()
    {
        $this->stock = $this->fields[71];
    }

    public function addCustomFields()
    {
        if ($this->stock > 4) {
            $this->ean13 = $this->fields[2];

            $this->nombre = str_replace(array('\\"NIEVE\\"', '\\"RUNFLAT\\"', '\\"4E\\"'), ' ', $this->fields[5]);
            $this->nombre = str_replace("´", "'", $this->nombre);
            $this->nombre = utf8_decode($this->nombre);

            if (in_array($this->nombre[mb_strlen($this->nombre) - 1], ["@", ".", ","])) {
                $this->nombre = substr($this->nombre, 0, -1);
            }

            $this->marca = $this->fields[12];

            $this->ecotasa = Utils::getFloatFormatted($this->fields[64], 2);
            $this->precioneto = Utils::getFloatFormatted($this->fields[62], 2);

            // Calcular precioneto con IVA, menos ecotasa (la suma Prestashop automáticamente)
            $this->precioneto = (($this->precioneto + $this->ecotasa) * $this->iva) - $this->ecotasa;

            $this->imagen = strtolower($this->fields[24]);

            $this->anchura = Utils::getNumberFormatted($this->fields[29], 2);
            $this->altura = Utils::getNumberFormatted($this->fields[31], 2);
            $this->diametro = Utils::getNumberFormatted($this->fields[16], 2);
            $this->carga = Utils::getNumberFormatted($this->fields[37], 2);
            $this->velocidad = $this->fields[41];

            $this->eficienciaA = $this->fields[67];
            $this->eficienciaB = $this->fields[68];
            $this->eficienciaC = $this->fields[65];

            $this->nieve = ($this->fields[54] == '200') ? 1 : 0;
            $this->runflat = $this->fields[50];
            $this->estaciones = ($this->fields[54] == '300') ? 1 : 0;

            //$this->dot = $this->>$this->fields[8];
            $this->percarretera = $this->fields[26];
            $this->percampo = $this->fields[27];
            $this->cubrellanta = $this->fields[44];
            $this->modelo = $this->fields[52];
            $this->temporada = $this->fields[54];
            $this->tipo = $this->fields[55];
            $this->consumo = $this->fields[66];

            $descripcion_last_char = substr($this->nombre, -1);
            $this->oferta = ($descripcion_last_char == '.') ? 1 : 0;
            $this->recomendado = ($descripcion_last_char == ',') ? 1 : 0;
        }
    }


}
