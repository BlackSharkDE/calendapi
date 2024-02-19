<?php
/**
 * Geburtstagsfunktionen
 * 
 * Skripte, die diese Datei inkludieren, müssen auch folgende Skripte inkludieren:
 * - misc.php
 */

//====================================================================================================================================
// -- Klasse --

class Birthday {

    //Speicher für die verschiedenen Birthday-Objekte
    private static array $publicBirthdays;  //"public"
    private static array $privateBirthdays; //"private"

    //Name der Person
    private string $name;

    //Geburtstag (Datum des Geburtsjahrs)
    private DateTime $date;

    /**
     * -- Konstruktor --
     * @param string   Name der Person
     * @param DateTime Geburtstag (Datum des Geburtsjahrs)
     */
    public function __construct(string $n, DateTime $d) {
        $this->name = $n;
        $this->date = $d;
    }

    /**
     * Vergleichsfunktion für "usort()"
     * @param Birthday Ein Birthday-Objekt
     * @param Birthday Ein Birthday-Objekt
     * @return bool    Ob $a für angegebenes Jahr vor $b liegt
     */
    private static function sortByBirthdayForYear(Birthday $a, Birthday $b) {
        return $a->getYearDate() > $b->getYearDate();
    }

    /**
     * Lädt die "birthday.json" und erstellt die Birthday-Objekte, die in den static-Attributen gespeichert werden (void - Funktion)
     */
    public static function loadBirthdayJson() {

        //JSON-Datei einlesen (Pfad aus Sicht von "api-events.php", weil diese Datei ein include ist)
        $birthdayJson = getJsonFile("./resources/birthdays.json");
        
        //"public"-Geburtstage laden
        Birthday::$publicBirthdays = array();
        $puB = mergeSubArrays($birthdayJson["public"]); //"public"-Array zusammenfügen
        foreach($puB as $name => $birthdayDate) {
            array_push(Birthday::$publicBirthdays, new Birthday($name,new DateTime($birthdayDate)));
        }

        //"public"-Geburtstage sortieren
        usort(Birthday::$publicBirthdays,"Birthday::sortByBirthdayForYear");

        //"private"-Geburtstage laden
        Birthday::$privateBirthdays = array();
        $prB = mergeSubArrays($birthdayJson["private"]); //"private"-Array zusammenfügen
        foreach($prB as $name => $birthdayDate) {
            array_push(Birthday::$privateBirthdays, new Birthday($name,new DateTime($birthdayDate)));
        }

        //"private"-Geburtstage sortieren
        usort(Birthday::$privateBirthdays,"Birthday::sortByBirthdayForYear");
    }

    /**
     * Gibt ein Array zurück, welches von der API in JSON umgewandelt werden kann
     * @return array Das Array
     */
    public function getApiArray() {
        return apiReturnArray($this->getAge() . ". Geburtstag von " . $this->name,$this->getYearDate());
    }

    /**
     * Berechnet das Alter für angegebenes Jahr
     * @return int Alter als Integer
     */
    private function getAge() {
        //Unterschied in Jahren berechnen
        $birthYear = (int) $this->date->format("Y");
        $age = EasterDay::getYear() - $birthYear; //Unterschied zu Geburtsjahr in Jahren
        return $age;
    }

    /**
     * Da das Geburtstagsdatum das Geburtsjahr enthält, muss das Datum für aktuelle Vergleiche auf das angegebene Jahr angepasst werden können
     * @return DateTime Datum für angegebenes Jahr
     */
    private function getYearDate() {
        
        //Kopiere Datum-Objekt, um damit zu arbeiten
        $workDate = clone($this->date);

        //-- Neues Datum erstellen (für angegebenes Jahr) --

        //Alter im angegebenen Jahr
        $age = $this->getAge();

        //Ob der Intervall invertiert werden muss (also ob er negativ ist)
        $invert = false;

        //Sollte das Alter negativ sein
        if($age < 0) {
            $age = abs($age); //Alter positiv machen
            $invert = true;   //Damit der Intervall das positive Alter negativ betrachtet
        }

        //Intervall erstellen
        $dI = new DateInterval("P" . $age . "Y");
        $dI->invert = $invert;

        //Geburtstag im angegebenen Jahr
        $birthdayForYear = $workDate->add($dI);
        
        return $birthdayForYear;
    }

    /**
     * Gibt entsprechenes Array mit Birthday-Objekten zurück
     * Für welche Kategorie (OPTIONAL ; "public", "private" oder leer für beide)
     * @return array Array mit Birthday-Objekten
     */
    private static function getBirthdaysByType(string $type = "") {
        
        $requestedType = array();

        //Welche Typen abgefragt werden
        if($type === "private") {
            //Nur "private"
            $requestedType = Birthday::$privateBirthdays;
        } else if($type === "public") {
            //Nur "public"
            $requestedType = Birthday::$publicBirthdays;
        } else {
            //Beide
            $requestedType = array_merge(Birthday::$publicBirthdays,Birthday::$privateBirthdays);
        }

        return $requestedType;
    }

    /**
     * Findet die nächsten anstehenden Geburtstage
     * @param string Für welche Kategorie (OPTIONAL ; "public", "private" oder leer für beide)
     * @return array Array mit Arrays für API-Ausgabe
     */
    public static function getNextBirthdays(string $type = "") {
        
        //Mit diesen Geburtstagen wird gearbeitet
        $workArray = Birthday::getBirthdaysByType($type);

        //Rückgabe der Methode
        $nextBirthdays = array();
        
        //Datum jetzt
        $today = new DateTime();

        //Geburtstage finden, die in der Zukunft liegen
        foreach($workArray as $birthday) {
            if($birthday->getYearDate() > $today) {
                array_push($nextBirthdays,$birthday->getApiArray());
            }
        }

        return $nextBirthdays;
    }

    /**
     * Fndet alle heutigen Geburtstage
     * @param string Für welche Kategorie (OPTIONAL ; "public", "private" oder leer für beide)
     * @return array Array mit Arrays für API-Ausgabe
     */
    public static function getTodayBirthdays(string $type = "") {
        
        //Mit diesen Geburtstagen wird gearbeitet
        $workArray = Birthday::getBirthdaysByType($type);

        //Rückgabe der Methode
        $todayBirthdays = array();

        //Datum jetzt als String mit YYYY-MM-DD - Format
        $today = (new DateTime())->format("Y-m-d");

        //Geburtstage finden, die in der Zukunft liegen
        foreach($workArray as $birthday) {
            if($birthday->getYearDate()->format("Y-m-d") === $today) {
                array_push($todayBirthdays,$birthday->getApiArray());
            }
        }

        return $todayBirthdays;
    }

    /**
     * Gibt alle Geburtstage eines Typs aus
     * @param string Für welche Kategorie (OPTIONAL ; "public", "private" oder leer für beide)
     * @return array Array mit Arrays für API-Ausgabe
     */
    public static function getBirthdays(string $type = "") {
        
        $allBirthdays = array();

        foreach(Birthday::getBirthdaysByType($type) as $birthday) {
            array_push($allBirthdays,$birthday->getApiArray());
        }
    
        return $allBirthdays;
    }

    /**
     * Gibt alle verbleibenden Geburtstage eines Typs für das angegebene Jahr aus
     * @param string Für welche Kategorie (OPTIONAL ; "public", "private" oder leer für beide)
     * @return array Array mit Arrays für API-Ausgabe
     */
    public static function getRemainingBirthdays(string $type = "") {

        $remainingBirthdays = array();

        $birthdays = Birthday::getBirthdaysByType($type);

        foreach($birthdays as $birthday) {
            $birthdayApiArray = $birthday->getApiArray();
            if($birthdayApiArray["diff"] > 0) {
                array_push($remainingBirthdays,$birthdayApiArray);
            }
        }

        return $remainingBirthdays;
    }
}

//====================================================================================================================================
// -- API-Methoden --

/**
 * Alle nächsten Geburtstage
 * @return array Siehe "Birthday::getNextBirthdays()"
 */
function nextBirthdays() {
    return Birthday::getNextBirthdays();
}

/**
 * Alle nächsten Geburtstage des Typs "public"
 * @return array Siehe "Birthday::getNextBirthdays()"
 */
function nextPublicBirthdays() {
    return Birthday::getNextBirthdays("public");
}

/**
 * Alle nächsten Geburtstage des Typs "private"
 * @return array Siehe "Birthday::getNextBirthdays()"
 */
function nextPrivateBirthdays() {
    return Birthday::getNextBirthdays("private");
}

/**
 * Alle heutigen Geburtstage
 * @return array Siehe "Birthday::getTodayBirthdays()"
 */
function todayBirthdays() {
    return Birthday::getTodayBirthdays();
}

/**
 * Alle heutigen Geburtstage des Typs "public"
 * @return array Siehe "Birthday::getTodayBirthdays()"
 */
function todayPublicBirthdays() {
    return Birthday::getTodayBirthdays("public");
}

/**
 * Alle heutigen Geburtstage des Typs "private"
 * @return array Siehe "Birthday::getTodayBirthdays()"
 */
function todayPrivateBirthdays() {
    return Birthday::getTodayBirthdays("private");
}

/**
 * Alle Geburtstage
 * @return array Siehe "Birthday::getBirthdays()"
 */
function allBirthdays() {
    return Birthday::getBirthdays();    
}

/**
 * Alle Geburtstage des Typs "public"
 * @return array Siehe "Birthday::getBirthdays()"
 */
function allPublicBirthdays() {
    return Birthday::getBirthdays("public");
}

/**
 * Alle Geburtstage des Typs "private"
 * @return array Siehe "Birthday::getBirthdays()"
 */
function allPrivateBirthdays() {
    return Birthday::getBirthdays("private");
}

/**
 * Alle übrigen Geburtstage des angegeben Jahres 
 */
function remainingBirthdays() {
    return Birthday::getRemainingBirthdays();
}

/**
 * Alle übrigen Geburtstage des angegeben Jahres des Typs "public"
 */
function remainingPublicBirthdays() {
    return Birthday::getRemainingBirthdays("public");
}

/**
 * Alle übrigen Geburtstage des angegeben Jahres des Typs "private"
 */
function remainingPrivateBirthdays() {
    return Birthday::getRemainingBirthdays("private");
}