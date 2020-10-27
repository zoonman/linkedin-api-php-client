<?php

require "bootstrap.php";

use Pricat\Services\Product\GetProducts;

$serviceProducts = new GetProducts();


var_dump($serviceProducts->getActiveProducts());
