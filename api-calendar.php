<?php
/**
 * API-Endpunkt für Kalenderfunktionen
 * 
 * --> Work in Progress / Sketch / Idee <--
 * 
 * Termine würde man in eine Datenbank speichern...
 */

class Appointment {

    //Standard-Kram
    private string $title;
    private string $location;
    private string $category;

    //Zeitraum des Termins
    private DateTime $start;
    private DateTime $end;

    //String für DateInterval()-Klasse --> "P2D" alle 2 Tage / Leer, keine Wiederholung
    private string $intervalString;
    private DateTime $intervalEnd; //Wenn eine Serie, wann diese enden soll (soll diese nicht Enden, ist hier das Startdatum drin)

    //Erinnerung --> 5 Min, 2 Stunden, 5 Tage etc.
    private DateTime $remember;

    //Beschreibung
    private string $description;

    //Array mit URLs (Strings) zu den Anhängen
    private array $attachementUrls;

    /**
     * -- Konstruktor --
     */
    public function __construct(string $t, string $l, string $c = "", DateTime $s, DateTime $e, string $i = "", DateTime $ie, DateTime $r = Null, string $d = "",array $a = array()) {

        $this->title = $t;
        $this->location = $l;
        $this->category = $c;
        
        $this->start = $s;
        $this->end = $e;

        $this->intervalString = $i;
        $this->intervalEnd = $ie;

        $this->remember = $r;

        $this->description = $d;
        $this->attachmentUrls = $a;
    }

    public function getApiArray() {
        return array(
            "title" => $this->title,
            "location" => $this->location,
            "category" => $this->category,

            "start" => $this->start,
            "end" => $this->end,

            "intervalString" => $this->intervalString,
            "intervalEnd" => $this->intervalEnd,

            "remember" => $this->remember,

            "description" => $this->description,
            "attachmentUrls" => $this->attachmentUrls
        );
    }
}

$a1 = new Appointment(
    "Sport",
    "Drinnen oder Draußen",
    "",
    new DateTime("2022-06-14 09:00"),
    new DateTime("2022-06-14 10:00"),
    "P7D",
    new DateTime("2022-06-14 09:00"),
    new DateTime("2022-06-13 14:00"),
    "Mach mal wieder Sport!",
    array("http://some-domain.de/files/trainingsplan.pdf","http://some-other-domain.com/funny.jpg")
);

$a2 = new Appointment(
    "Schlafen",
    "In meinem Bett",
    "",
    new DateTime("2022-06-18 09:00"),
    new DateTime("2022-06-18 09:30"),
    "P7D",
    new DateTime("2022-06-18 09:00"),
    new DateTime("2022-06-18 08:00"),
    "Schlafen ist wichtig"
);

$returnArray = $a1->getApiArray();

//====================================================================================================================================

$returnArray = array("Information" => "Dieser Endpunkt ist nicht im Betrieb!"); //Information

//====================================================================================================================================
// -- Ausgabe der API --

//JSON als Rückgabe angeben
header("Content-type: application/json");
echo(json_encode($returnArray));