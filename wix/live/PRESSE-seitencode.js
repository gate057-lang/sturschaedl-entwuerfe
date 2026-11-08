// Sturschädl – Seitencode der Seite PRESSE (Wirtshaus-Design)
// #html16 = Seiten-Baustein (Seitenkopf + Downloads + Kurzinfo + CTA). Links im Baustein laufen über wixLocation.
import { verbindeBausteine } from 'public/bausteine.js';

$w.onReady(function () {
    verbindeBausteine($w, ['#html16']);
});
