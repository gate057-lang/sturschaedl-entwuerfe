// Sturschädl – masterPage.js (läuft auf allen Seiten)
// Footer-Baustein #html5 verbinden.
// Teilen-Leiste #htmlTeilen (am linken Rand angeheftet, schwebend): bekommt die Adresse der offenen Seite.
//   Ausblenden geht über die CMS-Sammlung „Schalter“, Feld „Teilen ausblenden“ (angehakt = Leiste weg).
// WhatsApp-Knopf #htmlWhatsapp (unten links angeheftet, beim Laden ausgeblendet) wird nur gezeigt,
//   wenn in „Schalter“ „WhatsApp anzeigen“ angehakt ist.
import wixData from 'wix-data';
import wixLocation from 'wix-location';
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
    const teilen = element('#htmlTeilen');
    const wa = element('#htmlWhatsapp');

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
