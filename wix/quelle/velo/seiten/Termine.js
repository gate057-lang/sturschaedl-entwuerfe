// Sturschädl – Seitencode der Seite TERMINE (im Wix-Editor: Seite „Termine“ → Code-Leiste → alles ersetzen)
// Benötigte Elemente:
//   #kommendRepeater     Wiederholungsfeld für kommende Termine  (#tMonat #tTag #tTitel #tInfo #tStatus, optional #tLink)
//   #rueckblickRepeater  Wiederholungsfeld für vergangene Termine (gleiche Elemente)
//   #rueckblickBereich   Abschnitt/Box um den Rückblick (wird versteckt, wenn leer)
//   #freiBox             Kasten „Euer Fest?“
import { ladeTermine, fuelleZeile } from 'public/termine.js';

const RUECKBLICK_MAX = 30;

$w.onReady(async function () {
    try {
        const { kommend, vorbei } = await ladeTermine();

        const k = $w('#kommendRepeater');
        k.onItemReady(($item, t) => fuelleZeile($item, t, 'Live'));
        k.data = kommend;
        if (!kommend.length) k.collapse();

        const v = $w('#rueckblickRepeater');
        v.onItemReady(($item, t) => fuelleZeile($item, t, 'Vorbei'));
        v.data = vorbei.slice(0, RUECKBLICK_MAX);
        if (!vorbei.length) { try { $w('#rueckblickBereich').collapse(); } catch (e) { } }
    } catch (e) {
        console.log('Termine:', e);
    }
});
