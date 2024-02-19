<?php
/**
 * Bundesländer-Konstanten
 * 
 * Dies sind Arrays mit einem Eintrag: "Kürzel" => "Name"
 */

//B
define("BW",array("BW" => "Baden-Württemberg"));
define("BY",array("BY" =>"Bayern"));
define("BE",array("BE" => "Berlin"));
define("BB",array("BB" => "Brandenburg"));
define("HB",array("HB" => "Bremen"));

//H
define("HH",array("HH" =>"Hamburg"));
define("HE",array("HE" => "Hessen"));

//M
define("MV",array("MV" => "Mecklenburg-Vorpommern"));

//N
define("NI",array("NI" => "Niedersachsen"));
define("NW",array("NW" => "Nordrhein-Westfalen"));

//R
define("RP",array("RP" => "Rheinland-Pfalz"));

//S
define("SL",array("SL" => "Saarland"));
define("SN",array("SN" => "Sachsen"));
define("ST",array("ST" => "Sachsen-Anhalt"));
define("SH",array("SH" => "Schleswig-Holstein"));

//T
define("TH",array("TH" => "Thüringen"));

//-- Alle Bundesländer --
define("DE", array(BW,BY,BE,BB,HB,HH,HE,MV,NI,NW,RP,SL,SN,ST,SH,TH));

//DE_COMPARE nur für Vergleiche benutzen!
define("DE_COMPARE", array_merge(BW,BY,BE,BB,HB,HH,HE,MV,NI,NW,RP,SL,SN,ST,SH,TH));

//Alle Bundesländerkürzel
define("DE_ABBR", array_keys(DE_COMPARE));