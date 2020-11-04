<?php


namespace Pricat\Services\Product;

use PrestaShopException;
use Pricat\Entities\Tire;
use Pricat\Utils\Helper as Utils;
use Product;

class InsertDescription
{

    public function short(Product $productModelPrestashop, Tire $tire)
    {
        $label = "";
        $classSeason = "fas fa-sun";
        if ($tire->temporadaText == "4 Estaciones") {
            $label = "Neumático 4 estaciones";
        }
        if ($tire->temporadaText == "Invierno") {
            $label = "Neumático de invierno";
            $classSeason = "fas fa-snowflake";
        }
        if ($tire->temporadaText == "Verano") {
            $label = "Neumático de verano";
        }
        $str = <<<EOF
            <div class="etiqueta">
               <span class="{$tire->eficienciaA}"> <i class="fas fa-gas-pump" title="Resistencia "></i>&nbsp;</span>&nbsp;&nbsp;
               <span class="{$tire->eficienciaB}">  <i class="fas fa-cloud-rain" title="Adherencia"></i>&nbsp;</span>&nbsp;&nbsp; 
               <i class="fas fa-volume-up" title="Índice sonoro"></i>&nbsp; dB &nbsp;
               <i class="$classSeason" title="$label"></i>
            </div>
        EOF;

        $productModelPrestashop->description_short = $str;
        try {
            $productModelPrestashop->save();
        } catch (PrestaShopException $e) {
            Utils::printInfo(sprintf("[Error: short description] No se ha podido insertar la descripcion del producto: %s\n", $productModelPrestashop->id));
        }
    }

    public function long(Product $productModelPrestashop, Tire $tire)
    {
        $marcaFormatted = ucwords($tire->marca);

        $firstBlock = <<<EOF
            <h2>{$tire->modelo} {$tire->medida}</h2>
            <p>El neumático {$tire->nombre} de la marca {$marcaFormatted} te ofrece altas prestaciones en todo tipo de superficies.</p>
        EOF;

        $blockTextTemporada = "";

        if ($tire->temporadaText == "Verano") {
            $blockTextTemporada = <<<EOF
            <p>Se trata de un neumático de verano apto para circular en condiciones ambientales normales y durante los meses más calurosos donde la carretera presenta temperaturas más altas.</p>
        EOF;
        }

        if ($tire->temporadaText == "Invierno") {
            $blockTextTemporada = <<<EOF
            <p>Se trata de un neumático de invierno apto para circular en los meses más fríos y en condiciones meteorológicas adversas .</p>
        EOF;
        }

        if ($tire->temporadaText == "4 Estaciones") {
            $blockTextTemporada = <<<EOF
            <p>Se trata de un neumático 4 estaciones apto para circular durante todo el año.</p>
        EOF;
        }

        $blockRunFalt = "";
        if ($tire->runflat == "1") {
            $blockTextTemporada = <<<EOF
            <p>Se trata de un neumático runflat o antipinchazos, el cual permite en caso de pinchazo, seguir circulando hasta 250 kilómetros (según el tipo de fabricante) y a una velocidad que no supere los 80km/h.</p>
        EOF;
        }

        $blockMedidas = <<<EOF
            <p>Cuenta con unas medidas de: anchura de {$tire->anchura} mm, un perfil de {$tire->altura} mm y un diámetro de <strong>R {$tire->diametro}</strong>.</p>
            <p>Su índice de carga es de {$tire->carga}{$this->formatKg($tire->carga)}.</p>
            <p>El {$tire->nombre} tiene un <strong>índice de velocidad {$tire->velocidad}</strong>{$this->formatSpeed($tire->velocidad)}.</p>
        EOF;

        if ($tire->eficienciaB == "A" || $tire->eficienciaB == "B" || $tire->eficienciaB == "C") {
            $blockAdherencia = <<<EOF
            <h3>Adherencia y frenado</h3>
            <p>- Alta durabilidad y adherencia en carreteras</p>
            <p>- Buena estabilidad en condiciones climatológicas adversas</p>
            <p>Buena capacidad de frenado</p>
        EOF;
        } else {
            $blockAdherencia = <<<EOF
            <h3>Adherencia y frenado</h3>
            <p>- Durabilidad y adherencia moderadas</p>
            <p>- Estabilidad media en condiciones climatológicas adversas</p>
            <p>- Capacidad de frenado moderada</p>
        EOF;
        }

        $productModelPrestashop->description = "$firstBlock $blockTextTemporada $blockRunFalt $blockMedidas $blockAdherencia";
        try {
            $productModelPrestashop->save();
        } catch (PrestaShopException $e) {
            Utils::printInfo(sprintf("[Error: long description] No se ha podido insertar la descripcion del producto: %s\n", $productModelPrestashop->id));
        }
    }

    private function formatSpeed(string $value): string
    {
        $format = "";
        if ($value == "M") {
            $format = "130";
        }
        if ($value == "P") {
            $format = "150";
        }
        if ($value == "S") {
            $format = "180";
        }
        if ($value == "R") {
            $format = "170";
        }
        if ($value == "U") {
            $format = "200";
        }
        if ($value == "VR") {
            $format = ">210";
        }
        if ($value == "W") {
            $format = "270";
        }
        if ($value == "N") {
            $format = "140";
        }
        if ($value == "Q") {
            $format = "160";
        }
        if ($value == "T") {
            $format = "190";
        }
        if ($value == "H") {
            $format = "210";
        }
        if ($value == "ZR") {
            $format = ">240";
        }
        if ($value == "V") {
            $format = "240";
        }
        if ($value == "Y") {
            $format = "300";
        }
        if (empty($format)) {
            return $format;
        }
        return ", este índice permite una velocidad máxima de $format Km/h";
    }

    private function formatKg(string $value): string
    {
        $format = "";
        if ($value == "65") {
            $format = "290";
        }
        if ($value == "66") {
            $format = "300";
        }
        if ($value == "69") {
            $format = "325";
        }
        if ($value == "72") {
            $format = "355";
        }
        if ($value == "75") {
            $format = "387";
        }
        if ($value == "78") {
            $format = "425";
        }
        if ($value == "81") {
            $format = "462";
        }
        if ($value == "84") {
            $format = "500";
        }
        if ($value == "67") {
            $format = "307";
        }
        if ($value == "70") {
            $format = "335";
        }
        if ($value == "73") {
            $format = "365";
        }
        if ($value == "76") {
            $format = "400";
        }
        if ($value == "79") {
            $format = "437";
        }
        if ($value == "82") {
            $format = "475";
        }
        if ($value == "85") {
            $format = "515";
        }
        if ($value == "68") {
            $format = "315";
        }
        if ($value == "71") {
            $format = "345";
        }
        if ($value == "74") {
            $format = "375";
        }
        if ($value == "77") {
            $format = "412";
        }
        if ($value == "80") {
            $format = "450";
        }
        if ($value == "83") {
            $format = "487";
        }
        if ($value == "86") {
            $format = "530";
        }
        if (empty($format)) {
            return $format;
        }
        return ", que equivale a una <strong>carga máxima de $format kilogramos</strong>";
    }
}
