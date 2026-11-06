// Sturschädl – Seitencode der STARTSEITE (im Wix-Editor: Startseite öffnen → Code-Leiste unten → alles ersetzen)
// Benötigte Elemente (IDs im Editor unter „Eigenschaften“ setzen):
//   #laufband            Custom Element „sturschaedl-laufband“
//   #naechsterTitel      Text im Kasten „Nächster Auftritt“ (Titel)
//   #naechsterDatum      Text im Kasten „Nächster Auftritt“ (Datum)
//   #termineRepeater     Wiederholungsfeld mit #tMonat #tTag #tTitel #tInfo #tStatus
//   #freiBox             Kasten „Euer Fest?“ (wird gezeigt, wenn kein Termin ansteht)
import { ladeTermine, fuelleZeile, langesDatum } from 'public/termine.js';
import { ladeLaufband } from 'public/laufband.js';

const ANZAHL_TERMINE = 3;           // so viele Zeilen zeigt die Startseite
const DESIGN = '1';                 // 1 Bierzelt-Nacht · 2 Wirtshaus · 3 Rock'n'Raute · 4 Festplakat · 5 Hochzeit Elegant

$w.onReady(async function () {
    // Laufband
    try {
        const woerter = await ladeLaufband();
        $w('#laufband').setAttribute('design', DESIGN);
        $w('#laufband').setAttribute('woerter', JSON.stringify(woerter));
    } catch (e) { console.log('Laufband:', e); }

    // Termine
    try {
        const { kommend, vorbei } = await ladeTermine();

        if (kommend.length) {
            text('#naechsterTitel', kommend[0].title);
            text('#naechsterDatum', langesDatum(kommend[0]._datum));
            zeige('#freiBox', false);
        } else {
            text('#naechsterTitel', 'Saison 2027 – Termine folgen');
            text('#naechsterDatum', 'Jetzt noch Wunschtermin sichern');
            zeige('#freiBox', true);
        }

        const zeilen = kommend.slice(0, ANZAHL_TERMINE)
            .concat(vorbei.slice(0, Math.max(0, ANZAHL_TERMINE - kommend.length)));
        const r = $w('#termineRepeater');
        r.onItemReady(($item, t) => {
            fuelleZeile($item, t, t._datum < new Date(new Date().toDateString()) ? 'War a Gaudi' : 'Live');
        });
        r.data = zeilen;
    } catch (e) { console.log('Termine:', e); }
});

function text(id, wert) {
    try { $w(id).text = wert || ''; } catch (e) { }
}
function zeige(id, an) {
    try { an ? $w(id).expand() : $w(id).collapse(); } catch (e) { }
}
