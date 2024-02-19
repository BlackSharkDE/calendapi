<?php
/**
 * Klasse für Berechnungen des Tages Ostersonntags
 * 
 * Für Algorithmus siehe PHP-Handbuch "easter_date"-Funktionshinweise
 * 
 * !!! Für ein Jahr vor 1752 funktioniert das Osterdatum nicht wirklich !!!
 */

class EasterDay {
    
    //Datum als DateTime-Objekt
    private static DateTime $easterDay;

    private static int $year;  //Jahr als Integer
    private static int $month; //Monat als Integer
    private static int $day;   //Tag als Integer

    /**
     * Berechnet und setzt die Attribute des Datums von Ostersonntag
     * @param int Jahr als Integer (optional, wird automatisch gesetzt)
     */
    public static function setEasterDatetime($yearValue = 0) {

        if($yearValue === 0) {
            //Automatische Zuweisung
            EasterDay::$year = idate("Y");
        } else {
            //Manuelle Zuweisung
            EasterDay::$year = $yearValue;
        }

        //Berechnung (umgeht Zeitzonenprobleme)
        $base = new DateTime(EasterDay::$year . "-03-21");
        $days = easter_days(EasterDay::$year);
        $base->add(new DateInterval("P{$days}D"));

        //Das Datum setzen
        EasterDay::$easterDay = $base;

        //Den Monat setzen
        EasterDay::$month = (int) $base->format("m");

        //Den Tag setzen
        EasterDay::$day = (int) $base->format("d");
    }

    //-- Getter --

    public static function getEasterDay() {
        return EasterDay::$easterDay;
    }

    public static function getYear() {
        return EasterDay::$year;
    }

    public static function getMonth() {
        return EasterDay::$month;
    }

    public static function getDay() {
        return EasterDay::$day;
    }
}