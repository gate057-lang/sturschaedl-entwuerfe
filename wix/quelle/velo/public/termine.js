// Sturschädl – gemeinsame Helfer für Termine (Wix Velo, Datei: public/termine.js)
// Liest die Sammlung „Termine“ und bereitet die Zeilen für die Wiederholungsfelder (Repeater) auf.
import wixData from 'wix-data';

const MONATE = ['Jan.', 'Feb.', 'März', 'Apr.', 'Mai', 'Juni', 'Juli', 'Aug.', 'Sep.', 'Okt.', 'Nov.', 'Dez.'];
const MONATE_LANG = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
const TAGE = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

/** Datum aus dem CMS lesen: Datumsfeld (Date) oder Text „JJJJ-MM-TT“ bzw. „TT.MM.JJJJ“. */
export function alsDatum(wert) {
    if (!wert) return null;
    if (wert instanceof Date) return isNaN(wert) ? null : new Date(wert.getFullYear(), wert.getMonth(), wert.getDate());
    const s = String(wert).trim();
    let m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (m) return new Date(+m[1], +m[2] - 1, +m[3]);
    m = s.match(/^(\d{1,2})\.(\d{1,2})\.(\d{4})/);
    if (m) return new Date(+m[3], +m[2] - 1, +m[1]);
    const d = new Date(s);
    return isNaN(d) ? null : new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

export function heute() {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

/** Alle Termine laden und in kommend (aufsteigend) und vorbei (absteigend) teilen. */
export async function ladeTermine() {
    const res = await wixData.query('Termine').limit(1000).find();
    const h = heute();
    const alle = res.items
        .map((t) => ({ ...t, _datum: alsDatum(t.datum) }))
        .filter((t) => t._datum);
    const kommend = alle.filter((t) => t._datum >= h).sort((a, b) => a._datum - b._datum);
    const vorbei = alle.filter((t) => t._datum < h).sort((a, b) => b._datum - a._datum);
    return { kommend, vorbei };
}

/** Infozeile: „Ort · 20:00 Uhr · Zusatz“ */
export function infoZeile(t) {
    const zeit = t.uhrzeit ? String(t.uhrzeit).replace(/\s*uhr$/i, '') + ' Uhr' : '';
    return [t.ort, zeit, t.zusatz].filter(Boolean).join(' · ');
}

export function langesDatum(d) {
    return d ? `${TAGE[d.getDay()]}, ${d.getDate()}. ${MONATE_LANG[d.getMonth()]} ${d.getFullYear()}` : '';
}

/**
 * Eine Repeater-Zeile füllen. Erwartete Elemente in der Zeile:
 * #tMonat, #tTag, #tTitel, #tInfo, #tStatus (optional: #tLink als Knopf)
 */
export function fuelleZeile($item, t, statusText) {
    const d = t._datum || alsDatum(t.datum);
    setze($item, '#tMonat', d ? MONATE[d.getMonth()] : '');
    setze($item, '#tTag', d ? String(d.getDate()).padStart(2, '0') : '?');
    setze($item, '#tTitel', t.title || '');
    setze($item, '#tInfo', infoZeile(t));
    setze($item, '#tStatus', statusText);
    try {
        const knopf = $item('#tLink');
        if (knopf && t.link && statusText !== 'Vorbei') {
            knopf.link = t.link;
            knopf.target = '_blank';
            knopf.expand();
        } else if (knopf) {
            knopf.collapse();
        }
    } catch (e) { /* #tLink gibt es nicht – kein Problem */ }
}

function setze($item, id, text) {
    try {
        const el = $item(id);
        if (el && 'text' in el) el.text = text;
    } catch (e) { /* Element fehlt – wird übersprungen */ }
}
