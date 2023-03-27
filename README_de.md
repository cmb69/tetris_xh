# Tetris_XH

Tetris_XH ist eine Implementierung des bekannten Tetris-Spiels zum Einbinden
in eine CMSimple_XH Website.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
  - [Vulnerabilität der Highscores](#vulnerabilität-der-highscores)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Tetris_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0 mit der JSON Extension.

## Download

Das [aktuelle Release](https://github.com/cmb69/tetris_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

The Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.
Im [CMSimple_XH-Wiki](https://wiki.cmsimple-xh.org/de/?fuer-anwender/arbeiten-mit-dem-cms/plugins)
finden Sie weitere Informationen.

1. **Sichern Sie die Daten auf Ihrem Server.**
1. Entpacken Sie die ZIP-Datei auf Ihrem Computer.
1. Laden Sie den gesamten Ordner `tetris/` auf Ihren Server in den `plugins/`
   Ordner von CMSimple_XH hoch.
1. Vergeben Sie Schreibrechte für die Unterordner `config/`, `css/` und `languages/`.
1. Navigieren Sie zu `Tetris` im Administrationsbereich, und prüfen Sie, ob
   alle Vorraussetzungen erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen CMSimple_XH
Plugins auch im Administrationsbereich der Website. Wählen Sie `Plugins` → `Tetris`.

Sie können die Vorinstellungen von Tetris_XH unter `Konfiguration` ändern.
Beim Überfahren der Hilfe-Icons mit der Maus werden Hinweise zu den
Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder sie entsprechend Ihren Anforderungen
anpassen.

Das Aussehen von Tetris_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Um das Tetris-Spiel auf einer Seite anzuzeigen, fügen Sie dort ein:

    {{{tetris()}}}

## Einschränkungen

Um Tetris zu spielen, muss JavaScript im Browser des Besuchers aktiviert
sein.

### Vulnerabilität der Highscores

Es ist nicht trivial für Hacker die Highscores zu manipulieren ohne
diese tatsächlich erspielt zu haben. Aber dies stellt keine wirkliche
Sicherheitslücke dar, da die Größe der Highscore-Datenbank eingeschränkt
ist.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/tetris_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Tetris_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Tetris_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Tetris_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © 2011-2023 Christoph M. Becker

Polnische Übersetzung © 2012 Kamil Kresz  
Tschechische Übersetzung © 2012 Josef Němec  
Slovakische Übersetzung © 2012 Dr. Martin Sereday

## Danksagung

Tetris_XH basiert auf Tetris with jQuery von Franck Marcia.
Vielen Dank dafür, dass er diese nette und einfache Implementierung von Tetris
entwickelt und den Code unter einer Open-Source-Lizenz veröffentlicht hat.

Das Plugin-Icon wurde von [AtuX](https://www.deviantart.com/atux) entworfen.
Vielen Dank für die Veröffentlichung unter einer freien Lizenz.

Vielen Dank an die Community im [CMSimple_XH Forum](https://www.cmsimpleforum.com)
für Tipps, Anregungen und das Testen.
Besonders möchte ich *Gert* und *oldnema* für ihre Verbesserungsvorschläge danken.
Und ich muss mich bei *bca* entschuldigen, der die Online-Demo als erster getestet hat,
aber aufgrund eines Fehlers im Plugin seinen Highscore nicht eintragen konnte.

Vielen Dank an Luda Wieland, die mich darauf hinwies, dass die Highscores in
Installationen in einem `UserDir` nicht gespeichert werden konnten, und die mir
beim Debuggen des Problems half.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
