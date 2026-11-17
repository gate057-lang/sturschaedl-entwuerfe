// Sturschädl – Code der Wix-Lightbox „Teilen“ (Teilen-Fenster für das Handy, 17.11.2026)
// Einziges Element im Pop-up = HTML-Baustein teilen-fenster.html. Bekommt {url, titel} von masterPage.js
// (Knopf „Teilen“ in der Handy-Kopfzeile) und schließt sich bei ✕ oder Tipp daneben.
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
    const senden = () => el.postMessage({ typ: 'teilen', url: ctx.url || '', titel: ctx.titel || '' });
    el.onMessage((ev) => {
        const d = (ev && ev.data) || {};
        if (d.typ === 'bereit') senden();
        if (d.typ === 'schliessen') wixWindow.lightbox.close();
    });
    senden();
});
