from pathlib import Path
import re

base = Path(r'd:\laragon\www\Jurnal_Sekolah\resources\views')
for p in base.rglob('*.blade.php'):
    txt = p.read_text(encoding='utf-8')
    if '<<<<<<< HEAD' not in txt:
        continue
    before, rest = txt.split('<<<<<<< HEAD', 1)
    middle, after = rest.split('=======', 1)
    end = after.split('>>>>>>>', 1)[1]
    new = before + middle + end
    p.write_text(new, encoding='utf-8')
    print(f'cleaned {p}')
