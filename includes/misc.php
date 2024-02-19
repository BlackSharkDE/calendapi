<?php
/**
 * Was so übrig bleibt
 * 
 * Skripte, die diese Datei inkludieren, müssen auch folgende Skripte inkludieren:
 * - gausseaster.php
 */

//====================================================================================================================================
// -- Wochentage --

/**
 * Findet den ersten definierten Wochentag (beispielsweise Montag) im Monat
 * @param int       Monat als Ganzzahl (Oktober = 10, Juni = 6 etc.)
 * @param int       Wochentag als Zahl: Samstag = 6, Sonntag = 0, Montag = 1 etc.
 * @return DateTime Datum als DateTime-Objekt
 */
function getFirstWeekdayOfMonth(int $month, int $weekday) {

    //Erster Tag des Monats (formatiere Monat ggf. auf 2 Stellen um)
    $fmw = new DateTime(EasterDay::getYear() . "-" . sprintf("%02d",$month) . "-01");
    
    //Solange der aktuelle Wochentag nicht der gesuchte ist, einen Tag addieren
    while(getWeekday($fmw) !== $weekday) {
        $fmw->add(new DateInterval("P1D"));
    }

    return $fmw;
}

/**
 * Gibt den Wochentag-Indes eines Datums zurück
 * @param DateTime Datum als DateTime-Objekt, das überprüft werden soll
 * @return int     Wochentag als Zahl: Samstag = 6, Sonntag = 0, Montag = 1 etc.
 */
function getWeekday(DateTime $dateTime) {
    
    $y = (int) $dateTime->format("Y");
    $m = (int) $dateTime->format("m");
    $d = (int) $dateTime->format("d");

    return (int) date("w", mktime(0, 0, 0, $m, $d, $y));
}

//====================================================================================================================================
// -- Datum selbst --

/**
 * Berechnet die Zeitspanne von jetzt (bzw. heute) zum Datum in Tagen
 * @param DateTime Das Datum als DateTime-Objekt
 * @return array   Ein Array mit zwei Einträgen: [0] = Tage als Integer, [1] = Tage als String-Ausdruck
 */
function getDayDiff(DateTime $date) {

    //Rückgabearray
    $ret = array();

    //Unterschied in Tagen berechnen (runde auf, damit keine Missverständnisse entstehen)
    $now = time();
    $datediff = $date->getTimestamp() - $now;
    $datediff = ceil($datediff / (60 * 60 * 24));

    //Erste Rückgabe speichern
    $ret[0] = $datediff;

    //String-Ausdruck
    $datestr = "in ";

    //Wenn Zeitspanne negativ ist
    if($datediff < 0) {
        $datestr = "vor ";
    }

    //Zweite Rückgabe speichern
    $ret[1] = $datestr . abs($datediff) . " Tagen";

    return $ret;
}

/**
 * Gibt einen Formatierten Datumsstring zurück
 * @param DateTime Das Datum als DateTime-Objekt
 * @return string  Das Datum als String im Format "Montag, 13. Juni 2022"
 */
function getDateString(DateTime $dateTime) {
    $dateString = strftime("%A, %d. %B %Y",$dateTime->getTimeStamp());
    return utf8_encode($dateString); //strftime konvertiert die UTF-8 Zeichen nicht richtig, also nochmal manuell
}

/**
 * Erstellt ein DateTime-Objekt mit dem ersten Januar des angegebenen Jahres
 * @param int       Jahr als Integer
 * @return DateTime Das DateTime-Objekt am 01.01. um 00:00:00 Uhr des angegeben Jahres
 */
function getFirstOfJanuary(int $year) {
    return new DateTime(date("Y-m-d H:i:s",strtotime($year . "-01-01 00:00:00")));
}

/**
 * Alle Tage innerhalb eines Jahres
 * @param int    Jahr als Integer
 * @return array Ein Array mit DateTime-Objekten aller Tage des angegebenen Jahres (sollten 365 bzw. 366 sein)
 */
function getAllDatesOfYear(int $year) {

    //Alle Tage innerhalb Start <=> Ende im Intervall von 1 Tag
    $dP = new DatePeriod(
        getFirstOfJanuary($year),    //01.01. des aktuellen Jahres
        new DateInterval('P1D'),     //Intervall ist ein Tag
        getFirstOfJanuary($year + 1) //01.01. des nächsten Jahres (nicht enhalten)
    );

    //Enthalt die DateTime-Objekte nach dem umkopieren
    $allDatesOfYear = array();

    //Umkopieren der DateTime-Objekte aus der DatePeriod
    foreach($dP as $number => $dateTimeObject) {
        array_push($allDatesOfYear,$dateTimeObject);
    }

    return $allDatesOfYear;
}

/**
 * Aktueller Tag im Jahr
 * @param DateTime Das Datum des Tages als DateTime-Objekt
 * @return int     Tag des Jahres (Startet bei 1 => 01.01.)
 */
function dayOfYear(DateTime $dateOfDay) {
    return intval($dateOfDay->format("z")) + 1; //Startet bei 0 bis maximal 365
}

/**
 * Aktuelle Kalenderwoche im Jahr
 * @param DateTime Das Datum als DateTime-Objekt
 * @return int     Woche des Jahres (Startet bei 1)
 */
function weekOfYear(DateTime $dateTime) {
    return intval($dateTime->format("W"));
}

//====================================================================================================================================
// -- Sonstiges --

/**
 * Liest eine JSON-Datei ein
 * @param string Dateipfad
 * @return array JSON-Array
 */
function getJsonFile(string $filepath) {
    $content = file_get_contents($filepath);
    return json_decode($content,True);
}

/**
 * Fügt Arrays innerhalb eines Array zusammen
 * @param array Das Array
 */
function mergeSubArrays(array $theArray) {
    $merged = array();
    foreach($theArray as $aArray) {
        $merged = array_merge($merged, $aArray);
    }
    return $merged;
}

/**
 * Vereinheitlichte API-Ausgabe-Vorlage
 * @param string   Name der Rückgabe (beispielsweise Feiertagsname)
 * @param DateTime Das Datum / Ergebnis
 * @return array Array für API-Ausgabe (kann noch weiter verändert werden)
 */
function apiReturnArray(string $name, DateTime $date) {

    //Für Rückgabe
    $diff = getDayDiff($date);

    return array(
        "name"        => $name,
        "date"        => $date->format("Y-m-d"),
        "date_string" => getDateString($date),
        "diff"        => $diff[0],
        "diff_string" => $diff[1],
        "dayOfYear"   => dayOfYear($date),
        "weekOfYear"  => weekOfYear($date)
    );
}