<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
//use libreja/Sru;
echo '<h1>Test searching for book:</h1><br />';
use Libreja\SruCatalog;
$sruCatalog = new SruCatalog\CatalogMain();
$sruCatalog->service = "dnb";
var_dump($sruCatalog->parse([
  "title" => 'Meier',
]));