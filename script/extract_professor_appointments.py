#!/usr/bin/env python3
"""
Extrai nomeacoes de professores/docentes das atas da ESS/UFRJ.
Abordagem simplificada e eficiente: procura SIAPE e extrai nome proximo.
"""

import csv
import re
import subprocess
from pathlib import Path

PDF_DIR = Path("/home/luis/Downloads/atas_ess")
OUTPUT_CSV = Path("/home/luis/Downloads/atas_ess_nomeacoes_professores.csv")


def extract_date(filename, text):
    """Extrai a data da reuniao do nome do arquivo ou do texto."""
    m = re.search(r"(\d{2})-(\d{2})-(\d{4})", filename)
    if m:
        return f"{m.group(1)}/{m.group(2)}/{m.group(3)}"
    m = re.search(r"(\d{2})-(\d{2})-(\d{2})\.", filename)
    if m:
        return f"{m.group(1)}/{m.group(2)}/20{m.group(3)}"
    m = re.search(r"realizada-em-(\d{2})-(\d{2})-(\d{2})", filename)
    if m:
        return f"{m.group(1)}/{m.group(2)}/20{m.group(3)}"
    # Tenta no texto
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


def extract_uppercase_name_before(text, pos, max_chars=200):
    """Extrai nome em MAIUSCULAS que aparece antes da posicao."""
    chunk = text[max(0, pos - max_chars):pos].strip()
    # Remove quebras de linha para juntar nomes quebrados
    chunk = chunk.replace("\n", " ")
    # Procura sequencia de palavras em maiusculas no final do chunk
    # Palavras podem conter de/da/do/dos/das/e em minusculas
    words = chunk.split()
    name_words = []
    # Percorre do final para tras
    for w in reversed(words):
        w_clean = w.strip(",;:().")
        if not w_clean:
            break
        # Aceita maiusculas ou conectores
        if (len(w_clean) >= 2 and w_clean.isupper()) or w_clean.lower() in ("de", "da", "do", "dos", "das", "e", "del"):
            name_words.insert(0, w_clean)
        else:
            break
    if len(name_words) >= 2:
        return " ".join(name_words)
    return ""


def extract_prof_name_before(text, pos, max_chars=100):
    """Extrai nome apos 'Prof(a) Dr(a)' antes da posicao."""
    chunk = text[max(0, pos - max_chars):pos]
    m = re.search(r"Prof[ªaº]?\s*\.?\s*Dr[ªaº]?\s*\.?\s+([A-ZÀ-Ú][a-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})\s*$", chunk)
    if m:
        return m.group(1).strip()
    return ""


def find_department(text, pos, window=500):
    """Procura departamento ao redor da posicao."""
    chunk = text[max(0, pos - window):pos + window].upper()
    if "DFUSS" in chunk or "DEPARTAMENTO DE FUNDAMENTOS" in chunk:
        return "DFUSS - Depto. de Fundamentos do Servico Social"
    if "DMTSS" in chunk or "DEPARTAMENTO DE METODOS" in chunk or "DEPARTAMENTO DE MÉTODOS" in chunk:
        return "DMTSS - Depto. de Metodos e Tecnicas do Servico Social"
    if "DPSSS" in chunk or "DEPARTAMENTO DE POLITICA" in chunk or "DEPARTAMENTO DE POLÍTICA" in chunk:
        return "DPSSS - Depto. de Politica Social e Servico Social Aplicado"
    return ""


def determine_cargo(context):
    """Determina tipo de cargo pelo contexto."""
    ctx = context.lower()
    if "visitante" in ctx:
        return "Professor Visitante"
    if "substitut" in ctx and ("eventual" in ctx or "chefia" in ctx):
        return "Substituto Eventual de Chefia"
    if "substitut" in ctx:
        return "Professor Substituto"
    if "efetiv" in ctx or "concurso publico" in ctx or "concurso público" in ctx or "provimento" in ctx:
        return "Professor Efetivo (concurso publico)"
    if "tempor" in ctx or "simplificado" in ctx:
        return "Professor Temporario (processo simplificado)"
    if "chefia" in ctx or "chefe do departamento" in ctx or "chefe de departamento" in ctx:
        return "Chefia de Departamento"
    if "dire" in ctx and ("ess" in ctx or "gabinete" in ctx):
        return "Direcao"
    if "associad" in ctx:
        return "Professor Associado"
    if "adjunt" in ctx:
        return "Professor Adjunto"
    if "titular" in ctx:
        return "Professor Titular"
    if "representante" in ctx:
        return "Representante"
    if "comiss" in ctx:
        return "Membro de Comissao"
    return "Docente"


def find_siape_for_name(text, name):
    """Procura SIAPE associado a um nome no texto."""
    for m in re.finditer(re.escape(name), text, re.IGNORECASE):
        pos = m.end()
        chunk = text[pos:pos + 150]
        siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})", chunk, re.IGNORECASE)
        if siape_m:
            return siape_m.group(1)
        chunk_before = text[max(0, m.start() - 150):m.start()]
        siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})", chunk_before, re.IGNORECASE)
        if siape_m:
            return siape_m.group(1)
    return ""


def process_pdf(pdf_path):
    """Processa um PDF e retorna lista de nomeacoes encontradas."""
    text = extract_text(pdf_path)
    if not text:
        return []

    date = extract_date(pdf_path.name, text)
    results = []

    # ESTRATEGIA 1: Procurar por SIAPE + nome proximo
    for m in re.finditer(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})", text, re.IGNORECASE):
        siape = m.group(1)
        pos = m.start()

        # Tenta extrair nome em MAIUSCULAS antes do SIAPE
        name = extract_uppercase_name_before(text, pos, max_chars=200)

        # Se nao achou em maiusculas, tenta Prof Dr antes
        if not name:
            name = extract_prof_name_before(text, pos, max_chars=150)

        if not name or len(name.split()) < 2:
            continue

        # Contexto para classificacao
        ctx_start = max(0, pos - 400)
        ctx_end = min(len(text), pos + 400)
        context = text[ctx_start:ctx_end]

        dept = find_department(text, pos, window=500)
        cargo = determine_cargo(context)

        results.append({
            "data_reuniao": date,
            "nome": name,
            "siape": siape,
            "departamento": dept,
            "tipo_cargo": cargo,
            "filename": pdf_path.name,
            "contexto": context.replace("\n", " ").strip()[:300],
        })

    # ESTRATEGIA 2: Procurar por nomeacoes SEM SIAPE (padroes especificos)
    # Padroes simples sem backtracking problematico
    nomeacao_patterns = [
        # "nomeacao da professora NOME"
        (r"[Nn]omea[çc][ãa]o\s+(?:da|do)\s+professor[ªa]?\s+([A-ZÀ-Ú][a-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "nomeacao"),
        # "Nomeacao da Prof(a) Dr(a) NOME"
        (r"[Nn]omea[çc][ãa]o\s+(?:da|do)\s+Prof[ªaº]?\s*\.?\s*Dr[ªaº]?\s*\.?\s+([A-ZÀ-Ú][a-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "nomeacao"),
        # "contratacao da professora NOME" / "contratacao do professor NOME"
        (r"contrata[çc][ãa]o\s+(?:da|do)\s+professor[ªa]?\s+([A-ZÀ-Ú][a-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "contratacao"),
        # "contratacao da professora substituta NOME"
        (r"contrata[çc][ãa]o\s+da\s+professor[ªa]?\s+substitut[oa]?\s+([A-ZÀ-Ú][a-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "contratacao_substituto"),
        # "contratacao efetiva do candidato NOME"
        (r"contrata[çc][ãa]o\s+efetiva\s+do\s+candidato\s+([A-ZÀ-Úa-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "contratacao_efetiva"),
        # "recem nomead... NOME"
        (r"rec[ée]m\s+nomead[oa][,]?\s+(?:a\s+)?(?:docente|professor[ªa]?\s+)?([A-ZÀ-Úa-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "nomeacao"),
        # "nomeada e em exercicio no cargo junto ao"
        (r"([A-ZÀ-Úa-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})\s*\(?\s*j[áa]\s+nomead", "nomeacao"),
        # "nomeacao da candidata ... NOME"
        (r"nomea[çc][ãa]o\s+da\s+candidat[oa][^.]{0,60}([A-ZÀ-Úa-zà-ú]+(?:\s+(?:de|da|do|dos|das)\s+|[A-ZÀ-Úa-zà-ú]+){1,8})", "nomeacao_candidata"),
        # "convocacao ... professora NOME"
        (r"convoca[çc][ãa]o[^.]{0,80}professor[ªa]?\s+([A-ZÀ-Úa-zà-ú]+(?:\s+[A-ZÀ-Úa-zà-ú]+){1,8})", "convocacao"),
    ]

    for pattern, ptype in nomeacao_patterns:
        for m in re.finditer(pattern, text, re.IGNORECASE):
            name = m.group(1).strip()
            name = re.sub(r"\s+", " ", name)
            parts = name.split()
            if len(parts) < 2:
                continue

            # Filtra nomes invalidos
            bad = {"congregacao", "departamento", "universidade", "professor",
                   "substituto", "substituta", "visitante", "efetivo",
                   "reuniao", "ordinaria", "extraordinaria", "aprovada",
                   "homologacao", "ensino", "pesquisa", "extensao",
                   "direcao", "chefe", "presidente", "secretario",
                   "recem", "nomeada", "nomeado"}
            if parts[0].lower() in bad or parts[1].lower() in bad:
                continue

            pos = m.start()
            # Procura SIAPE no texto completo
            siape = find_siape_for_name(text, name)

            ctx_start = max(0, pos - 300)
            ctx_end = min(len(text), pos + 400)
            context = text[ctx_start:ctx_end]
            dept = find_department(text, pos, window=500)
            cargo = determine_cargo(context)

            results.append({
                "data_reuniao": date,
                "nome": name,
                "siape": siape,
                "departamento": dept,
                "tipo_cargo": cargo,
                "filename": pdf_path.name,
                "contexto": context.replace("\n", " ").strip()[:300],
            })

    return results


def main():
    pdfs = sorted(PDF_DIR.glob("*.pdf"))
    print(f"Total de PDFs: {len(pdfs)}")
    print("Extraindo e analisando texto...\n")

    all_results = []
    for i, pdf_path in enumerate(pdfs, 1):
        results = process_pdf(pdf_path)
        all_results.extend(results)
        if i % 50 == 0:
            print(f"  Processados {i}/{len(pdfs)}...")

    # Remove duplicatas
    seen = set()
    unique = []
    for r in all_results:
        key = (r["nome"].lower().strip(), r["data_reuniao"], r["tipo_cargo"])
        if key not in seen:
            seen.add(key)
            unique.append(r)

    unique.sort(key=lambda x: x["data_reuniao"] if x["data_reuniao"] != "?" else "00/00/0000")

    fields = ["data_reuniao", "nome", "siape", "departamento", "tipo_cargo", "filename", "contexto"]
    with open(OUTPUT_CSV, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fields)
        writer.writeheader()
        for r in unique:
            writer.writerow({k: r[k] for k in fields})

    print(f"\nCSV gerado: {OUTPUT_CSV}")
    print(f"Total de nomeacoes encontradas: {len(unique)}")

    com_siape = sum(1 for r in unique if r["siape"])
    com_dept = sum(1 for r in unique if r["departamento"])
    print(f"Com SIAPE: {com_siape}/{len(unique)}")
    print(f"Com departamento: {com_dept}/{len(unique)}")

    print("\n--- Resumo por tipo de cargo ---")
    tipos = {}
    for r in unique:
        tipos[r["tipo_cargo"]] = tipos.get(r["tipo_cargo"], 0) + 1
    for t, c in sorted(tipos.items(), key=lambda x: -x[1]):
        print(f"  {t}: {c}")

    print("\n--- Resumo por departamento ---")
    depts = {}
    for r in unique:
        d = r["departamento"] or "Nao identificado"
        depts[d] = depts.get(d, 0) + 1
    for d, c in sorted(depts.items(), key=lambda x: -x[1]):
        print(f"  {d}: {c}")

    print(f"\n{'='*90}")
    print("NOMEACOES ENCONTRADAS")
    print(f"{'='*90}")
    print(f"{'Data':<12} {'Nome':<45} {'SIAPE':<10} {'Tipo':<30} {'Depto'}")
    print("-" * 90)
    for r in unique:
        siape_s = r["siape"] if r["siape"] else "N/A"
        dept_s = r["departamento"][:20] if r["departamento"] else "N/A"
        print(f"{r['data_reuniao']:<12} {r['nome'][:44]:<45} {siape_s:<10} {r['tipo_cargo'][:29]:<30} {dept_s}")


if __name__ == "__main__":
    main()
