<?php

require "bootstrap.php";

use Pricat\Services\Product\GetProductReference;

$bags = (new GetProductReference())->run();

var_dump($bags->getItems());
