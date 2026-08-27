#!/usr/bin/env python3
"""
Reprocessa a procura de SIAPE usando apenas o nome completo (sem variantes de sobrenome).
Corrige SIAPES incorretos encontrados pelo script anterior.
"""

import csv
import re
import subprocess
from pathlib import Path

PDF_DIR = Path("/home/luis/Downloads/atas_ess")
INPUT_CSV = Path("/home/luis/Downloads/atas_ess_tabela_nomeacoes_final.csv")
OUTPUT_CSV = Path("/home/luis/Downloads/atas_ess_tabela_nomeacoes_corrigida.csv")


def extract_text(pdf_path):
    try:
        proc = subprocess.run(
            ["pdftotext", str(pdf_path), "-"],
            capture_output=True, text=True, timeout=30
        )
        return proc.stdout if proc.returncode == 0 else ""
    except Exception:
        return ""


def find_siape_for_name(texts, name):
    """Procura SIAPE associado a um nome usando apenas o nome completo."""
    for text in texts:
        # Procura o nome exato no texto
        for m in re.finditer(re.escape(name), text, re.IGNORECASE):
            pos = m.end()
            # Procura SIAPE nos proximos 200 caracteres apos o nome
            chunk = text[pos:pos + 200]
            siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})", chunk, re.IGNORECASE)
            if siape_m:
                return siape_m.group(1)
            # Procura SIAPE nos 200 caracteres antes do nome
            chunk_before = text[max(0, m.start() - 200):m.start()]
            siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})\s*", chunk_before, re.IGNORECASE)
            if siape_m:
                return siape_m.group(1)

        # Se nao encontrou com nome completo, tenta com "NOME EM MAIUSCULAS, SIAPE"
        name_upper = name.upper()
        # Remove acentos para matching mais flexivel
        name_no_accents = re.sub(r"[À-Úà-ú]", lambda c: {
            'Á':'A','À':'A','Â':'A','Ã':'A','Ä':'A','Å':'A',
            'É':'E','Ê':'E','È':'E','Ë':'E',
            'Í':'I','Ì':'I','Î':'I','Ï':'I',
            'Ó':'O','Ò':'O','Ô':'O','Õ':'O','Ö':'O',
            'Ú':'U','Ù':'U','Û':'U','Ü':'U',
            'Ç':'C',
            'á':'a','à':'a','â':'a','ã':'a','ä':'a',
            'é':'e','ê':'e','è':'e','ë':'e',
            'í':'i','ì':'i','î':'i','ï':'i',
            'ó':'o','ò':'o','ô':'o','õ':'o','ö':'o',
            'ú':'u','ù':'u','û':'u','ü':'u',
            'ç':'c',
        }.get(c.group(0), c.group(0)), name_upper)

        for text in texts:
            for variant in [name_upper, name_no_accents]:
                for m in re.finditer(re.escape(variant), text, re.IGNORECASE):
                    pos = m.end()
                    chunk = text[pos:pos + 100]
                    siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})", chunk, re.IGNORECASE)
                    if siape_m:
                        return siape_m.group(1)
                    chunk_before = text[max(0, m.start() - 100):m.start()]
                    siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})\s*", chunk_before, re.IGNORECASE)
                    if siape_m:
                        return siape_m.group(1)
    return ""


def main():
    # Le o CSV
    with open(INPUT_CSV, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        registros = list(reader)

    print(f"Total de registros: {len(registros)}")

    # Extrai texto de todos os PDFs
    print("Extraindo texto de todos os PDFs...")
    pdfs = sorted(PDF_DIR.glob("*.pdf"))
    all_texts = []
    for i, pdf_path in enumerate(pdfs, 1):
        all_texts.append(extract_text(pdf_path))
        if i % 50 == 0:
            print(f"  Extraidos {i}/{len(pdfs)}...")

    # Reprocura SIAPE para cada nome
    print("Reprocurando SIAPES (usando apenas nome completo)...")
    for r in registros:
        name = r["nome"]
        old_siape = r["siape"]
        new_siape = find_siape_for_name(all_texts, name)
        if new_siape and new_siape != old_siape:
            print(f"  CORRIGIDO: {name} | {old_siape} -> {new_siape}")
            r["siape"] = new_siape
        elif not new_siape and old_siape:
            # Se nao encontrou com nome completo, remove o SIAPE antigo (provavelmente errado)
            print(f"  REMOVIDO: {name} | {old_siape} (nao confirmado)")
            r["siape"] = ""
        elif new_siape == old_siape and new_siape:
            print(f"  CONFIRMADO: {name} | {new_siape}")

    # Escreve CSV corrigido
    fields = ["data_reuniao", "nome", "siape", "departamento", "tipo_cargo"]
    with open(OUTPUT_CSV, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fields)
        writer.writeheader()
        for r in registros:
            writer.writerow(r)

    com_siape = sum(1 for r in registros if r["siape"])
    print(f"\nCSV corrigido gerado: {OUTPUT_CSV}")
    print(f"Com SIAPE confirmado: {com_siape}/{len(registros)}")


if __name__ == "__main__":
    main()
