<?php
/**
 * Ferien-Funktionen
 * 
 * Diese Ferien beziehen sich nur auf Deutschland.
 * 
 * Für die Funktionalität dieses Skripts, wird die Datei "/resources/states.json" benötigt (für Aufbau, siehe Datei selbst).
 * 
 * Skripte, die diese Datei inkludieren, müssen auch folgende Skripte inkludieren:
 * - misc.php
 * - states.php
 */

//====================================================================================================================================
// -- Oberklassen für Ferien --

class StateHoliday {

    //Name, z.B. "Osterferien"
    private string $name;

    //Datum vor "-" -> Start der Ferien
    private DateTime $start;
    
    //Datum hinter "-" -> Ende der Ferien
    private DateTime $end;
    
    //EINE State-Konstante, z.B. "SH"
    private array $state;

    /**
     * -- Konstruktor --
     * @param string   Name
     * @param DateTime Start
     * @param DateTime Ende
     * @param array    State-Konstante/deren Wert
     */
    public function __construct(string $n, DateTime $s, DateTime $e, array $st) {
        $this->name  = $n;
        $this->start = $s;
        $this->end   = $e;
        $this->state = $st;
    }

    /**
     * Gibt ein Array zurück, welches von der API in JSON umgewandelt werden kann
     * @return array Das Array
     */
    public function getApiArray() {
        $t = apiReturnArray($this->name,$this->start);
        $t["date"] = array(
            "start" => $this->start->format("Y-m-d"),
            "end"   => $this->end->format("Y-m-d")
        );
        unset($t["date_string"]); //Diesen Key entfernen
        $apiArray = array(
            array_keys($this->state)[0] => $t
        );
        return $apiArray;
    }
}

abstract class Holidays {
    protected array $calendarYearJson; //JSON als Array für ein Kalenderjahr aus "holidays.json"
    protected array $stateHolidays;    //Array mit StateHoliday-Objekten

    /**
     * Gibt ein Array mit "StateHoliday"-Objekten eines Feriennamens zurück
     * @param string Name der Ferien, z.B. "Ostern"
     * @param string Anderer Name für die Ferien (OPTIONAL)
     * @return array Array mit "StateHoliday"-Objekten
     */
    protected final function getFromJsonArray(string $holidaysName, string $otherHolidaysName = "") {
        
        //Rückgabe-Array
        $stateHolidaysArray = array();

        //Alle Ferien des Kalenderjahres durchgehen
        foreach($this->calendarYearJson as $state => $holidays) {
            $stateHoliday      = $holidays[$holidaysName];
            $stateHolodayStart = new DateTime((explode("-",$stateHoliday))[0]);
            $stateHolidayEnd   = new DateTime((explode("-",$stateHoliday))[1]);
            $stateHolidayState = constant($state);
            
            //Neues "StateHoliday"-Objekt dem Rückgabe-Array hinzufügen
            array_push(
                $stateHolidaysArray,
                new StateHoliday(
                    ((strlen($otherHolidaysName) > 0) ? $otherHolidaysName : $holidaysName),
                    $stateHolodayStart,
                    $stateHolidayEnd,
                    $stateHolidayState
                )
            );
        }
        return $stateHolidaysArray;
    }

    /**
     * Abstrakte Methode, die in den Unterklassen die Methode "getFromJsonArray()" mit bestimmten Parameter aufrufen soll
     */
    protected abstract function setStateHolidays();

    /**
     * -- Konstruktor --
     * @param array Array mit geladenem JSON für ein Kalenderjahr aus "holidays.json" (z.B. 2022)
     */
    public function __construct(array $cYJ) {
        $this->calendarYearJson = $cYJ;
        $this->stateHolidays = $this->setStateHolidays();
        $this->calendarYearJson = array(); //Wird nicht mehr gebraucht, also entfernen
    }

    /**
     * Gibt ein Array zurück, welches von der API in JSON umgewandelt werden kann
     * @return array Das Array
     */
    public function getApiArray() {
        $apiArray = array();
        foreach($this->stateHolidays as $stateHoliday) {
            $apiArray = array_merge($apiArray,$stateHoliday->getApiArray());
        }
        return $apiArray;
    }
}

//====================================================================================================================================
// -- Die Ferien --

class Ostern extends Holidays {
    protected function setStateHolidays() {
        return $this->getFromJsonArray("Ostern","Osterferien");
    }
}

class Pfingsten extends Holidays {
    protected function setStateHolidays() {
        return $this->getFromJsonArray("Pfingsten");
    }
}

class Sommer extends Holidays {
    protected function setStateHolidays() {
        return $this->getFromJsonArray("Sommer","Sommerferien");
    }
}

class Herbst extends Holidays {
    protected function setStateHolidays() {
        return $this->getFromJsonArray("Herbst","Herbstferien");
    }
}

class Weihnachten extends Holidays {
    protected function setStateHolidays() {
        return $this->getFromJsonArray("Weihnachten","Weihnachtsferien");
    }
}