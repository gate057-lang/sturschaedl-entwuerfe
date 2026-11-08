"""Baut die HTML-Bausteine (Wix „Code einbetten“) für das Wirtshaus-Design.

CSS und HTML stammen 1:1 aus dem gebauten Entwurf sturschaedl-entwurf-2-wirtshaus.html.
Jeder Baustein rendert den Entwurf in Originalgröße (Desktop 1440 px Breite, sichtbar der 1180-px-Inhaltsbereich, Handy 390 px)
und skaliert ihn per CSS-zoom auf die Breite des Wix-Elements:
  Desktop: Element auf volle Breite gestreckt; Faktor fest 980/1116 (= 0,878), Inhalt zentriert auf dem
           980er-Raster, Hintergründe laufen randlos über die ganze Fensterbreite
  Handy:   Element 320 px breit, X = 0  ->  Faktor 320/390

Ausgabe: wix/live/bausteine/<name>.html  +  hoehen.json (Höhe je Baustein für Desktop/Handy)
Aufruf:  python3 wix/werkzeuge/bausteine-bauen.py
"""
import json, pathlib, re, subprocess, tempfile

REPO = pathlib.Path(__file__).resolve().parents[2]
ENTWURF = (REPO / "sturschaedl-entwurf-2-wirtshaus.html").read_text(encoding="utf-8")
AUS = REPO / "wix" / "live" / "bausteine"
AUS.mkdir(parents=True, exist_ok=True)

BASIS = "https://www.sturschaedlband.de"
WIX = "https://static.wixstatic.com/media/708e31_"
BILD = {  # Dateien in der Wix-Mediathek (identisch zu quelle/assets, per md5 geprüft)
    "ig-1.jpg": "f50bf818ea8c41dcbd99c6774af57c1b~mv2.jpg",
    "ig-2.jpg": "8cec5502b40547c7a1ec5b74659255c1~mv2.jpg",
    "ig-3.jpg": "63aad68b9a5f4a5a90b4c312b380785a~mv2.jpg",
    "ig-4.jpg": "c91f1a531ffe4292a90b31357219b5fc~mv2.jpg",
    "ig-5.jpg": "aed4e449fb74424ca2f24f7e9b6bf0df~mv2.jpg",
    "ig-6.jpg": "154c705f545847e0a544f1fd2627a3a4~mv2.jpg",
    "ig-7.jpg": "e5a06f5e6a214c90bf2ea5ef18acfce0~mv2.jpg",
    "ig-8.jpg": "4079959609d9450ab9a622612578e2b9~mv2.jpg",
    "ig-9.jpg": "56196bc35b0449959eadf9e4e4646e5b~mv2.jpg",
    "ig-10.jpg": "35e5228abd3e4663824ca5fdb53f3bd8~mv2.jpg",
    "ig-11.jpg": "a062dcb019e0465581057dbb25c5dd3f~mv2.jpg",
    "band-wald.jpg": "90c04f62be55433085bb852e59225f3f~mv2.jpg",
    "band-wirtshaus.jpg": "f3e2cdef419341ef8cc9acb6ddf682e8~mv2.jpg",
    "bulle-hell.png": "31b7fc203858494794963fffd0307fa7~mv2.png",
    "bulle-dunkel.png": "352bfb0db9924201825c1bde2428348a~mv2.png",
    "angebot-hochzeit.jpg": "979aaab7021545079dbc2a341b5eadd0~mv2.jpg",
    "angebot-kirwa.jpg": "631ea1787bb74e3294b0d0c6eb512d93~mv2.jpg",
    "angebot-party.jpg": "ec5dc8d58fbd48a987d662464980c238~mv2.jpg",
    "hochzeit-gaeste.jpg": "91368e33f1174199b28a8677c8a103ca~mv2.jpg",
    "hochzeit-gitarre.jpg": "f91e801672dc4a0d9ff316af6282dc35~mv2.jpg",
    "hochzeit-oben.png": "546a6fa11daf48d69c35f97e42c882d6~mv2.png",
}
GROSS = {"band-wald.jpg", "band-wirtshaus.jpg", "angebot-hochzeit.jpg", "angebot-kirwa.jpg", "angebot-party.jpg", "hochzeit-gaeste.jpg", "hochzeit-gitarre.jpg", "hochzeit-oben.png"}  # große Fotos über Wix-Bilddienst verkleinern
ZIEL = {"start": "/", "termine": "/termine", "hochzeit": "/hochzeiten", "medien": "/medien",
        "ueber": "/über-uns", "kontakt": "/kontakt"}
INSTA = "https://www.instagram.com/stur.schaedl_band"
FACEBOOK = "https://www.facebook.com/Sturschaedl5/"


def key(name):
    return "i-" + re.sub(r"\W", "-", name)


def bild_url(n):
    u = WIX + BILD[n]
    if n in GROSS:
        u += f"/v1/fit/w_1500,h_1500,q_85/{n}"
    return u


# ---------- CSS aus dem Entwurf ----------
CSS = "\n".join(re.findall(r"<style>(.*?)</style>", ENTWURF, re.S))
CSS = CSS.split("/* ---------- Design-Tokens ---------- */", 1)[1]  # Steuerleiste weglassen
FONTS = re.search(r'<link rel="stylesheet" href="(https://fonts[^"]+)"', ENTWURF).group(1)

EIGEN = """
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:transparent;overflow:hidden}
html.mess,html.mess body{overflow:visible}
#site{width:1440px;overflow:visible}
body.m #site{width:390px !important;margin-left:0 !important}
a{color:inherit;text-decoration:none}
.cta-band .bg{background-image:url(BETON)}
.gig{color:inherit;text-decoration:none}
.gig>div:nth-child(2){min-width:0}
.gig h3,.gig p{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
a.gig:not(.past):hover h3{color:var(--red)}
.insta a{display:block}
.legal a{display:inline;margin:0;color:inherit;text-decoration:none}
.legal a:hover{color:#fff}
.filters span{cursor:pointer}
a.video{display:flex;text-decoration:none}
.f .f-senden{display:flex;align-items:center;gap:18px}
.f-meldung{font:500 15px/1.4 var(--fb);color:var(--mute);flex:1;min-width:0}
.f-meldung.ok{color:#2f6b2f}.f-meldung.fehler{color:var(--red)}
.f button:disabled{opacity:.6;cursor:wait}
.side a{text-decoration:none}
.gal .gal-leer{grid-column:1/-1;grid-row:span 2;display:flex;align-items:center;justify-content:center;text-align:center;padding:24px;background:var(--bg2);border:1px solid var(--line);font:700 20px/1.4 var(--fh);letter-spacing:.06em;text-transform:uppercase;color:var(--ink);text-decoration:none}
.gal .gal-leer:hover{color:var(--red)}
"""

JS = r"""
const site=document.getElementById('site');
let fitBreite=-1;
function fit(){const m=innerWidth<600;if(innerWidth===fitBreite)return;fitBreite=innerWidth;document.body.classList.toggle('m',m);if(m){site.style.zoom=innerWidth/390;site.style.width='';site.style.marginLeft='';return;}const z=980/1116,v=innerWidth/z,w=Math.max(1440,v);site.style.zoom=z;site.style.width=w+'px';site.style.marginLeft=((v-w)/2)+'px';}
// Wix lädt den Rahmen oft erst schmal und zieht ihn später breit; Chrome meldet das bei Rahmen außerhalb des Bildes nicht zuverlässig per resize.
fit();addEventListener('resize',fit);addEventListener('pageshow',fit);addEventListener('load',fit);try{new ResizeObserver(fit).observe(document.documentElement);}catch(e){}setInterval(fit,300);
let codeDa=false;
document.addEventListener('click',e=>{const a=e.target.closest('a[data-go]');if(!a||!codeDa)return;e.preventDefault();parent.postMessage({typ:'gehe',ziel:a.dataset.go},'*');});
const MON=['Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'];
const esc=s=>String(s==null?'':s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
function zeile(t){
  const d=new Date(t.datum);const ort=[t.ort,t.zeit?t.zeit+' Uhr':''].filter(Boolean).join(' · ');
  const href=t.url?(' href="'+esc(t.url)+'" target="_top"'):'';
  if(t.vorbei)return '<div class="gig past"><div class="d"><span>'+MON[d.getMonth()]+'</span><b>'+String(d.getDate()).padStart(2,'0')+'</b></div><div><h3>'+esc(t.titel)+'</h3><p>'+esc(ort)+'</p></div><span class="tag">War a Gaudi</span></div>';
  return '<a class="gig"'+href+'><div class="d"><span>'+MON[d.getMonth()]+'</span><b>'+String(d.getDate()).padStart(2,'0')+'</b></div><div><h3>'+esc(t.titel)+'</h3><p>'+esc(ort)+'</p></div><span class="tag">Infos →</span></a>';
}
function zeigeTermine(liste){
  const box=document.querySelector('.gigs[data-termine]');if(!box||!Array.isArray(liste))return;
  const max=+box.dataset.termine||3;const frei=box.querySelector('.gig.free').outerHTML;
  const kommend=liste.filter(t=>!t.vorbei).sort((a,b)=>new Date(a.datum)-new Date(b.datum));
  const vorbei=liste.filter(t=>t.vorbei).sort((a,b)=>new Date(b.datum)-new Date(a.datum));
  let html=kommend.slice(0,max).map(zeile);
  if(html.length<max)html.push(frei);
  html=html.concat(vorbei.slice(0,max-html.length).map(zeile));
  box.innerHTML=html.join('');
}
function zeigeTerminSeite(liste){
  const k=document.querySelector('.gigs[data-kommend]'),v=document.querySelector('.gigs[data-vorbei]');if(!k||!v||!Array.isArray(liste))return;
  const gesamt=+k.dataset.kommend||8;const frei=k.querySelector('.gig.free').outerHTML;
  const kommend=liste.filter(t=>!t.vorbei).sort((a,b)=>new Date(a.datum)-new Date(b.datum)).slice(0,gesamt-2);
  const vorbei=liste.filter(t=>t.vorbei).sort((a,b)=>new Date(b.datum)-new Date(a.datum));
  k.innerHTML=kommend.map(zeile).join('')+frei;
  v.innerHTML=vorbei.slice(0,gesamt-kommend.length-1).map(t=>zeile(t).replace('War a Gaudi','Vorbei')).join('');
}
function zeigeLaufband(w){const s=document.querySelector('.strip span');if(!s||!Array.isArray(w)||!w.length)return;const eins=w.map(x=>esc(x)+' <em>✕</em>').join(' ');s.innerHTML=eins+' '+eins;}
// Medien: Filter
document.querySelectorAll('.filters span').forEach(f=>f.addEventListener('click',()=>{const g=document.querySelector('.gal');if(g&&!g.dataset.h){g.dataset.h=1;g.style.minHeight=g.getBoundingClientRect().height/(parseFloat(site.style.zoom)||1)+'px';}document.querySelectorAll('.filters span').forEach(x=>x.classList.toggle('on',x===f));const k=f.dataset.kat||'alle';document.querySelectorAll('.gal img').forEach(i=>{i.style.display=(k==='alle'||(i.dataset.kat||'').split(' ').includes(k))?'':'none';});}));
document.querySelectorAll('.filters span').forEach(f=>f.addEventListener('click',()=>{const g=document.querySelector('.gal');if(!g)return;let n=g.querySelector('.gal-leer');if(!n){n=document.createElement('a');n.className='gal-leer';n.href='https://www.instagram.com/stur.schaedl_band';n.target='_blank';n.rel='noopener';n.textContent='Unsere Clips gibt’s auf Instagram · @stur.schaedl_band →';g.appendChild(n);}n.style.display=[...g.querySelectorAll('img')].some(i=>i.style.display!=='none')?'none':'flex';}));
// Kontakt: Anlass-Chips + Absenden
document.querySelectorAll('.chips').forEach(c=>c.addEventListener('click',e=>{const s=e.target.closest('.chips>span');if(!s)return;c.querySelectorAll(':scope>span').forEach(x=>x.classList.toggle('on',x===s));}));
const formular=document.querySelector('form.f');
function meldung(t,ok){const m=document.querySelector('.f-meldung');if(!m)return;m.textContent=t;m.className='f-meldung '+(ok?'ok':'fehler');}
if(formular){formular.addEventListener('submit',e=>{e.preventDefault();
  const w=n=>(formular.querySelector('[name='+n+']')||{}).value||'';
  const daten={anlass:(formular.querySelector('.chips .on')||{}).textContent||'',datum:w('datum').trim(),ort:w('ort').trim(),name:w('name').trim(),gaeste:w('gaeste').trim(),email:w('email').trim(),telefon:w('telefon').trim(),nachricht:w('nachricht').trim()};
  if(!daten.name||!daten.email){meldung('Bitte Name und E-Mail angeben.',false);return;}
  if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(daten.email)){meldung('Bitte eine gültige E-Mail-Adresse angeben.',false);return;}
  if(!codeDa){meldung('Senden ist gerade nicht möglich – bitte direkt an sturschaedl@web.de schreiben.',false);return;}
  formular.querySelector('button').disabled=true;meldung('Wird gesendet …',true);
  parent.postMessage({typ:'anfrage',daten},'*');
});}
addEventListener('message',e=>{const d=e.data||{};if(d.typ==='hallo')codeDa=true;if(d.typ==='termine'){codeDa=true;zeigeTermine(d.liste);zeigeTerminSeite(d.liste);}
  if(d.typ==='anfrage-ok'&&formular){formular.reset();formular.querySelector('button').disabled=false;meldung('Danke! Eure Anfrage ist angekommen – mia meldn uns gscheid schnell.',true);}
  if(d.typ==='anfrage-fehler'&&formular){formular.querySelector('button').disabled=false;meldung('Das hat leider nicht geklappt. Bitte direkt an sturschaedl@web.de schreiben.',false);}if(d.typ==='laufband')zeigeLaufband(d.woerter);});
parent.postMessage({typ:'bereit'},'*');
"""


def links(html):
    """data-go -> echte Links (Fallback ohne Seitencode), Bilder -> Wix-Mediathek."""
    def go(m):
        z = ZIEL[m.group(1)]
        return f'href="{BASIS}{z}" target="_top" data-go="{z}"'
    html = re.sub(r'data-go="(\w+)"', go, html)
    html = re.sub(r'data-img="([\w.-]+)"', lambda m: f'src="{bild_url(m.group(1))}"', html)
    return html



def frei_als_link(html):
    """Startseite: Zeile „Euer Fest? … jetzt anfragen“ komplett klickbar (→ Kontakt), Optik unverändert."""
    alt = '<div class="gig free">'
    i = html.index(alt)
    j = html.index('<span class="tag">Frei</span></div>', i) + len('<span class="tag">Frei</span>')
    return (html[:i] + f'<a class="gig free" href="{BASIS}/kontakt" target="_top" data-go="/kontakt">'
            + html[i + len(alt):j] + '</a>' + html[j + len('</div>'):])

def seite(name):
    m = re.search(r'<div class="page[^"]*" data-page="%s">(.*?)\n</div>\n' % name, ENTWURF, re.S)
    return m.group(1)


def vorlage(tid):
    return re.search(r'<template id="%s">(.*?)</template>' % tid, ENTWURF, re.S).group(1)


def sections(html):
    return re.findall(r"  <section.*?</section>\n", html, re.S)


def beton_uri():
    import base64, io
    from PIL import Image
    im = Image.open(REPO / "quelle" / "assets" / "beton.jpg").convert("RGB"); im.thumbnail((1500, 1500))
    buf = io.BytesIO(); im.save(buf, "JPEG", quality=72, optimize=True, progressive=True)
    return "data:image/jpeg;base64," + base64.b64encode(buf.getvalue()).decode()


def bild_vars():
    # nur als CSS-Hintergrund nutzbare Bilder; die Angebotsfotos stehen direkt als <img> im Baustein
    return "#site{" + ";".join(f"--{key(n)}:url({bild_url(n)})" for n in BILD if not n.startswith(("angebot-", "hochzeit-"))) + "}"


# Laufband liegt direkt unter dem Bandfoto: Springt die Maus vom Foto direkt in diesen Rahmen, meldet der Browser
# das Verlassen des Fotos nicht immer. Deshalb meldet das Laufband „Maus ist hier“ → Seitencode zeigt wieder die Band.
MAUS = "\nlet mausZeit=0;document.addEventListener('mousemove',()=>{const t=Date.now();if(t-mausZeit<250)return;mausZeit=t;parent.postMessage({typ:'maus'},'*');},{passive:true});\n"


def baustein(name, inhalt, css=""):
    doc = f"""<!doctype html>
<html lang="de"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="{FONTS}">
<style>{CSS}
{EIGEN.replace("url(BETON)", "url(" + beton_uri() + ")") if "cta-band" in inhalt else EIGEN}{bild_vars() if "var(--i-" in inhalt else ""}{css}</style></head>
<body><div id="site" class="light paper">
{links(inhalt)}
</div>
<script>{JS}{MAUS if name == "laufband" else ""}</script>
</body></html>
"""
    (AUS / f"{name}.html").write_text(doc, encoding="utf-8")
    return name


# ---------- Startseite ----------
START = seite("start")
S = sections(START)  # 0 Fakten, 1 Angebote, 2 Vorstellung, 3 Termine, 4 Instagram
termine = S[3].replace('<div class="gigs">', '<div class="gigs" data-termine="3">', 1)
insta = S[4].replace('<a class="btn ghost">Auf Instagram folgen →</a>',
                     f'<a class="btn ghost" href="{INSTA}" target="_blank" rel="noopener">Auf Instagram folgen →</a>')
insta = re.sub(r'(<img data-img="ig-\d+\.jpg" alt="">)',
               lambda m: f'<a href="{INSTA}" target="_blank" rel="noopener">{m.group(1)}</a>', insta)

# Angebotskarten: eigene Bandfotos (08.11.2026 von Markus geliefert) statt der Instagram-Bilder aus dem Entwurf
angebote = S[1]
for go, bild in (("hochzeit", "angebot-hochzeit.jpg"), ("termine", "angebot-kirwa.jpg"), ("kontakt", "angebot-party.jpg")):
    angebote, n = re.subn(r'(<a class="offer" data-go="%s"><img data-img=")[\w.-]+(")' % go, r"\g<1>%s\2" % bild, angebote)
    assert n == 1, go

strip = re.search(r'  <div class="strip">.*?</div>\n', START, re.S).group(0)
namen = [
    baustein("laufband", strip),
    baustein("start-a-fakten-angebote", S[0] + angebote),
    baustein("start-b-vorstellung", S[2]),
    baustein("start-c-termine-instagram", frei_als_link(termine) + insta),
    baustein("cta", vorlage("ctaT")),
]

fuss = vorlage("footT")
fuss = fuss.replace("<a>Bensch Seitz</a>", '<a href="tel:+491605555637" target="_top">Bensch Seitz</a>')
fuss = fuss.replace("<a>+49 160 5555637</a>", '<a href="tel:+491605555637" target="_top">+49 160 5555637</a>')
fuss = fuss.replace("<a>sturschaedl@web.de</a>", '<a href="mailto:sturschaedl@web.de" target="_top">sturschaedl@web.de</a>')
fuss = fuss.replace("<a>Instagram · @stur.schaedl_band</a>", f'<a href="{INSTA}" target="_blank" rel="noopener">Instagram · @stur.schaedl_band</a>')
fuss = fuss.replace("<a>Facebook</a>", f'<a href="{FACEBOOK}" target="_blank" rel="noopener">Facebook</a>')
fuss = fuss.replace("<span>Impressum · Datenschutz · AGB</span>",
                    f'<span><a href="{BASIS}/impressum" target="_top" data-go="/impressum">Impressum</a> · '
                    f'<a href="{BASIS}/datenschutz" target="_top" data-go="/datenschutz">Datenschutz</a> · '
                    f'<a href="{BASIS}/agb" target="_top" data-go="/agb">AGB</a></span>')
assert "<a>Facebook" not in fuss and "tel:" in fuss
namen.append(baustein("fuss", fuss, "footer{border-top:0}html,body{background:#0b0a09 !important}"))

# ---------- Unterseiten ----------
def unterseite(name, cta=True):
    html = seite(name).replace("  <slot-nav></slot-nav>\n", "").replace("<slot-foot></slot-foot>", "")
    html = html.replace("<slot-cta></slot-cta>", vorlage("ctaT") if cta else "")
    assert "slot-" not in html, name
    return html

t = unterseite("termine")
t = t.replace('<div class="gigs">', '<div class="gigs" data-kommend="8">', 1)
t = t.replace('<div class="gigs">', '<div class="gigs" data-vorbei="1">', 1)
namen.append(baustein("seite-termine", t))

# Hochzeitsseite (08.11.2026): Texte wie von Markus direkt in Wix geändert übernommen (Überschrift „Auftakt“,
# Entwurfssatz und „prüfen“-Schilder raus), Kicker ohne „Vorschlag“, und zwei Fotos im Stil der Seite:
# groß das Hochzeitsfoto im Rahmen mit versetzter Linie, darunter versetzt zwei Fotos weiß gerahmt und schräg
# wie das Bandfoto im Kopf der Startseite (Gitarre + Biergarten von oben); kein Bild überdeckt ein anderes.
h = unterseite("hochzeit")
for a, b in (
    ('<div class="kicker">Neue Seite · Vorschlag</div>', '<div class="kicker">Hochzeiten</div>'),
    ('<h2>Vom Sekt bis<br>zum Schluss</h2>', '<h2>Vom Auftakt bis<br>zum Schlussakkord</h2>'),
    ('<p>Hochzeiten sind eine eigene Zielgruppe mit eigenen Fragen. Eine eigene Seite holt Brautpaare direkt ab.</p>', '<p></p>'),
    ('Eigene Ton- und Lichttechnik <span class="ph-note">prüfen</span>', 'Eigene Ton- und Lichttechnik '),
    ('Flexibles Ende <span class="ph-note">prüfen</span>', 'Flexibles Ende '),
    ('<div class="photo-frame"><img data-img="ig-7.jpg" alt=""></div>',
     '<div class="foto-collage">'
     '<div class="photo-frame"><img data-img="hochzeit-gaeste.jpg" alt="Sturschädl-Musiker in weißen Hemden bei einer Hochzeit"></div>'
     '<div class="collage-unten">'
     '<img class="polaroid links" data-img="hochzeit-gitarre.jpg" alt="Sturschädl-Gitarrist auf einer Hochzeitsfeier">'
     '<img class="polaroid rechts" data-img="hochzeit-oben.png" alt="Sturschädl spielt akustisch im Biergarten">'
     '</div></div>'),
):
    assert a in h, a
    h = h.replace(a, b)
assert "ph-note" not in h and "Vorschlag" not in h
DUO_CSS = """
.foto-collage{position:relative}
.collage-unten{display:flex;justify-content:space-between;align-items:flex-start;margin-top:5%}
.collage-unten .polaroid{display:block;border:10px solid #fff;box-shadow:0 18px 40px rgba(26,23,20,.28);position:relative;z-index:2}
.collage-unten .links{width:38%;margin-left:-14px;transform:rotate(-3deg)}
.collage-unten .rechts{width:54%;margin-top:9%;margin-right:-6px;transform:rotate(2.5deg)}
body.m .collage-unten .polaroid{border-width:6px}
body.m .collage-unten .links{margin-left:0}
body.m .collage-unten .rechts{margin-right:0}
"""
namen.append(baustein("seite-hochzeit", h, DUO_CSS))

m = unterseite("medien")
m = m.replace('<div class="video"><div class="play"></div>',
              f'<a class="video" href="{INSTA}" target="_blank" rel="noopener"><div class="play"></div>')
m = m.replace('Saisonrückblick</span></div>', 'Saisonrückblick</span></a>')
m = m.replace('<span class="on">Alle</span><span>Kirwa &amp; Bierzelt</span><span>Hochzeiten</span><span>Band</span><span>Videos</span>',
              '<span class="on" data-kat="alle">Alle</span><span data-kat="kirwa">Kirwa &amp; Bierzelt</span><span data-kat="hochzeit">Hochzeiten</span><span data-kat="band">Band</span><span data-kat="videos">Videos</span>')
KAT = {"ig-1.jpg": "kirwa", "ig-2.jpg": "kirwa", "ig-9.jpg": "kirwa", "ig-4.jpg": "kirwa", "band-wald.jpg": "band",
       "ig-6.jpg": "kirwa", "ig-11.jpg": "kirwa", "ig-7.jpg": "hochzeit", "band-wirtshaus.jpg": "band", "ig-10.jpg": "kirwa"}
m = re.sub(r'<img( class="[^"]*")? data-img="([\w.-]+)"', lambda x: f'<img{x.group(1) or ""} data-kat="{KAT.get(x.group(2), "")}" data-img="{x.group(2)}"', m)
assert 'Saisonrückblick</span></a>' in m
namen.append(baustein("seite-medien", m, ".gal{align-content:start}"))

namen.append(baustein("seite-ueber", unterseite("ueber")))

k = unterseite("kontakt", cta=False)
k = k.replace('<form class="f" onsubmit="return false">', '<form class="f" novalidate>')
for alt, neu in (('placeholder="TT.MM.JJJJ"', 'name="datum" placeholder="TT.MM.JJJJ"'),
                 ('placeholder="z. B. Deining"', 'name="ort" placeholder="z. B. Deining"'),
                 ('placeholder="Vor- und Nachname"', 'name="name" placeholder="Vor- und Nachname"'),
                 ('placeholder="z. B. 150"', 'name="gaeste" placeholder="z. B. 150"'),
                 ('type="email" placeholder', 'type="email" name="email" placeholder'),
                 ('type="tel" placeholder', 'type="tel" name="telefon" placeholder'),
                 ('<textarea placeholder', '<textarea name="nachricht" placeholder'),
                 ('<div class="full"><button class="btn red" type="button">Anfrage senden →</button></div>',
                  '<div class="full f-senden"><button class="btn red" type="submit">Anfrage senden →</button><span class="f-meldung" role="status"></span></div>'),
                 ('<a class="big" href="#">+49 160 5555637</a>', '<a class="big" href="tel:+491605555637" target="_top">+49 160 5555637</a>'),
                 ('<a class="big" style="font-size:22px" href="#">sturschaedl@web.de</a>', '<a class="big" style="font-size:22px" href="mailto:sturschaedl@web.de" target="_top">sturschaedl@web.de</a>'),
                 ('Instagram @stur.schaedl_band<br>Facebook „Sturschädl“',
                  f'<a href="{INSTA}" target="_blank" rel="noopener" style="color:inherit">Instagram @stur.schaedl_band</a><br><a href="{FACEBOOK}" target="_blank" rel="noopener" style="color:inherit">Facebook „Sturschädl“</a>')):
    assert alt in k, alt
    k = k.replace(alt, neu)
namen.append(baustein("seite-kontakt", k))

# ---------- Rechtsseiten (nicht im Entwurf: Wirtshaus-Seitenkopf + Text der bisherigen Seite, unverändert) ----------
RECHT_CSS = (".recht{max-width:780px}.recht h2{font-size:32px;margin:44px 0 12px}.recht h3{font-size:24px;margin:30px 0 8px}"
             ".recht p{font-size:18px;line-height:1.75;color:var(--mute);margin:0 0 16px}.recht p strong{color:var(--ink)}"
             ".recht a{color:var(--red);text-decoration:none}.recht a:hover{text-decoration:underline}")


def rechtsseite(titel, bild, inhalt):
    return (f'<header class="phead"><div class="bg" style="background-image:var(--{key(bild)})"></div><div class="wrap">\n'
            f'    <div class="kicker">Rechtliches</div><h1>{titel}</h1>\n  </div></header>\n'
            f'  <section><div class="wrap"><div class="recht">\n{inhalt}\n  </div></div></section>\n')


TEL = '<a href="tel:+491605555637" target="_top">+49 160 5555637</a>'
MAIL = '<a href="mailto:sturschaedl@web.de" target="_top">sturschaedl@web.de</a>'
namen.append(baustein("recht-impressum", rechtsseite("Impressum", "ig-5.jpg",
    f"    <p><strong>Sturschädl GbR</strong><br>Kugelgasse 7, 92364 Deining</p>\n"
    f"    <h3>Vertreten durch:</h3>\n"
    f"    <p>Benjamin Seitz<br>Tel: {TEL}<br>E-Mail: {MAIL}<br>www.sturschaedlband.de</p>\n"
    f"    <p>USt-IdNr.: DE133546405</p>"), RECHT_CSS))
namen.append(baustein("recht-datenschutz", rechtsseite("Datenschutz", "ig-5.jpg",
    "    <p>Als Band für Partys legen wir großen Wert auf den Schutz Ihrer Daten. Datenschutz ist für uns ein wichtiger Aspekt unserer Website. "
    "Die folgende Datenschutzerklärung gibt Ihnen einen Überblick darüber, wie wir Ihre Daten schützen und welche Art von Daten wir sammeln. "
    "Bitte beachten Sie, dass die Datenschutzerklärung je nach den Funktionen unserer Website variieren kann. Wir sind bestrebt, Ihre Daten zu schützen "
    "und transparent mit ihnen umzugehen, daher passen wir die Datenschutzerklärung entsprechend an. Die Datenschutzerklärung muss alle "
    "Drittanbieter-Komponenten auflisten, die auf unserer Website verwendet werden. Der Link zur Datenschutzerklärung muss von jeder Seite unserer "
    "Website aus leicht erreichbar sein.</p>\n"
    "    <h2>Beispielinhalte:</h2>\n"
    "    <h3>Datenerhebung, -nutzung und Weitergabe</h3>\n"
    "    <p>In dieser Sektion erklären wir, welche Informationen wir sammeln, wie wir sie nutzen und ob wir sie an Dritte weitergeben.</p>\n"
    "    <h3>Kontrolle über Daten</h3>\n"
    "    <p>Wir geben Ihnen die Möglichkeit, Ihre personenbezogenen Informationen und Daten einzusehen, zu ändern und zu aktualisieren. "
    "Zudem können Sie Bedenken bezüglich der Verwendung Ihrer Daten äußern.</p>\n"
    "    <h3>Datensicherheit</h3>\n"
    "    <p>Der Schutz Ihrer Daten liegt uns am Herzen. Daher ergreifen wir umfassende Schutzmaßnahmen, einschließlich Datenverschlüsselung und "
    "Sicherheitsvorkehrungen für die Speicherung und Übertragung Ihrer Daten. Ihre Sicherheit steht bei uns an erster Stelle.</p>"), RECHT_CSS))
namen.append(baustein("recht-agb", rechtsseite("AGB", "ig-5.jpg",
    "    <p>Unsere Allgemeinen Geschäftsbedingungen (AGB) regeln die vertraglichen Beziehungen zwischen sturschaedlband.de und unseren Kunden. "
    "Sie dienen dazu, unsere Rechte und Pflichten klar und verständlich darzulegen. In unseren AGB finden Sie alle relevanten Informationen zu "
    "unseren Leistungen, Preisen, Zahlungsbedingungen und den Bedingungen für Vertragsabschluss, Stornierung und Widerruf. Wir legen großen Wert "
    "darauf, dass unsere AGB gesetzeskonform und kundenorientiert sind. Dennoch empfehlen wir, unsere AGB von einem Rechtsexperten prüfen zu lassen, "
    "um sicherzustellen, dass sie allen gesetzlichen Anforderungen entsprechen.</p>"), RECHT_CSS))

# ---------- Höhen messen (headless Chrome) ----------
CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
hoehen = {}
for n in namen:
    hoehen[n] = {}
    for art, breite in (("desktop", 1353), ("handy", 320)):
        mess = (AUS / f"{n}.html").read_text(encoding="utf-8").replace(
            "function fit(){const m=innerWidth<600;", f"function fit(){{const innerWidth={breite};const m=innerWidth<600;").replace(
            "parent.postMessage({typ:'bereit'},'*');",
            "document.fonts.ready.then(()=>Promise.all([...document.images].map(i=>i.complete?1:new Promise(r=>{i.onload=i.onerror=r})))).then(()=>{document.title='H='+document.documentElement.scrollHeight+'/'+site.getBoundingClientRect().height});")
        with tempfile.NamedTemporaryFile("w", suffix=".html", delete=False, encoding="utf-8") as f:
            f.write(mess)
        out = subprocess.run([CHROME, "--headless=new", "--disable-gpu", "--hide-scrollbars",
                              f"--window-size={breite},4000", "--virtual-time-budget=15000",
                              "--dump-dom", "file://" + f.name], capture_output=True, text=True, timeout=90).stdout
        h = re.search(r"<title>H=[\d.]+/([\d.]+)</title>", out)
        hoehen[n][art] = round(float(h.group(1))) if h else None
(AUS / "hoehen.json").write_text(json.dumps(hoehen, indent=2, ensure_ascii=False), encoding="utf-8")
print(json.dumps(hoehen, indent=2, ensure_ascii=False))
for n in namen:
    print(n, (AUS / f"{n}.html").stat().st_size, "Bytes")
