// Sturschädl – Seitencode für MEDIEN, ÜBER UNS, HOCHZEITEN (Wirtshaus-Design)
// #html6 = Seiten-Baustein (Seitenkopf + Inhalt + CTA). Links im Baustein laufen über wixLocation.
import { verbindeBausteine } from 'public/bausteine.js';

$w.onReady(function () {
    verbindeBausteine($w, ['#html6']);
});
