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
        if ($tire->temporadaText == "4 Estaciones") {
            $label = "Neumático 4 estaciones";
        }
        if ($tire->temporadaText == "Invierno") {
            $label = "Neumático de invierno";
        }
        if ($tire->temporadaText == "Verano") {
            $label = "Neumático de verano";
        }
        $str = <<<EOF
            <div class="etiqueta">
               <span class=""> <i class="fas fa-gas-pump" title="Resistencia "></i>&nbsp;</span>&nbsp;&nbsp;
               <span class="">  <i class="fas fa-cloud-rain" title="Adherencia"></i>&nbsp;</span>&nbsp;&nbsp; 
               <i class="fas fa-volume-up" title="Índice sonoro"></i>&nbsp; dB &nbsp;
               <i class="fas fa-sun" title="$label"></i>
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
            <p>Su índice de carga es de {$tire->carga}, que equivale a una <strong>carga máxima de {$tire->carga} kilogramos</strong>.</p>
            <p>El {$tire->nombre} tiene un <strong>índice de velocidad {$tire->velocidad}</strong>, este índice permite una velocidad máxima de {$tire->velocidad} Km/h.</p>
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
        $modeloLower = strtolower($tire->modelo);
        $blockMoreText = <<<EOF
            <p>Encuentra más neumáticos con la medida {$tire->anchura}/perfil/R{$tire->diametro}. Recuerda también que puedes encontrar neumáticos equivalentes 
            a la medida {$tire->anchura}/perfil/R{$tire->diametro}</p> utilizando nuestra 
            <a href="https://www.yofindo.com/guias/equivalencia-de-neumaticos">calculadora de equivalencias.</a>.</p>
            <p>Encuentra otros neumáticos de la marca {$productModelPrestashop->manufacturer_name} al mejor precio.</p>
            <p>¿Necesitas otra versión o medida del {$tire->modelo}? Encuentra más neumáticos <a href="https://www.yofindo.com/comprar-neumaticos/{$modeloLower}">{$tire->modelo}</a>.</p>
        EOF;

        $productModelPrestashop->description = "$firstBlock $blockTextTemporada $blockRunFalt $blockMedidas $blockAdherencia $blockMoreText";
        try {
            $productModelPrestashop->save();
        } catch (PrestaShopException $e) {
            Utils::printInfo(sprintf("[Error: long description] No se ha podido insertar la descripcion del producto: %s\n", $productModelPrestashop->id));
        }
    }
}
