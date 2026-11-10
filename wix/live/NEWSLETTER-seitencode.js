// Sturschädl – Seitencode der Seite NEWSLETTER (im Seitenmenü ausgeblendet, noch nicht freigeschaltet)
// #html6 = Seiten-Baustein mit Anmeldeformular. Anmeldungen landen in der CMS-Sammlung „Newsletter“
// (Felder: Vorname, E-Mail, Einwilligung, Quelle). Berechtigung der Sammlung: Formular-Einsendungen
// (Besucher dürfen eintragen, nur die Band darf lesen).
import wixData from 'wix-data';
import { verbindeBausteine } from 'public/bausteine.js';

$w.onReady(function () {
    verbindeBausteine($w, ['#html6']);
    let el;
    try { el = $w('#html6'); } catch (e) { return; }
    el.onMessage(async (ev) => {
        const d = (ev && ev.data) || {};
        if (d.typ !== 'newsletter' || !d.daten) return;
        const x = d.daten;
        if (!x.einwilligung || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(String(x.email || ''))) {
            el.postMessage({ typ: 'newsletter-fehler' });
            return;
        }
        try {
            await wixData.insert('Newsletter', {
                title: String(x.vorname || '').slice(0, 80),
                email: String(x.email).trim().toLowerCase().slice(0, 200),
                einwilligung: true,
                quelle: 'Website ' + new Date().toISOString().slice(0, 10),
            });
            el.postMessage({ typ: 'newsletter-ok' });
        } catch (e) {
            console.log('Newsletter:', e);
            el.postMessage({ typ: 'newsletter-fehler' });
        }
    });
});
