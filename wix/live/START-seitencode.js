// Sturschädl – Seitencode der STARTSEITE (live auf sturschaedlband.de, Wirtshaus-Design)
// Bausteine (HTML einbetten): #html6 Laufband (Begriffe aus CMS „Laufband“), #html1 Fakten+Angebote,
// #html2 Vorstellung, #html3 Termine+Instagram (Termine live aus Wix Events), #html4 CTA. Footer #html5 → masterPage.js
import { ladeLaufband } from 'public/laufband.js';
import { verbindeBausteine } from 'public/bausteine.js';

$w.onReady(function () {
    verbindeBausteine($w, ['#html6', '#html1', '#html2', '#html3', '#html4'], '#html3', '#html6', ladeLaufband);
});
