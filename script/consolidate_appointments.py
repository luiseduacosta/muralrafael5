#!/usr/bin/env python3
"""
Consolida as nomeacoes extraidas pelos agentes e procura SIAPES nos PDFs originais.
"""

import csv
import re
import subprocess
from pathlib import Path

PDF_DIR = Path("/home/luis/Downloads/atas_ess")
OUTPUT_CSV = Path("/home/luis/Downloads/atas_ess_tabela_nomeacoes_final.csv")

# Todos os registros extraidos pelos 4 agentes
# Formato: (data_reuniao, nome, siape, departamento, tipo_cargo)
registros = [
    # PARTE 1
    ("26/04/2022", "Charles Toniolo de Souza", "", "DFUSS", "Chefia de Departamento"),
    ("26/04/2022", "Fátima Valéria Ferreira de Souza", "", "DFUSS", "Substituta Eventual de Chefia"),
    ("26/04/2022", "Ilma Rezende Soares", "", "", "Colaborador Voluntário"),
    ("11/09/2025", "Mathias Seibel Luce", "", "DPSSS", "Chefia de Departamento"),
    ("11/09/2025", "Kátia Sento Sé Mello", "", "DPSSS", "Substituta Eventual de Chefia"),
    ("02/10/2025", "Maria Clara de Arruda Barbosa", "", "DMTSS", "Efetivo (concurso público)"),
    ("02/10/2025", "Rhaysa Sampaio Ruas da Fonseca", "", "DPSSS", "Efetivo (concurso público)"),
    ("11/03/2025", "Luís Eduardo da Rocha Maia Fernandes", "", "DMTSS", "Efetivo (concurso público)"),
    ("11/03/2025", "Juliana Batistuta Vale", "", "DMTSS", "Efetivo (concurso público)"),
    ("11/03/2025", "Thamires Meirelles", "", "DPSSS", "Professor Substituto"),
    ("11/03/2025", "Clara Gomide", "", "DPSSS", "Professor Substituto"),
    ("11/03/2025", "Rejane Carolina Hoeveler", "", "DPSSS", "Efetivo (concurso público)"),
    ("11/03/2025", "Mably Jane Trindade Tenenblat", "", "DFUSS", "Chefia de Departamento"),
    ("11/03/2025", "Lenise Lima Fernandes", "", "DFUSS", "Substituta Eventual de Chefia"),
    ("15/12/2025", "Priscila Rodrigues de Castro", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("15/12/2025", "Carolina Alves de Oliveira", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("15/12/2025", "Marcos Paulo Oliveira Botelho", "", "DMTSS", "Chefia de Departamento"),
    ("15/12/2025", "Elaine Martins Moreira", "", "DMTSS", "Substituta Eventual de Chefia"),
    ("15/12/2025", "Clara Calazans Espindola", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("15/12/2025", "Weslany Thaise Lins Prudêncio", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("15/12/2025", "Jéssica dos Santos Costa", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("29/05/2025", "Sheila Dias Almeida", "", "DFUSS", "Efetivo (concurso público)"),
    ("04/04/2024", "Charles Toniolo de Souza", "", "DFUSS", "Chefia de Departamento"),
    ("04/04/2024", "Marilene Aparecida Coelho", "", "DFUSS", "Substituta Eventual de Chefia"),
    ("09/01/2025", "Elaine Martins Moreira", "", "DMTSS", "Chefia de Departamento"),
    ("09/01/2025", "Marcos Paulo Oliveira Botelho", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("18/12/2023", "Fátima Valéria Ferreira de Souza", "", "DFUSS", "Substituta Eventual de Chefia"),
    ("18/12/2023", "Jonathan Henri Sebastião Jaumont", "", "DPSSS", "Chefia de Departamento"),
    ("18/12/2023", "Cibele da Silva Henriques", "", "DPSSS", "Substituta Eventual de Chefia"),
    ("19/12/2022", "Gláucia Lelis Alves", "", "DMTSS", "Chefia de Departamento"),
    ("19/12/2022", "Aline Caldeira Lopes", "", "DMTSS", "Substituta Eventual de Chefia"),
    ("26/05/2022", "Karla Fernanda Valle", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("26/05/2022", "Mariana Figueiredo de Castro Pereira", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("26/05/2022", "Iris Sunsyaray Mendes Feliciano de Andrade", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("26/05/2022", "Mariane Raquel de Oliveira Fonseca", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("26/05/2022", "Alan de Loiola Alves", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("26/05/2022", "Aline Caldeira Lopes", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Débora Holanda Leite Menezes", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Gênesis de Oliveira Pereira", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Mably Jane Trindade Tenenblat", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Rafael Barros Vieira", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Cibele da Silva Henriques", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Daniel de Souza Campos", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Jonathan Henri Sebastião Jaumont", "", "", "Efetivo (concurso público)"),
    ("26/05/2022", "Lilian Angélica da Silva Souza", "", "", "Efetivo (concurso público)"),
    ("26/10/2023", "Camila Faria Pançardes", "", "DFUSS", "Efetivo (concurso público)"),
    ("27/01/2023", "Guilherme de Rocamora Figueiredo da Silva", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("27/01/2023", "Evelyn Melo da Silva", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("27/01/2023", "Andreia da Silva Lima", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("27/01/2023", "Geisa Emokdisi Pedrosa Bordenave", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("27/01/2023", "Taiane Damasceno da Hora", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("27/01/2023", "José Amilton de Almeida", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("30/03/2023", "Charles Toniolo de Souza", "", "DFUSS", "Chefia de Departamento"),
    ("30/03/2023", "Fátima Valéria Ferreira de Souza", "", "DFUSS", "Substituta Eventual de Chefia"),
    ("14/11/2019", "Sheila Backx", "", "DMTSS", "Substituta Eventual de Chefia"),

    # PARTE 2
    ("02/03/2011", "Mariela Natalia Becher", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("02/03/2011", "Eliane Santos de Assis", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("02/03/2011", "Janaina Albuquerque de Camargo Schmidt", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("02/03/2011", "Alzita Mitz Bernardes Guarany", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("21/08/2008", "Maria Paula Gomes dos Santos", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("16/08/2018", "Ana Elizabeth Motta", "", "", "Visitante"),
    ("16/08/2018", "Ivanete Boschetti", "", "DMTSS", "Efetivo (redistribuição)"),
    ("21/11/2017", "Rita de Cássia Cavalcante Lima", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("13/06/2019", "Carla Cecília Campos Ferreira", "", "DMTSS", "Chefia de Departamento"),
    ("13/06/2019", "Alzira Mitz Campos Ferreira", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("17/12/2019", "Antonio Israel Carlos da Silva", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("17/12/2019", "Lilian Angélica da Silva Souza", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("17/12/2019", "Rosemere Santos Maia", "", "DFUSS", "Colaboradora Voluntária"),
    ("17/12/2019", "Daniel de Souza Campos", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("17/12/2019", "Ariana Kelly dos Santos", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("17/12/2019", "Greyssy Kelly Araujo de Souza", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("03/12/2020", "Ariana Santos", "", "", "Professor Substituto (contratada)"),
    ("03/12/2020", "Liana Amaro Augusto de Carvalho", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("03/12/2020", "Cibele Henriques", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("31/10/2024", "Elaine Martins Moreira", "", "DMTSS", "Chefia de Departamento"),
    ("31/10/2024", "Marcos Paulo Oliveira Botelho", "", "DMTSS", "Substituto Eventual de Chefia"),

    # PARTE 3
    ("14/10/2021", "Rachel Gouveia Passos", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("27/01/2022", "Rosana Morgado Paiva", "", "DMTSS", "Chefia de Departamento"),
    ("27/01/2022", "Joana Angélica Barbosa Garcia", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("28/08/2020", "Marina Machado Magalhães Gouvêa", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("01/07/2010", "Patrícia Silveira de Farias", "", "DPSSS", "Substituto Eventual de Chefia"),
    ("01/09/2016", "Maria Josefina Mastropaolo", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("02/06/2016", "Gracyelle Costa Ferreira", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("17/10/2019", "Sheila de Souza Backx", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("22/06/2017", "Ana Elizabete Fiuza Simões da Mota", "", "", "Professora Visitante Sênior"),

    # PARTE 4
    ("25/04/2019", "Caio Martins", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("25/04/2019", "Rodrigo Marcelino da Silva", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("25/04/2019", "Bruno Alves de França", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("25/04/2019", "Camila Rebouças Fernandes", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("25/04/2019", "Mathias Seibel Luce", "", "DPSSS", "Chegada/Início de atividades"),
    ("26/03/2015", "Juan Pablo Sierra Tapiro", "", "DMTSS", "Professor Substituto (processo seletivo)"),
    ("26/04/2012", "Marina Machado de Magalhães Gouvêa", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("26/04/2012", "Victor Neves de Souza", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("26/09/2018", "Ana Izabel Moura de Carvalho Moreira", "", "DFUSS", "Chefia de Departamento"),
    ("26/09/2018", "Fátima da Silva Grave Ortiz", "", "DFUSS", "Substituto Eventual de Chefia"),
    ("28/03/2019", "Paula Ferreira Poncioni", "", "DPSSS", "Professor Colaborador"),
    ("28/04/2016", "Tatiana Brettas Waehneldt", "", "", "Direção (CD-4)"),
    ("28/04/2016", "Thaiany Silva da Motta", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("28/04/2016", "Mónica Brun Beveder", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("28/07/2016", "Maria Josefina Mastropaolo", "", "DPSSS", "Professor Substituto (processo seletivo)"),
    ("28/07/2016", "Raquel Caldeira Varela", "", "", "Professor Visitante Sênior"),
    ("29/08/2019", "Ana Elizabeth Fiuza Simões da Mota", "", "", "Professor Visitante Sênior"),
    ("29/09/2016", "Rita de Cássia Cavalcante Lima", "", "DMTSS", "Substituto Eventual de Chefia"),
    ("29/09/2011", "Elaine", "", "DMTSS", "Efetivo (concurso público)"),
    ("29/10/2015", "Leile Silvia Candido Teixeira", "", "DPSSS", "Chefia de Departamento"),
    ("29/10/2015", "Silvina Verônica Galizia", "", "DPSSS", "Substituto Eventual de Chefia"),
    ("29/11/2018", "Maria Josefina Mastropaolo", "", "DMTSS", "Efetivo (concurso público)"),
    ("29/11/2018", "Guilherme Silva de Almeida", "", "DMTSS", "Efetivo (concurso público)"),
    ("30/10/2014", "Gabriela Maria Lema Icasuriaga", "", "DPSSS", "Substituto Eventual de Chefia"),
    ("31/08/2006", "Luana de Souza Siqueira", "", "DFUSS", "Professor Substituto (processo seletivo)"),
    ("31/08/2006", "Maria Nasaré Ferreira Pinto", "", "DFUSS", "Professor Substituto (processo seletivo)"),
]


def extract_text(pdf_path):
    try:
        proc = subprocess.run(
            ["pdftotext", str(pdf_path), "-"],
            capture_output=True, text=True, timeout=30
        )
        return proc.stdout if proc.returncode == 0 else ""
    except Exception:
        return ""


def find_siape_for_name(all_texts, name):
    """Procura SIAPE associado a um nome em todos os textos extraidos."""
    # Normaliza nome para busca (remove acentos para matching flexivel)
    name_variants = [name, name.upper()]
    # Tenta tambem apenas os ultimos 2 nomes (sobrenome)
    parts = name.split()
    if len(parts) >= 2:
        name_variants.append(" ".join(parts[-2:]))
        name_variants.append(" ".join(parts[-2:]).upper())

    for text in all_texts:
        for variant in name_variants:
            for m in re.finditer(re.escape(variant), text, re.IGNORECASE):
                pos = m.end()
                chunk = text[pos:pos + 200]
                siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})", chunk, re.IGNORECASE)
                if siape_m:
                    return siape_m.group(1)
                # Procura antes do nome
                chunk_before = text[max(0, m.start() - 200):m.start()]
                siape_m = re.search(r"SIAPE\s*(?:n[ºo.]*)?\s*:?\s*(\d{6,8})\s*", chunk_before, re.IGNORECASE)
                if siape_m:
                    return siape_m.group(1)
    return ""


def main():
    print(f"Total de registros: {len(registros)}")

    # Remove duplicatas exatas (mesmo nome + data + tipo)
    seen = set()
    unique = []
    for r in registros:
        key = (r[1].lower().strip(), r[0], r[4])
        if key not in seen:
            seen.add(key)
            unique.append(r)
    print(f"Registros unicos: {len(unique)}")

    # Extrai texto de todos os PDFs para procurar SIAPE
    print("Extraindo texto de todos os PDFs para procurar SIAPE...")
    pdfs = sorted(PDF_DIR.glob("*.pdf"))
    all_texts = {}
    for i, pdf_path in enumerate(pdfs, 1):
        all_texts[pdf_path.name] = extract_text(pdf_path)
        if i % 50 == 0:
            print(f"  Extraidos {i}/{len(pdfs)}...")

    # Procura SIAPE para cada nome
    print("Procurando SIAPES...")
    resultados_finais = []
    for data, nome, siape, dept, cargo in unique:
        if not siape:
            siape = find_siape_for_name(list(all_texts.values()), nome)
        # Normaliza departamento
        if dept:
            dept_full = dept
        else:
            dept_full = ""
        resultados_finais.append({
            "data_reuniao": data,
            "nome": nome,
            "siape": siape,
            "departamento": dept_full,
            "tipo_cargo": cargo,
        })

    # Ordena por data (mais antigo primeiro)
    def sort_key(r):
        d = r["data_reuniao"]
        parts = d.split("/")
        if len(parts) == 3:
            try:
                return (int(parts[2]), int(parts[1]), int(parts[0]))
            except ValueError:
                return (9999, 99, 99)
        return (9999, 99, 99)

    resultados_finais.sort(key=sort_key)

    # Escreve CSV
    fields = ["data_reuniao", "nome", "siape", "departamento", "tipo_cargo"]
    with open(OUTPUT_CSV, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fields)
        writer.writeheader()
        for r in resultados_finais:
            writer.writerow(r)

    com_siape = sum(1 for r in resultados_finais if r["siape"])
    com_dept = sum(1 for r in resultados_finais if r["departamento"])
    print(f"\nCSV gerado: {OUTPUT_CSV}")
    print(f"Total de nomeacoes: {len(resultados_finais)}")
    print(f"Com SIAPE: {com_siape}/{len(resultados_finais)}")
    print(f"Com departamento: {com_dept}/{len(resultados_finais)}")

    # Resumo por tipo
    print("\n--- Resumo por tipo de cargo ---")
    tipos = {}
    for r in resultados_finais:
        tipos[r["tipo_cargo"]] = tipos.get(r["tipo_cargo"], 0) + 1
    for t, c in sorted(tipos.items(), key=lambda x: -x[1]):
        print(f"  {t}: {c}")

    # Resumo por departamento
    print("\n--- Resumo por departamento ---")
    depts = {}
    for r in resultados_finais:
        d = r["departamento"] or "Nao especificado"
        depts[d] = depts.get(d, 0) + 1
    for d, c in sorted(depts.items(), key=lambda x: -x[1]):
        print(f"  {d}: {c}")

    # Lista final
    print(f"\n{'='*100}")
    print(f"{'Data':<12} {'Nome':<48} {'SIAPE':<10} {'Departamento':<8} {'Tipo de Cargo'}")
    print(f"{'='*100}")
    for r in resultados_finais:
        siape_s = r["siape"] if r["siape"] else "N/A"
        dept_s = r["departamento"] if r["departamento"] else "N/A"
        print(f"{r['data_reuniao']:<12} {r['nome'][:47]:<48} {siape_s:<10} {dept_s:<8} {r['tipo_cargo']}")


if __name__ == "__main__":
    main()
