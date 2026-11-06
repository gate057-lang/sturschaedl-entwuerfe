"""Erzeugt die Schritt-für-Schritt-PDFs (Chrome „Als PDF drucken“) und prüft sie automatisch.
Aufruf: python3 -P anleitungen/werkzeuge/pdf-bauen.py [wordpress|wix]  → anleitungen/*.pdf + Seitenbilder in anleitungen/_pruefung/"""
import re, subprocess, sys, pathlib, shutil
import pymupdf
A = pathlib.Path(__file__).resolve().parents[1]
CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
DOKS = {"wordpress": "Sturschaedl-Anleitung-WordPress.pdf", "wix": "Sturschaedl-Anleitung-Wix.pdf"}
VERBOTEN = [r"\bdumm", r"\bdummies\b", r"\bfuer\b", r"\bueber\b", r"\bkoennen\b", r"\bmuessen\b", r"\bwaehlen\b", r"\boeffnen\b", r"\bgroesse\b", r"\bschaltflaeche\b", r"\bMenue\b", r"\bzurueck\b", r"\bloeschen\b", r"\bpruefen\b", r"\bfuegen\b", r"\bStrasse\b", r"\bheisst\b", "�"]
MM = 72 / 25.4

def bauen(name):
    src = A / "quelle" / f"{name}.html"
    pdf = A / DOKS[name]
    subprocess.run([CHROME, "--headless=new", "--disable-gpu", "--allow-file-access-from-files", "--no-pdf-header-footer",
                    "--virtual-time-budget=8000", f"--print-to-pdf={pdf}", src.as_uri()], check=True, capture_output=True)
    return pdf

def pruefen(pdf, bilder_dir):
    doc = pymupdf.open(pdf)
    befunde = []
    W, H = doc[0].rect.width, doc[0].rect.height
    for i, page in enumerate(doc, 1):
        text = page.get_text()
        for muster in VERBOTEN:
            for m in re.finditer(muster, text, re.I):
                befunde.append(f"S.{i}: verbotenes Wort/Zeichen „{m.group(0)}“")
        d = page.get_text("dict")
        spans = [s for b in d["blocks"] if b["type"] == 0 for l in b["lines"] for s in l["spans"] if s["text"].strip()]
        imgs = [b for b in d["blocks"] if b["type"] == 1]
        if i > 1:
            for s in spans:
                x0, y0, x1, y1 = s["bbox"]
                if x1 > W - 10 * MM or x0 < 10 * MM:
                    befunde.append(f"S.{i}: Text am/über Rand: „{s['text'][:40]}“")
        # Überschrift allein am Seitenende (große Schrift im unteren Bereich ohne Folgetext)
        if spans:
            unten = [s for s in spans if s["bbox"][1] > H - 40 * MM and s["bbox"][1] < H - 14 * MM]
            for s in unten:
                if s["size"] >= 13.5:
                    darunter = [t for t in spans if t["bbox"][1] > s["bbox"][3] + 1 and t["bbox"][1] < H - 14 * MM]
                    if len(darunter) < 2:
                        befunde.append(f"S.{i}: Überschrift allein am Seitenende: „{s['text'][:40]}“")
        # Teil-Überschrift (groß) im unteren Seitendrittel = Inhalt beginnt erst auf der nächsten Seite
        for s_ in spans:
            folgt = [t for t in spans if t["bbox"][1] > s_["bbox"][3] + 2 and t["bbox"][1] < H - 14 * MM]
            if s_["size"] >= 20 and s_["bbox"][1] > H * 0.68 and len(folgt) < 5:
                befunde.append(f"S.{i}: Teil-Überschrift zu weit unten: „{s_['text'][:40]}“")
        # Fast leere Seite
        inhalt = sum(len(s["text"]) for s in spans if s["bbox"][1] < H - 14 * MM)
        if inhalt < 120 and not imgs:
            befunde.append(f"S.{i}: fast leere Seite ({inhalt} Zeichen)")
        # Unterer Weißraum (große Lücke) – nur Hinweis
        letzte = max([s["bbox"][3] for s in spans if s["bbox"][1] < H - 14 * MM] + [b["bbox"][3] for b in imgs] + [0])
        if i > 1 and letzte < H * 0.45 and i < len(doc):
            befunde.append(f"S.{i}: Seite nur bis {round(letzte / H * 100)} % gefüllt (Hinweis)")
        pix = page.get_pixmap(dpi=60)
        pix.save(bilder_dir / f"{pdf.stem}-s{i:02d}.png")
    # Bilder geladen? (Chrome zeigt bei fehlenden Bildern nichts – Zahl vergleichen)
    return len(doc), befunde

if __name__ == "__main__":
    namen = sys.argv[1:] or list(DOKS)
    pr = A / "_pruefung"
    shutil.rmtree(pr, ignore_errors=True); pr.mkdir()
    fehler = 0
    for n in namen:
        src = (A / "quelle" / f"{n}.html").read_text(encoding="utf-8")
        for bild in re.findall(r'src="([^"]+)"', src):
            if not (A / "quelle" / bild).resolve().exists():
                print(f"FEHLT Bild: {bild}"); fehler += 1
        pdf = bauen(n)
        seiten, bef = pruefen(pdf, pr)
        print(f"{pdf.name}: {seiten} Seiten, {round(pdf.stat().st_size / 1024)} KB")
        for b in bef:
            print("  ", b)
        fehler += sum(1 for b in bef if "Hinweis" not in b)
    print("Prüfung:", "bestanden" if not fehler else f"{fehler} Befund(e)")
    sys.exit(1 if fehler else 0)
