<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use Libreja\SruCatalog;
$sruCatalog = new SruCatalog\ServicesList();

$table = [];
foreach($sruCatalog->supportedKeys as $key=>$val){
  $table[$key] = [];
}
$html = "| key | German-trans |";
foreach($sruCatalog->getServices() as $serviceKey=>$serviceValue){
  $html .= " ".$serviceKey." |";
  foreach($table as $searchKey => $searchValue){
    array_push($table[$searchKey],array_key_exists($searchKey,$serviceValue["search"]));
  }
}
$html .="\n|-|-|-|-|-|-|-|";

foreach($table as $key => $value){
  $html .= "\n| ".$key." |";

  //German
  $html .= " ".$sruCatalog->supportedKeys[$key]["de"]." |";

  foreach($value as $exists){
    $html .= " ".($exists?"x":"")." |";
  }
}
echo $html;