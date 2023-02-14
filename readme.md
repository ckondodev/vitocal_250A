<H1>Vitocal 250A</H1>

<H2>Ziel des Projektes:</H2>
Datenabfrage der Viessmann API von einer gehosteten PHP Webseite in eine gehostete MySQL Datenbank. Kann beim gleichen Provider sein, muss aber nicht.



Die php Scripte waren ursprünglich nicht für die Weitergabe bestimmt, aber ich habe mal versucht alle persöhnlichen Zugangsdaten zu entfernen. In PHP habe ich nicht so viel Erfahrung, daher kann es sein, dass der Code nicht perfekt ist. Aber bei mir tut er, was er soll.


__Dennoch übernehme ich keine Garantie für den Code!__



Durch den Aufruf der wp.php Seite erfolgt eine Anmeldung bei der API und die Messdaten werden abgefragt.

Der Aufruf wp-events.php startet auch eine Anmeldung und ruft anschließend die aktuellen Events ab.

Sowohl die Messdaten, als auch die Events werden beim Abruf in die Datenbank gespeichert.


Die Tabellen in der Datenbank müssen im Vorfeld manuell angelegt worden sein. Die Php Scripte legen selber keine Tabellen an.


