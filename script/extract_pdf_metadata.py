#!/usr/bin/env python3
"""Extrai metadados de todos os PDFs de uma pasta usando pdfinfo e gera relatorio CSV."""

import csv
import subprocess
from pathlib import Path

PDF_DIR = Path("/home/luis/Downloads/atas_ess")
OUTPUT_CSV = Path("/home/luis/Downloads/atas_ess_metadados.csv")


def extract_metadata(pdf_path):
    """Executa pdfinfo e retorna um dict com os metadados."""
    result = {"filename": pdf_path.name, "filepath": str(pdf_path)}
    try:
        proc = subprocess.run(
            ["pdfinfo", str(pdf_path)],
            capture_output=True, text=True, timeout=30
        )
        if proc.returncode == 0:
            for line in proc.stdout.splitlines():
                if ":" in line:
                    key, _, value = line.partition(":")
                    key_norm = key.strip().lower().replace(" ", "_").replace("-", "_")
                    result[key_norm] = value.strip()
        else:
            result["error"] = proc.stderr.strip() or f"exit code {proc.returncode}"
    except subprocess.TimeoutExpired:
        result["error"] = "timeout"
    except Exception as e:
        result["error"] = str(e)
    return result


def main():
    pdfs = sorted(PDF_DIR.glob("*.pdf"))
    print(f"Total de PDFs encontrados: {len(pdfs)}")
    print("Processando...")

    all_metadata = []
    errors = []

    for i, pdf_path in enumerate(pdfs, 1):
        meta = extract_metadata(pdf_path)
        all_metadata.append(meta)
        if "error" in meta:
            errors.append(pdf_path.name)
        if i % 50 == 0:
            print(f"  Processados {i}/{len(pdfs)}...")

    # Coletar todas as chaves possiveis
    all_keys = ["filename"]
    seen = set()
    for meta in all_metadata:
        for k in meta.keys():
            if k not in seen and k != "filename" and k != "filepath":
                all_keys.append(k)
                seen.add(k)
    all_keys.append("filepath")

    # Escrever CSV
    with open(OUTPUT_CSV, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=all_keys, extrasaction="ignore")
        writer.writeheader()
        for meta in all_metadata:
            writer.writerow(meta)

    print(f"\nCSV gerado: {OUTPUT_CSV}")
    print(f"Total processado: {len(all_metadata)}")
    print(f"Com erro: {len(errors)}")
    if errors:
        print(f"Arquivos com erro:")
        for e in errors:
            print(f"  - {e}")

    # Resumo estatistico
    print("\n" + "=" * 60)
    print("RESUMO ESTATISTICO")
    print("=" * 60)

    # Paginas
    pages_list = []
    for m in all_metadata:
        p = m.get("pages")
        if p and p.isdigit():
            pages_list.append(int(p))
    if pages_list:
        print(f"\nPaginas:")
        print(f"  Total: {sum(pages_list)}")
        print(f"  Media: {sum(pages_list) / len(pages_list):.1f}")
        print(f"  Min: {min(pages_list)} | Max: {max(pages_list)}")

    # Tamanho dos arquivos
    sizes = []
    for m in all_metadata:
        s = m.get("file_size")
        if s:
            num = s.replace(" bytes", "").replace(" byte", "").strip()
            if num.isdigit():
                sizes.append(int(num))
    if sizes:
        total_mb = sum(sizes) / (1024 * 1024)
        print(f"\nTamanho dos arquivos:")
        print(f"  Total: {total_mb:.2f} MB")
        print(f"  Media: {total_mb / len(sizes):.2f} MB")
        print(f"  Min: {min(sizes) / 1024:.1f} KB | Max: {max(sizes) / 1024:.1f} KB")

    # Titulos
    titles = {}
    for m in all_metadata:
        t = m.get("title", "").strip()
        if t:
            titles[t] = titles.get(t, 0) + 1
    if titles:
        print(f"\nTitulos (top 15):")
        for t, c in sorted(titles.items(), key=lambda x: -x[1])[:15]:
            print(f"  [{c}x] {t}")
    else:
        print("\nTitulos: nenhum titulo encontrado nos PDFs")

    # Autores
    authors = {}
    for m in all_metadata:
        a = m.get("author", "").strip()
        if a:
            authors[a] = authors.get(a, 0) + 1
    if authors:
        print(f"\nAutores (top 10):")
        for a, c in sorted(authors.items(), key=lambda x: -x[1])[:10]:
            print(f"  [{c}x] {a}")
    else:
        print("\nAutores: nenhum autor encontrado nos PDFs")

    # Criadores
    creators = {}
    for m in all_metadata:
        c_val = m.get("creator", "").strip()
        if c_val:
            creators[c_val] = creators.get(c_val, 0) + 1
    if creators:
        print(f"\nCriadores (top 10):")
        for c_val, c in sorted(creators.items(), key=lambda x: -x[1])[:10]:
            print(f"  [{c}x] {c_val}")

    # Producers
    producers = {}
    for m in all_metadata:
        p_val = m.get("producer", "").strip()
        if p_val:
            producers[p_val] = producers.get(p_val, 0) + 1
    if producers:
        print(f"\nProdutores (top 10):")
        for p_val, c in sorted(producers.items(), key=lambda x: -x[1])[:10]:
            print(f"  [{c}x] {p_val}")

    # Versoes PDF
    versions = {}
    for m in all_metadata:
        v = m.get("pdf_version", "").strip()
        if v:
            versions[v] = versions.get(v, 0) + 1
    if versions:
        print(f"\nVersoes PDF:")
        for v, c in sorted(versions.items()):
            print(f"  {v}: {c}")

    # Tamanhos de pagina
    page_sizes = {}
    for m in all_metadata:
        ps = m.get("page_size", "").strip()
        if ps:
            page_sizes[ps] = page_sizes.get(ps, 0) + 1
    if page_sizes:
        print(f"\nTamanhos de pagina (top 10):")
        for ps, c in sorted(page_sizes.items(), key=lambda x: -x[1])[:10]:
            print(f"  [{c}x] {ps}")

    # Datas de criacao
    dates = [m.get("creationdate", "") for m in all_metadata if m.get("creationdate")]
    print(f"\nDatas de criacao:")
    print(f"  {len(dates)} PDFs com data de criacao")
    if dates:
        for d in dates[:5]:
            print(f"  {d}")
        if len(dates) > 5:
            print(f"  ... ({len(dates)} total)")

    # Datas de modificacao
    moddates = [m.get("moddate", "") for m in all_metadata if m.get("moddate")]
    print(f"\nDatas de modificacao:")
    print(f"  {len(moddates)} PDFs com data de modificacao")


if __name__ == "__main__":
    main()
