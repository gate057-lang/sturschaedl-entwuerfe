"""Baut das Wix-Paket: wix/sturschaedl-wix-paket.zip + wix/anleitung.html (für den Online-Link).
Aufruf: python3 -P wix/werkzeuge/paket-bauen.py"""
import base64, io, pathlib, shutil, subprocess, zipfile
from PIL import Image
W = pathlib.Path(__file__).resolve().parents[1]
REPO = W.parent
THEME = REPO / "wordpress" / "sturschaedl" / "assets"
Q = W / "quelle"
subprocess.run(["python3", str(W / "werkzeuge" / "laufband-bauen.py")], check=True)

DESIGNS = [
    ("1-bierzelt-nacht", "Bierzelt-Nacht", "Dunkel, Rot, Bühnenlicht.",
     dict(Grund="#0f0d0c", **{"Grund 2": "#1a1715", "Kasten": "#221e1b", "Schrift": "#f4eee2", "Leise": "#b3aa9c", "Akzent": "#e3262b", "Akzent dunkel": "#b81c20"}),
     "Überschrift: Barlow Condensed 800, GROSS · Text: Barlow 400 · Zier: Pirata One",
     ["barlow-400", "barlow-500", "barlow-600", "barlow-condensed-500", "barlow-condensed-700", "barlow-condensed-800", "pirata-one-400"]),
    ("2-wirtshaus", "Wirtshaus", "Hell und warm, Serifen, runde Knöpfe.",
     dict(Grund="#f4ecdc", **{"Grund 2": "#eadcc2", "Kasten": "#fbf5ea", "Schrift": "#2b1d14", "Leise": "#6e5a48", "Akzent": "#b3261e", "Akzent dunkel": "#8e1c16", "Kicker": "#8a5a2b"}),
     "Überschrift: Alegreya 800 (normal geschrieben) · Text: Alegreya Sans · Zier: Pirata One · Laufband: Alegreya kursiv",
     ["alegreya-800", "alegreya-500-italic", "alegreya-sans-400", "alegreya-sans-500", "alegreya-sans-700", "pirata-one-400"]),
    ("3-rock-n-raute", "Rock’n’Raute", "Schwarz, Blau-Weiß, die rockigste Variante.",
     dict(Grund="#0a0c10", **{"Grund 2": "#121721", "Kasten": "#151b26", "Schrift": "#f5f7fa", "Leise": "#9aa6b8", "Akzent": "#1d7fe0", "Akzent dunkel": "#1566b8", "Kicker": "#5aa8f5"}),
     "Überschrift: Anton, GROSS · Text: Archivo · Zier: Pirata One",
     ["anton-400", "archivo-400", "pirata-one-400"]),
    ("4-festplakat", "Festplakat", "Packpapier, Stempel-Rot, Schreibmaschine.",
     dict(Grund="#e6d3ac", **{"Grund 2": "#dcc597", "Kasten": "#efe1c2", "Schrift": "#1b1410", "Leise": "#5a4630", "Akzent": "#c4271f", "Akzent dunkel": "#9e1e18"}),
     "Titel: Alfa Slab One · Überschrift klein/Knöpfe: Oswald 600, GROSS · Text: Courier Prime",
     ["alfa-slab-one-400", "oswald-500", "courier-prime-400", "courier-prime-700"]),
    ("5-hochzeit-elegant", "Hochzeit Elegant", "Ruhig, edel, Grün und Gold.",
     dict(Grund="#faf7f1", **{"Grund 2": "#eef0e8", "Kasten": "#ffffff", "Schrift": "#22302a", "Leise": "#66736c", "Akzent": "#2f5a46", "Akzent dunkel": "#244636", "Gold": "#b8955a"}),
     "Überschrift: Cormorant Garamond 600 (normal geschrieben) · Text: Jost 300/400 · Zier: Pirata One",
     ["cormorant-garamond-600", "cormorant-garamond-500-italic", "jost-400", "pirata-one-400"]),
]

def b64(p): return base64.b64encode(p.read_bytes()).decode()
def vorschau(slug):
    im = Image.open(REPO / "vorschau" / f"{slug}.jpg").convert("RGB"); im.thumbnail((640, 640))
    buf = io.BytesIO(); im.save(buf, "JPEG", quality=78, optimize=True); return base64.b64encode(buf.getvalue()).decode()

karten = []
for i, (slug, name, info, farben, schrift, _) in enumerate(DESIGNS, 1):
    sw = "".join(f'<span><i style="background:{v}"></i>{k}<br>{v}</span>' for k, v in farben.items())
    karten.append(f'<div class="design"><img src="data:image/jpeg;base64,{vorschau(slug)}" alt="Vorschau {name}"><div class="t"><small>Design {i} · Ordner schriften/{slug}</small><b>{name}</b><p style="margin:4px 0 0;color:var(--mute)">{info}</p><div class="sw">{sw}</div><div class="fonts">{schrift}</div></div></div>')

F = THEME / "fonts"
schrift_css = "\n".join(
    f"@font-face{{font-family:'{fam}';font-weight:{w};font-display:swap;src:url(data:font/woff2;base64,{b64(F / f)}) format('woff2')}}"
    for fam, w, f in [("Barlow", 400, "barlow-400.woff2"), ("Barlow", 600, "barlow-600.woff2"), ("Barlow Condensed", 800, "barlow-condensed-800.woff2"), ("Barlow Condensed", 600, "barlow-condensed-600.woff2"), ("Pirata One", 400, "pirata-one-400.woff2")])
html = (Q / "anleitung-vorlage.html").read_text(encoding="utf-8")
html = (html.replace("{{SCHRIFT_CSS}}", schrift_css).replace("{{DESIGN_KARTEN}}", "\n".join(karten))
        .replace("{{BULLE}}", "data:image/png;base64," + b64(THEME / "img" / "bulle-hell.png")).replace("{{DATUM}}", "06.11.2026"))
assert "{{" not in html
(W / "anleitung.html").write_text(html, encoding="utf-8")

# Paketordner
P = W / "_paket" / "sturschaedl-wix-paket"
shutil.rmtree(W / "_paket", ignore_errors=True)
P.mkdir(parents=True)
shutil.copy(W / "anleitung.html", P / "ANLEITUNG.html")
shutil.copy(REPO / "anleitungen" / "Sturschaedl-Anleitung-Wix.pdf", P / "Anleitung-Wix.pdf")
(P / "LIESMICH.txt").write_text("""Sturschädl-Website – Paket für WIX
==================================

Inhalt:
  Anleitung-Wix.pdf   Schritt-für-Schritt-Anleitung (bitte zuerst lesen)
  ANLEITUNG.html      Ausführliche Bauanleitung mit allen Maßen (im Browser öffnen)
  cms/                Termine, Laufband, Mitglieder zum Importieren ins Wix-CMS
  velo/               Fertiger Code zum Kopieren in Wix (Dev Mode)
  schriften/          Schriften, ein Ordner pro Design
  bilder/             Logos und Fotos
  vorschau/           Die fünf Designs als Bild

Dieses Paket ist nur für Wix. Für WordPress gibt es ein eigenes Paket
(sturschaedl-wordpress-paket.zip).
""", encoding="utf-8")
shutil.copytree(Q / "cms", P / "cms")
shutil.copytree(Q / "velo", P / "velo")
for slug, *_rest in DESIGNS:
    d = P / "schriften" / slug; d.mkdir(parents=True)
    for f in _rest[-1]:
        shutil.copy(F / f"{f}.woff2", d / f"{f}.woff2")
(P / "bilder").mkdir()
for f in sorted((THEME / "img").iterdir()):
    if f.suffix in (".jpg", ".png") and f.name != "beton.jpg":
        shutil.copy(f, P / "bilder" / f.name)
shutil.copytree(REPO / "vorschau", P / "vorschau")
ziel = W / "sturschaedl-wix-paket.zip"
ziel.unlink(missing_ok=True)
with zipfile.ZipFile(ziel, "w", zipfile.ZIP_DEFLATED) as z:
    for f in sorted(P.rglob("*")):
        if f.is_file() and f.name != ".DS_Store":
            z.write(f, f.relative_to(P.parent))
shutil.rmtree(W / "_paket")
print(ziel.name, round(ziel.stat().st_size / 1024), "KB ·", "anleitung.html", round((W / "anleitung.html").stat().st_size / 1024), "KB")
