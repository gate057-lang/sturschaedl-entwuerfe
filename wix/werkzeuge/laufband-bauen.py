"""Baut public/custom-elements/sturschaedl-laufband.js mit eingebetteten Schriften (aus dem WordPress-Theme)."""
import base64, json, pathlib
W = pathlib.Path(__file__).resolve().parents[1]
F = W.parent / "wordpress" / "sturschaedl" / "assets" / "fonts"
S = {
    "StBarlowCond": ("barlow-condensed-800.woff2", "normal", 800),
    "StAlegreya": ("alegreya-500-italic.woff2", "italic", "400 900"),
    "StAnton": ("anton-400.woff2", "normal", 400),
    "StAlfaSlab": ("alfa-slab-one-400.woff2", "normal", 400),
    "StCormorant": ("cormorant-garamond-500-italic.woff2", "italic", "300 700"),
}
schriften = {k: {"style": st, "weight": wt, "data": base64.b64encode((F / f).read_bytes()).decode()} for k, (f, st, wt) in S.items()}
js = (W / "quelle" / "laufband-vorlage.js").read_text(encoding="utf-8").replace("{{SCHRIFTEN}}", json.dumps(schriften))
ziel = W / "quelle" / "velo" / "public" / "custom-elements" / "sturschaedl-laufband.js"
ziel.write_text(js, encoding="utf-8")
print(ziel.name, round(len(js) / 1024), "KB")
