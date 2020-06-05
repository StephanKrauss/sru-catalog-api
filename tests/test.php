<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
//use libreja/Sru;
use Libreja\SruCatalog;
$sruCatalog = new SruCatalog\CatalogMain();
$sruCatalog->service = "dnb";
var_dump($sruCatalog->parse([
  "title" => 'Meier',
]));