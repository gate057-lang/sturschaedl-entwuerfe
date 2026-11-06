// Sturschädl – Laufband-Begriffe aus der Sammlung „Laufband“ (Wix Velo, Datei: public/laufband.js)
import wixData from 'wix-data';

export async function ladeLaufband() {
    const res = await wixData.query('Laufband').ascending('reihenfolge').limit(100).find();
    return res.items.map((i) => i.title).filter(Boolean);
}
