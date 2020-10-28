<?php

namespace Pricat\Services\Csv;

use Exception;
use Pricat\Entities\Product;
use Pricat\Services\Manufacturer\AddManufacturer;
use Pricat\Services\Manufacturer\BagManufacturers;
use Pricat\Services\Manufacturer\GetManufacturers;
use Pricat\Services\Product\BuilderTire;
use Pricat\Services\Product\DeactivateProduct;
use Pricat\Services\Product\DeactivateProductWithEmptyHash;
use Pricat\Services\Product\GetProducts;
use Pricat\Services\Product\NewProduct;
use Pricat\Services\Product\UpdateProduct;
use Pricat\Utils\Helper as Utils;
use SplFileInfo;
use SplFileObject;

class Management
{

    /**
     * @var BagManufacturers
     */
    private $manufacturers;
    /**
     * @var array
     */
    private $products;
    /**
     * @var array
     */
    private $activeProducts;
    /**
     * @var array
     */
    private $redirect301;

    public function __construct()
    {
        $this->manufacturers = (new GetManufacturers())->run();
        $serviceGetProducts = new GetProducts();
        $this->products = $serviceGetProducts->getAllProducts();
        $this->activeProducts = $serviceGetProducts->getActiveProducts();
        $this->redirect301 = [];
    }

    /**
     * @param bool $todo
     * @throws Exception
     */
    public function run(bool $todo = false)
    {
        $fileInfo = new SplFileInfo(PATH_CSV_FILE);
        if (!$fileInfo->isReadable()) {
            throw new Exception('CSV file is not readable: ' . PATH_CSV_FILE);
        }

        $fileObj = $fileInfo->openFile('r');
        $fileObj->flock(LOCK_SH);

        $eofReached = false;
        $fileObj->fseek(-50, SEEK_END);
        while (!$fileObj->eof()) {
            if (strpos($fileObj->fgets(), 'END_OF_FILE') !== false) {
                $eofReached = true;
                $fileObj->rewind();
                break;
            }
        }

        if (!$eofReached) {
            throw new Exception('EOF not found');
        }

        $fileObj->setFlags(SplFileObject::READ_CSV);
        $fileObj->setCsvControl(';');
        $fileObj->seek(3);

        //Variables para cache y logs
        $acciones = ['Sin cambios', 'Sin stock', 'Cambio stock', 'Actualizacion', 'Nuevo', 'Productos nuevos sin stock (No importados)'];
        $datosupd = [0, 0, 0, 0, 0];
        $arrprod = [];
        $nostock = [];

        while (!$fileObj->eof()) {
            $fields = $fileObj->current();
            $fileObj->next();

            $fields = array_map('trim', $fields);
            $fields = array_map('addslashes', $fields);

            if ($fields[0] == 'END_OF_FILE') {
                break;
            }

            if (empty($fields) || count($fields) < 70) {
                Utils::printInfo("Error import\n");
                continue;
            }

            $n = (new BuilderTire())->build($fields);
            Utils::printDebug(sprintf("Import [%s]: %s - %s\n", $fields[1], $n->reference, $n->nombre));

            // Crear marca si no existe
            if (!empty($n->marca) && !$this->manufacturers->existsManufacturer($n->marca)) {
                $n->marca = (new AddManufacturer($this->manufacturers))->run($n->marca);
            }

            //Actualizamos producto, creamos uno nuevo o no hacemos nada según el caso
            $temp = microtime(true);
            $product = 0;
            if (array_key_exists($n->reference, $this->products)) {
                /** @var $product Product */
                $product = $this->products[$n->reference];
                //$product->id = $product->getId();
                $est = (new UpdateProduct($this->manufacturers))->run($product, $n, $todo);
                if (!$n->stock) {
                    $nostock[] = $product->getId();
                }
            } else if ($n->stock > 0) {
                $product = (new NewProduct($this->manufacturers))->run($n);
                $est = 4;
            } else $est = 5;

            //Si se ha actuado sobre un producto sacamos información y hacemos acciones adicionales
            if ($product) {
                $datosupd[$est]++;
                if ($est) {
                    Utils::printInfo($product->id . '->' . $acciones[$est] . ' -> ' . $n->stock . ' -> ' . ((microtime(true) - $temp) * 0.6) . "\n");
                    $arrprod[] = $product->id;
                }

                if ($est > 2 and (property_exists($n, 'marca') && property_exists($n, 'nombre'))) {
                    if (SHOP_NAME == 'muchoneumatico') {
                        $this->redirect301[] = [
                            \Tools::link_rewrite(sprintf('%s-comprar-neumaticos-%s-%s-%s', $product->id, trim($n->marca), trim($n->nombre), $product->ean13)),
                            sprintf('%s-%s', $product->id, $product->link_rewrite[SHOP_LANG])
                        ];
                    }
                }
            }
            // Eliminar referencia del array. Posteriormente se ocultarán los restantes
            unset($this->activeProducts[$n->reference_ori]);

            // Liberar memoria
            unset($fields, $n, $product);
        }

        $fileObj->flock(LOCK_UN);
        $fileObj = null;

        //Resumen de productos tratados
        foreach ($datosupd as $k => $t) {
            Utils::printInfo("Total " . $acciones[$k] . ": " . $t . "\n");
        }

        // Ocultar productos no presentes en el CSV o sin stock
        if (count($nostock)) {
            (new DeactivateProduct())->run($nostock);
        }
        if (count($this->products) > 0) {
            (new DeactivateProductWithEmptyHash())->run(
                array_map(function (Product $product) {
                    return $product->getId();
                }, $this->activeProducts)
            );
        }
        //Se devuelve un array con los ids de los productos actualizados
        return $arrprod;
    }
}
