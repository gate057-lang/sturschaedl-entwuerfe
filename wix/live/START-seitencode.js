// Sturschädl – Seitencode der STARTSEITE (live auf sturschaedlband.de, Wirtshaus-Design)
// Lädt die Laufband-Begriffe aus der CMS-Sammlung „Laufband“ in das Laufband (#customElement1).
// Termine kommen über Wix Events (Veranstaltungen) und brauchen keinen Code.
import { ladeLaufband } from 'public/laufband.js';

$w.onReady(async function () {
    try {
        const band = $w('#customElement1');
        band.setAttribute('design', '2'); // 2 = Wirtshaus
        const woerter = await ladeLaufband();
        if (woerter.length) band.setAttribute('woerter', JSON.stringify(woerter));
    } catch (e) {
        console.log('Laufband:', e);
    }
});
