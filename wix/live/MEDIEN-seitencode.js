// Sturschädl – Seitencode der Seite MEDIEN (Wirtshaus-Design)
// #html16 = Seiten-Baustein. Die Galerie-Bilder kommen aus der CMS-Sammlung „Galerie“
// (Felder: Titel, Bild, Kategorie = Kirwa / Hochzeit / Band, Reihenfolge = Zahl, optional;
// Bilder ohne Reihenfolge stehen vorne, neueste zuerst).
// Die Band pflegt die Bilder selbst im Wix-Dashboard unter CMS → Galerie.
import wixData from 'wix-data';
import { verbindeBausteine } from 'public/bausteine.js';

// wix:image://v1/708e31_abc~mv2.jpg/name.jpg#originWidth=… → Bild-Adresse in passender Größe
function bildUrl(bild) {
    if (!bild) return '';
    if (/^https?:/.test(bild)) return bild;
    const m = String(bild).match(/^wix:image:\/\/v1\/([^/#]+)/);
    if (!m) return '';
    return `https://static.wixstatic.com/media/${m[1]}/v1/fit/w_1200,h_1200,q_85/${m[1]}`;
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
            .map((i) => ({ url: bildUrl(i.bild), titel: i.title || '', kat: kategorie(i.kategorie) }))
            .filter((b) => b.url);
    } catch (e) {
        console.log('Galerie:', e);
        return [];
    }
}

$w.onReady(function () {
    verbindeBausteine($w, ['#html16']);
    let el;
    try { el = $w('#html16'); } catch (e) { return; }
    const bilder = ladeGalerie();
    const senden = async () => {
        const b = await bilder;
        if (b.length) el.postMessage({ typ: 'galerie', bilder: b });
    };
    el.onMessage((ev) => { if (ev && ev.data && ev.data.typ === 'bereit') senden(); });
    senden();
});
