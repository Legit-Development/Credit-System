Copyright by LegitDev/Legit-Development

In dem plugin_data Ordner sollte eine Transsaktionen.yml vorhanden sein da diese beim starten des Server erstellt wird.
Hier werden sämtliche Transaktionen mit Uhrzeit ,Datum, Spielernamen und Anzahl der Credits gespiechert

Syntax:
pay : {tag.monat.jahr} | {stunde.minute.sekunde} Console -> {spieler2} Credits : {anzahl der credits}
givecredits : {tag.monat.jahr} | {stunde.minute.sekunde} Console -> {spieler2} Credits : {anzahl der credits}
removecredits : {tag.monat.jahr} | {stunde.minute.sekunde} Console <- {spieler2} Credits : {anzahl der credits}
setcredits : {tag.monat.jahr} | {stunde.minute.sekunde} {spieler1} set {spieler2} Credits : {anzahl der credits}

Befehle:
/credits: Siehe den Konto stand von dir oder von anderen Spielern an 
/pay: Gebe anderen Spielern Credits
/givecredits: (Adminestrator Befehl) gebe dir selber Credits
/removecredits: (Adminestrator Befehl) entziehe dir oder anderen Spielern Credits
/setcredits: (Adminestrator Befehl) setzte die Creits von dir oder von andern Spielern

Syntax:
/credits {name}
/pay {name} {credits}
/givecredits {credits}
/removecredits{name} {credits}
/setcredits {name} {credits}
