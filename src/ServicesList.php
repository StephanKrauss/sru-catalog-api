<?php

namespace Libreja\SruCatalog;

class ServicesList
{
  public function getServices(){
    return $this->services;
  }
  public $supportedKeys = [
    "all" => [
      "de"=> "Alles"
      ],
    "title" => [
      "de"=> "Titel"
    ],
    "author" => [
      "de"=> "Autor"
    ],
    "subject" => [
      "de"=> "Stichwort"
    ],
    "idn" => [
      "de"=> "Identifikationnr des Katalogs (ppn)"
    ],
    "isxn" => [
      "de"=> "ISXN"
    ],
    "isbn" => [
      "de"=> "ISBN"
    ],
    "issn" => [
      "de"=> "ISSN"
    ],
    "publisher" => [
      "de"=> "Verleger/Firma"
    ],
    "publisherPlace" => [
      "de"=> "Verleger Ort"
    ],
    "year" => [
      "de"=> "Jahr"
    ],
    "language" => [
      "de"=> "Sprache"
    ],
    "corperation" => [
      "de"=> "Körperschaft"
    ],
  ];
  private $services = [

    "gvk" => [
//Niedersachsen, Sachsen-Anhalt, Thüringen, Hamburg, Bremen, Schleswig-Holstein und Mecklenburg-Vorpommern
      "name" => "Gemeinsamer Verbundkatalog (GVK)",//Gemeinsamer Bibliotheksverbund (GBV)",
      "docs" => "https://verbundwiki.gbv.de/display/VZG/SRU",
      "host" => "http://sru.k10plus.de/gvk",
      "searchlist" => "http://sru.k10plus.de/gvk",
      "permalink" => "https://kxp.k10plus.de/DB=2.1/PPNSET?PPN=",
      "search" => [
        "all" => "pica.all",
        "title" => "pica.tit",
        "author" => "pica.gpk",//prs per  >[GPK] Person/Körperschaft (Stichwort)
        "subject" => "pica.slw",//schlagwort
        "idn" => "pica.ppn",
        "isxn" => "pica.num",
        "isbn" => "pica.isb",
        "issn" => "pica.iss",
        "publisher" => "pica.vlg", //NUR VERLAG?   Veröffentlichungssort pica vlo
        "publisherPlace" => "pica.vlo",
        "year" => "pica.jah",
        "language" => "pica.spr",
        "corperation" => "pica.kor", //[KOR] Körperschaft, Konferenz, Geografikum (Stichwort)
      ],
    ],
    "swb" => [
//"name"      => "K10Plus",
      //Südwestdeutscher Bibliotheksverbund Baden-Württemberg, Saarland, Sachsen (SWB)
      "docs" => "https://wiki.k10plus.de/pages/viewpage.action?pageId=132874251",
      "host" => "https://sru.bsz-bw.de/swb299",
      "recordSchema" => "marcxmlvb",//siehe https://wiki.k10plus.de/pages/viewpage.action?pageId=132874251#SRU/SRWBSZ-Format-%3CrecordSchema%3E
      "permalink" => "https://kxp.k10plus.de/DB=2.1/PPNSET?PPN=",
      "search" => [
        "title" => "pica.tit",
        "author" => "pica.pne",
        "subject" => "",
        "idn" => "",
        "isxn" => "pica.num",
        "year" => "",
      ],
      "exp" => '
Liste aller such moeglichkeiten
https://sru.bsz-bw.de/swb
'
    ],
    "bvb" => [
      "name" => "Bibliotheksverbund Bayern",
      "docs" => "https://www.bib-bvb.de/web/b3kat/z39.50",
      "host" => "http://bvbr.bib-bvb.de:5661/bvb01sru",
      "permalink" => "http://gateway-bayern.de/",
      "query" => '
http://bvbr.bib-bvb.de:5661/bvb01sru
Suche in allen Feldern(das Format-Präfixvor dem Suchattribut entfällt hier query=musik
marcxml.title=TITLE
marcxml.creator= Autor ex. "biagi, dario" (Die „%22“ stehen für das Doppel-Hochkomma.)
marcxml.subject = Schlagwort
marcxml.idn = Suche nach Verbund-ID („BV-Nummer“
extra:
marcxml.isbn
marcxml.identifier : funktioniert nicht
',
      "search" => [
        "title" => "marcxml.title",
        "author" => "marcxml.creator",
        "subject" => "marcxml.subject",
        "idn" => "marcxml.idn",
        "gndId" => "marcxml.gndid",
        "isbn" => "marcxml.isbn",
        "isxn" => "marcxml.isbn",
//        "year" => "",
      ]
    ],

    "dnb" => [
      "name" => "Deutsche Nationalbibliothek",
//https://www.dnb.de/EN/Professionell/Metadatendienste/Datenbezug/SRU/sru_node.html#doc250692bodyText8
      "docs" => "https://www.dnb.de/sru",
      "host" => "https://services.dnb.de/sru/dnb",
//      https://services.dnb.de/sru/bib?operation=searchRetrieve&version=1.1&query=isl%3DAT-OBV&recordSchema=PicaPlus-xml
      "recordSchema" => "MARC21-xml",
      "accessToken" => "c0b87d1797904dc9e4af2a6e5062b894",
      "permalink" => "http://d-nb.info/",
      "search" => [
        "all" => "dc.any",
        "title" => "dc.title",
        "fullTitle" => "dnb.tst",
        "author" => "dc.contributor",//creator
        /*<ns:index search="true" scan="false" sort="true" id="dnb.ka"><ns:title lang="de" primary="true">Komponist/Autor</ns:title><ns:map><ns:name set="dnb">ka</ns:name></ns:map></ns:index> */
        "subject" => "dc.subject",
        "isxn" => "dc.identifier",
        "publisher" => "dc.publisher",//Verleger/Firma, Ort
        "publisherPlace" => "dc.publisher",
        "year" => "dc.date",
        "language" => "pica.spr",
        "corperation" => "dnb.koe",
        "idn" => "dnb.identifier", //SAME with ISBN
      ],
      "help" => '
Such indexe
https://services.dnb.de/sru/dnb?operation=explain&version=1.1
https://services.dnb.de/sru/dnb.dma?operation=explain&version=1.1

',
    ],

    "loc" => [
      "name" => "Library of Congress",
      "docs" => "https://www.loc.gov/standards/sru/resources/lcServers.html",
      "host" => "http://lx2.loc.gov:210/lcdb",
//"permalink" => "https://lccn.loc.gov/", // use with LCCN
      "permalink" => "https://catalog.loc.gov/vwebv/holdingsInfo?bibId=",
      "search" => [
        "all" => "cql.anywhere",
        "title" => "dc.title",
        "author" => "dc.author",
        "subject" => "dc.subject",
        "isbn" => "bath.isbn",
        "issn" => "bath.iss",
        "lccn" => "bath.lccn",
        "isxn" => "bath.isbn",
        "corperation" => "bath.corporateName",
        "idn" => "bath.standardIdentifier",
      ],
    ],
//    "swissbib" => [
////"name" => "swissbib",
//      "docs" => "http://www.swissbib.org/wiki/index.php?title=SRU",
//      "host" => "http://sru.swissbib.ch/sru/search/defaultdb",
//    ],
  ];

}