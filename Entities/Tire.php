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
    public $cubrellantaText;
    public $modelo;
    public $temporada;
    public $temporadaText;
    public $tipo;
    public $tipoText;
    public $consumo;
    public $oferta;
    //private $dot;
    public $recomendado;
    public $stock;
    public $medida;
    public $segmento;
    public $xl;
    public $mS;

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
        $this->ean13 = $this->fields[2];

        $this->marca = $this->fields[12];

        $this->ecotasa = Utils::getFloatFormatted($this->fields[64], 2);
        $this->precioneto = Utils::getFloatFormatted($this->fields[62], 2);

        // Calcular precioneto con IVA, menos ecotasa (la suma Prestashop automáticamente)
        $this->precioneto = (($this->precioneto + $this->ecotasa) * IVA) - $this->ecotasa;

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
        $this->tipo = $this->fields[55];
        $this->consumo = $this->fields[66];

        $this->nombre = ucwords("{$this->marca} {$this->modelo}") . strtoupper("{$this->anchura}/{$this->altura} R {$this->diametro} {$this->carga}{$this->velocidad}");

        $descripcion_last_char = substr($this->nombre, -1);
        $this->oferta = ($descripcion_last_char == '.') ? 1 : 0;
        $this->recomendado = ($descripcion_last_char == ',') ? 1 : 0;

        $this->assingTemporada();
        $this->assingTipo();
        $this->assingCubreLlanta();
        $this->assingSegmento();

        $this->medida = "{$this->anchura} {$this->altura} R{$this->diametro}";
        $this->xl = strpos($this->nombre, "XL") !== false ? "Sí" : "";
        $this->mS = $this->fields[75] == '1' ? "Sí" : "";

    }

    private function assingTemporada()
    {
        if ($this->fields[54] == '300') {
            $this->temporadaText = "4 Estaciones";
            return;
        }

        if ($this->fields[54] == '200') {
            $this->temporadaText = "Invierno";
            return;
        }
        $this->temporadaText = "Verano";
    }

    private function assingTipo()
    {
        if ($this->fields[55] == 'C0') {
            $this->tipoText = "Turismo";
            return;
        }

        if ($this->fields[55] == 'L0') {
            $this->tipoText = "Furgoneta";
            return;
        }

        if ($this->fields[55] == 'L4') {
            $this->tipoText = "4x4";
            return;
        }

        if ($this->fields[55] == 'T0') {
            $this->tipoText = "Camión";
            return;
        }
    }

    private function assingCubreLlanta()
    {
        if ($this->fields[44] == 'X') {
            $this->cubrellantaText = "Sí";
            return;
        }
    }

    private function assingSegmento()
    {
        if ($this->fields[79] == 'PREMIUM') {
            $this->segmento = "Premium";
        }
        if ($this->fields[79] == 'QUALITY') {
            $this->segmento = "Calidad/Precio";
        }
        if ($this->fields[79] == 'BUDGET') {
            $this->segmento = "Ecónomicos";
        }
    }

}
