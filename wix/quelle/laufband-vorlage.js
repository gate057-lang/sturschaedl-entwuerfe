// Sturschädl – Laufband als Wix Custom Element
// Datei im Wix Dev Mode: public/custom-elements/sturschaedl-laufband.js
// Tag-Name im Editor: sturschaedl-laufband
// Attribute: woerter  = JSON-Liste ["Kirwa","Bierzelt",…] (setzt der Seitencode aus der CMS-Sammlung „Laufband“)
//            design   = 1 Bierzelt-Nacht · 2 Wirtshaus · 3 Rock'n'Raute · 4 Festplakat · 5 Hochzeit Elegant
//            tempo    = Sekunden für einen Durchlauf (Standard 30)
const SCHRIFTEN = {{SCHRIFTEN}};
const DESIGNS = {
    '1': { bg: '#e3262b', fg: '#ffffff', x: 'rgba(255,255,255,.55)', font: 'StBarlowCond', style: 'normal', weight: 800, upper: true, size: 26 },
    '2': { bg: '#2b1d14', fg: '#f4ecdc', x: 'rgba(244,236,220,.55)', font: 'StAlegreya', style: 'italic', weight: 500, upper: false, size: 26 },
    '3': { bg: '#ffffff', fg: '#0a0c10', x: '#1d7fe0', font: 'StAnton', style: 'normal', weight: 400, upper: true, size: 26 },
    '4': { bg: '#1b1410', fg: '#e6d3ac', x: 'rgba(230,211,172,.55)', font: 'StAlfaSlab', style: 'normal', weight: 400, upper: true, size: 26 },
    '5': { bg: '#2f5a46', fg: '#ffffff', x: 'rgba(255,255,255,.55)', font: 'StCormorant', style: 'italic', weight: 500, upper: false, size: 28 },
};
const STANDARD = ['Kirwa', 'Bierzelt', 'Hochzeit', 'Feuerwehrfest', 'Fasching', 'Firmenfeier', 'Geburtstag'];

function schriftenLaden() {
    if (document.getElementById('st-laufband-fonts')) return;
    const css = Object.entries(SCHRIFTEN).map(([name, f]) =>
        `@font-face{font-family:'${name}';font-style:${f.style};font-weight:${f.weight};font-display:swap;src:url(data:font/woff2;base64,${f.data}) format('woff2')}`).join('\n');
    const s = document.createElement('style');
    s.id = 'st-laufband-fonts';
    s.textContent = css;
    document.head.appendChild(s);
}

class SturschaedlLaufband extends HTMLElement {
    static get observedAttributes() { return ['woerter', 'design', 'tempo']; }
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }
    connectedCallback() { schriftenLaden(); this.render(); }
    attributeChangedCallback() { this.render(); }
    woerter() {
        const a = this.getAttribute('woerter');
        if (!a) return STANDARD;
        try { const l = JSON.parse(a); if (Array.isArray(l) && l.length) return l.map(String); } catch (e) {
            const l = a.split(/[|,\n]/).map((x) => x.trim()).filter(Boolean);
            if (l.length) return l;
        }
        return STANDARD;
    }
    render() {
        if (!this.shadowRoot) return;
        const d = DESIGNS[this.getAttribute('design') || '1'] || DESIGNS['1'];
        const tempo = Math.max(5, parseFloat(this.getAttribute('tempo')) || 30);
        const esc = (t) => t.replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
        const reihe = this.woerter().map((w) => `${esc(w)}<em>✕</em>`).join('');
        this.shadowRoot.innerHTML = `<style>
:host{display:block;width:100%;height:100%;overflow:hidden;background:${d.bg}}
.band{display:flex;align-items:center;height:100%;min-height:58px;white-space:nowrap;overflow:hidden;
  color:${d.fg};font-family:'${d.font}',sans-serif;font-style:${d.style};font-weight:${d.weight};font-size:${d.size}px;line-height:1;
  letter-spacing:.06em;${d.upper ? 'text-transform:uppercase;' : ''}}
.lauf{display:inline-block;padding-left:0;animation:lauf ${tempo}s linear infinite}
em{font-style:normal;color:${d.x};margin:0 22px}
@keyframes lauf{to{transform:translateX(-50%)}}
@media (max-width:760px){.band{font-size:${Math.round(d.size * 0.77)}px}}
@media (prefers-reduced-motion:reduce){.lauf{animation:none}}
</style><div class="band" role="marquee" aria-label="${esc(this.woerter().join(', '))}"><span class="lauf">${reihe}${reihe}${reihe}${reihe}</span></div>`;
    }
}
if (!customElements.get('sturschaedl-laufband')) customElements.define('sturschaedl-laufband', SturschaedlLaufband);
