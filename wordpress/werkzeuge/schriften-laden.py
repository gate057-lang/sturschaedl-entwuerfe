"""Lädt die Google-Schriften der Entwürfe einmalig herunter (nur latin) und legt sie lokal ab.
Kein Google-Aufruf beim Besucher (DSGVO). Schreibt assets/fonts/*.woff2 und assets/designs/<slug>-fonts.css."""
import re, urllib.request, pathlib
T = pathlib.Path(__file__).resolve().parents[1] / "sturschaedl" / "assets"
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130 Safari/537.36"
SETS = {
  "2-wirtshaus": "family=Alegreya:ital,wght@0,500;0,700;0,800;1,500&family=Alegreya+Sans:wght@400;500;700",
  "3-rock-n-raute": "family=Anton&family=Archivo:wght@400;500;700",
  "4-festplakat": "family=Alfa+Slab+One&family=Oswald:wght@500;600;700&family=Courier+Prime:wght@400;700",
  "5-hochzeit-elegant": "family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Jost:wght@300;400;500;600",
}
for slug, q in SETS.items():
    req = urllib.request.Request(f"https://fonts.googleapis.com/css2?{q}&display=swap", headers={"User-Agent": UA})
    css = urllib.request.urlopen(req).read().decode()
    out = []
    for sub, body in re.findall(r'/\* (\S+) \*/\s*@font-face \{(.*?)\}', css, re.S):
        if sub != "latin": continue
        fam = re.search(r"font-family: '([^']+)'", body).group(1)
        sty = re.search(r"font-style: (\w+)", body).group(1)
        w = re.search(r"font-weight: ([\d ]+);", body).group(1)
        url = re.search(r"url\((\S+?)\)", body).group(1)
        rng = re.search(r"unicode-range: ([^;]+);", body).group(1)
        fn = f"{fam.lower().replace(' ', '-')}-{w.replace(' ', '-')}{'-italic' if sty == 'italic' else ''}.woff2"
        if not (T / "fonts" / fn).exists():
            urllib.request.urlretrieve(url, T / "fonts" / fn)
        out.append(f"@font-face{{font-family:'{fam}';font-style:{sty};font-weight:{w};font-display:swap;src:url(../fonts/{fn}) format('woff2');unicode-range:{rng}}}")
    (T / "designs" / f"{slug}-fonts.css").write_text("\n".join(out) + "\n", encoding="utf-8")
    print(slug, len(out))
