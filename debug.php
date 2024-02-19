<?php
/**
 * DEBUG - Funktionen bzw. die dabei Helfen
 * 
 * -- Als Vergleich/Abgleich von den Resultaten der "api-events.php" --
 * 
 * Feiertage:
 * - https://www.rechner.club/feiertage
 * - https://www.schulferien.org/deutschland/feiertage/2022/
 * 
 * Ferien:
 * - https://www.schulferien.org/deutschland/ferien/2022/
 * 
 * Naturereignisse:
 * - https://niirrty.de/berechnung-von-jahreszeiten/
 * - https://www.rechner.club/jahreszeiten
 * - https://www.rechner.club/zeitumstellung
 */

//====================================================================================================================================

//Brauchen manche Funktionen
require __DIR__ . '/includes/gausseaster.php';
require __DIR__ . '/includes/misc.php';
require __DIR__ . '/includes/states.php';


//====================================================================================================================================
// -- DEBUG-Konstanten --

define("API_EVENTS_URL","http://localhost/calendAPI/api-events.php");

//====================================================================================================================================
// -- JSON-Vorlagen --

/**
 * Gibt Vorlage für ein Jahr in "holidays.json" aus (void - Funktion)
 * @param string Jahr als String
 */
function printJsonHolidayTemplate(string $year) {

    //Bundesländer-Abkürzungen
    $stateAbbreviations = DE_ABBR;

    $jsonArray = array();
    $jsonArray[$year] = array();

    foreach($stateAbbreviations as $stateAbbreviation) {

        $stateArray = array(
            $stateAbbreviation => array(
                "Ostern"      => "",
                "Pfingsten"   => "",
                "Sommer"      => "",
                "Herbst"      => "",
                "Weihnachten" => ""
            )
        );

        array_push($jsonArray[$year],$stateArray);
    }

    $jsonArray = json_encode($jsonArray);
    var_dump($jsonArray);
}

//====================================================================================================================================
// -- DEBUG-Funktionen --

/**
 * Prüft, ob der Tag ein bestimmter Wochentag ist
 * @param DateTime Datum als DateTime-Objekt, das überprüft werden soll
 * @param int      Wochentag als Zahl: Samstag = 6, Sonntag = 0, Montag = 1 etc.
 * @return bool    True, wenn ja / False, wenn nein
 */
function isWeekday(DateTime $dateTime, int $w) {

    $dayInt = getWeekday($dateTime);

    if($dayInt === $w) {
        return True;
    }

    return False;
}

/**
 * Für "h" und "m"-Parameter
 * @param string Parametername (also h oder m)
 * @param string Wert für Parameter
 * @param string Wert für "state"-Parameter (OPTIONAL)
 * @param int    Startjahr (OPTIONAL)
 * @param int    Endjahr (OPTIONAL)
 * @param int    Wenn auf einen bestimmten Wochentag geprüft werden soll (siehe "isWeekday" Parameter "$w") (OPTIONAL)
 */
function outputMultipleYears(string $paramName, string $paramValue, string $stateValue = "", int $startYear = 1752, int $endYear = 2099, int $weekday = -1) {
    $res = array();
    for($currentYear = $startYear; $currentYear <= $endYear; $currentYear++) {
        $r = file_get_contents(API_EVENTS_URL . "?" . $paramName . "=" . $paramValue . "&year=" . $currentYear . ((strlen($stateValue) > 0) ? "&state=" . $stateValue : ""));
        $r = json_decode($r,True);
        //var_dump($r);
        array_push($res,$r);
    }

    foreach($res as $r) {
        var_dump($r);
        
        //Wochentag ggf. überprüfen
        if($weekday > -1) {
            foreach($r as $rA) {
                echo("<br>");
                var_dump(isWeekday(new DateTime($r["Result"]["date"]),$weekday));
            }
        }

        echo("<br><br>");
    }    
}

//====================================================================================================================================

