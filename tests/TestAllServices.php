<?php
use PHPUnit\Framework\TestCase;
use Libreja\SruCatalog\CatalogMain;

class TestAllServices extends TestCase
{
//  public function testExpectFooActualFoo()
//  {
//    $this->expectOutputString('foo');
//    print 'foo';
//  }

  public function testCheckServices()
  {
    $sruCatalog = new CatalogMain();
    $sruCatalog->service = "gvk";
    $fetch = $sruCatalog->parse([
      "isxn" => '9780553375404',
    ]);
    $book1 = $fetch["records"][0];
    $this->assertEquals(263, $book1["numberOfPages"]);
    $this->assertEquals("Quinn, Daniel", $book1["author"]);
    $this->assertEquals("Bantam Books", $book1["publisher"]);


    $sruCatalog->service = "dnb";
    $fetch = $sruCatalog->parse([
      "title" => 'Der Richter und sein Henker',
      "year" => '1985',
      "publisher" => 'Reclam',
      "author" => 'Dürrenmatt',
    ]);
    $book2 = $fetch["records"][0];
    $this->assertEquals(1, $fetch["numberOfRecords"]);

    $this->assertEquals("1985-00-00", $book2["datePublished"]);
    $this->assertEquals("Leipzig", $book2["publisherPlace"]);
    $this->assertEquals("ger", $book2["inLanguage"]);

//    $this->expectOutputString('bar');
  }
}
