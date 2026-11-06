"""Erzeugt aus den Mockup-Quellen (quelle/) die CSS-Dateien des WordPress-Themes:
style.css-Grundteil (zwischen den Markern) und assets/designs/<slug>.css je Entwurf.
Aufruf: python3 wordpress/werkzeuge/css-erzeugen.py"""
import re, pathlib
REPO = pathlib.Path(__file__).resolve().parents[2]
Q = REPO / "quelle"
T = REPO / "wordpress" / "sturschaedl"
tpl = (Q / "sturschaedl-mockups.html").read_text(encoding="utf-8")
css = re.search(r'/\* ---------- Design-Tokens ---------- \*/(.*?)</style>', tpl, re.S).group(1)

# gleiche Verallgemeinerungen wie build.py
css = css.replace('"Barlow Condensed"', "var(--fh)")
css = css.replace('font-family:"Pirata One",serif', "font-family:var(--fx)").replace('font:400 30px/1 "Pirata One"', "font:400 30px/1 var(--fx)")
css = css.replace("background:var(--bg);color:var(--ink);font:400 17px/1.6 Barlow,sans-serif;overflow-x:hidden}",
    "background:var(--bg);color:var(--ink);font:400 17px/1.6 var(--fb);--fh:\"Barlow Condensed\",sans-serif;--fb:Barlow,sans-serif;--fx:\"Pirata One\",serif}")
css = css.replace(".nav{position:absolute;inset:0 0 auto 0;z-index:10;color:#f4eee2}", ".nav{position:absolute;inset:0 0 auto 0;z-index:10;color:var(--navc,#f4eee2)}\n.lg-d{display:none}")
css = css.replace(".nav a:hover,.nav a.cur{opacity:1;color:#fff;", ".nav a:hover,.nav a.cur{opacity:1;")
css = css.replace('.page{display:none}.page.active{display:block}', '')

def wp(c, img):
    c = c.replace('#site.light .on-bg', 'body.light .on-bg').replace('#site.light', 'body.light')
    c = c.replace('#site:not(.light)', 'body:not(.light)').replace('#site{container-type:inline-size;', 'body{').replace('#site', 'body')
    c = c.replace('cqi', 'vw').replace('@container (max-width:760px)', '@media (max-width:760px)')
    c = c.replace('url(assets/', 'url(' + img)
    return alias(c)

ALIAS = [(".photo-frame", ".st-content .is-style-rahmen"), (".flow div", ".st-content .st-flow .st-schritt"),
         (".checks li", ".st-content ul.is-style-haken li")]
def alias(c):
    """Regeln für Mockup-Elemente auch auf die gleichwertigen Block-Editor-Elemente anwenden."""
    def fix(m):
        sels = m.group(1).split(",")
        extra = []
        for sel in sels:
            for alt, neu in ALIAS:
                if alt in sel:
                    extra.append(sel.replace(alt, neu))
        return ",".join(sels + extra) + "{"
    return re.sub(r"(?m)(?<=[}\n])([^{}@\n][^{}]*?)\{", fix, c)

base = wp(css, 'assets/img/')
st = (T / "style.css").read_text(encoding="utf-8")
A, B = "/* >>> GRUNDSTIL (erzeugt) */", "/* <<< GRUNDSTIL */"
if A in st:
    st = st[:st.index(A)] + A + base + B + st[st.index(B) + len(B):]
else:  # Erstlauf: alten Block zwischen Kopf und WordPress-Ergänzungen ersetzen
    i = st.index("/* ---------- Design-Tokens ---------- */") if "Design-Tokens" in st else st.index("body{")
    j = st.index("/* ---------- WordPress-Ergänzungen")
    st = st[:i] + A + base + B + "\n\n" + st[j:]
(T / "style.css").write_text(st, encoding="utf-8")

src = (Q / "build.py").read_text(encoding="utf-8")
part = src[src.index("GF = "):src.index("INFO = {")]
ns = {"HERO_A": ""}
exec(part, ns)
d = T / "assets" / "designs"; d.mkdir(parents=True, exist_ok=True)
for t in ns["THEMES"]:
    (d / (t["slug"] + ".css")).write_text(f"/* Entwurf {t['name']} – erzeugt aus quelle/build.py */\n" + wp(t["css"], '../img/').strip() + "\n", encoding="utf-8")
    fam = re.findall(r'family=([^&"]+)', t["fonts"])
    print(t["slug"], repr(t["cls"]), fam)
