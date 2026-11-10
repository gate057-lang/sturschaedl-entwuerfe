// Sturschädl – masterPage.js (läuft auf allen Seiten)
// Footer-Baustein #html5 verbinden. WhatsApp-Knopf #htmlWhatsapp (unten links angeheftet, beim Laden
// ausgeblendet) wird nur gezeigt, wenn in der CMS-Sammlung „Schalter“ „WhatsApp anzeigen“ angehakt ist.
import wixData from 'wix-data';
import { verbindeBausteine } from 'public/bausteine.js';

function waNummer(n) {
    let z = String(n || '').replace(/\D/g, '');
    if (z.startsWith('00')) z = z.slice(2);
    else if (z.startsWith('0')) z = '49' + z.slice(1);
    return z;
}

$w.onReady(function () {
    verbindeBausteine($w, ['#html5']);
    let wa;
    try { wa = $w('#htmlWhatsapp'); } catch (e) { return; }
    wixData.query('Schalter').limit(1).find().then((res) => {
        const s = res.items[0] || {};
        const nummer = waNummer(s.whatsAppNummer);
        if (!s.whatsAppAnzeigen || !nummer) { wa.hide(); return; }
        const senden = () => wa.postMessage({ typ: 'whatsapp', nummer, text: 'Servus Sturschädl, ich hätte eine Anfrage: ' });
        wa.onMessage((ev) => { if (ev && ev.data && ev.data.typ === 'bereit') senden(); });
        senden();
        wa.show();
    }).catch(() => wa.hide());
});
