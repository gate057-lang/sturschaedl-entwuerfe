# Sturschädl – Website-Entwürfe

Fünf klickbare Entwürfe für eine neue Website der Band Sturschädl (bayrisch-rockige Party- und Hochzeitsband).

- Übersicht online: https://gate057-lang.github.io/sturschaedl-entwuerfe/
- Jede `sturschaedl-entwurf-*.html` ist eigenständig (Bilder eingebettet) und kann auch einzeln verschickt werden.
- Quellen in `quelle/`: Vorlage `sturschaedl-mockups.html`, Themen in `build.py`. Neu bauen mit `python3 quelle/build.py` (braucht Pillow).

Texte und Bilder stammen von sturschaedlband.de und Instagram @stur.schaedl_band. Reine Entwürfe, keine fertige Website.

## Pakete zum Weitergeben

- **WordPress:** `wordpress/sturschaedl-wordpress-paket.zip` – Theme-ZIP (nicht entpacken), Anleitung-WordPress.pdf, Vorschaubilder.
- **Wix:** `wix/sturschaedl-wix-paket.zip` – Anleitung-Wix.pdf, CMS-Daten, Code, Schriften, Bilder.
- Schritt-für-Schritt-PDFs einzeln: `anleitungen/*.pdf` (Quelle `anleitungen/quelle/*.html`, Bau + Prüfung: `python3 -P anleitungen/werkzeuge/pdf-bauen.py`).
- Reihenfolge beim Neubauen: PDFs → `wordpress/werkzeuge/zip-bauen.sh` → `python3 -P wix/werkzeuge/paket-bauen.py`.

## WordPress-Theme (`wordpress/`)

- `wordpress/sturschaedl-theme.zip` – in WordPress unter *Design → Themes → Theme hochladen* installieren.
- Alle fünf Entwürfe als umschaltbare Designs (Backend: *Sturschädl → Design*). Termine, Laufband, Fakten, Angebote, Galerie, Video, Mitglieder, Kontaktdaten im Backend pflegbar.
- Beim Aktivieren werden Seiten, Menüs, Bilder und Beispieltermine angelegt. Schriften lokal (DSGVO).
- Neu bauen: `wordpress/werkzeuge/zip-bauen.sh` (erzeugt CSS aus `quelle/` und packt das ZIP). Schriften neu laden: `werkzeuge/schriften-laden.py`.

## Wix-Paket (`wix/`)

- `wix/sturschaedl-wix-paket.zip` – Bauanleitung (`ANLEITUNG.html`), CMS-CSV (Termine, Laufband, Mitglieder), Velo-Code, Laufband als Custom Element, Schriften je Design, Bilder.
- Wix nimmt keine Themes/Layouts als Datei an; die Seiten werden im Editor nach Anleitung gebaut, Termine und Laufband pflegt die Band danach im Wix-CMS.
- Neu bauen: `python3 -P wix/werkzeuge/paket-bauen.py` (braucht Pillow). Velo-Test: `node wix/test/velo-test.mjs`.
