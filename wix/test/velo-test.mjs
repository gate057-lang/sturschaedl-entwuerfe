// Testet den Velo-Code mit nachgebauten Wix-Funktionen ($w, wix-data). Aufruf: node wix/test/velo-test.mjs
import fs from 'node:fs'; import path from 'node:path'; import url from 'node:url';
const hier = path.dirname(url.fileURLToPath(import.meta.url)); const q = path.join(hier, '..', 'quelle', 'velo');
const lauf = path.join(hier, 'lauf'); fs.mkdirSync(lauf, { recursive: true });
const heute = new Date(); const iso = (d) => d.toISOString().slice(0, 10);
const plus = (n) => { const d = new Date(heute); d.setDate(d.getDate() + n); return d; };
const DATEN = {
  Termine: [
    { _id: 'a', title: 'Kirwa Stöckelsberg', datum: '2026-10-26', uhrzeit: '20:00', ort: '92348 Berg', zusatz: '' },
    { _id: 'b', title: 'Zukunftsfest', datum: iso(plus(30)), uhrzeit: '19:00 Uhr', ort: 'Deining', zusatz: 'Eintritt frei', link: 'https://example.org' },
    { _id: 'c', title: 'Heute-Fest', datum: plus(0), uhrzeit: '', ort: 'Neumarkt' },             // echtes Date-Objekt
    { _id: 'd', title: 'Altes Fest', datum: '24.05.2026', uhrzeit: '19:00', ort: 'Döllwang' },     // deutsches Format
    { _id: 'e', title: 'Ohne Datum', datum: '', ort: 'x' },
  ],
  Laufband: [{ title: 'Bierzelt', reihenfolge: 2 }, { title: 'Kirwa', reihenfolge: 1 }],
};
const wixData = { query: (n) => { let rows = [...DATEN[n]]; const api = {
  ascending: (f) => { rows.sort((a, b) => a[f] - b[f]); return api; }, limit: () => api,
  find: async () => ({ items: rows }) }; return api; } };
fs.writeFileSync(path.join(lauf, 'wix-data.mjs'), 'export default globalThis.__wixData;');
globalThis.__wixData = wixData;
const umbau = (src) => src.replace(/from 'wix-data'/g, "from './wix-data.mjs'").replace(/from 'public\/(\w+)\.js'/g, "from './$1.mjs'");
for (const f of ['termine', 'laufband']) fs.writeFileSync(path.join(lauf, f + '.mjs'), umbau(fs.readFileSync(path.join(q, 'public', f + '.js'), 'utf8')));
// $w-Nachbau
function element(id) { return { id, text: '', link: '', target: '', collapsed: false, attrs: {},
  expand() { this.collapsed = false; }, collapse() { this.collapsed = true; }, setAttribute(k, v) { this.attrs[k] = v; },
  onItemReady(fn) { this._ready = fn; }, set data(rows) { this._rows = rows.map((r) => { const els = {}; const $item = (i) => (els[i] ||= element(i)); this._ready($item, r); return els; }); }, get data() { return this._rows; } }; }
async function seite(datei) {
  const els = {}; let ready; globalThis.$w = (id) => (els[id] ||= element(id)); globalThis.$w.onReady = (fn) => { ready = fn; };
  fs.writeFileSync(path.join(lauf, datei + '.mjs'), umbau(fs.readFileSync(path.join(q, 'seiten', datei + '.js'), 'utf8')));
  await import(path.join(lauf, datei + '.mjs') + '?' + Math.random()); await ready(); return els;
}
const zeile = (els) => ['#tMonat', '#tTag', '#tTitel', '#tInfo', '#tStatus'].map((i) => els[i]?.text).join(' | ') + (els['#tLink'] ? (els['#tLink'].collapsed ? ' [kein Link]' : ' [Link ' + els['#tLink'].link + ']') : '');
let fehler = 0; const pruefe = (ok, was) => { console.log((ok ? 'OK   ' : 'FEHL ') + was); if (!ok) fehler++; };

const s = await seite('Startseite');
console.log('\nStartseite:'); s['#termineRepeater'].data.forEach((e) => console.log('  ' + zeile(e)));
pruefe(s['#laufband'].attrs.woerter === '["Kirwa","Bierzelt"]', 'Laufband sortiert: ' + s['#laufband'].attrs.woerter);
pruefe(s['#naechsterTitel'].text === 'Heute-Fest', 'Nächster Auftritt = heutiger Termin: ' + s['#naechsterTitel'].text + ' / ' + s['#naechsterDatum'].text);
pruefe(s['#freiBox'].collapsed === true, '„Euer Fest?“ versteckt, weil Termine anstehen');
pruefe(s['#termineRepeater'].data.length === 3, 'Startseite zeigt 3 Zeilen');
const t = await seite('Termine');
console.log('\nTermine kommend:'); t['#kommendRepeater'].data.forEach((e) => console.log('  ' + zeile(e)));
console.log('Rückblick:'); t['#rueckblickRepeater'].data.forEach((e) => console.log('  ' + zeile(e)));
pruefe(t['#kommendRepeater'].data.length === 2 && t['#rueckblickRepeater'].data.length === 2, '2 kommend, 2 vorbei, Termin ohne Datum ausgelassen');
pruefe(t['#kommendRepeater'].data[1]['#tInfo'].text === 'Deining · 19:00 Uhr · Eintritt frei', 'Infozeile ohne doppeltes „Uhr“');
// leere Sammlung
DATEN.Termine = []; const leer = await seite('Startseite');
pruefe(leer['#freiBox'].collapsed === false && leer['#naechsterTitel'].text.startsWith('Saison'), 'Ohne Termine: „Euer Fest?“ sichtbar, Ersatztext im Kasten');
console.log(fehler ? `\n${fehler} Fehler` : '\nAlle Prüfungen bestanden'); process.exit(fehler ? 1 : 0);
