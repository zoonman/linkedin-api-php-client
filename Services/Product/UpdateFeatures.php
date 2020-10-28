<?php

namespace Pricat\Services\Product;

use Db;
use FeatureValue;
use Pricat\Entities\Tire;
use Pricat\Utils\Helper as Utils;
use ProductCore;

class UpdateFeatures
{
    /**
     * @var string[]
     */
    private $attributes;

    public function __construct()
    {
        $this->attributes = [
            'altura' => '3',
            'anchura' => '4',
            'diametro' => '5',
            'velocidad' => '6',
            'carga' => '7',
            'ecotasa' => '8',
            'nieve' => '9',
            'runflat' => '10',
            'estaciones' => '11',
            'combustible' => '12',
            'adherencia' => '13',
            'ruido' => '14',
            //'dot'=>'15',
            'percarretera' => '15',
            'percampo' => '16',
            'cubrellanta' => '17',
            'modelo' => '18',
            'temporada' => '19',
            'tipo' => '20',
            'consumo' => '21'
        ];
    }

    public function run($idProduct, Tire $tire)
    {
        //Se borran las caracteristicas previas del producto
        // Asignadas las IDs correctas con las nuevas caracteristicas de la tienda
        Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'feature_product WHERE id_product=' . $idProduct);
        foreach ($this->attributes as $attr) {
            $idFeature = intval($attr);
            if ($attr == '3') {
                $idFeature = 3;
                $value = $tire->altura;
            } else if ($attr == '4') {
                $idFeature = 4;
                $value = $tire->anchura;
            } else if ($attr == '5') {
                $idFeature = 5;
                $value = $tire->diametro;
            } else if ($attr == '6') {
                $idFeature = 6;
                $value = $tire->velocidad;
            } else if ($attr == '7') {
                $idFeature = 7;
                $value = $tire->carga;
            } else if ($attr == '8') {
                $idFeature = 8;
                $value = $tire->ecotasa;
            } else if ($attr == '9') {
                $idFeature = 9;
                $value = $tire->nieve;
            } else if ($attr == '10') {
                $idFeature = 10;
                $value = $tire->runflat;
            } else if ($attr == '11') {
                $idFeature = 11;
                $value = $tire->estaciones;
            } else if ($attr == '12') {
                $idFeature = 12;
                $value = $tire->eficienciaA;
            } else if ($attr == '13') {
                $idFeature = 13;
                $value = $tire->eficienciaB;
            } else if ($attr == '14') {
                $idFeature = 14;
                $value = $tire->eficienciaC;
            } elseif ($idFeature > 14 and $idFeature <= 22) {
                switch ($idFeature) {
                    //case 15:$value=$tire->dot;break;
                    case 15:
                        $value = $tire->percarretera;
                        break;
                    case 16:
                        $value = $tire->percampo;
                        break;
                    case 17:
                        $value = $tire->cubrellanta;
                        break;
                    case 18:
                        $value = $tire->modelo;
                        break;
                    case 19:
                        $value = $tire->temporada;
                        break;
                    case 20:
                        $value = $tire->tipo;
                        break;
                    case 21:
                        $value = $tire->consumo;
                        break;
                }
            } else {
                $idFeature = -1;
                $value = -1;
            }

            Utils::printDebug(sprintf("[actualizaCaracteristicas] %s: %s\n", $idFeature, $value));

            if ($idFeature != -1 && !empty($value)) {
                $idFeatureValue = FeatureValue::addFeatureValueImport($idFeature, $value, null, SHOP_LANG);
                ProductCore::addFeatureProductImport($idProduct, $idFeature, $idFeatureValue);
            }
        }
    }
}
