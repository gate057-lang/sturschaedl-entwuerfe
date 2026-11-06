"""Legt die Seitenbilder einer PDF als Kontaktbogen nebeneinander (für die Sichtprüfung)."""
import sys, pathlib, pymupdf
from PIL import Image
pdf = pathlib.Path(sys.argv[1]); von, bis = int(sys.argv[2]), int(sys.argv[3]); ziel = sys.argv[4]
doc = pymupdf.open(pdf); sp = 4
bilder = []
for i in range(von - 1, min(bis, len(doc))):
    pix = doc[i].get_pixmap(dpi=int(sys.argv[5]) if len(sys.argv) > 5 else 50)
    bilder.append(Image.frombytes("RGB", (pix.width, pix.height), pix.samples))
w, h = bilder[0].size; z = (len(bilder) + sp - 1) // sp
bogen = Image.new("RGB", (sp * w + (sp + 1) * 8, z * h + (z + 1) * 8), (120, 120, 120))
for k, b in enumerate(bilder):
    bogen.paste(b, (8 + (k % sp) * (w + 8), 8 + (k // sp) * (h + 8)))
bogen.save(ziel); print(bogen.size)
