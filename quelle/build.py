"""Baut aus sturschaedl-mockups.html fünf eigenständige Entwürfe (Bilder als data-URI eingebettet)."""
import base64, io, re, pathlib
from PIL import Image

HIER = pathlib.Path(__file__).parent
TPL = (HIER / "sturschaedl-mockups.html").read_text(encoding="utf-8")
ASSETS = HIER / "assets"
OUT = HIER.parent  # Repo-Wurzel = GitHub-Pages-Ausgabe
OUT.mkdir(exist_ok=True)

# ---------- Bilder komprimieren und einbetten ----------
_cache = {}
def data_uri(name):
    if name in _cache:
        return _cache[name]
    p = ASSETS / name
    im = Image.open(p)
    buf = io.BytesIO()
    if p.suffix == ".png":
        im.thumbnail((520, 520)); im.save(buf, "PNG", optimize=True); mime = "image/png"
    else:
        im = im.convert("RGB"); im.thumbnail((1500, 1500)); im.save(buf, "JPEG", quality=72, optimize=True, progressive=True); mime = "image/jpeg"
    _cache[name] = f"data:{mime};base64," + base64.b64encode(buf.getvalue()).decode()
    return _cache[name]

def key(name):
    return "i-" + re.sub(r"\W", "-", name)

def embed(html):
    """Jedes Bild nur einmal einbetten: <img> bekommen data-img, CSS nutzt Variablen."""
    used = sorted(set(re.findall(r"assets/([\w.-]+\.(?:jpg|png))", html)))
    html = re.sub(r'src="assets/([\w.-]+\.(?:jpg|png))"', lambda m: f'data-img="{m.group(1)}"', html)
    html = re.sub(r"url\(assets/([\w.-]+\.(?:jpg|png))\)", lambda m: f"var(--{key(m.group(1))})", html)
    assert "assets/" not in html
    js = "const IMGS={" + ",".join(f'"{n}":"{data_uri(n)}"' for n in used) + "};\n"
    js += ("(function(){const s=document.getElementById('site');for(const n in IMGS){s.style.setProperty('--'+'i-'+n.replace(/\\W/g,'-'),'url('+IMGS[n]+')');}"
           "document.querySelectorAll('img[data-img]').forEach(i=>i.src=IMGS[i.dataset.img]);})();\n")
    # erst nach dem Einsetzen der Vorlagen (Navigation, Footer) ausführen
    return html.replace("</script>\n</body>", js + "</script>\n</body>", 1)

# ---------- Vorlage generalisieren ----------
base = TPL
def rep(old, new, count=1):
    global base
    assert old in base, old[:60]
    base = base.replace(old, new, count)

rep('<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;600;700;800&family=Pirata+One&display=swap" rel="stylesheet">', "{{FONTS}}")
base = base.replace('"Barlow Condensed"', "var(--fh)")
base = base.replace('font-family:"Pirata One",serif', "font-family:var(--fx)")
base = base.replace('font:400 30px/1 "Pirata One"', "font:400 30px/1 var(--fx)")
rep("background:var(--bg);color:var(--ink);font:400 17px/1.6 Barlow,sans-serif;overflow-x:hidden}",
    "background:var(--bg);color:var(--ink);font:400 17px/1.6 var(--fb);overflow-x:hidden;--fh:\"Barlow Condensed\",sans-serif;--fb:Barlow,sans-serif;--fx:\"Pirata One\",serif}")
rep(".nav{position:absolute;inset:0 0 auto 0;z-index:10;color:#f4eee2}", ".nav{position:absolute;inset:0 0 auto 0;z-index:10;color:var(--navc,#f4eee2)}\n.lg-d{display:none}\n.page{position:relative}")
rep(".nav a:hover,.nav a.cur{opacity:1;color:#fff;", ".nav a:hover,.nav a.cur{opacity:1;")
rep('<img src="assets/bulle-hell.png" alt=""><span>Sturschädl</span>', '<img class="lg-h" src="assets/bulle-hell.png" alt=""><img class="lg-d" src="assets/bulle-dunkel.png" alt=""><span>Sturschädl</span>')
rep("</style>", "</style>\n<style>{{THEME_CSS}}</style>")
rep('<div id="site" class="paper">', '<div id="site" class="{{CLS}}">')
# Steuerleiste: Stilwahl durch Entwurfs-Namen ersetzen
base = re.sub(r'<div><b>Stil</b>.*?</span></div>', '<div class="ent"><b>Entwurf {{NR}} / 5</b><span style="font-weight:600">{{NAME}}</span></div>', base, flags=re.S)
rep('<span class="hint">Gelb markiert = Platzhalter, Inhalt fehlt noch</span>', '<span class="hint">Klickbarer Entwurf · Gelb markiert = Platzhalter</span>')
rep("seg('stSeg','s',s=>site.classList.toggle('light',s==='light'));\n", "")
# Start-Hero austauschbar machen
m = re.search(r'  <header class="hero">.*?</header>\n', base, flags=re.S)
HERO_A = m.group(0)
base = base[:m.start()] + "{{HERO}}" + base[m.end():]
rep("<title>Sturschädl Mockups</title>", "<title>Sturschädl – Entwurf {{NR}}: {{NAME}}</title>")

GF = '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?{}&display=swap">'
FONT_BASE = "family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@500;600;700;800&family=Pirata+One"

THEMES = []

# ===== 1 · Bierzelt-Nacht =====
THEMES.append(dict(
    slug="1-bierzelt-nacht", name="Bierzelt-Nacht", cls="",
    fonts=GF.format(FONT_BASE), hero=HERO_A, css=""))

# ===== 2 · Wirtshaus =====
THEMES.append(dict(
    slug="2-wirtshaus", name="Wirtshaus", cls="light paper",
    fonts=GF.format(FONT_BASE + "&family=Alegreya:ital,wght@0,500;0,700;0,800;1,500&family=Alegreya+Sans:wght@400;500;700"),
    css="""
#site{--fh:Alegreya,serif;--fb:"Alegreya Sans",sans-serif;--bg:#f4ecdc;--bg2:#eadcc2;--card:#fbf5ea;--ink:#2b1d14;--mute:#6e5a48;--red:#b3261e;--red2:#8e1c16;--navc:#2b1d14;--line:rgba(43,29,20,.18)}
#site h1,#site h2,#site h3{text-transform:none;letter-spacing:-.01em;font-weight:800}
#site .kicker{color:#8a5a2b;font-family:"Alegreya Sans";letter-spacing:.2em}
.btn{font-family:"Alegreya Sans" !important;border-radius:40px !important}
.nav .lg-h{display:none}.nav .lg-d{display:block}
.nav{background:rgba(244,236,220,.94);border-bottom:1px solid var(--line);position:sticky !important;top:0}
.nav a:hover,.nav a.cur{box-shadow:0 2px 0 var(--red)}
.wh-hero{padding:70px 0 90px;position:relative}
.wh-hero .wrap{display:grid;grid-template-columns:1fr 1.05fr;gap:56px;align-items:center}
.wh-hero h1{font-size:clamp(54px,7.5cqi,104px);line-height:.95;margin:14px 0 22px}
.wh-hero h1 em{font-family:var(--fx);font-style:normal;color:var(--red);display:block;font-size:.7em}
.wh-hero p{font-size:20px;color:var(--mute);max-width:500px;margin-bottom:30px}
.wh-hero .pic{position:relative}
.wh-hero .pic img{width:100%;display:block;border:12px solid #fff;box-shadow:0 30px 60px rgba(60,35,15,.25);transform:rotate(1.5deg)}
.wh-hero .stamp{position:absolute;left:-40px;bottom:-34px;width:150px;height:150px;border-radius:50%;background:var(--red);color:#fff;display:flex;align-items:center;justify-content:center;text-align:center;font:700 17px/1.1 Alegreya;transform:rotate(-12deg);box-shadow:0 10px 30px rgba(0,0,0,.25)}
.wh-hero .badge{display:flex;gap:26px;margin-top:34px;color:var(--mute);font-size:15px}
.wh-hero .badge b{display:block;font:800 34px/1 Alegreya;color:var(--ink)}
.strip{background:#2b1d14;font-family:Alegreya !important;text-transform:none !important;font-style:italic}
.offer{border-radius:6px}
.phead{padding-top:120px}
.phead h1{text-transform:none}
.cta-band{background:#2b1d14}
.photo-frame::before{border-color:#8a5a2b}
@container (max-width:760px){.wh-hero .wrap{grid-template-columns:1fr;gap:50px}.wh-hero .stamp{left:auto;right:-6px;width:110px;height:110px;font-size:14px}.wh-hero{padding:40px 0 60px}}
""",
    hero="""  <header class="wh-hero"><div class="wrap">
    <div>
      <div class="kicker">Party- &amp; Hochzeitsband · Oberpfalz</div>
      <h1><em>Mia san die</em>Sturschädl</h1>
      <p>Bayrisch-rockige Musi für Kirwa, Bierzelt und Hochzeit. Mit Quetschn, E-Gitarre und am Schuss bayerischer Lässigkeit – seit 2015.</p>
      <div class="cta" style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn red" data-go="kontakt">Termin anfragen</a><a class="btn ghost" data-go="ueber">Wer mia san</a></div>
      <div class="badge"><div><b>2015</b>gegründet</div><div><b>5</b>Musikanten</div><div><b>100 %</b>Gaudi</div></div>
    </div>
    <div class="pic"><img src="assets/band-wirtshaus.jpg" alt="Die Band im Wirtshaus"><div class="stamp">Bayrisch<br>&amp; Rockig<br>seit 2015</div></div>
  </div></header>
"""))

# ===== 3 · Rock'n'Raute =====
THEMES.append(dict(
    slug="3-rock-n-raute", name="Rock’n’Raute", cls="",
    fonts=GF.format(FONT_BASE + "&family=Anton&family=Archivo:wght@400;500;700"),
    css="""
#site{--fh:Anton,sans-serif;--fb:Archivo,sans-serif;--bg:#0a0c10;--bg2:#121721;--card:#151b26;--ink:#f5f7fa;--mute:#9aa6b8;--red:#1d7fe0;--red2:#1566b8;--line:rgba(255,255,255,.12)}
#site h1,#site h2,#site h3{font-weight:400;letter-spacing:.005em}
.raute{background-color:#fff;background-image:linear-gradient(135deg,#1d7fe0 25%,transparent 25%),linear-gradient(225deg,#1d7fe0 25%,transparent 25%),linear-gradient(45deg,#1d7fe0 25%,transparent 25%),linear-gradient(315deg,#1d7fe0 25%,#fff 25%);background-size:44px 44px;background-position:22px 0,22px 0,0 0,0 0}
.rr-hero{position:relative;min-height:860px;overflow:hidden;color:#fff;background:#0a0c10}
.rr-hero .ph{position:absolute;inset:0 0 0 36%;background:url(assets/band-wirtshaus.jpg) 80% 30%/cover;clip-path:polygon(14% 0,100% 0,100% 100%,0 100%);filter:grayscale(1) contrast(1.25) brightness(.9)}
.rr-hero .ph::before{content:"";position:absolute;inset:0;background:#1d7fe0;mix-blend-mode:multiply;opacity:.55}
.rr-hero .ph::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,#0a0c10 0%,rgba(10,12,16,0) 22%)}
.rr-hero .band{position:absolute;left:-5%;right:-5%;bottom:120px;height:64px;transform:rotate(-4deg);box-shadow:0 10px 40px rgba(0,0,0,.5)}
.rr-hero .wrap{position:relative;padding-top:200px}
.rr-hero h1{font-size:clamp(64px,8.6cqi,124px);line-height:.88}
.rr-hero h1 span{display:block;-webkit-text-stroke:2px #fff;color:transparent}
.rr-hero p{max-width:470px;font-size:19px;color:#c9d2df;margin:26px 0 30px}
.rr-hero .bull{position:absolute;right:32px;top:130px;width:170px;filter:drop-shadow(0 10px 30px rgba(0,0,0,.6))}
.strip{background:#fff;color:#0a0c10;font-family:Anton !important;font-weight:400 !important}
.strip em{color:#1d7fe0;opacity:1 !important}
.btn{transform:skewX(-8deg)}
.offer h3{font-size:46px}
.facts b{color:#1d7fe0}
.gig .d span{color:#1d7fe0}
.cta-band{background:#1d7fe0}.cta-band .bg{opacity:.12}
.cta-band .btn.red{background:#fff;color:#0a0c10}
.photo-frame::before{border-color:#1d7fe0}
.kicker{color:#5aa8f5 !important}
@container (max-width:760px){.rr-hero{min-height:0;padding-bottom:110px}.rr-hero .ph{inset:0 0 auto 0;aspect-ratio:2048/1678;clip-path:polygon(0 0,100% 0,100% 88%,0 100%);background-position:center;filter:grayscale(1) contrast(1.2)}.rr-hero .ph::after{background:linear-gradient(180deg,rgba(10,12,16,.55) 0%,transparent 30%)}.rr-hero .bull{display:none}.rr-hero .wrap{padding-top:calc(82cqi + 10px)}.rr-hero .band{bottom:34px;height:40px}}
""",
    hero="""  <header class="rr-hero">
    <div class="ph"></div>
    <div class="band raute"></div>
    <img class="bull" src="assets/bulle-hell.png" alt="">
    <div class="wrap">
      <div class="kicker">Bayrisch · Rockig · Laut</div>
      <h1>Sturschädl<span>Live!</span></h1>
      <p>Quetschn trifft Verstärker: Party-Hymnen, Rock-Klassiker und Bierzelt-Kracher – für Kirwa, Feuerwehrfest und Hochzeit.</p>
      <div style="display:flex;gap:14px;flex-wrap:wrap"><a class="btn red" data-go="kontakt">Band buchen →</a><a class="btn ghost" data-go="medien">Videos ansehen</a></div>
    </div>
  </header>
"""))

# ===== 4 · Festplakat =====
THEMES.append(dict(
    slug="4-festplakat", name="Festplakat", cls="light",
    fonts=GF.format(FONT_BASE + "&family=Alfa+Slab+One&family=Oswald:wght@500;600;700&family=Courier+Prime:wght@400;700"),
    css="""
#site{--fh:Oswald,sans-serif;--fb:"Courier Prime",monospace;--bg:#e6d3ac;--bg2:#dcc597;--card:#efe1c2;--ink:#1b1410;--mute:#5a4630;--red:#c4271f;--red2:#9e1e18;--navc:#1b1410;--line:rgba(27,20,16,.3);
 background-image:radial-gradient(rgba(80,50,20,.10) 1px,transparent 1.4px),radial-gradient(rgba(255,255,255,.25) 1px,transparent 1.2px);background-size:5px 5px,7px 7px;font-size:16px}
#site h1,#site h2{font-family:"Alfa Slab One",serif;font-weight:400}
.nav .lg-h{display:none}.nav .lg-d{display:block}
.nav{border-bottom:3px double var(--ink)}
.pl-hero{padding:130px 0 70px;text-align:center}
.poster{max-width:900px;margin:0 auto;border:6px solid var(--ink);outline:2px solid var(--ink);outline-offset:6px;padding:46px 40px 40px;background:rgba(255,248,230,.35);position:relative}
.poster .top{font:700 18px/1 Oswald;letter-spacing:.4em;text-transform:uppercase}
.poster .stars{color:var(--red);letter-spacing:.6em;margin:14px 0}
.poster h1{font-size:clamp(56px,11cqi,150px);line-height:.88;color:var(--red);text-shadow:4px 4px 0 var(--ink)}
.poster .sub{font:700 clamp(20px,3cqi,34px)/1.1 Oswald;letter-spacing:.18em;text-transform:uppercase;margin:16px 0 26px;border-top:3px solid var(--ink);border-bottom:3px solid var(--ink);padding:10px 0}
.poster img.ph{width:100%;display:block;filter:grayscale(1) contrast(1.25) sepia(.35);mix-blend-mode:multiply;border:3px solid var(--ink)}
.poster .bull{position:absolute;width:120px;right:-50px;top:-50px;transform:rotate(14deg)}
.poster .bottom{display:grid;grid-template-columns:1fr 1fr 1fr;margin-top:22px;font:600 15px/1.3 Oswald;text-transform:uppercase;letter-spacing:.1em}
.poster .bottom div{border-right:2px solid var(--ink);padding:6px}.poster .bottom div:last-child{border:0}
.poster .bottom b{display:block;font-size:26px;color:var(--red)}
.btn{border-radius:0 !important;font-family:Oswald !important;box-shadow:4px 4px 0 var(--ink)}
.btn.ghost{background:transparent}
.strip{background:var(--ink);color:#e6d3ac;font-family:"Alfa Slab One" !important;font-weight:400 !important}
.offer{border:3px solid var(--ink);box-shadow:8px 8px 0 var(--ink)}
.offer img{filter:sepia(.3) contrast(1.1)}
.offer h3{font-family:"Alfa Slab One";font-weight:400}
.facts{border-top:3px double var(--ink);border-bottom:3px double var(--ink)}
.facts b{font-family:"Alfa Slab One" !important;color:var(--red)}
.gig .d{border:3px solid var(--ink);background:var(--card)}
.phead{background:var(--ink)}.phead .bg{filter:grayscale(1) sepia(.4)}
.phead h1{color:#e6d3ac}
.photo-frame img{filter:sepia(.25);border:3px solid var(--ink)}
.photo-frame::before{border:3px solid var(--red)}
.cta-band{background:var(--red)}.cta-band .btn.red{background:var(--ink)}
.insta img,.gal img{filter:sepia(.2) contrast(1.05);border:2px solid var(--ink)}
form.f,.side .box,.member{border:3px solid var(--ink) !important;box-shadow:6px 6px 0 var(--ink)}
@container (max-width:760px){.poster{padding:28px 18px}.poster .bull{width:80px;right:-12px;top:-40px}.poster .bottom{grid-template-columns:1fr}.poster .bottom div{border-right:0;border-bottom:2px solid var(--ink)}.pl-hero{padding:100px 0 50px}}
""",
    hero="""  <header class="pl-hero"><div class="wrap"><div class="poster">
    <img class="bull" src="assets/bulle-dunkel.png" alt="">
    <div class="top">Achtung · Live-Musik</div>
    <div class="stars">★ ★ ★ ★ ★</div>
    <h1>Die Stur&shy;schädl</h1>
    <div class="sub">Bayrisch-rockige Dorfmusi</div>
    <img class="ph" src="assets/band-wald.jpg" alt="Die Band">
    <div class="bottom"><div><b>Kirwa</b>&amp; Bierzelt</div><div><b>Hochzeit</b>&amp; Feier</div><div><b>Seit 2015</b>aus Deining</div></div>
    <div style="margin-top:28px;display:flex;gap:14px;justify-content:center;flex-wrap:wrap"><a class="btn red" data-go="kontakt">Jetzt buchen</a><a class="btn ghost" data-go="termine">Termine</a></div>
  </div></div></header>
"""))

# ===== 5 · Hochzeit Elegant =====
THEMES.append(dict(
    slug="5-hochzeit-elegant", name="Hochzeit Elegant", cls="light",
    fonts=GF.format(FONT_BASE + "&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Jost:wght@300;400;500;600"),
    css="""
#site{--fh:"Cormorant Garamond",serif;--fb:Jost,sans-serif;--bg:#faf7f1;--bg2:#eef0e8;--card:#fff;--ink:#22302a;--mute:#66736c;--red:#2f5a46;--red2:#244636;--gold:#b8955a;--navc:#22302a;--line:rgba(34,48,42,.14);font-weight:300}
#site h1,#site h2,#site h3{text-transform:none;font-weight:600;letter-spacing:0;line-height:1}
#site .kicker{color:var(--gold);font-family:Jost;font-weight:500;letter-spacing:.3em}
.btn{font-family:Jost !important;font-weight:500 !important;font-size:14px !important;letter-spacing:.18em !important;border-radius:999px !important;padding:16px 30px !important}
.nav .lg-h{display:none}.nav .lg-d{display:block}
.nav .brand span{font-family:"Cormorant Garamond";font-weight:700}
.nav ul{font-family:Jost;font-weight:400;font-size:13px}
.el-hero{padding:130px 0 80px;text-align:center}
.el-hero h1{font-size:clamp(54px,8cqi,112px);margin:18px 0 10px}
.el-hero h1 i{font-weight:500;color:var(--red)}
.el-hero p{max-width:600px;margin:0 auto 34px;color:var(--mute);font-size:19px}
.arch{max-width:980px;margin:60px auto 0;display:grid;grid-template-columns:1fr 1.4fr 1fr;gap:20px;align-items:end}
.arch img{width:100%;display:block;object-fit:cover}
.arch .m{aspect-ratio:4/5;border-radius:999px 999px 0 0}
.arch .s{aspect-ratio:3/4;border-radius:999px 999px 0 0;opacity:.95}
.el-hero .sig{font-family:var(--fx);font-size:30px;color:var(--gold);margin-top:30px}
.strip{background:var(--red);font-family:"Cormorant Garamond" !important;font-style:italic;text-transform:none !important;font-weight:600 !important}
.facts b{font-family:"Cormorant Garamond" !important;font-weight:600;color:var(--red)}
.offer{border-radius:200px 200px 0 0}
.offer h3{font-weight:600}
.phead{background:var(--red)}
.phead h1{text-transform:none}
.photo-frame img{border-radius:300px 300px 0 0}
.photo-frame::before{border:1px solid var(--gold);border-radius:300px 300px 0 0}
.cta-band{background:#22302a}
.member .ph{border-radius:200px 200px 0 0}
.flow div::before{border-top-color:var(--gold);color:var(--gold)}
.checks li::before{background:var(--gold)}
@container (max-width:760px){.arch{grid-template-columns:1fr 1fr}.arch .m{grid-column:1/-1;order:-1}.el-hero{padding:110px 0 60px}}
""",
    hero="""  <header class="el-hero"><div class="wrap">
    <div class="kicker">Hochzeits- &amp; Partyband aus Bayern</div>
    <h1>Euer schönster Tag –<br><i>mit Herz &amp; Lederhosn</i></h1>
    <p>Vom Sektempfang über die Brautentführung bis zum letzten Tanz: Mia wissen, wann’s romantisch sei muss – und wann die Tanzfläche brennt.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap"><a class="btn red" data-go="kontakt">Hochzeit anfragen</a><a class="btn ghost" data-go="hochzeit">So läuft’s ab</a></div>
    <div class="arch"><img class="s" src="assets/ig-7.jpg" alt=""><img class="m" src="assets/band-wald.jpg" alt=""><img class="s" src="assets/ig-2.jpg" alt=""></div>
    <div class="sig">Eure Sturschädl</div>
  </div></header>
"""))

INFO = {
    "1-bierzelt-nacht": "Dunkel, Rot, Bühnenlicht. Wirkt wie ein Konzertabend im Festzelt.",
    "2-wirtshaus": "Hell und warm, Serifenschrift, Wirtshaus-Gefühl. Bodenständig und einladend.",
    "3-rock-n-raute": "Schwarz mit bayerischer Raute in Blau-Weiß. Die rockigste Variante.",
    "4-festplakat": "Wie ein altes Veranstaltungsplakat: Packpapier, Stempel-Rot, Schreibmaschine.",
    "5-hochzeit-elegant": "Ruhig, edel, Grün und Gold. Spricht vor allem Brautpaare an.",
}
cards = []
for i, t in enumerate(THEMES, 1):
    cards.append(f'<a class="card" href="sturschaedl-entwurf-{t["slug"]}.html"><img src="data:image/jpeg;base64,{base64.b64encode((OUT / "vorschau" / (t["slug"] + ".jpg")).read_bytes()).decode()}" alt="Vorschau Entwurf {i}"><div><small>Entwurf {i}</small><b>{t["name"]}</b><p>{INFO[t["slug"]]}</p></div></a>')
idx = (HIER / "index-vorlage.html").read_text(encoding="utf-8")
idx = idx.replace("{{CARDS}}", "\n".join(cards)).replace("{{DATUM}}", "06.11.2026").replace("{{BULLE}}", data_uri("bulle-hell.png"))
(OUT / "index.html").write_text(idx, encoding="utf-8")

for i, t in enumerate(THEMES, 1):
    html = (base.replace("{{FONTS}}", t["fonts"]).replace("{{THEME_CSS}}", t["css"])
            .replace("{{CLS}}", t["cls"]).replace("{{HERO}}", t["hero"])
            .replace("{{NR}}", str(i)).replace("{{NAME}}", t["name"]))
    assert "{{" not in html
    html = embed(html)
    f = OUT / f"sturschaedl-entwurf-{t['slug']}.html"
    f.write_text(html, encoding="utf-8")
    print(f.name, round(f.stat().st_size / 1024), "KB")
