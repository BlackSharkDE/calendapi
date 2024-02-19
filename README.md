# calendAPI

Eine API für Kalender und Ereignisse (dieses Projekt ist ein reines API-Projekt und soll keine Nutzer-UI haben).

## Voraussetzungen

* Apache 2.4.43
* PHP mindestens in der Version 7.4.29

## Setup

Es muss lediglich der Ordner `calendAPI` auf den Webserver kopiert werden.

## Anfragen an die API stellen

Aktuell funktioniert nur den Endpunkt `api-events.php`. Dieser beinhaltet aktuell alle Funktionalitäten des Projekts. Dieser Endpunkt gibt JSON aus und ist über
HTTP-GET-Parameter ansteuerbar. Welche Parameter und wie man diese kombinieren kann, steht in der Datei im Kopfbereich.

## Ferien definieren

Die Datei `resources/holidays.json` hat den Aufbau, dass ein Jahr mehrere Bundesländer beinhaltet, die wiederum die Ferienzeiträume definieren.

Das hinzufügen von neuen Einträgen sollte selbsterklärend sein, da für ein neues Jahr und für die Bundesländer in den Jahren jeweils bereits Beispiele
in der Datei zu finden sind.

## Geburtstage definieren

Die Datei `resources/birthdays.json` hat die Unterteilung in `public` und `private` (damit man die Geburtstage filtern/unterscheiden kann):
* Alle Geburtstage in `public` sind von interessanten Personen des Öffentlichen Lebens und andere bekannte Personen, die man nicht privat kennt.
* Die Geburtstage in `private` sind von Personen aus dem privaten Umfeld.

Einen neuen Geburtstag hinzuzufügen sollte auch hier selbsterklärend sein, da bereits Beispiele vorhanden sind.