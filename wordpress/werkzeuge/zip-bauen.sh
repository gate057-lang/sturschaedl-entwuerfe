#!/bin/bash
# Baut 1) sturschaedl-theme.zip (Design → Themes → Theme hochladen)
#      2) sturschaedl-wordpress-paket.zip (Theme-ZIP + Anleitung als PDF + Vorschaubilder + LIESMICH)
# Vorher die PDF bauen: python3 -P anleitungen/werkzeuge/pdf-bauen.py wordpress
set -e
cd "$(dirname "$0")/.."
python3 werkzeuge/css-erzeugen.py >/dev/null
cp ../anleitungen/Sturschaedl-Anleitung-WordPress.pdf sturschaedl/anleitung.pdf
rm -f sturschaedl-theme.zip
zip -rq -X sturschaedl-theme.zip sturschaedl -x '*.DS_Store'

P=_paket/sturschaedl-wordpress-paket
rm -rf _paket && mkdir -p "$P/vorschau"
cp sturschaedl-theme.zip "$P/"
cp ../anleitungen/Sturschaedl-Anleitung-WordPress.pdf "$P/Anleitung-WordPress.pdf"
cp ../vorschau/*.jpg "$P/vorschau/"
cat > "$P/LIESMICH.txt" <<'TXT'
Sturschädl-Website – Paket für WORDPRESS
=========================================

Inhalt:
  Anleitung-WordPress.pdf   Schritt-für-Schritt-Anleitung (bitte zuerst lesen)
  sturschaedl-theme.zip     Das Theme. NICHT entpacken!
                            In WordPress hochladen unter: Design → Themes →
                            Theme hinzufügen → Theme hochladen
  vorschau/                 Die fünf Designs als Bild

Das Design wird später in WordPress umgeschaltet unter:
  Sturschädl → Reiter „Design“ → Bild anklicken → „Änderungen speichern“

Dieses Paket ist nur für WordPress. Für Wix gibt es ein eigenes Paket
(sturschaedl-wix-paket.zip).
TXT
rm -f sturschaedl-wordpress-paket.zip
(cd _paket && zip -rq -X ../sturschaedl-wordpress-paket.zip sturschaedl-wordpress-paket -x '*.DS_Store')
rm -rf _paket
ls -la sturschaedl-theme.zip sturschaedl-wordpress-paket.zip
