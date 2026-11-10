// Sturschädl – Seitencode der Seite MEDIEN (Wirtshaus-Design)
// #html17 = Seiten-Baustein. Die Galerie-Bilder kommen aus der CMS-Sammlung „Galerie“
// (Felder: Titel, Bild, Kategorie = Kirwa / Hochzeit / Band, Reihenfolge = Zahl, optional;
// Bilder ohne Reihenfolge stehen vorne, neueste zuerst). Die Band pflegt sie selbst unter CMS → Galerie.
// Mauseffekt und Großansicht werden in der CMS-Sammlung „Schalter“ an- und ausgeschaltet.
// Großansicht = Wix-Lightbox „Bildansicht“ (Vollbild mit Pfeilen).
import wixData from 'wix-data';
import wixWindow from 'wix-window';
import { verbindeBausteine } from 'public/bausteine.js';

// wix:image://v1/708e31_abc~mv2.jpg/name.jpg#originWidth=… → Bild-Adresse in passender Größe
function bildUrl(bild, groesse) {
    if (!bild) return '';
    if (/^https?:/.test(bild)) return bild;
    const m = String(bild).match(/^wix:image:\/\/v1\/([^/#]+)/);
    if (!m) return '';
    return `https://static.wixstatic.com/media/${m[1]}/v1/fit/w_${groesse},h_${groesse},q_85/${m[1]}`;
}

// Freitext der Band → Filter der Seite (kirwa / hochzeit / band)
function kategorie(text) {
    const t = String(text || '').toLowerCase();
    if (t.includes('hochzeit')) return 'hochzeit';
    if (t.includes('band')) return 'band';
    return 'kirwa';
}

async function ladeGalerie() {
    try {
        const res = await wixData.query('Galerie').limit(1000).find();
        return res.items
            .filter((i) => i.bild)
            // Neue Bilder ohne Reihenfolge-Zahl zuerst (neueste vorne), danach nach Reihenfolge aufsteigend
            .sort((a, b) => {
                const ra = typeof a.reihenfolge === 'number' ? a.reihenfolge : -Infinity;
                const rb = typeof b.reihenfolge === 'number' ? b.reihenfolge : -Infinity;
                if (ra !== rb) return ra - rb;
                return new Date(b._createdDate) - new Date(a._createdDate);
            })
            .map((i) => ({ url: bildUrl(i.bild, 1200), gross: bildUrl(i.bild, 2000), titel: i.title || '', kat: kategorie(i.kategorie) }))
            .filter((b) => b.url);
    } catch (e) {
        console.log('Galerie:', e);
        return [];
    }
}

async function ladeSchalter() {
    try {
        const res = await wixData.query('Schalter').limit(1).find();
        const s = res.items[0] || {};
        return { effekt: s.mauseffekt !== false, gross: s.grossansicht !== false };
    } catch (e) {
        return { effekt: false, gross: false };
    }
}

$w.onReady(function () {
    verbindeBausteine($w, ['#html17']);
    let el;
    try { el = $w('#html17'); } catch (e) { return; }
    const daten = Promise.all([ladeGalerie(), ladeSchalter()]);
    const senden = async () => {
        const [bilder, schalter] = await daten;
        el.postMessage({ typ: 'galerie', bilder, effekt: schalter.effekt, gross: schalter.gross });
    };
    el.onMessage((ev) => {
        const d = (ev && ev.data) || {};
        if (d.typ === 'bereit') senden();
        if (d.typ === 'grossansicht' && Array.isArray(d.bilder)) {
            wixWindow.openLightbox('Bildansicht', { bilder: d.bilder, index: d.index || 0 });
        }
    });
    senden();
});
