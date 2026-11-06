#!/bin/bash
# Baut sturschaedl-theme.zip (zum Hochladen unter Design → Themes → Theme hochladen)
set -e
cd "$(dirname "$0")/.."
python3 werkzeuge/css-erzeugen.py >/dev/null
rm -f sturschaedl-theme.zip
zip -rq -X sturschaedl-theme.zip sturschaedl -x '*.DS_Store'
ls -la sturschaedl-theme.zip
