// Sturschädl – Verbindung zu den HTML-Bausteinen (Wix Velo, Datei: public/bausteine.js)
// Die Bausteine (Wix-Element „HTML einbetten“) schicken {typ:'bereit'} und {typ:'gehe', ziel:'/kontakt'}.
// Dieser Code antwortet mit {typ:'hallo'} (dann laufen Links über wixLocation) und liefert die Termine
// aus Wix Events als {typ:'termine', liste:[{titel, datum, zeit, ort, url, vorbei}]}.
import wixLocation from 'wix-location';
import wixData from 'wix-data';

const ZEITZONE = 'Europe/Berlin';

function uhrzeit(d) {
    try {
        return new Intl.DateTimeFormat('de-DE', { hour: '2-digit', minute: '2-digit', timeZone: ZEITZONE }).format(d);
    } catch (e) {
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }
}

function ortVon(e) {
    const l = e.location || e.eventLocation || {};
    const name = l.name || e.locationName || '';
    const a = l.address || e.locationAddress || '';
    let ort = typeof a === 'string' ? a : (a.formatted || a.city || '');
    ort = ort.replace(/,\s*(Deutschland|Germany)\s*$/i, '').replace(/^\d{5}\s+/, '').trim();
    if (name && ort.endsWith('-' + name)) ort = ort.slice(0, -(name.length + 1));
    ort = ort.replace(/ in der Oberpfalz/, ' i.d.OPf.');
    return ort || name;
}

function startVon(e) {
    const s = e.start || e.startDate || (e.scheduling && (e.scheduling.startDate || (e.scheduling.config && e.scheduling.config.startDate))) || (e.dateAndTimeSettings && e.dateAndTimeSettings.startDate);
    const d = s ? new Date(s) : null;
    return d && !isNaN(d) ? d : null;
}

function urlVon(e) {
    if (e.eventPageUrl) {
        if (typeof e.eventPageUrl === 'string') return e.eventPageUrl;
        if (e.eventPageUrl.base) return e.eventPageUrl.base + (e.eventPageUrl.path || '');
    }
    return e.slug ? wixLocation.baseUrl + '/event-details/' + e.slug : '';
}

/** Alle Veranstaltungen aus Wix Events (CMS-Sammlung „Events/Events“). */
export async function ladeTermine() {
    let items = [];
    try {
        const res = await wixData.query('Events/Events').limit(100).find();
        items = res.items;
    } catch (e) {
        console.log('Termine (Events/Events):', e);
        return null;
    }
    const jetzt = new Date();
    return items
        .map((e) => {
            const d = startVon(e);
            if (!d) return null;
            const ende = e.end ? new Date(e.end) : d;
            return {
                titel: e.title || '',
                datum: d.toISOString(),
                zeit: uhrzeit(d),
                ort: ortVon(e),
                url: urlVon(e),
                vorbei: (ende < jetzt) || /ENDED|CANCELED/i.test(String(e.status || '')),
            };
        })
        .filter(Boolean);
}

/**
 * Bausteine einer Seite verbinden.
 * @param $w        das $w der Seite (bzw. der masterPage)
 * @param ids       z. B. ['#html1', '#html2']
 * @param termineId Baustein, der die Termine anzeigt (optional)
 * @param laufbandId Laufband-Baustein (optional), ladeWoerter = Funktion, die die Begriffe liefert
 * @param sendeAnfrage async Funktion(daten) für das Kontaktformular (optional); Fehler → Meldung im Baustein
 */
export function verbindeBausteine($w, ids, termineId, laufbandId, ladeWoerter, sendeAnfrage) {
    const termine = termineId ? ladeTermine() : Promise.resolve(null);
    const woerter = laufbandId && ladeWoerter ? ladeWoerter().catch(() => []) : Promise.resolve([]);
    ids.forEach((id) => {
        let el;
        try { el = $w(id); } catch (e) { return; }
        if (!el || typeof el.onMessage !== 'function') return;
        const senden = async () => {
            el.postMessage({ typ: 'hallo' });
            if (id === termineId) {
                const liste = await termine;
                if (liste) el.postMessage({ typ: 'termine', liste });
            }
            if (id === laufbandId) {
                const w = await woerter;
                if (w && w.length) el.postMessage({ typ: 'laufband', woerter: w });
            }
        };
        el.onMessage((ev) => {
            const d = (ev && ev.data) || {};
            if (d.typ === 'gehe' && d.ziel) wixLocation.to(d.ziel);
            if (d.typ === 'bereit') senden();
            if (d.typ === 'anfrage' && sendeAnfrage) {
                Promise.resolve()
                    .then(() => sendeAnfrage(d.daten || {}))
                    .then(() => el.postMessage({ typ: 'anfrage-ok' }))
                    .catch((e) => { console.log('Anfrage:', e); el.postMessage({ typ: 'anfrage-fehler' }); });
            }
        });
        senden();
    });
}
