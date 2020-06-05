<?php

namespace Libreja\SruCatalog;

class HelperFunctions
{
  public static function isbn_check($isbn)
  {
    $isbn = preg_replace('=[- ]=', '', strtoupper($isbn)); //Leerzeichen und - werden als nicht existent betrachtet und auch in "leerem" Feld (nur mit Leerezeichen) entfernt
    if (strlen($isbn) == 0) return array('', '', NULL);

    if (substr($isbn, 0, 1) == "M") {
      $isbn = 9790 . substr($isbn, 1);
    }

    if (!preg_match('=^[0-9]{7}[0-9X]{1}$|^[0-9]{9}[0-9X]{1}$|^[0-9]{12}|^[0-9]{13}$=', $isbn)) return array(false, false, false);
    $len = strlen($isbn);
    switch ($len) {
      case 8:
        $isbn_10 = $isbn;
        $isbn_13 = '977' . substr($isbn, 0, 7) . '00';
        $kurz_len = 8;
        break;
      case 10:
        $isbn_10 = $isbn;
        $isbn_13 = '978' . $isbn;
        $kurz_len = 10;
        break;
      case 12:
        $tmp = substr($isbn, 0, 3);
        if (($tmp == '978') or ($tmp == '979')) //wenn es sich um eine noch nicht vollständig einegebenen Buch-ISBN handelt => Fehler ausgeben, da sonst ein "korrekt, aber nicht gefunden" erscheinen würde, obwohl man noch beim Tippen ist: Außerdem wird bei Anerkennung der Teileingabe eine Anfrage beim Daten-Anbieter ausgelöst, was eine Blockade der folgenden korrekten Eingabe durch Zeit-Constarints zur Folge haben kann
          return array(false, false, false);
        $isbn_10 = '';
        $isbn_13 = '0' . $isbn;
        $kurz_len = 0;
        break;
      case 13:
        if (substr($isbn, 0, 3) == '978') {
          $isbn_10 = substr($isbn, 3, 12);
          $kurz_len = 10;
        } elseif (substr($isbn, 0, 4) == '9790') { // if ISMB (International Standard Music Number ) add M for ISMN 10
          $isbn_10 = "M" . substr($isbn, 4, 9);
          $kurz_len = 10;
        } else {
          $isbn_10 = '';
          $kurz_len = 0;
        }
        $isbn_13 = $isbn;
        break;
      default:
        return array(false, false, false);
    }

    //Berechnung für ISBN-10/ISSN
    $tmp_10 = 0;
    for ($i = 0; $i < ($kurz_len - 1); $i++) $tmp_10 += $isbn_10[$i] * ($kurz_len - $i);
    $tmp_10 = 11 - ($tmp_10 % 11);
    if ($tmp_10 == 11) $tmp_10 = 0;
    if ($tmp_10 == 10) $tmp_10 = 'X';


    //Berechnung für ISBN-13
    $tmp_13 = 0;
    for ($i = 0; $i < 12; $i++) $tmp_13 += $isbn_13[$i] * (($i % 2) * 2 + 1);
    $tmp_13 = 10 - ($tmp_13 % 10);
    $tmp_13 = $tmp_13 % 10;

    switch ($len) {
      case 8:
        if ($tmp_10 <> $isbn_10[7]) return array(false, false, false);
        $erg[] = $isbn;
        $erg[] = substr($isbn_10, 0, 4) . '-' . substr($isbn_10, 4, 4);
        $erg[] = substr($isbn_13, 0, 12) . $tmp_13;
        break;
      case 10:
        if ($tmp_10 <> $isbn_10[9]) return array(false, false, false);
        $erg[] = $isbn;
        $erg[] = $isbn_10;
        $erg[] = substr($isbn_13, 0, 12) . $tmp_13;
        break;
      case 12:
      case 13:
        if ($tmp_13 <> $isbn_13[12]) return array(false, false, false);
        $erg[] = $isbn;
        if (substr($isbn_10, 0, 1) == "M") {
          $erg[] = $isbn_10;
        } else {
          $erg[] = ($kurz_len > 0) ? substr($isbn_10, 0, 9) . $tmp_10 : '';
        }
        $erg[] = $isbn_13;
        break;
    }
    return $erg;
  }

  public static function datum2timestamp($datum)
  {
    $datum = str_replace(' ', '', $datum);
    if (empty($datum)) return '';
    $tmp = explode('-', $datum);
    if (count($tmp) == 3) {
      $tag = $tmp[2];
      $monat = $tmp[1];
      $jahr = $tmp[0];
    } else {
      $tmp = explode('/', $datum);
      if (count($tmp) == 3) {
        $tag = $tmp[1];
        $monat = $tmp[0];
        $jahr = $tmp[2];
      } else {
//      setlocale(LC_TIME, 'de_DE');
//      for($m=1; $m<=12; ++$m){
//        echo strftime("%B", mktime(0, 0, 0, $m, 1));
//      }exit;
        $monate = array( //TODO language!
          //english
          'January' => '01.',
          'February' => '02.',
          'March' => '03.',
          'April' => '04.',
          'May' => '05.',
          'June' => '06.',
          'July' => '07.',
          'August' => '08.',
          'September' => '09.',
          'October' => '10.',
          'November' => '11.',
          'December' => '12.',
          'Jan.' => '01.',
          'Feb.' => '02.',
          'Mar.' => '03.',
          'Apr.' => '04.',
          'May.' => '05.',
          'Jun.' => '06.',
          'Jul.' => '07.',
          'Aug.' => '08.',
          'Sept.' => '09.',
          'Oct.' => '10.',
          'Nov.' => '11.',
          'Dec.' => '12.',
          //German
          'Januar' => '01.',
          'Februar' => '02.',
          'März' => '03.',
          'Mai' => '05.',
          'Juni' => '06.',
          'Juli' => '07.',
          'Oktober' => '10.',
          'Dezember' => '12.',
          'Okt.' => '10.',
          'Dez.' => '12.',

        ); //führende Nullen, damit Suche mit LIKE funktioniert
        $datum = str_ireplace(array_keys($monate), array_values($monate), $datum);
        if (preg_match('/[^0-9.]/', $datum)) return false;
        preg_match('/((([0-9]{1,2})[.])?(([0-9]{1,2})[.]+))?([0-9]{2,4})$/', $datum, $treffer);
        if (count($treffer) >= 7) {
          list(, , , $tag, , $monat, $jahr) = $treffer;
        } else {
          return false;
        }
      }
    }
    if ($jahr == 0) return false;
    if ($tag == 0) {
      if ($monat >= 1 and $monat <= 12) return $jahr . '-' . $monat . '-00';
      elseif ($monat == 0) return $jahr . '-00-00';
      else return false;
    }
    if (!checkdate($monat, $tag, $jahr)) return false; else return $jahr . '-' . $monat . '-' . $tag;
  }

  public static function strtodate($string)
  {
    $date = HelperFunctions::datum2timestamp($string);

    if ($date) {
      return $date;
    }
//  elseif(strtotime($string)){
//
//  }
    //$string = trim($string, ".");
    if (preg_match('/\d{4}/', $string, $matches)) {// TODO match "2001c" to 2001   //  ^(\D)*(\d{4})(\D)*$       /\b\d{4}\b/
      $year = $matches[0];
      return $year . '-00-00';
    } else {
      return null;
//      throw new Exception("cannot return date ". $string);
    }
    //return "";
  }

  public static function formatCurrency($value, $currency, $locale = "de_DE")
  {

    $fmt = numfmt_create('de_DE', \NumberFormatter::CURRENCY);
    return numfmt_format_currency($fmt, $value, $currency);

  }

  public static function str2currency($string, $defaultC = false, $strict = true)
  {
    /*
     * regex
     * (\d+\.\d{1,2})
     * (eur|usd|sfr|chf) (\d+\.\d{1,2}) \(de|at|freier preis\)
     *
     *
     * 18.00 EUR
     * $2.95
     */
    if (!$string) {
      return [null, null];
    }

    $price = false;
    $currency = false;
    $currencyMatch = '(euro|dm|eur|usd|sfr|chf|gbp|dem|ddm|inr|\$|€|£|¥|₺|rp|HK\$|₹|฿|rs|m)';

    $numberMatchDecimal = '(\d+\.\d{2})';
    $numberMatchAll = '(\d+)(\.\d{2})?';

    if ($strict == false) {
      //For APIs
      if (preg_match("/(eur) $numberMatchDecimal \(de\)/i", $string, $matches)) {
        //match EUR GERMAN price
        $price = $matches[2];
        $currency = $matches[1];
      } elseif (preg_match("/(eur)( |)$numberMatchDecimal/i", $string, $matches)) {
        //match EUR
        $price = $matches[3];
        $currency = $matches[1];
      } elseif (preg_match("/$currencyMatch( |)$numberMatchDecimal/i", $string, $matches)) {
        $price = $matches[3];
        $currency = $matches[1];
      } elseif (preg_match("/$numberMatchDecimal( |)$currencyMatch/i", $string, $matches)) {
        $price = $matches[1];
        $currency = $matches[3];
      }
    } else {
      //for form in Libreja
      $string = trim($string);
      //DONT allow comma and point
      if (strstr($string, ",")) {
        if (strstr($string, ".")) {
          return array(false, false);
        }
        $string = str_replace(",", ".", $string);
      }
      //
      if (preg_match("/^$numberMatchAll( |)$currencyMatch$/i", trim($string), $matches)) {
        //var_dump($matches);exit;
        $currency = $matches[4];
        $price = $matches[1] . $matches[2];
      } elseif ($defaultC && preg_match("/^$numberMatchAll$/", strtr($string, [",-" => "", "," => "."]))) {
        //match any float and set to EUR
        $currency = $defaultC;
        $price = (float)$string;
      }
    }
//TODO wrong entry
//var_dump($price);exit;

    $currency = strtolower($currency);
    $replace = [
      "euro" => "eur",
      "€" => "eur",
      "$" => "usd",
      "£" => "gbp",
      "₹" => "inr",
      "dm" => "dem",
      "m" => "ddm",
    ];
    if (isset($replace[$currency])) {
      $currency = $replace[$currency];
    }

    if ($price && strlen($currency) == 3) {
      return array($price, strtoupper($currency));
    }
    return array(false, false);
  }

}