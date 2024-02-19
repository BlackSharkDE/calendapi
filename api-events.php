<?php
/**
 * API-Endpunkt für jegliche Events
 * 
 * Übernimmt Funktion für "birthdays", "holiday", "holidays", "nature"
 * --> holiday => Gibt Liste an verfügbaren Feiertagen & Ferien aus (Parameter h)
 * --> methods => Gibt Liste an verfügbaren Methoden aus (Parameter m)
 * --> states  => Gibt Liste aller Bundesländer(kürzel) aus
 * --> h       => Feiertag berechnen/Ferien ausgeben (String aus "holiday"-Liste)
 * --> m       => Methode ausführen (String aus "methods"-Liste)
 * --> year    => OPTIONAL, das Jahr, das verwendet werden soll
 * --> state   => OPTIONAL, Bundeslandkürzel (String aus "states"-Liste). Damit "h" und "m" auf Ergebnisse beschränkt werden, die nur für dieses Bundesland gelten
 */

//====================================================================================================================================
// -- Includes --

//Wird allgemein benötigt
require __DIR__ . '/includes/gausseaster.php';
require __DIR__ . '/includes/misc.php';
require __DIR__ . '/includes/states.php';

//Ferien und Feiertage
require __DIR__ . '/includes/holiday.php';
require __DIR__ . '/includes/holidays.php';

//Weiteres
require __DIR__ . '/includes/birthdays.php';
require __DIR__ . '/includes/nature.php';
require __DIR__ . '/includes/info.php';

//====================================================================================================================================

//Rückgabe-Array der API
$returnArray = array();

//====================================================================================================================================
// -- Optionale Parameter --

//Standardwerte
$year  = 0;  //Für welches Jahr (Optional) => Gespeichert in "EasterDay::$year"
$state = ""; //Sollen nur Feiertage berücksichtigt werden, die in diesem Bundesland gelten, hier ein Bundeslandkürzel angeben (Optional)

if(isset($_GET["year"])) {
    
    //Für Vergleiche zwischenspeichern
    $yearTemp = intval($_GET["year"]);

    //Akzeptiere nur Jahre 1752 - 2099 -> Siehe $referceDates & "EasterDay"-Klasse
    if($yearTemp >= 1752 && $yearTemp <= 2099) {
        $year = $yearTemp;
    } else {
        $returnArray["Warning"] = "year-Parameter muss zwischen 1752 und 2099 liegen (year='" . $_GET["year"] . ")' ! Benutze aktuelles Jahr!";
    }
}

if(isset($_GET["state"])) {
    $state = strval($_GET["state"]);
}

//====================================================================================================================================
// -- Vorbereitungen --

//Damit die Datumsausgabe (strftime) auf Deutsch ist
setlocale(LC_TIME, 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO_8859-1', 'German_Germany');

//Datum für Berechnungen setzen
EasterDay::setEasterDatetime($year);

//Laden der JSON-Datei "birthdays.json" (muss sowieso für jede "birthday"-Methode immer gemacht werden)
Birthday::loadBirthdayJson();

//====================================================================================================================================
// -- Mögliche API-Abfragen --

//Alle Feiertage
$holidayClasses = array(
    
    //holiday-Klassen (API-Name => Klassenname)
    "holiday" => array(

        //NUR FÜR DEBUG (später wieder auskommentieren)
        //"dummy_1" => "DummyHoliday_1",
        //"dummy_2" => "DummyHoliday_2",

        "neujahr"                  => "Neujahr",
        "heiligedreikoenige"       => "HeiligeDreiKoenige",
        "valentinstag"             => "Valentinstag",
        "schmutzigerdonnerstag"    => "SchmutzigerDonnerstag",
        "rosenmontag"              => "Rosenmontag",
        "fastnachtsdienstag"       => "Fastnachtsdienstag",
        "aschermittwoch"           => "Aschermittwoch",
        "internationalerfrauentag" => "InternationalerFrauentag",
        "palmsonntag"              => "Palmsonntag",
        "gruendonnerstag"          => "Gruendonnerstag",
        "karfreitag"               => "Karfreitag",
        "ostersonntag"             => "Ostersonntag",
        "ostermontag"              => "Ostermontag",
        "walpurgisnacht"           => "Walpurgisnacht",
        "erstermai"                => "ErsterMai",
        "starwarsday"              => "StarWarsDay",
        "eisheilige_mamertus"      => "EisheiligeMamertus",
        "eisheilige_pankratius"    => "EisheiligePankratius",
        "eisheilige_servatius"     => "EisheiligeServatius",
        "eisheilige_bonifatius"    => "EisheiligeBonifatius",
        "eisheilige_sophia"        => "EisheiligeSophia",
        "muttertag"                => "Muttertag",
        "himmelfahrtvatertag"      => "HimmelfahrtVatertag",
        "pfingstsonntag"           => "Pfingstsonntag",
        "pfingstmontag"            => "Pfingstmontag",
        "fronleichnam"             => "Fronleichnam",
        "siebenschlaefertag"       => "Siebenschlaefertag",
        "mariaehimmelfahrt"        => "MariaeHimmelfahrt",
        "weltkindertag"            => "Weltkindertag",
        "erntedankfest"            => "Erntedankfest",
        "tagderdeutscheneinheit"   => "TagDerDeutschenEinheit",
        "reformationstaghalloween" => "ReformationstagHalloween",
        "allerheiligen"            => "Allerheiligen",
        "allerseelen"              => "Allerseelen",
        "martinstag"               => "Martinstag",
        "volkstrauertag"           => "Volkstrauertag",
        "bussundbettag"            => "BussUndBettag",
        "totensonntag"             => "Totensonntag",
        "ersteradvent"             => "ErsterAdvent",
        "zweiteradvent"            => "ZweiterAdvent",
        "nikolaustag"              => "Nikolaustag",
        "dritteradvent"            => "DritterAdvent",
        "vierteradvent"            => "VierterAdvent",
        "heiligabend"              => "Heiligabend",
        "ersterweihnachtstag"      => "ErsterWeihnachtstag",
        "zweiterweihnachtstag"     => "ZweiterWeihnachtstag",
        "silvester"                => "Silvester"
    ),

    //holidays-Klassen (API-Name => Klassenname)
    "holidays" => array(
        "ostern"      => "Ostern",
        "pfingsten"   => "Pfingsten",
        "sommer"      => "Sommer",
        "herbst"      => "Herbst",
        "weihnachten" => "Weihnachten"
    )
);

//Alle API-Methoden
$methods = array(
    
    //holiday-Funktionen
    "todayholidays",
    "nextholidays",
    "allholidays",
    "remainingholidays",

    //birthdays
    "nextbirthdays",
    "nextpublicbirthdays",
    "nextprivatebirthdays",
    "todaybirthdays",
    "todaypublicbirthdays",
    "todayprivatebirthdays",
    "allbirthdays",
    "allpublicbirthdays",
    "allprivatebirthdays",
    "remainingbirthdays",
    "remainingpublicbirthdays",
    "remainingprivatebirthdays",

    //nature
    "fruehlingsanfang",
    "sommeranfang",
    "herbstanfang",
    "winteranfang",
    "beginnsommerzeit",
    "endesommerzeit",
    "allenaturereignisse",
    "verbleibendenaturereignisse",

    //info
    "yearinfo",
    "monthinfo",
    "dayinfo"
);

//====================================================================================================================================
// -- API-Logik --

if(isset($_GET["h"]) && strlen($_GET["h"]) > 0) {
    //-- Feiertage/Ferien (h) --

    //Parameter h speichern
    $hParameter = strtolower($_GET["h"]); //Lowercase für bessere Kompatibilität

    //-- Versuche h in $holiday-Array zu finden --
    if(array_key_exists($hParameter,$holidayClasses["holiday"])) {
        //h ist in "holiday"-Unterarray

        if($state === "" || in_array($state,DE_ABBR)) {
            //Neues Objekt einer "Holiday"-Unterklasse erstellen
            $holidayObj = new ReflectionClass($holidayClasses["holiday"][$hParameter]);
            $holidayObj = $holidayObj->newInstanceArgs(); //Instanziieren
            $returnArray["Result"] = $holidayObj->getApiArray();

            //Wenn GET-Parameter "state" angegeben wurde, Überprüfung ausführen
            if($state !== "" && !$holidayObj->isHolidayInState($state)) {
                $returnArray = array("Error" => "Feiertag " . $holidayObj->getName() . " ist kein gesetzlicher Feiertag in " . $state . "!");
            }

        } else {
            $returnArray = array("Error" => "Bundesland " . $state . " nicht verfügbar! Verfügbar sind: " . implode(DE_ABBR,", "));
        }

    } else if(array_key_exists($hParameter,$holidayClasses["holidays"])) {
        //h ist in "holidays"-Unterarray

        //JSON-Datei einlesen
        $holidaysJson = getJsonFile(__DIR__ . "/resources/holidays.json");
        
        //Diese Jahre können abgefragt werden
        $loadedYears = array_keys($holidaysJson);

        //Prüfe, ob Daten für das angegebene Jahr vorhanden sind
        if(in_array(EasterDay::getYear(),$loadedYears)) {
            //JSON vom angewähltem Jahr zusammenfügen
            $holidays = mergeSubArrays($holidaysJson[EasterDay::getYear()]);

            //Diese Bundesländer können abgefragt werden
            $loadedStates = array_keys($holidays);
            
            //Prüfe, ob Daten für das eventuell angegebene Bundesland vorhanden sind
            if($state === "" || in_array($state,$loadedStates)) {
                //Neues Objekt einer "Holidays"-Unterklasse erstellen
                $holidaysObj = new ReflectionClass($holidayClasses["holidays"][$hParameter]);
                $holidaysObj = $holidaysObj->newInstanceArgs(array("cYJ" => $holidays)); //Instanziieren
                $returnArray["Result"] = $holidaysObj->getApiArray();

                //Wenn GET-Parameter "state" angegeben wurde, ungewollte Bundesländer rausfiltern
                if($state !== "" && in_array($state,DE_ABBR)) {
                    //Alle Resultate von $returnArray abgehen
                    for($i = 0; $i < count($returnArray["Result"]); $i++) {
                        //Aktueller Array-Key (Bundesland-Kürzel)
                        $currentState = array_keys($returnArray["Result"])[$i];
                        
                        //Wenn aktuelles Bundesland nicht mit gesuchtem übereinstimmt
                        if($currentState !== $state) {
                            //Aus Resultat entfernen
                            unset($returnArray["Result"][$currentState]);
                            
                            //Array-Index zurückstellen
                            $i--;
                        }
                    }
                }
                
            } else {
                $returnArray = array("Error" => "Bundesland " . $state . " nicht in JSON-Datei für Jahr " . EasterDay::getYear() . " verfügbar! Verfügbar sind: " . implode($loadedStates,", "));
            }

        } else {
            $returnArray = array("Error" => "Jahr " . EasterDay::getYear() . " nicht in JSON-Datei verfügbar! Verfügbar sind: " . implode($loadedYears,", "));
        }

    } else {
        $returnArray = array("Error" => "Unbekannter Feiertag bzw. Ferien '" . $hParameter . "'" . ((strlen($state) === 2) ? " (für state = " . $state .")" : "") . " !");
    }

} else if(isset($_GET["m"]) && strlen($_GET["m"]) > 0) {
    //-- Methoden (m) --

    //Parameter m speichern
    $mParameter = strtolower($_GET["m"]); //Lowercase für bessere Kompatibilität

    //Wenn valide Methode
    if(in_array($mParameter,$methods)) {
        $returnArray["Result"] = call_user_func($mParameter); //Führe Methode aus
    } else {
        $returnArray = array("Error" => "Unbekannte Methode '" . $mParameter . "' !");
    }

} else if(isset($_GET["holiday"])) {
    //-- Hilfe -> Alle Feiertage (h - Parameter) --
    
    $returnArray = $holidayClasses;

} else if(isset($_GET["methods"])) {
    //-- Hilfe -> Alle Methoden-Namen (m - Parameter) --

    $returnArray = $methods;

} else if(isset($_GET["states"])) {
    //-- Hilfe -> Alle registrierten Bundesländer --

    $returnArray = DE_COMPARE;

} else {
    $returnArray = array("Error" => "Kein GET-Parameter h ODER m angegeben bzw. ohne Wert!");
}

//====================================================================================================================================
// -- Ausgabe der API --

//JSON als Rückgabe angeben
header("Content-type: application/json");
echo(json_encode($returnArray));