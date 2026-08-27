#!/usr/bin/env python3
"""
Extrai trechos relevantes sobre nomeacao/contratacao de professores das atas.
Salva os trechos em arquivo para analise posterior.
"""

import re
import subprocess
from pathlib import Path

PDF_DIR = Path("/home/luis/Downloads/atas_ess")
OUTPUT_FILE = Path("/home/luis/Downloads/atas_ess_trechos_nomeacoes.txt")


def extract_date(filename, text):
    m = re.search(r"(\d{2})-(\d{2})-(\d{4})", filename)
    if m:
        return f"{m.group(1)}/{m.group(2)}/{m.group(3)}"
    m = re.search(r"(\d{2})-(\d{2})-(\d{2})\.", filename)
    if m:
        return f"{m.group(1)}/{m.group(2)}/20{m.group(3)}"
    m = re.search(r"realizada-em-(\d{2})-(\d{2})-(\d{2})", filename)
    if m:
        return f"{m.group(1)}/{m.group(2)}/20{m.group(3)}"
    meses = ["janeiro", "fevereiro", "mar", "abril", "maio", "junho",
             "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"]
    for i, mes in enumerate(meses, 1):
        pat = rf"realizada\s+em\s+(\d{{1,2}})\s+de\s+{mes}\w*\s+de\s+(\d{{4}})"
        m = re.search(pat, text, re.IGNORECASE)
        if m:
            return f"{m.group(1).zfill(2)}/{str(i).zfill(2)}/{m.group(2)}"
    return "?"


def extract_text(pdf_path):
    try:
        proc = subprocess.run(
            ["pdftotext", str(pdf_path), "-"],
            capture_output=True, text=True, timeout=30
        )
        return proc.stdout if proc.returncode == 0 else ""
    except Exception:
        return ""


def main():
    pdfs = sorted(PDF_DIR.glob("*.pdf"))
    print(f"Total de PDFs: {len(pdfs)}")

    # Palavras-chave para identificar nomeacoes
    keywords = re.compile(
        r"nomea[çc][ãa]o|contrata[çc][ãa]o\s+(?:efetiva|tempor|da\s+prof|do\s+prof)"
        r"|rec[ée]m\s+nomead|empossad|convoca[çc][ãa]o\s+da|convoca[çc][ãa]o\s+do"
        r"|assum(?:e|ir)[aá]\s+como\s+professor|professor[ªa]?\s+substitut[oa]"
        r"|professor[ªa]?\s+visitante|chegada\s+de\s+novo"
        r"|tomando\s+posse|assumir[áa]\s+o\s+cargo\s+de\s+[Cc]hefe"
        r"|Substitut[oa]\s+Eventual\s+da\s+[Cc]hefia"
        r"|nomead[oa]\s+e\s+em\s+exerc[íi]cio"
        r"|Processo\s+Seletivo\s+Simplificado.*Professor\s+Substituto"
        r"|provimento\s+efetivo"
        r"|contrata[çc][ãa]o\s+efetiva\s+do\s+candidato",
        re.IGNORECASE
    )

    output_lines = []
    pdfs_with_matches = 0

    for i, pdf_path in enumerate(pdfs, 1):
        text = extract_text(pdf_path)
        if not text:
            continue

        date = extract_date(pdf_path.name, text)

        # Encontra todas as posicoes de match
        matches = list(keywords.finditer(text))
        if not matches:
            continue

        pdfs_with_matches += 1
        trechos = []

        for m in matches:
            pos = m.start()
            # Extrai contexto amplo (500 chars antes e 800 depois)
            start = max(0, pos - 300)
            end = min(len(text), pos + 600)
            context = text[start:end].replace("\n", " ").strip()
            # Limpa espacos multiplos
            context = re.sub(r"\s+", " ", context)
            trechos.append(context)

        if trechos:
            output_lines.append(f"\n{'='*80}")
            output_lines.append(f"ARQUIVO: {pdf_path.name}")
            output_lines.append(f"DATA DA REUNIAO: {date}")
            output_lines.append(f"{'='*80}")
            for j, t in enumerate(trechos, 1):
                output_lines.append(f"\n--- Trecho {j} ---")
                output_lines.append(t)

        if i % 50 == 0:
            print(f"  Processados {i}/{len(pdfs)}...")

    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        f.write("\n".join(output_lines))

    print(f"\nArquivo gerado: {OUTPUT_FILE}")
    print(f"PDFs com trechos relevantes: {pdfs_with_matches}")
    print(f"Total de trechos: {len([l for l in output_lines if l.startswith('--- Trecho')])}")


if __name__ == "__main__":
    main()
