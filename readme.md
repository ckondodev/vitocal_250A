# Vitocal 250A

## Ziel des Projektes:
Datenabfrage der Viessmann API von einer gehosteten PHP Webseite in eine gehostete MySQL Datenbank. Kann beim gleichen Provider sein, muss aber nicht.

### Disclaimer
Die php Scripte waren ursprünglich nicht für die Weitergabe bestimmt, aber ich habe mal versucht alle persönlichen Zugangsdaten zu entfernen. In PHP habe ich nicht so viel Erfahrung, daher kann es sein, dass der Code nicht perfekt ist. Aber bei mir tut er, was er soll.

__Dennoch übernehme ich keine Garantie für den Code!__

ACHTUNG: Der Code enthält auch noch Debug Ausgaben, die ggf. Interna der Anlage anzeigen. Daher die Links auf die eigenen, fertig konfigurierten, Systeme __nicht__ öffentlich weitergeben.


### Wie funktioniert das?
* Durch den Aufruf der __wp.php__ Seite erfolgt eine Anmeldung bei der API und die Messdaten werden abgefragt.
* Der Aufruf __wp-events.php__ startet auch eine Anmeldung und ruft anschließend die aktuellen Events ab.
* Sowohl die Messdaten, als auch die Events werden beim Abruf in die Datenbank gespeichert.
* Den Aufruf dieser php Scripte erledigt bei mir eine Wetterstation, die regelmäßig Daten auf die Webseite schickt. Das kann aber auch in einem Cron Job erledigt werden. Zum Speichern der Daten müssen diese beiden Scripte nur regelmäßig aufgerufen werden. Ein 5-10 Minuten Takt liefert sehr detailierte Daten. Aber auch ein 20 oder 30 Minuten Takt liefert gute Werte.

### Was fehlt ?
* In den Dateien müssen einige Daten eingefügt werden. Diese Stellen sind durch Platzhalter in spitzen Klammern gekennzeichnet. Das können zum Beispiel diese Platzhalter sein:  ```<ID>, <GUID>, <URI>, ...```
* mit ```<URI>,<PATH>``` ist die URI zum Verzeichnis gemeint, in dem die Scripte liegen. Also die URI, mit der die Scripte aufgerufen werden.
* In der Datei __database/config.php__ müssen die Zugangsdaten zur mySQL Datenbank eingetragen werden.
* Die Tabellen in der Datenbank müssen im Vorfeld manuell angelegt worden sein. Die Php Scripte legen selber keine Tabellen an.

Bitte unbedingt auch die Anleitung unter [Viessmann Developer](https://app.developer.viessmann.com) lesen. Dort werden die notwendigen Schritte für die Anmeldung näher erläutert. Unter anderem wird hier auch die ClientID erzeugt, die für die OAuth Anmeldung notwendig ist. Als OAuth Provider nutze ich [auth0.com](https://auth0.com/).
