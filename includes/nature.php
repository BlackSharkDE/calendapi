<?php
/**
 * Funktionen für Naturereignisse
 * 
 * Skripte, die diese Datei inkludieren, müssen auch folgende Skripte inkludieren:
 * - gausseaster.php
 * - misc.php
 */

//====================================================================================================================================
// -- Jahreszeiten --

//Jeder Anfang einer Jahreszeit verschiebt sich im Vergleich zum Vorjahr um eine spezifische Zeitspanne
//--> Zeitspannen "Gesamt in Sekunden" (Gesamt in Tagen als Sekunden)
$seasonTimespans = array(           //|   NORD   |   SÜD    | Tage | Stunden | Minuten | Sekunden | Gesamt in Tagen |
    "fruehlingsanfang" => 31556942, //| Frühling | Herbst   | 365  | 5       | 49      | 2        | 365,24238       |
    "sommeranfang"     => 31556877, //| Sommer   | Winter   | 365  | 5       | 47      | 57       | 365,24164       |
    "herbstanfang"     => 31556911, //| Herbst   | Frühling | 365  | 5       | 48      | 31       | 365,24203       |
    "winteranfang"     => 31556973  //| Winter   | Sommer   | 365  | 5       | 49      | 33       | 365,24275       |
);

//Für verschiedene, näher liegende Zeiträume werden dazu die folgenden Zeitpunkte als Referenzdatum empfohlen,
//von dem aus die Berechnungen für den angegebenen Jahres-Zeitraum stattfinden sollten.
$referceDates = array(
    [
        "start" => 1700,
        "end"   => 1899,
        "dates" => [                                                       //|   NORD   |   SÜD    |
            "fruehlingsanfang" => new DateTime('21.03.1805 00:57:30 UTC'), //| Frühling | Herbst   |
            "sommeranfang"     => new DateTime('21.06.1805 21:42:30 UTC'), //| Sommer   | Winter   |
            "herbstanfang"     => new DateTime('23.09.1805 11:22:30 UTC'), //| Herbst   | Frühling |
            "winteranfang"     => new DateTime('21.12.1805 05:06:30 UTC')  //| Winter   | Sommer   |
        ]
    ],
    [
        "start" => 1900,
        "end"   => 2099,
        "dates" => [                                                       //|   NORD   |   SÜD    |
            "fruehlingsanfang" => new DateTime('20.03.2005 11:33:19 UTC'), //| Frühling | Herbst   |
            "sommeranfang"     => new DateTime('21.06.2005 06:39:11 UTC'), //| Sommer   | Winter   |
            "herbstanfang"     => new DateTime('22.09.2005 22:16:34 UTC'), //| Herbst   | Frühling |
            "winteranfang"     => new DateTime('21.12.2005 18:34:51 UTC')  //| Winter   | Sommer   |
        ]
    ]
);

//Simples Names-Mapping für API-Rückgabe
$seasonNames = array(
    "fruehlingsanfang" => "Frühlingsanfang",
    "sommeranfang"     => "Sommeranfang",
    "herbstanfang"     => "Herbstanfang",
    "winteranfang"     => "Winteranfang"
);

/**
 * Berechnet den jeweiligen Jahreszeitenanfang
 * @param string Ein String (Array-Key) aus $seasonTimespans
 * @return array Array für API-Ausgabe / Bei falscher $seasonString-Angabe leeres Array
 */
function seasonStart(string $seasonString) {

    global $referceDates;
    global $seasonTimespans;
    global $seasonNames;

    //Wenn $seasonString valide
    if(array_key_exists($seasonString,$seasonTimespans)) {
        //Referenzdatum für Zeitraum in dem "EasterDay::$year" liegt finden
        $refDate = null;
        foreach($referceDates as $referenceDate) {
            if($referenceDate["start"] <= EasterDay::getYear() && $referenceDate["end"] >= EasterDay::getYear()) {
                $refDate = $referenceDate["dates"][$seasonString];
            }
        }

        //Differenz in Jahren berechnen
        $yearDifference = EasterDay::getYear() - (int) $refDate->format("Y");

        //Interval festlegen (abs(), da Multiplikation negativ sein kann, was ungültig ist / für vergangene Jahre)
        $interval = new DateInterval("PT" . abs($yearDifference * $seasonTimespans[$seasonString]) . 'S');

        //Intervall hinzufügen (positive Sekunden) ober abziehen (negative Sekunden)
        if($yearDifference > 0) {
            $refDate->add($interval);
        } else {
            $refDate->sub($interval);
        }

        //Entferne die Stunden, Minuten und Sekunden aus dem Datum, da diese für die Repräsentation nicht wichtig sind
        $refDate = new DateTime(date("Y-m-d",$refDate->getTimestamp()));

        return apiReturnArray($seasonNames[$seasonString],$refDate);
    }

    return array();
}

/**
 * Frühlingsanfang (Ende Winter) -> return siehe "seasonStart()"
 * - Tag-und-Nacht-Gleiche zwischen Winter und Sommer (auch Äquinoktium)
 */
function fruehlingsanfang() {
    return seasonStart("fruehlingsanfang");
}

/**
 * Sommeranfang (Ende Frühling) -> return siehe "seasonStart()"
 * - Sommersonnenwende
 */
function sommeranfang() {
    return seasonStart("sommeranfang");
}

/**
 * Herbstanfang (Ende Sommer) -> return siehe "seasonStart()"
 * - Tag-und-Nacht-Gleiche zwischen Sommer und Winter
 */
function herbstanfang() {
    return seasonStart("herbstanfang");
}

/**
 * Winteranfang (Ende Herbst) -> return siehe "seasonStart()"
 * - Wintersonnenwende
 */
function winteranfang() {
    return seasonStart("winteranfang");
}

//====================================================================================================================================
// -- Sommerzeit-Funktionen --

/**
 * Berechnet Sommerzeit Anfang (Ende Normalzeit/Winterzeit) / Ende (Anfang Normalzeit/Winterzeit)
 * - Beginn: Seit dem Jahr 1981 immer am letzten Sonntag im März für Deutschland ; Seit 1996 gilt diese Regelung in der ganzen EU
 * - Ende  : Seit dem Jahr 1996 in der ganzen EU immer am letzten Sonntag im Oktober
 * @return array Array für API-Ausgabe / Bei falscher $monthInt-Angabe leeres Array
 */
function summertimePeriod(int $monthInt) {
    if($monthInt === 3 ) {
        return apiReturnArray("Sommerzeit Anfang",new DateTime("last sunday of march " . EasterDay::getYear()));
    }
    else if($monthInt === 10) {
        return apiReturnArray("Sommerzeit Ende",new DateTime("last sunday of october " . EasterDay::getYear()));
    }
    return array();
}

/**
 * Beginn Sommerzeit -> return siehe "summertimePeriod()"
 */
function beginnSommerzeit() {
    return summertimePeriod(3);
}

/**
 * Ende Sommerzeit -> return siehe "summertimePeriod()"
 */
function endeSommerzeit() {
    return summertimePeriod(10);
}

//====================================================================================================================================
// -- Zusammenfassungsfunktionen --

/**
 * Liefert alle Naturereignisse des angegebenen Jahres
 * @return array Array mit Arrays für API-Ausgabe
 */
function allenaturereignisse() {
    $r = array();

    //Jahreszeiten
    array_push($r,fruehlingsanfang());
    array_push($r,sommeranfang());
    array_push($r,herbstanfang());
    array_push($r,winteranfang());

    //Sommerzeit
    array_push($r,beginnSommerzeit());
    array_push($r,endeSommerzeit());

    //Sortieren (anhand der Differenz zu heute)
    usort($r,fn($a,$b) => $a["diff"] > $b["diff"]);

    return $r;
}

/**
 * Liefert alle verbleibenden Naturereignisse des angegebenen Jahres
 */
function verbleibendenaturereignisse() {
    $r = allenaturereignisse();
    $r = array_filter($r,fn($x) => $x["diff"] > 0);
    return $r;
}