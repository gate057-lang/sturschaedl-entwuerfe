// Sturschädl – Code der Wix-Lightbox „Bildansicht“ (Großansicht der Medien-Galerie)
// Einziges Element im Pop-up = HTML-Baustein bildansicht.html (Vollbild). Bekommt {bilder, index} von der Seite MEDIEN.
// Der Baustein wird über seinen Typ gefunden – die Wix-ID (#html…) ist damit egal.
import wixWindow from 'wix-window';

$w.onReady(function () {
    const ctx = wixWindow.lightbox.getContext() || {};
    let el;
    try { el = $w('HtmlComponent')[0]; } catch (e) { /* weiter unten */ }
    if (!el) {
        for (const id of ['#html6', '#html16', '#html1']) {
            try { const k = $w(id); if (k && k.type === '$w.HtmlComponent') { el = k; break; } } catch (e) { /* nächste */ }
        }
    }
    if (!el) return;
    const senden = () => el.postMessage({ typ: 'bilder', bilder: ctx.bilder || [], index: ctx.index || 0 });
    el.onMessage((ev) => {
        const d = (ev && ev.data) || {};
        if (d.typ === 'bereit') senden();
        if (d.typ === 'schliessen') wixWindow.lightbox.close();
    });
    senden();
});
