// Sturschädl – Seitencode der STARTSEITE (live auf sturschaedlband.de, Wirtshaus-Design)
// Bausteine (HTML einbetten): #html6 Laufband (Begriffe aus CMS „Laufband“), #html1 Fakten+Angebote,
// #html2 Vorstellung, #html3 Termine+Instagram (Termine live aus Wix Events), #html4 CTA. Footer #html5 → masterPage.js
// #image11 = Bandfoto im Kopfbereich: Maus drauf → schwarzer Stierkopf, Maus weg → sofort wieder die Band.
// Auf Handy/Tablet (keine Maus) wechselt ein Tippen zwischen Band und Stierkopf.
import wixWindow from 'wix-window';
import { ladeLaufband } from 'public/laufband.js';
import { verbindeBausteine } from 'public/bausteine.js';

const STIERKOPF = 'wix:image://v1/708e31_352bfb0db9924201825c1bde2428348a~mv2.png/bulle-dunkel.png#originWidth=882&originHeight=904';

function bandfotoWechsel() {
    let foto;
    try { foto = $w('#image11'); } catch (e) { return; }
    if (!foto || !foto.src) return;
    const band = foto.src;
    // Anpassungsmodus EINMAL fest auf „fit“: Das Bandfoto hat fast genau das Seitenverhältnis des Rahmens
    // (1600×1311 zu 466×386), sieht also gleich aus. Würde der Modus beim Wechsel umgestellt, blitzt der
    // Stierkopf kurz vergrößert auf, bevor die Band kommt.
    foto.fitMode = 'fit';
    let stier = false;
    const zeigeStier = () => {
        if (stier) return;
        stier = true;
        foto.src = STIERKOPF;
    };
    const zeigeBand = () => {
        if (!stier) return;
        stier = false;
        foto.src = band;
    };
    const touch = wixWindow.formFactor !== 'Desktop';
    if (touch) {
        foto.onClick(() => (stier ? zeigeBand() : zeigeStier()));
    } else {
        foto.onMouseIn(zeigeStier);
        foto.onMouseOut(zeigeBand);
        // Absicherung: Springt die Maus direkt aufs Laufband (eigener Rahmen), meldet es sich → Band zurück.
        try {
            $w('#html6').onMessage((ev) => {
                if (ev && ev.data && ev.data.typ === 'maus') zeigeBand();
            });
        } catch (e) { /* Laufband fehlt */ }
    }
}

$w.onReady(function () {
    verbindeBausteine($w, ['#html6', '#html1', '#html2', '#html3', '#html4'], '#html3', '#html6', ladeLaufband);
    bandfotoWechsel();
});
