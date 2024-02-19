<?php
/**
 * Feiertage-Funktionen
 * 
 * Diese Feiertage beziehen sich nur auf Deutschland.
 * 
 * Skripte, die diese Datei inkludieren, müssen auch folgende Skripte inkludieren:
 * - gausseaster.php
 * - misc.php
 * - states.php
 */

//====================================================================================================================================
// -- Oberklasse für Feiertage --

abstract class Holiday {

    //Name des Feiertags
    protected string $name;

    //Datum des Feiertags als DateTime-Objekt
    protected DateTime $date;

    //In welchen Bundesländern der Tag ein gesetzlicher Feiertag ist
    protected array $holidayInStates;

    /**
     * -- Konstruktor --
     */
    public function __construct() {

        //Standardmäßig leeres Array (muss ja kein gesetzlicher Feiertag sein)
        $this->holidayInStates = array();

        //Automatisch Attribute setzen und ggf. überschreiben
        $this->setAttributes();

        //Automatisch das Datum berechnen
        $this->updateDate();
    }

    /**
     * Simple Methode, die die Variable $name setzen soll (void - Funktion)
     * --> Soll in Kindklassen über "setAttributes()" aufgerufen werden
     */
    protected function setName($n) {
        $this->name = $n;
    }

    /**
     * Methode, die die Attribute $name und $holidayInStates besetzen soll
     */
    abstract protected function setAttributes();

    /**
     * $date-Variable berechnen und setzen (void - Funktion)
     */
    abstract protected function setDate();

    /**
     * Setzt die Variable "holidayInStates" (void - Funktion)
     * @param array Ein Array mit Konstanten aus "states.php"
     */
    protected function setHolidayInStates($statesArray) {
        
        //Arrays umschichten/mergen
        $merged = array();
        foreach($statesArray as $stateArray) {
            $merged = array_merge($merged,$stateArray);
        }

        //Speichern
        $this->holidayInStates = $merged;
    }

    /**
     * Gibt ein Array zurück, welches von der API in JSON umgewandelt werden kann
     * @return array Das Array
     */
    public function getApiArray() {
        $apiArray = apiReturnArray($this->name,$this->date);
        $apiArray["holidayInStates"] = $this->holidayInStates; //Weiteren Key einfügen
        return $apiArray;
    }

    /**
     * Möglichkeit, von außerhalb der Klasse, das Datum neu berechnen zu lassen (void - Funktion)
     */
    public function updateDate() {
        $this->setDate();
    }

    /**
     * Gibt den Monatsnamen als String
     * @return string Monatsname
     */
    public function getMonthName() {
        $monthName = strftime("%B",$this->date->getTimeStamp());
        return utf8_encode($monthName); //Siehe getDateString()
    }

    /**
     * Prüft, ob ein Feiertag im angegebenen Staat
     * @param array Kürzel eines Bundeslands / Name einer State-Konstante, z.B. "SH"
     */
    public function isHolidayInState(string $stateConstName) {
        return in_array($stateConstName,array_keys($this->holidayInStates));
    }

    //-- Getter --

    function getName() {
        return $this->name;
    }

    function getDate() {
        return $this->date;
    }

    function getHolidayInStates() {
        return $this->holidayInStates;
    }
}

//====================================================================================================================================
/** 
 * -- Die Feiertage --
 * 
 * Name des Feiertags
 * - Wann dieser immer ist
 * - in welchen Bundesländern dies ein gesetzlicher Feiertag ist (an gesetzlichen Feiertagen hat man dann frei)
 *   --> Wenn keine Bundesländer angegeben sind, dann ist der Tag kein gesetzlicher Feiertag
 */

/**
 * Neujahr
 * - Immer am 01.01.
 * - alle Bundesländer
 */
class Neujahr extends Holiday {
    
    protected function setAttributes() {
        $this->setName("Neujahr");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-01-01");
    }
}

/**
 * Heilige Drei Könige
 * - Immer am 06.01.
 * - nur in Baden-Württemberg, Bayern, Sachsen-Anhalt
 */
class HeiligeDreiKoenige extends Holiday {

    protected function setAttributes() {
        $this->setName("Heilige Drei Könige");
        $this->setHolidayInStates(array(BW,BY,ST));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-01-06");
    }
}

/**
 * Valentinstag
 * - Immer am 14.02.
 * - kein Bundesland
 */
class Valentinstag extends Holiday {

    protected function setAttributes() {
        $this->setName("Valentinstag");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-02-14");
    }

}

/**
 * Schmutziger Donnerstag
 * - Immer 52 Tage vor Ostersonntag
 * - kein Bundesland
 */
class SchmutzigerDonnerstag extends Holiday {

    protected function setAttributes() {
        $this->setName("Schmutziger Donnerstag");
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->sub(new DateInterval("P52D"));
    }
}

/**
 * Rosenmontag
 * - Immer 48 Tage vor Ostersonntag
 * - kein Bundesland
 */
class Rosenmontag extends Holiday {

    protected function setAttributes() {
        $this->setName("Rosenmontag");
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->sub(new DateInterval("P48D"));
    }
}

/**
 * Fastnachtsdienstag
 * - Immer 47 Tage vor Ostersonntag
 * - kein Bundesland
 */
class Fastnachtsdienstag extends Holiday {

    protected function setAttributes() {
        $this->setName("Fastnachtsdienstag");
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->sub(new DateInterval("P47D"));
    }
}

/**
 * Aschermittwoch
 * - Immer 46 Tage vor Ostersonntag
 * - kein Bundesland
 */
class Aschermittwoch extends Holiday {

    protected function setAttributes() {
        $this->setName("Aschermittwoch");
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->sub(new DateInterval("P46D"));
    }
}

/**
 * Internationaler Frauentag
 * - Seit 1921 immer am 08.03.
 * - nur in Berlin
 */
class InternationalerFrauentag extends Holiday {

    protected function setAttributes() {
        $this->setName("Internationaler Frauentag");
        $this->setHolidayInStates(array(BE));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-03-08");
    }
}

/**
 * Palmsonntag
 * - Immer der Sonntag vor Ostern
 * - kein Bundesland
 */
class Palmsonntag extends Holiday {

    protected function setAttributes() {
        $this->setName("Palmsonntag");
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->sub(new DateInterval("P7D"));
    }
}

/**
 * Gründonnerstag
 * - Immer ein Tag vor Karfreitag
 * - kein Bundesland
 */
class Gruendonnerstag extends Holiday {

    protected function setAttributes() {
        $this->setName("Gründonnerstag");
    }

    protected function setDate() {
        $karf = new Karfreitag();
        $karf = $karf->getDate();
        $this->date = $karf->sub(new DateInterval("P1D"));
    }
}

/**
 * Karfreitag
 * - Immer der letzte Freitag vor Ostern
 * - alle Bundesländer
 */
class Karfreitag extends Holiday {

    protected function setAttributes() {
        $this->setName("Karfreitag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->sub(new DateInterval("P2D"));
    }
}

/**
 * Ostersonntag
 * - Siehe "EasterDay"-Klasse
 * - alle Bundesländer
 */
class Ostersonntag extends Holiday {

    protected function setAttributes() {
        $this->setName("Ostersonntag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-" . EasterDay::getMonth() . "-" . EasterDay::getDay());
    }
}

/**
 * Ostermontag
 * - Immer der Montag nach Ostersonntag
 * - alle Bundesländer
 */
class Ostermontag extends Holiday {

    protected function setAttributes() {
        $this->setName("Ostermontag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->add(new DateInterval("P1D"));
    }
}

/**
 * Walpurgisnacht
 * - Immer am 30.04.
 * - kein Bundesland
 */
class Walpurgisnacht extends Holiday {

    protected function setAttributes() {
        $this->setName("Walpurgisnacht");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-04-30");
    }
}

/**
 * Erster Mai
 * - Immer am 01.05.
 * - alle Bundesländer
 */
class ErsterMai extends Holiday {

    protected function setAttributes() {
        $this->setName("Erster Mai");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-05-01");
    }
}

/**
 * Star Wars Day
 * - Immer am 04.05.
 * - kein Bundesland
 */
class StarWarsDay extends Holiday {

    protected function setAttributes() {
        $this->setName("Star Wars Day");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-05-04");
    }
}

/**
 * Eisheilige
 * - Immer 11.05. bis 15.05.
 * - kein Bundesland
 */
abstract class Eisheilige extends Holiday {

    //Tag des jeweiligen Eisheiligen, z.B. 12
    protected int $day;

    /**
     * Bestimmt anhand des Tages (11.05. bis 15.05.) den Eisheiligen
     * @return string Name des Eisheiligen als String
     */
    protected function getEisheiligenNamen() {

        if($this->day === 11) {
            return "Mamertus";
        } else if($this->day === 12) {
            return "Pankratius";
        } else if($this->day === 13) {
            return "Servatius";
        } else if($this->day === 14) {
            return "Bonifatius";
        }
        //15
        return "(Kalte) Sophie";
    }

    protected function setAttributes() {
        $this->setName("Eisheilige (" . ($this->getEisheiligenNamen()) . ")");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-05-" . $this->day);
    }
}
class EisheiligeMamertus extends Eisheilige {
    
    public function __construct() {
        $this->day = 11;
        parent::__construct();
    }
}
class EisheiligePankratius extends Eisheilige {
    
    public function __construct() {
        $this->day = 12;
        parent::__construct();
    }
}
class EisheiligeServatius extends Eisheilige {
    
    public function __construct() {
        $this->day = 13;
        parent::__construct();
    }
}
class EisheiligeBonifatius extends Eisheilige {
    
    public function __construct() {
        $this->day = 14;
        parent::__construct();
    }
}
class EisheiligeSophia extends Eisheilige {
    
    public function __construct() {
        $this->day = 15;
        parent::__construct();
    }
}

/**
 * Muttertag
 * - Immer am 2. Sonntag im Mai
 * - kein Bundesland
 */
class Muttertag extends Holiday {

    protected function setAttributes() {
        $this->setName("Muttertag");
    }

    protected function setDate() {

        //Finde ersten Sonntag im Mai
        $mt = getFirstWeekdayOfMonth(5,0);

        //Eine Woche addieren
        $mt->add(new DateInterval("P7D"));

        $this->date = new DateTime($mt->format("Y-m-d"));
    }
}

/**
 * Christi Himmelfahrt & Vatertag
 * - Immer am 40. Tag nach Ostersonntag (der dabei als Tag 1 mitzählt, sollte immer ein Donnerstag sein)
 * - alle Bundesländer (Himmelfahrt)
 */
class HimmelfahrtVatertag extends Holiday {

    protected function setAttributes() {
        $this->setName("Christi Himmelfahrt & Vatertag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->add(new DateInterval("P39D"));
    }
}

/**
 * Pfingstsonntag
 * - Immer am 50. Tag nach Ostersonntag (der dabei als Tag 1 mitzählt, sollte immer Sonntags sein)
 * - alle Bundesländer
 */
class Pfingstsonntag extends Holiday {

    protected function setAttributes() {
        $this->setName("Pfingstsonntag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->add(new DateInterval("P49D"));
    }
}

/**
 * Pfingstmontag
 * - Immer ein Tag nach Pfingstsonntag bzw. 7 Wochen plus 1 Tag (Ostersonntag zählt als Tag 1, sollte immer Montags sein)
 * - alle Bundesländer
 */
class Pfingstmontag extends Holiday {

    protected function setAttributes() {
        $this->setName("Pfingstmontag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $ost = new Ostersonntag(); //Gehe von Ostersonntag aus, da ansonsten Pfingstsonntag + Ostersonntag berechnet werden müssten
        $ost = $ost->getDate();
        $this->date = $ost->add(new DateInterval("P50D"));
    }
}

/**
 * Fronleichnam
 * - Immer 60 Tage nach Ostersonntag (Ostermontag ist der 1. Tag)
 * - nur in Baden-Württemberg, Bayern, Hessen, Nordrhein-Westfalen, Rheinland-Pfalz, Saarland
 */
class Fronleichnam extends Holiday {
    
    protected function setAttributes() {
        $this->setName("Fronleichnam");
        $this->setHolidayInStates(array(BW,BY,HE,NW,RP,SL));
    }

    protected function setDate() {
        $ost = new Ostersonntag();
        $ost = $ost->getDate();
        $this->date = $ost->add(new DateInterval("P60D"));
    }
}

/**
 * Siebenschläfertag
 * - Immer am 27.06.
 * - kein Bundesland
 */
class Siebenschlaefertag extends Holiday {

    protected function setAttributes() {
        $this->setName("Siebenschläfertag");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-06-27");
    }
}

/**
 * Mariä Himmelfahrt
 * - Immer am 15.08.
 * - nur in Bayern und im Saarland
 */
class MariaeHimmelfahrt extends Holiday {

    protected function setAttributes() {
        $this->setName("Mariä Himmelfahrt");
        $this->setHolidayInStates(array(BY,SL));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-08-15");
    }
}

/**
 * Weltkindertag
 * - Immer am 20.09.
 * - nur in Thüringen
 */
class Weltkindertag extends Holiday {

    protected function setAttributes() {
        $this->setName("Weltkindertag");
        $this->setHolidayInStates(array(TH));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-09-20");
    }
}

/**
 * Erntedankfest
 * - Immer am ersten Sonntag im Oktober
 * - kein Bundesland
 */
class Erntedankfest extends Holiday {

    protected function setAttributes() {
        $this->setName("Erntedankfest");
    }

    protected function setDate() {
        $this->date = getFirstWeekdayOfMonth(10,0);
    }
}

/**
 * Tag der Deutschen Einheit
 * - Seit 1990 immer am 03.10.
 * - alle Bundesländer
 */
class TagDerDeutschenEinheit extends Holiday {

    protected function setAttributes() {
        $this->setName("Tag der Deutschen Einheit");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-10-03");
    }
}

/**
 * Reformationstag & Halloween
 * - Immer am 31.10.
 * - nur in Brandenburg, Mecklenburg-Vorpommern, Sachsen, Sachsen-Anhalt, Thüringen, Schleswig-Holstein, Hamburg, Niedersachsen, Bremen (Reformationstag)
 */
class ReformationstagHalloween extends Holiday {

    protected function setAttributes() {
        $this->setName("Reformationstag & Halloween");
        $this->setHolidayInStates(array(BB,MV,SN,ST,TH,SH,HH,NI,HB));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-10-31");
    }
}

/**
 * Allerheiligen
 * - Immer am 01.11.
 * - nur in Baden-Württemberg, Bayern, Nordrhein-Westfalen, Rheinland-Pfalz, Saarland
 */
class Allerheiligen extends Holiday {

    protected function setAttributes() {
        $this->setName("Allerheiligen");
        $this->setHolidayInStates(array(BW,BY,NW,RP,SL));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-11-01");
    }
}

/**
 * Allerseelen
 * - Immer am 02.11.
 * - kein Bundesland
 */
class Allerseelen extends Holiday {

    protected function setAttributes() {
        $this->setName("Allerseelen");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-11-02");
    }
}

/**
 * Martinstag
 * - Immer am 11.11.
 * - kein Bundesland
 */
class Martinstag extends Holiday {

    protected function setAttributes() {
        $this->setName("Martinstag");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-11-11");
    }
}

/**
 * Volkstrauertag
 * - Immer 2 Sonntage vor dem 1. Advent
 * - kein Bundesland
 */
class Volkstrauertag extends Holiday {

    protected function setAttributes() {
        $this->setName("Volkstrauertag");
    }

    protected function setDate() {
        $ea = new ErsterAdvent();
        $ea = $ea->getDate();
        $this->date = $ea->sub(new DateInterval("P14D"));
    }
}

/**
 * Buß- und Bettag
 * - Immer 11 Tage vor dem 1. Advent (sollte immer ein Mittwoch sein)
 * - nur in Sachsen
 */
class BussUndBettag extends Holiday {

    protected function setAttributes() {
        $this->setName("Buß- und Bettag");
        $this->setHolidayInStates(array(SN));
    }

    protected function setDate() {
        $ea = new ErsterAdvent();
        $ea = $ea->getDate();
        $this->date = $ea->sub(new DateInterval("P11D"));
    }
}

/**
 * Totensonntag
 * - Immer der Sonntag vor dem 1. Advent
 * - kein Bundesland
 */
class Totensonntag extends Holiday {

    protected function setAttributes() {
        $this->setName("Totensonntag");
    }

    protected function setDate() {
        $ea = new ErsterAdvent();
        $ea = $ea->getDate();
        $this->date = $ea->sub(new DateInterval("P7D"));
    }
}

/**
 * 1. Advent
 * - Immer der vierte Sonntag vor Weihnachten
 * - kein Bundesland
 */
class ErsterAdvent extends Holiday {

    protected function setAttributes() {
        $this->setName("1. Advent");
    }

    protected function setDate() {
        $va = new VierterAdvent();
        $va = $va->getDate();
        $this->date = $va->sub(new DateInterval("P21D")); //21 Tage vor dem 4. Advent
    }
}

/**
 * 2. Advent
 * - Immer der dritte Sonntag vor Weihnachten
 * - kein Bundesland
 */
class ZweiterAdvent extends Holiday {

    protected function setAttributes() {
        $this->setName("2. Advent");
    }

    protected function setDate() {
        $va = new VierterAdvent();
        $va = $va->getDate();
        $this->date = $va->sub(new DateInterval("P14D")); //14 Tage vor dem 4. Advent
    }
}

/**
 * Nikolaustag
 * - Immer am 06.12.
 * - kein Bundesland
 */
class Nikolaustag extends Holiday {
    
    protected function setAttributes() {
        $this->setName("Nikolaustag");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-12-06");
    }
}

/**
 * 3. Advent
 * - Immer der zweite Sonntag vor Weihnachten
 * - kein Bundesland
 */
class DritterAdvent extends Holiday {

    protected function setAttributes() {
        $this->setName("3. Advent");
    }

    protected function setDate() {
        $va = new VierterAdvent();
        $va = $va->getDate();
        $this->date = $va->sub(new DateInterval("P7D")); //7 Tage vor dem 4. Advent
    }
}

/**
 * 4. Advent
 * - Immer der letzte Sonntag vor Weihnachten
 * - kein Bundesland
 */
class VierterAdvent extends Holiday {
    
    protected function setAttributes() {
        $this->setName("4. Advent");
    }

    protected function setDate() {
        $ew = new ErsterWeihnachtstag();
        $ew = $ew->getDate();

        $di = getWeekday($ew);

        //Sonntag-Integer auf 7 korrigieren, weil 7. Tag der Woche --> Brauche andere Skala für Berechnung
        if($di === 0) {
            $di = 7;
        }

        //Subtrahiere vom Ersten Weihnachtstag (25.12.) den Wochentag-Index (1-7) des selben Tages ab
        $this->date = new DateTime(EasterDay::getYear() . "-12-" . (25 - $di));
    }
}

/**
 * Heiligabend
 * - Immer am 24.12.
 * - kein Bundesland
 */
class Heiligabend extends Holiday {

    protected function setAttributes() {
        $this->setName("Heiligabend");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-12-24");
    }
}

/**
 * 1. Weihnachtstag
 * - Immer am 25.12.
 * - alle Bundesländer
 */
class ErsterWeihnachtstag extends Holiday {
    
    protected function setAttributes() {
        $this->setName("1. Weihnachtstag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-12-25");
    }
}

/**
 * 2. Weihnachtstag
 * - Immer am 26.12.
 * - alle Bundesländer
 */
class ZweiterWeihnachtstag extends Holiday {
    
    protected function setAttributes() {
        $this->setName("2. Weihnachtstag");
        $this->setHolidayInStates(DE);
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-12-26");
    }
}

/**
 * Silvester
 * - Immer am 31.12.
 * - kein Bundesland
 */
class Silvester extends Holiday {

    protected function setAttributes() {
        $this->setName("Silvester");
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-12-31");
    }
}

//====================================================================================================================================
// -- DEBUG --

//-- Dummy-Klasse für Debugging/Tests (aus $holidayClasses später wieder auskommentieren) --

class DummyHoliday_1 extends Holiday {
    protected function setAttributes() {
        $this->setName("DummyHoliday_1");
        $this->setHolidayInStates(array(SH));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-01-04");
    }
}

class DummyHoliday_2 extends Holiday {
    protected function setAttributes() {
        $this->setName("DummyHoliday_2");
        $this->setHolidayInStates(array(SH));
    }

    protected function setDate() {
        $this->date = new DateTime(EasterDay::getYear() . "-01-04");
    }
}

//====================================================================================================================================
// -- API-Methoden --

/**
 * Bestimmt, ob heute Feiertage sind
 * @return array Array für API-Ausgabe / leeres Array, wenn keinerlei Feiertag ist
 */
function todayHolidays() {

    global $holidayClasses;
    global $state;

    //Rückgabe (bleibt leer, sollte heute keinerlei Feiertag sein)
    $todayHolidays = array();

    //Datum jetzt als String mit YYYY-MM-DD - Format
    $today = (new DateTime())->format("Y-m-d");

    //Alle Feiertage überprüfen
    foreach($holidayClasses["holiday"] as $holidayName) {

        //Holiday-Objekt erstellen
        $holidayObj = new ReflectionClass($holidayName);
        $holidayObj = $holidayObj->newInstanceArgs(); //Instanziieren

        if($holidayObj->getDate()->format("Y-m-d") === $today) {
            
            //Wurde GET-Parameter "state" angegeben?
            if($state !== "") {
                //Ja - Überprüfung ausführen
                if($holidayObj->isHolidayInState($state)) {
                    array_push($todayHolidays,$holidayObj->getApiArray());
                }
            } else {
                //Nein - Überprüfung NICHT ausführen
                array_push($todayHolidays,$holidayObj->getApiArray());
            }
        }
    }

    return $todayHolidays;
}

/**
 * Gibt nächste Feiertag(e) innerhalb eines Tages zurück (wenn heute ein Feiertag ist, der nächste)
 * @return array Array für API-Ausgabe / leeres Array, wenn keinerlei Feiertag ist
 */
function nextHolidays() {

    global $holidayClasses;
    global $state;

    $today = new DateTime();

    //Rückgabe (bleibt leer, sollte heute keinerlei Feiertag sein)
    $nextHolidays = array();

    //Nächstes Datum mit Feiertag(en)
    $nextHolidaysDate = NULL;

    //Alle Feiertage überprüfen
    foreach($holidayClasses["holiday"] as $holidayName) {
        
        //Holiday-Objekt erstellen
        $holidayObj = new ReflectionClass($holidayName);
        $holidayObj = $holidayObj->newInstanceArgs(); //Instanziieren

        //Datum von aktuell zu überprüfenden Feiertag zwischenspeichern
        $holidayObjDate = $holidayObj->getDate();

        if($holidayObjDate > $today) {

            //Fange ersten Tag ab, der einen oder potenziell mehrere Feiertage enthält
            if(is_null($nextHolidaysDate)) {
                $nextHolidaysDate = $holidayObjDate;
            }

            //Datum des zu überprüfenden Feiertages muss zwischen heute und dem Datum des bisher nächsten Feiertag liegen
            if($holidayObjDate <= $nextHolidaysDate) {

                //Wurde GET-Parameter "state" angegeben?
                if($state !== "") {
                    //Ja - Überprüfung ausführen
                    if($holidayObj->isHolidayInState($state)) {
                        array_push($nextHolidays,$holidayObj->getApiArray());
                    }
                } else {
                    //Nein - Überprüfung NICHT ausführen
                    array_push($nextHolidays,$holidayObj->getApiArray());
                }
            }
        }
    }

    return $nextHolidays;
}

/**
 * Alle Feiertage des angegebenen Jahres
 * @return array Array für API-Ausgabe
 */
function allHolidays() {

    global $holidayClasses;
    global $state;

    //Rückgabe
    $allHolidays = array();

    foreach($holidayClasses["holiday"] as $holidayName) {
        
        //Holiday-Objekt erstellen
        $holidayObj = new ReflectionClass($holidayName);
        $holidayObj = $holidayObj->newInstanceArgs(); //Instanziieren

        //-- Feiertag in Array packen --

        //Wurde GET-Parameter "state" angegeben?
        if($state !== "") {
            //Ja - Überprüfung ausführen
            if($holidayObj->isHolidayInState($state)) {
                array_push($allHolidays,$holidayObj->getApiArray());
            }
        } else {
            //Nein - Überprüfung NICHT ausführen
            array_push($allHolidays,$holidayObj->getApiArray());
        }
    }

    return $allHolidays;
}

/**
 * Alle übrigen Feiertage des angegeben Jahres
 * @return array Array für API-Ausgabe
 */
function remainingHolidays() {

    //Rückgabe
    $remainingHolidays = array();

    //Alle Feiertage für das angegebene Jahr und ggf. Bundesland
    $allHolidays = allHolidays();

    foreach($allHolidays as $holiday) {
        if($holiday["diff"] > 0) {
            array_push($remainingHolidays,$holiday);
        }
    }

    return $remainingHolidays;
}