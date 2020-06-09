<?php

namespace Libreja\SruCatalog;

//use libreja\SruCatalog\ServicesList;
define('LINEBREAK', '[br]' . PHP_EOL);


class CatalogMain
{

  public $service = null;
  private $services;
  private $marc21Mapping;

  /**
   * Create a new Skeleton Instance
   */
  public function __construct()
  {
    $this->services = new ServicesList();
    $this->services = $this->services->getServices();
    $this->marc21Mapping = Marc21Mapping::$marc21Mapping;
  }

  /**
   * Returns records after requesting it
   * @param $fields
   * @return array
   */
  public function parse($fields)
  {

    $error = false;
    $xml = $this->request($fields);

    if ($xml === false) {
      $error = "notFound";
      return ["error" => $error, "records" => [], "numberOfRecords" => 0];
    }
    $records = [];

    $xmlArray = $this->xml2array($xml, 1, '');
    $count = @$xmlArray['searchRetrieveResponse']['numberOfRecords']['value'];

    if ($count && $count != 0) {

      //if it's a single value array, which it shouldnt be (http://libreja.local:9080/app_dev.php/bibliography/search/gbv/pica.tit=Caucus%20of%20Corruption)
      if (isset($xmlArray['searchRetrieveResponse']['records']['record']['recordData'])) {
        $xmlArray['searchRetrieveResponse']['records']['record'] = array($xmlArray['searchRetrieveResponse']['records']['record']);
      }
      if (empty($xmlArray['searchRetrieveResponse']['records'])) {
        $error = "notValid";
        return ["error" => $error, "records" => [], "numberOfRecords" => 0];
      }
      foreach ($xmlArray['searchRetrieveResponse']['records']['record'] as $key => $title) {
        $item = $this->parseSingleRecord($title);
        $records[] = $item;
      }

    }

    return [
      "error" => $error,
      "records" => $records,
      "numberOfRecords" => $count,
    ];

  }

  /**
   * Request a service using predefined fields
   * @param $fields
   * @param array $params
   * @return bool|false|string
   */
  public function request($fields, $params = array())
  {
    if (!$this->service) {
      throw new \Exception("Please specify service");
    }
    $service = $this->services[$this->service];

    $params["version"] = "1.1"; //result format, maybe change to 1.2
    $params["recordSchema"] = "marcxml";

    if (isset($service["accessToken"])) {
      $params["accessToken"] = $service["accessToken"];
    }
    if (isset($service["recordSchema"])) {
      $params["recordSchema"] = $service["recordSchema"];
    }

    // additional parameters
    $params["operation"] = "searchRetrieve";
    $params["query"] = "";
    if (is_array($fields)) {
      foreach ($fields as $key => $value) {
        if (isset($service["search"][$key]) && $value) {
          if ($params["query"]) {
            $params["query"] .= " and ";
          }
          $params["query"] .= $service["search"][$key] . "=" . $value;
        }
      }
    }
    //    if($term === false){
    //      $params["query"] = $fields;
    //    }else {
    //      $params["query"] = $service["search"][$fields] . "=" . $term; //978-3-319-51822-0
    //    }

    $params["maximumRecords"] = 100;//100; //a00 should be enough to capture all results for one ISBN/ISSN

    // create the canonicalized query
    $canonicalized_query = http_build_query($params);//implode("&", $canonicalized_query);

    // create request
    $request = $service["host"] . "?" . $canonicalized_query;
    //var_dump($request);
    $this->services[$this->service]["lasturl"] = $request;

    try {
      $contents = file_get_contents($request);
    } catch (\Exception $exception) {
      return false;
    }
    return $contents;
  }

  public function xml2array($xml, $get_attributes = 1, $priority = 'tag')
  {
    //$contents = "";
    if (!function_exists('xml_parser_create')) {
      return array();
    }
    $parser = xml_parser_create('');

    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($xml), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
      return; //Hmm...
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();
    $current = &$xml_array;
    $repeated_tag_index = array();
    foreach ($xml_values as $data) {
      unset ($attributes, $value);
      extract($data);
      $tag = str_replace("zs:", "", $tag); // refs #3733 replace SRU prefix
      $result = array();
      $attributes_data = array();
      if (isset ($value)) {
        if ($priority == 'tag')
          $result = $value;
        else
          $result['value'] = $value;
      }
      if (isset ($attributes) and $get_attributes) {
        foreach ($attributes as $attr => $val) {
          if ($priority == 'tag')
            $attributes_data[$attr] = $val;
          else
            $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
        }
      }
      if ($type == "open") {
        $parent[$level - 1] = &$current;
        if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
          $current[$tag] = $result;
          if ($attributes_data)
            $current[$tag . '_attr'] = $attributes_data;
          $repeated_tag_index[$tag . '_' . $level] = 1;
          $current = &$current[$tag];
        } else {
          if (isset ($current[$tag][0])) {
            $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
            $repeated_tag_index[$tag . '_' . $level]++;
          } else {
            $current[$tag] = array(
              $current[$tag],
              $result
            );
            $repeated_tag_index[$tag . '_' . $level] = 2;
            if (isset ($current[$tag . '_attr'])) {
              $current[$tag]['0_attr'] = $current[$tag . '_attr'];
              unset ($current[$tag . '_attr']);
            }
          }
          $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
          $current = &$current[$tag][$last_item_index];
        }
      } elseif ($type == "complete") {
        if (!isset ($current[$tag])) {
          $current[$tag] = $result;
          $repeated_tag_index[$tag . '_' . $level] = 1;
          if ($priority == 'tag' and $attributes_data)
            $current[$tag . '_attr'] = $attributes_data;
        } else {
          if (isset ($current[$tag][0]) and is_array($current[$tag])) {
            $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
            if ($priority == 'tag' and $get_attributes and $attributes_data) {
              $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
            }
            $repeated_tag_index[$tag . '_' . $level]++;
          } else {
            $current[$tag] = array(
              $current[$tag],
              $result
            );
            $repeated_tag_index[$tag . '_' . $level] = 1;
            if ($priority == 'tag' and $get_attributes) {
              if (isset ($current[$tag . '_attr'])) {
                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                unset ($current[$tag . '_attr']);
              }
              if ($attributes_data) {
                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
              }
            }
            $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
          }
        }
      } elseif ($type == 'close') {
        $current = &$parent[$level - 1];
      }
    }
    return ($xml_array);
  }

  /**
   * @param array $title
   * @return array
   */
  private function parseSingleRecord(array $title)
  {

    $result = array();
    $convertFields = array();

    //parse leader data
    //dump($title);die;
    $leader = $title['recordData']['record']['leader']['value'];

    switch (substr($leader, 5, 3)) {
      case 'pam':
      case 'nam':
        $result['mediaType'] = 'Book';
        break;
      case 'nas':
        $result['mediaType'] = 'Magazine';
        break;
    }

    //detect electronic resources
    switch (substr($leader, 17, 1)) {
      case 'u':
        $result['mediaType'] = 'Electronic';
        break;
    }

    //parse control fields
    foreach ($title['recordData']['record']['controlfield'] as $xmlField) {
      switch ($xmlField['attr']['tag']) {
        case '001':
          $result['catalogId'] = $xmlField['value'];
          break;
      }
    }


    $xmlFields = $title['recordData']['record']['datafield'];

    foreach ($xmlFields as $xmlField) {
      $fieldMarcMain = $xmlField['attr']['tag'];
      if (empty($xmlField['subfield']['value'])) {
        // > 1
        foreach ($xmlField['subfield'] as $subField) {
          $this->parseField($fieldMarcMain, $subField, $result, $convertFields);
        }
      } else {
        // == 1
        $this->parseField($fieldMarcMain, $xmlField['subfield'], $result, $convertFields);
      }

    }


    //format price
    if (array_key_exists('price', $result)) {

      $ll = explode(':', $result['price'][0]);
      if (count($ll) > 1) {
        $result["einband"] = $ll[0];
        $price_rest = @$ll[1];
      } else {
        $price_rest = $ll[0];
      }

      $convert = HelperFunctions::str2currency($price_rest, false, false);

      if ($convert) {
        $result["listingPrice"] = ($convert[0] ? $convert[0] : null);
        $result["currencyCode"] = ($convert[1] ? $convert[1] : null);
        $result["formattedPrice"] = HelperFunctions::formatCurrency($result["listingPrice"], $result["currencyCode"]);

      }


      $this->add('notes', $result, $result['price'][0], $convertFields, LINEBREAK);


    }
    /*


    //cut out first price for listenprice
    $posEnd = strlen($price_rest);
    foreach(array(',', '(') as $endCharacter) {
      $newPosEnd = strpos($price_rest, $endCharacter);
      if($newPosEnd !== false) {
        $posEnd = min($posEnd, $newPosEnd);
      }
    }

    $price = substr($price_rest, 0, $posEnd);
    list($price, $waehrung) = str2currency($price);
    $result['currencycode'] = $waehrung;
    $result['listenprice'] = $price;
    $result['einband'] = $einband;

    //add additional price info to notes
    $this->add('notes', $result, $result['price'][0], $convertFields, LINEBREAK);
  }*/

    //format datePublished
    if (!empty($result['datePublished'])) {

      foreach ($result['datePublished'] as $i => $datePublished) {
        if ($i === 0) {
          @list($datePublishedRaw, $datePublishedPeriodEnd) = explode('-', $datePublished);

          try {
            $datePublished = HelperFunctions::strtodate($datePublishedRaw);
            //error_log($datePublishedRaw . " => ". $datePublished);

          } catch (\Exception $e) {
            //$this->get('logger')->info($e);
            error_log($e);
            $datePublished = null;
          }

          $result['datePublished'] = array($datePublished);


          //format until if exists
          if (!empty($datePublishedPeriodEnd)) {
            try {
              $result['datePublishedPeriodEnd'] = HelperFunctions::strtodate(str_replace(array(';', ' '), '', $datePublishedPeriodEnd)); //not handled via $convertFields
            } catch (\Exception $e) {
              error_log($e);
            }
          }
        } else {
          //add additional datePublished info to erscheinungsverlauf
          $this->add('sequentialDate', $result, $datePublished, $convertFields, '; ');
        }
      }
    }


    //var_dump($result["isxn"]);exit;
    if (isset($result["isxn"]) && count($result["isxn"])) {
      list($result["isxn"]) = explode(' ', $result["isxn"][0]);//remove stuff after space such as paranthes
      list(, $result["isbn10"], $result["isbn13"]) = HelperFunctions::isbn_check($result["isxn"]);

      //try second ISXN
      if (!$result["isbn13"] && count($result["isxn"]) > 1) {
        list($result["isxn"]) = explode(' ', $result["isxn"][1]);//remove stuff after space such as paranthes
        list(, $result["isbn10"], $result["isbn13"]) = HelperFunctions::isbn_check($result["isxn"]);
      }


    }


    //remove duplicate entries
    //convert array of values to one string
    foreach ($convertFields as $field => $separator) {
      if (is_array($result[$field])) {
        $result[$field] = implode($separator, array_unique($result[$field]));
      }
    }
    $trimChars = ";: ,?";
    $trimCharsExt = $trimChars; //. "()[]"; auskommentier sonst Innsbruck [u.a.
    if (array_key_exists("publisher", $result)) {
      $result["publisher"] = trim($result["publisher"], $trimCharsExt);
    }
    if (array_key_exists("publisherPlace", $result)) {
      $result["publisherPlace"] = trim($result["publisherPlace"], $trimCharsExt);
    }
    if (array_key_exists("author", $result)) {
      $result["author"] = trim($result["author"], $trimChars);
    }
    // Ratingen, Germany] :

    //LATER kurztitel and gesamttitel should be one field in future!
    $result['fullTitle'] = @$result['title'] . " " . @$result['fullTitle'];

    //format 'numberOfPages'
    if (!empty($result['numberOfPages'])) {

      //      $re = '/(\d{1,3})([.,]?\d{0,2})? [c][m] x (\d{1,3})([.,]?\d{0,2})? [c][m]/m'; // match dimensions of a book
      //      preg_match_all($re, $result['numberOfPages'], $matches);
      //      $result['height'] =
      //      $result['width'] =

      $re = '/(\d{1,4}) [pPSs]/m'; //ggf. /w am Anfang TODO keine Nummber zuvor

      preg_match_all($re, $result['numberOfPages'], $matches);
      //var_dump($matches[1][0]);exit;
      if (isset($matches[1][0])) {
        $result['numberOfPages'] = $matches[1][0];
      } else {
        $result['numberOfPages'] = (int)$result['numberOfPages'];
      }
      if ($result['numberOfPages'] === 0) {
        //e.g. for 'Online-Resource'
        unset($result['numberOfPages']);
      }
    }


    //add PERMALINK
    if (isset($this->services[$this->service]["permalink"]) && isset($result["catalogId"])) {
      $result["permalink"] = $this->services[$this->service]["permalink"] . $result["catalogId"];
    }
    $result["catalogName"] = $this->service;
    /* if(isset($result["lccn"])) {
      $result["lccn"] = trim($result["lccn"]);
      $result["permalink"] = "https://lccn.loc.gov/" . $result["lccn"];
    } */

    return $result;
  }

  /**
   * @param $fieldMarcMain
   * @param array $subField
   * @param $result
   * @param $convertFields
   */
  private function parseField($fieldMarcMain, array $subField, &$result, &$convertFields)
  {
    if (empty($subField['value']) && empty($subField['attr']['code'])) {
      return;//prevent error message
    }
    $fieldMarc = $fieldMarcMain . $subField['attr']['code'];
    if (array_key_exists($fieldMarc, $this->marc21Mapping)) {
      if ($this->marc21Mapping[$fieldMarc]) {
        //save this field
        $this->addField($fieldMarc, $subField['value'], $result, $convertFields);
      }
    }
  }

  /**
   * @param $fieldMarc
   * @param $value
   * @param $result
   * @param $convertFields
   */
  private function addField($fieldMarc, $value, &$result, &$convertFields)
  {
    $mapping = $this->marc21Mapping[$fieldMarc];
    $field = $mapping['field'];
    $separator = (array_key_exists('separator', $mapping) ? $mapping['separator'] : '; ');

    //FIX Do-Not-Sort-Character
    $value = str_replace(array('', ''), array('¬', '¬'), $value);

    //escape '&' etc.
//      $value = recursive_xss_protection($value);TODO what is it?

    //add prefix if set
    if (!empty($mapping['prefix'])) {
      $value = $mapping['prefix'] . $value;
    }


    switch (@$mapping['func']) {
      case 'role':
        //add role to last person
        if(array_key_exists('author',$result)) {
          end($result['author']);
          $lastItemI = key($result['author']);
          reset($result['author']);
          $result['author'][$lastItemI] .= ' [' . $value . ']';
        }
        break;
      case 'role_secondaryPerson':
        //add role to last person
        if(array_key_exists('secondaryPerson',$result)) {
          end($result['secondaryPerson']);
          $lastItemI = key($result['secondaryPerson']);
          reset($result['secondaryPerson']);
          $result['secondaryPerson'][$lastItemI] .= ' [' . $value . ']';
        }
        break;
      case 'parenthesis':
        //add role to last person
        end($result[$field]);
        $lastItemI = key($result[$field]);
        reset($result[$field]);
        $result[$field][$lastItemI] .= ' (' . $value . ')';
        break;
      case 'add_before_last':
        //add role to last person
        end($result[$field]);
        $lastItemI = key($result[$field]);
        reset($result[$field]);
        $result[$field][$lastItemI] = $value . $mapping['add_before_last_separator'] . $result[$field][$lastItemI];
        break;
      case 'add_to_last':
        //add role to last person
        if (empty($result[$field])) {
          $this->add($field, $result, $value, $convertFields, $separator);
        } else {
          end($result[$field]);
          $lastItemI = key($result[$field]);
          reset($result[$field]);
          $result[$field][$lastItemI] .= $mapping['add_to_last_separator'] . $value;
        }
        break;
      case 'first_value':
        if (!array_key_exists($field, $result)) {
          $this->add($field, $result, $value, $convertFields, $separator);
        }
        break;
      case 'language':
        if (strlen($value) == 3 || strlen($value) == 6 || strlen($value) == 9) {
          $langs = str_split($value, 3);
          foreach ($langs as $lang) {
            $this->add($field, $result, $lang, $convertFields, $separator);
          }
        }
        break;
      case 'parenthesis_to_comment':
        if ($value[0] === '(') {
          //move all values like '(Produktform) ...' to comment
          $field = 'notes';
          $separator = '[br]' . PHP_EOL;
        }
      //no break;
      default:
        $this->add($field, $result, $value, $convertFields, $separator);
    }
  }

  /**
   * @param $field
   * @param $result
   * @param $value
   * @param $convertFields
   * @param $separator
   */
  private function add($field, &$result, $value, &$convertFields, $separator)
  {
    //normal field: set or add value
    if (array_key_exists($field, $result)) {
      $result[$field][] = $value; //check for duplicate entries is done later as they could be modified by 'func'
    } else {
      $result[$field] = array($value);
    }

    //add separator to convert list
    $convertFields[$field] = $separator;
  }

  /*
   * converts XML-string to an array
   */


}
