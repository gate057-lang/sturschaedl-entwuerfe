// Sturschädl – masterPage.js (läuft auf allen Seiten)
// Footer-Baustein #html5 verbinden.
// Teilen-Leiste #htmlTeilen (am linken Rand angeheftet, schwebend): bekommt die Adresse der offenen Seite.
//   Ausblenden geht über die CMS-Sammlung „Schalter“, Feld „Teilen ausblenden“ (angehakt = Leiste weg).
// WhatsApp-Knopf #htmlWhatsapp (unten links angeheftet, beim Laden ausgeblendet) wird nur gezeigt,
//   wenn in „Schalter“ „WhatsApp anzeigen“ angehakt ist.
// Auf dem Handy beide komplett weg (collapse): Wix zeigt im Handy ausgeblendete Kopfzeilen-Elemente sonst
//   beim Öffnen des Menüs trotzdem an (Fehler vom 17.11.2026).
// Handy statt Leiste: kleiner Knopf #htmlTeilenKnopf in der Handy-Kopfzeile öffnet die Lightbox „Teilen“.
import wixData from 'wix-data';
import wixLocation from 'wix-location';
import wixWindow from 'wix-window';
import { verbindeBausteine } from 'public/bausteine.js';

function waNummer(n) {
    let z = String(n || '').replace(/\D/g, '');
    if (z.startsWith('00')) z = z.slice(2);
    else if (z.startsWith('0')) z = '49' + z.slice(1);
    return z;
}

function element(id) {
    try { const e = $w(id); return e && e.type === '$w.HtmlComponent' ? e : null; } catch (e) { return null; }
}

const TITEL = 'Sturschädl – bayrisch-rockige Party- und Hochzeitsband aus der Oberpfalz';

$w.onReady(function () {
    verbindeBausteine($w, ['#html5']);
    let teilen = element('#htmlTeilen');
    let wa = element('#htmlWhatsapp');
    if (wixWindow.formFactor === 'Mobile') {
        if (teilen) teilen.collapse();
        if (wa) wa.collapse();
        teilen = null;
        wa = null;
        const knopf = element('#htmlTeilenKnopf');
        if (knopf) {
            knopf.onMessage((ev) => {
                if (ev && ev.data && ev.data.typ === 'teilen-oeffnen') {
                    wixWindow.openLightbox('Teilen', { url: wixLocation.url.split('?')[0], titel: TITEL });
                }
            });
        }
    }

    if (teilen) {
        const senden = () => teilen.postMessage({ typ: 'teilen', url: wixLocation.url.split('?')[0], titel: TITEL });
        teilen.onMessage((ev) => { if (ev && ev.data && ev.data.typ === 'bereit') senden(); });
        senden();
        wixLocation.onChange(() => senden());
    }

    wixData.query('Schalter').limit(1).find().then((res) => {
        const s = res.items[0] || {};
        if (teilen && s.teilenAusblenden === true) teilen.hide();
        if (!wa) return;
        const nummer = waNummer(s.whatsAppNummer);
        if (!s.whatsAppAnzeigen || !nummer) { wa.hide(); return; }
        const senden = () => wa.postMessage({ typ: 'whatsapp', nummer, text: 'Servus Sturschädl, ich hätte eine Anfrage: ' });
        wa.onMessage((ev) => { if (ev && ev.data && ev.data.typ === 'bereit') senden(); });
        senden();
        wa.show();
    }).catch(() => { if (wa) wa.hide(); });
});
