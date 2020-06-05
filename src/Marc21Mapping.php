<?php

namespace Libreja\SruCatalog;

class Marc21Mapping
{

  public static $marc21Mapping = array(
//please use https://schema.org/Book as reference
//http://access.rdatoolkit.org/document.php?id=jscmap2
// '001' => array('field' => 'controlNumber'), //LC control number
    '010a' => array('field' => 'lccn'), //LC control number
    '015a' => null,
    '015z' => null,
    '0152' => null,
    '016a' => null,
    '016z' => null,
    '0162' => null,
    '020a' => array('field' => 'isxn'),//ADD ISBN , 'func' => 'first_value'
    '020z' => array('field' => 'isxn'),//ADD ISBN , 'func' => 'first_value'
    '020c' => array('field' => 'price'),
    '0209' => null,

//ISSN
    '022a' => null,
    '0222' => null,

    '024a' => array('field' => 'isxn'),//ADD ISBN , 'func' => 'first_value'
    '0242' => null, //Quelle

    '028a' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Bestellnummer: '), // 'field' => 'bestellnummer' issueNumber Bestellnummer 'BA 6577 : EUR 10.00'  https://d-nb.info/1072442361/34 http://www.loc.gov/standards/mods/mods2marc-mapping.html Andere Musiknummer

//Weitere ISSN
    '029a' => null, //'0012-0413 = Deutsche Lebensmittel-Rundschau'

//Coden designation
    '030a' => null, //'DLRUA'

    '035a' => null,
    '040a' => null,
    '040b' => null, //array('field' => 'sprache', 'separator' => ','), //Sprache des Katalogs
    '040c' => null, //DE-101
    '040d' => null, //0292
    '040e' => null, //rda

    '041a' => array('field' => 'inLanguage', 'separator' => ',', 'func' => 'language'),
    '041h' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Originalsprache(n): '),

//Code für geograohische Gebiete
    '043c' => null, //'XA-DE'

//Ländercode der veröffentl./herstellenden Stelle
    '044c' => null,

//Dewey-Dezimal-Klassifikationsnummer
    '0828' => null, //Feldverknüpfung und Reihenfolge
    '082a' => array('field' => 'dewey'), //Notation
    '082q' => null, //Vergabestelle
    '0822' => null, //Ausgabenummer

//Zusätzliche Dewey-Dezimal-Klassifikationsnummer
    '0838' => null, //Feldverknüpfung und Reihenfolge
    '083a' => array('field' => 'dewey'), //Notation
    '083q' => null, //Vergabestelle
    '0832' => null, //Ausgabenummer

//Andere Klassifikationsnummer(n)
    '084a' => array('field' => 'dewey'), //Notation
    '084q' => null, //Vergabestelle
    '0842' => null, //Quelle

//Synthetische Notation und ihre Bestandteile
    '0858' => null, //Feldverknüpfung und Reihenfolge
    '085b' => array('field' => 'dewey'), //Grundnotation
    '085s' => null, //Aus Haupt- oder Hilfstafeln angehängte Ziffern
    '085z' => null, //Hilfstafelkennung

//Codeangaben
    '090a' => null, //Papierzustand
    '090n' => null, //weitere Codierungen

//100 - Main Entry-Personal Name (NR)
    '100a' => array('field' => 'author', 'separator' => '; '), //Personenname
    '100e' => array('field' => 'author', 'func' => 'role'),
    '100d' => null, //Datumsangaben in Verbindung mit einem Namen //LATER in Personendatenbank
    '1000' => null, //ID-Nummer (mit Link)
    '1004' => null, //Relator Code

//Kurztitel
    '210a' => null, //'Dtsch. Lebensm.-Rundsch.'

//Keytitle
    '222a' => null, //'Deutsche Lebensmittel-Rundschau.'
    '222b' => null, //'Göttingen'

//240 - Uniform Title (NR) - Verweis auf Werk?
    '240a' => array('field' => 'uniformTitle'), //'Conni &amp; Co' TODO currently not used
    '2400' => null, //'(DE-588)1111904863'

//243 - Collective Uniform Title (NR)
    '243a' => null, //'Sammlung'

//Titelangabe
    '245a' => array('field' => 'title'),
    '245b' => array('field' => 'title', 'separator' => ' : '),
    '245c' => array('field' => 'fullTitle', 'prefix' => ': '), //Verfasserangabe zur gesamten Vorlage // add to fullTitle as advised by henle //prefix necessary as separator does not work when fullTitle is empty an title is delivered by 'title'
    '245h' => null, //'Elektronische Ressource'
    '245n' => array('field' => 'reihe', 'separator' => '; '),
    '245p' => array('field' => 'reihe', 'separator' => '; '),

//246 - Varying Form of Title (R)
    '246a' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Alternativer Titel: '),
//'246a' => array('field' => 'fullTitle'), //z.B.: 'Conni und Co' anstatt 'Conni &amp; Co'

//Frühere(r) Titel
    '247a' => array('field' => 'notes', 'separator' => LINEBREAK),
    '247f' => array('field' => 'notes', 'separator' => LINEBREAK, 'func' => 'add_before_last', 'add_before_last_separator' => ': '),
    '247g' => array('field' => 'notes', 'separator' => LINEBREAK),

    '250a' => array('field' => 'edition'),
    '250b' => array('field' => 'edition', 'separator' => '; '),
    /*
    * <subfield code="a">Urtext der Neuen Bach-Ausgabe, 26. Auflage</subfield>
    * <subfield code="b">revidierte Ausgabe von Peter Wollny</subfield>
    */

//???
    '259a' => null,

//Publikation, Vertrieb usw. (Erscheinungsvermerk)
    '260a' => array('field' => 'publisherPlace'),
    '260b' => array('field' => 'publisher'),
    '260c' => array('field' => 'datePublished'),
    '264a' => array('field' => 'publisherPlace'),
    '264b' => array('field' => 'publisher'),
    '264c' => array('field' => 'datePublished'),

//Physische Beschreibung
    '300a' => array('field' => 'numberOfPages'), //Spez. Materialbenennung und Umfangsangabe
//'300a' => array('field' => 'notes', 'separator' => LINEBREAK),
    '300b' => array('field' => 'notes', 'separator' => LINEBREAK), //Sonstige physische und techn. Angaben
    '300c' => array('field' => 'dimensions', 'separator' => LINEBREAK), //Größe des Datenträgers
    '300e' => array('field' => 'notes', 'separator' => LINEBREAK), //Begleitmaterial

//Content Type (R)
    '336a' => null, //Content type term
    '336b' => null, //Content type code
    '3362' => null, //Source

//Media Type (R)
    '337a' => null, //Media type term
    '337b' => null, //Media type code
    '3372' => null, //Source

//Carrier Type (R)
    '338a' => null, //Carrier type term
    '338b' => null, //Carrier type code
    '3382' => null, //Source

//Erscheinungsdaten
    '362a' => array('field' => 'sequentialDate', 'separator' => '; '),

//Normierter Erscheinungsverlauf
    '363a' => null,
    '363i' => null,
    '3638' => null,

//Handelsprice, Einbandart und price
    '365b' => array('field' => 'price'),

//385 - Audience Characteristics (R)
    '385a' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Zielgruppe: '),
    '3850' => null, //'http://d-nb.info/gnd/4030550-8'
    '3852' => null, //Source (NR)

//Gesamttitelangabe
    '490a' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Beziehungen: '),
    '490v' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => '. '),

//Allgemeine Fußnote
    '500a' => array('field' => 'notes', 'separator' => LINEBREAK),

//Fußnote zu enthaltenen Werken
    '501a' => array('field' => 'notes', 'separator' => LINEBREAK),

//Fußnote zu Besonderheiten der Zählung und/oder An­gaben über Erscheinungs­weise und -dauer
    '515a' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Erscheinungsfrequenz: '),

//520 - Summary, Etc. (R)
    '520a' => array('field' => 'description', 'separator' => LINEBREAK),

//583 - Action Note (R)
    '583a' => null, //'Langzeitarchivierung gewährleistet'
    '583i' => null, //'LZA'

//Bemerkungen zur Titel­aufnahme
    '591a' => null, //'24Kopie'

//Nebeneintragung unter einem Schlagwort / Personennamen
    '6000' => null,
    '6002' => null, //'gnd'
    '600a' => array('field' => 'subject', 'separator' => '; '),
    '600g' => array('field' => 'subject', 'func' => 'parenthesis'),
    '600b' => array('field' => 'subject', 'func' => 'add_to_last', 'add_to_last_separator' => '. '),

//Nebeneintragung unter einem Schlagwort / Körperschaftsname
    '6100' => null,
    '6102' => null, //'gnd'
    '610a' => array('field' => 'subject', 'separator' => '; '),
    '610g' => array('field' => 'subject', 'func' => 'parenthesis'),
    '610b' => array('field' => 'subject', 'func' => 'add_to_last', 'add_to_last_separator' => '. '),

//Nebeneintragung mit einem Schlagwort / Zeitschlagwort
    '6482' => null, //Quelle
    '648a' => array('field' => 'subject', 'separator' => '; '),

//Nebeneintragung unter einem Schlagwort / Sach­schlagwort
    '650a' => array('field' => 'subject'),
    '650x' => array('field' => 'subject', 'func' => 'add_to_last', 'add_to_last_separator' => ' / '),
    '6500' => null, //ID-Nummer (mit Link) //http://d-nb.info/gnd/4010074-1
    '6502' => null, //Quelle

//Nebeneintragung unter einem Schlagwort / geogra­hischer Name
    '651a' => array('field' => 'subject'),
    '6510' => null, //ID-Nummer (mit Link)
    '6512' => null, //Quelle

//Index Term-Uncontrolled (R)
    '653a' => array('field' => 'subject', 'func' => 'parenthesis_to_comment'),

//Formschlagwort
    '655a' => array('field' => 'subject'),
    '6550' => null, //ID-Nummer (mit Link)
    '6552' => null, //Quelle

//RSWK-Folgen
    '689a' => array('field' => 'subject'), //Indikator des Kettengliedes
    '689g' => array('field' => 'subject', 'func' => 'parenthesis'),
    '689b' => array('field' => 'subject', 'func' => 'add_to_last', 'add_to_last_separator' => '. '),
    '689A' => null, //Indikator des Kettengliedes
    '689D' => null, //Entitätentyp der GND
    '6890' => null, //ID-Nummer (mit Link)
    '6895' => null, //Herkunftskennzeichen

//Nebeneintragung Personenname
    '700a' => array('field' => 'secondaryPerson', 'separator' => '; '), //Personenname //Secondary Person
    '700e' => array('field' => 'secondaryPerson', 'func' => 'role_secondaryPerson'),
    '700d' => null, //Datumsangaben in Verbindung mit einem Namen //LATER in Personendatenbank
    '700g' => null, //'dt.'
    '700t' => array('field' => 'werk'),
    '7000' => null, //ID-Nummer (mit Link)
    '7004' => null, //Relator Code

//Nebeneintragung – Körperschaftsname
    '7100' => null, //ID-Nummer (mit Link)
    '7104' => null,
//'7109x' => null, //Allgemeine Unterteilung
//'7109z' => null, //Geographische Unterteilung
    '710a' => array('field' => 'corporation', 'separator' => '; '), //Körperschaftsname
    '710b' => null, //Abteilung
    '710g' => null, //Sonstige Informationen
    '710n' => null, //Zählung der Abteilung

//730 - Added Entry-Uniform Title (R)
    '730a' => array('field' => 'werk'),
    '730g' => array('field' => 'werk', 'func' => 'parenthesis'),

//770 - Supplement/Special Issue Entry (R)
    '770i' => array('field' => 'notes', 'separator' => LINEBREAK), //'Beil.:'
    '770t' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ' '), //'Internationale Dokumentation der Ernährungswirtschaft und -wissenschaft'
    '770d' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ', '), //'Wien : Fachzeitschr.-Verl.-Ges., 1954-1983'
    '770h' => null, //'Online-Resource'
    '770n' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ': '), //'2012, 6-2016, 5'
    '770w' => null, //'(DE-600)506426-0'
    '770x' => null, //'0943-5425'

//773 - Host Item Entry (R)
    '773q' => null, //Enumeration and first page (NR)
    '773w' => null, //Record control number (R)   https://www.loc.gov/marc/bibliographic/bd773.html
    '773x' => array('field' => 'isxn'), //International Standard Serial Number (NR)  https://www.loc.gov/marc/bibliographic/bd773.html
    '773z' => array('field' => 'isxn'), //International Standard Book Number (R) https://www.loc.gov/marc/bibliographic/bd773.html


//776 - Additional Physical Form Entry (R)
    '776i' => array('field' => 'notes', 'separator' => LINEBREAK), //'Erscheint auch als'
    '776a' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ': '), //'Medienkanzler'
    '776n' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ' '), //'Online-Ausgabe'
    '776t' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ': '), //'Medienkanzler'
    '776d' => null, //'Wiesbaden : Springer Fachmedien Wiesbaden, 2016'
    '776h' => null, //'Online-Ressource'
    '776w' => null, //'(DE-101)107795428X'
    '776x' => null, //'2190-6262'
    '776z' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ': ISBN: '), //International Standard Book Number (R)

//780 - Preceding Entry (R)
    '780i' => array('field' => 'notes', 'separator' => LINEBREAK), //'Vorg.:'
    '780t' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ' '), //'Deutsche Nahrungsmittel-Rundschau'
    '780d' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ', '), //'Stuttgart : Wiss. Verl.-Ges., 1903-1935'
    '780w' => null, //'(DE-600)506417-x'
    '780x' => null, //'0084-5345'

//785 - Succeeding Entry (R)
    '785i' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => ' Frühere/spätere Titel: '),
    '785t' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ' '),
    '785d' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => ', '),
    '785w' => null,
    '785x' => null,

//830 - Series Added Entry-Uniform Title (R)
    '830a' => array('field' => 'notes', 'separator' => LINEBREAK, 'prefix' => 'Beziehungen: '),
    '830v' => array('field' => 'notes', 'func' => 'add_to_last', 'add_to_last_separator' => '. '),
    '830w' => null, //'(DE-101)016898699'
    '8307' => null, //'as'
    '8309' => null, //'49999ns 211'

//850 - Holding Institution (R)
    '850a' => null,

//Elektronische Adresse und Zugriffsart
    '856m' => null, //B:DE-101
    '856q' => null, //Mimetype
    '856u' => array('field' => 'index'),
    '856x' => null,
    '8563' => null, //Inhaltsverzeichnis

//883 - Machine-generated Metadata Provenance (R)
    '8838' => null, //'2\p'
    '883a' => null, //'Übernahme aus paralleler Ausgabe'
    '883d' => null, //'20140814'
    '883q' => null, //'DE-101'

//Angaben zum umgelenkten Datensatz
    '889w' => null,//'(DE-101)944598358'

//Weitere DNB-Codierungen Reihenzugehörigkeit
    '925a' => null, //ra
//???
    '926a' => null,
    '926o' => null,
    '926q' => null,
    '926x' => array('field' => 'subject'),
    '926v' => null,
  );
}