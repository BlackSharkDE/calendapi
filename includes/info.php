<?php
/**
 * Informationen zum angegeben Jahr
 * 
 * Skripte, die diese Datei inkludieren, müssen auch folgende Skripte inkludieren:
 * - gausseaster.php
 * - misc.php
 */

//====================================================================================================================================
// -- Hilfsfunktionen --

/**
 * Ob das angegebene Jahr ein Schaltjahr ist
 * @param int   Jahr als Integer
 * @return bool True wenn ja / False wenn nein
 */
function isLeapyear(int $year) {

    //Ob Schaltjahr
    $isLeapYear = false;

    //Findet alle 4 Jahre statt, außer in Jahren, die durch 100 und nicht durch 400 teilbar sind.
    //=> 1900 war kein Schaltjahr, 2000 war ein Schaltjahr (Gregorianischer Kalender)
    if($year % 4 === 0 && !($year % 100 === 0 && $year % 400 !== 0)) {
        $isLeapYear = true;
    }

    return $isLeapYear;
}

/**
 * Wie viele Tage das angegebene Jahr hat
 * @param int  Jahr als Integer
 * @return int Entweder 365 (normales Jahr) oder 366 bei Schaltjahren
 */
function daysInYear(int $year) {

    //Schaltjahre haben 366 Tage
    if(isLeapyear($year)) {
        return 366;
    }

    //Normale Jahre haben 365 Tage
    return 365;
}

/**
 * Gibt eine Übersicht der Anzahl der Wochentage wieder
 * @param int    Jahr als Integer
 * @return array Ein Array mit dem Format: "Wochentag" => Anzahl
 */
function numberOfWeekdays(int $year) {

    //Alle Tage innerhalb des angegebenen Jahres
    $datesOfYear = getAllDatesOfYear($year);

    //Rückgabe-Array
    $numberOfdays = [
        "Mondays"    => 0,
        "Tuesdays"   => 0,
        "Wednesdays" => 0,
        "Thursdays"  => 0,
        "Fridays"    => 0,
        "Saturdays"  => 0,
        "Sundays"    => 0
    ];

    //Für jedes Datum herausfinden, welcher Wochentag es ist
    foreach($datesOfYear as $currentDate) {
        switch(getWeekday($currentDate)) {
            case 0:
                $numberOfdays["Sundays"] += 1;
                break;
            case 1:
                $numberOfdays["Mondays"] += 1;
                break;
            case 2:
                $numberOfdays["Tuesdays"] += 1;
                break;
            case 3:
                $numberOfdays["Wednesdays"] += 1;
                break;
            case 4:
                $numberOfdays["Thursdays"] += 1;
                break;
            case 5:
                $numberOfdays["Fridays"] += 1;
                break;
            case 6:
                $numberOfdays["Saturdays"] += 1;
                break;
        }
    }

    return $numberOfdays;
}

//====================================================================================================================================
// -- API-Funktionen --

/**
 * Gibt Informationen zum angegeben Jahr aus
 * @return array Array für API-Ausgabe (Extra-Format)
 */
function yearInfo() {

    //Aktuelles Jahr zwischenspeichern
    $yearToAnalyze = EasterDay::getYear();

    //Haupt-Rückgabe
    $info = [
        "year"       => $yearToAnalyze,
        "daysInYear" => daysInYear($yearToAnalyze),
        "isLeapyear" => isLeapyear($yearToAnalyze)
        //"weeks"    => 52 //Ist überflüssig, da jedes Jahr gleich viele Wochen hat
    ];

    //Info über die Anzahlen der Wochentage
    $nOW = numberOfWeekdays($yearToAnalyze);

    return array_merge($info,$nOW);
}

/**
 * Gibt Informationen zum aktuellen Monat aus (ignoriert Jahresangaben)
 * @return array Array für API-Ausgabe (Extra-Format)
 */
function monthInfo() {

    //Datum jetzt
    $now = new DateTime();

    return [
        "name"       => strftime("%B",$now->getTimeStamp()),
        "shortName"  => strftime("%b",$now->getTimeStamp()),
        "currentDay" => intval($now->format("d")),
        "days"       => cal_days_in_month(CAL_GREGORIAN,intval($now->format("n")),intval($now->format("Y")))
    ];
}

/**
 * Gibt Informationen zum aktuellen Tag aus (ignoriert Jahresangaben)
 * @return array Array für API-Ausgabe (Extra-Format)
 */
function dayInfo() {

    //Datum jetzt
    $now = new DateTime();

    return [
        "date_string" => getDateString($now),
        "name"        => strftime("%A",$now->getTimeStamp()),
        "shortName"   => strftime("%a",$now->getTimeStamp()),
        "dayOfYear"   => dayOfYear($now),
        "weekOfYear"  => weekOfYear($now)
    ];
}