// Sturschädl – Seitencode KONTAKT (Wirtshaus-Design)
// #html6 = Baustein „Kontakt“ (Seitenkopf + Anfrageformular im Entwurfsdesign).
// Absenden: Die Daten gehen in das unsichtbare Wix-Formular #anfrageForm („Anfrage Website“, liegt hinter dem Baustein)
// und werden mit submit() eingereicht → E-Mail an die Band + Eintrag unter Formulare & Einreichungen im Dashboard.
import { verbindeBausteine } from 'public/bausteine.js';

let warteAuf = null;

$w.onReady(function () {
    const f = $w('#anfrageForm');
    f.onSubmitSuccess(() => { if (warteAuf) { warteAuf.ok(); warteAuf = null; } });
    f.onSubmitFailure((e) => { if (warteAuf) { warteAuf.fehler(e); warteAuf = null; } });
    verbindeBausteine($w, ['#html6'], null, null, null, sendeAnfrage);
});

async function sendeAnfrage(d) {
    const f = $w('#anfrageForm');
    const teile = String(d.name || '').trim().split(/\s+/);
    const vorname = teile.shift() || '';
    const nachricht = [
        'Anlass: ' + (d.anlass || '–'),
        'Datum: ' + (d.datum || '–'),
        'Ort: ' + (d.ort || '–'),
        'Gäste (ca.): ' + (d.gaeste || '–'),
        'Telefon: ' + (d.telefon || '–'),
        '',
        d.nachricht || '',
    ].join('\n');
    await f.setFieldValues({ first_name: vorname, last_name: teile.join(' '), email_d952: d.email, write_a_message: nachricht });
    await new Promise((ok, fehler) => {
        warteAuf = { ok, fehler };
        setTimeout(() => { if (warteAuf) { warteAuf.fehler(new Error('Zeitüberschreitung')); warteAuf = null; } }, 20000);
        Promise.resolve(f.submit()).catch((e) => { if (warteAuf) { warteAuf.fehler(e); warteAuf = null; } });
    });
}
