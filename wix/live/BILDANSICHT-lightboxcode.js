// Sturschädl – Code der Wix-Lightbox „Bildansicht“ (Großansicht der Medien-Galerie)
// #html1 = Baustein bildansicht.html (Vollbild). Bekommt {bilder, index} von der Seite MEDIEN.
import wixWindow from 'wix-window';

$w.onReady(function () {
    const ctx = wixWindow.lightbox.getContext() || {};
    let el;
    try { el = $w('#html1'); } catch (e) { return; }
    const senden = () => el.postMessage({ typ: 'bilder', bilder: ctx.bilder || [], index: ctx.index || 0 });
    el.onMessage((ev) => {
        const d = (ev && ev.data) || {};
        if (d.typ === 'bereit') senden();
        if (d.typ === 'schliessen') wixWindow.lightbox.close();
    });
    senden();
});
