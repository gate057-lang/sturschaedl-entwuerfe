// Sturschädl – Seitencode TERMINE (Wirtshaus-Design)
// #html6 = Baustein „Termine“; Demnächst + Rückblick kommen live aus Wix Events (Veranstaltungen).
import { verbindeBausteine } from 'public/bausteine.js';

$w.onReady(function () {
    verbindeBausteine($w, ['#html6'], '#html6');
});
