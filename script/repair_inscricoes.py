import mysql.connector
import sys
from datetime import datetime

# Database Configuration (Based on config/app_local.php)
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': 'root',
    'database': 'ess_apps'
}

def backup_and_update_aluno_id():
    """Creates a backup of inscricoes table and updates aluno_id from alunos using registro field."""
    print("Starting backup and aluno_id update process...")
    conn = None
    cursor = None
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        print("Successfully connected to the database.\n")

        # 1. Create backup table with timestamp
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        backup_table = f"inscricoes_backup_{timestamp}"
        print(f"Creating backup table: {backup_table}")
        cursor.execute(f"CREATE TABLE `{backup_table}` LIKE `inscricoes`")
        cursor.execute(f"INSERT INTO `{backup_table}` SELECT * FROM `inscricoes`")
        print(f"Backup created successfully. {cursor.rowcount} rows copied.\n")

        # 2. Update aluno_id from alunos using registro field
        print("Updating aluno_id from alunos table using registro field...")
        query_update_aluno_id = """
        UPDATE inscricoes i
        INNER JOIN alunos a ON a.registro = i.registro
        SET i.aluno_id = a.id
        WHERE i.aluno_id IS NULL OR i.aluno_id != a.id
        """
        cursor.execute(query_update_aluno_id)
        updated_count = cursor.rowcount
        print(f"Updated {updated_count} inscricoes records with correct aluno_id.\n")

        # 3. Show records that still have NULL aluno_id (no matching registro)
        query_null_aluno_id = """
        SELECT i.id, i.registro, i.muralestagio_id
        FROM inscricoes i
        WHERE i.aluno_id IS NULL
        LIMIT 10
        """
        cursor.execute(query_null_aluno_id)
        null_records = cursor.fetchall()
        if null_records:
            print("Warning: The following records still have NULL aluno_id (no matching registro in alunos):")
            for rec in null_records:
                print(f"  - inscricao id: {rec['id']}, registro: {rec['registro']}, muralestagio_id: {rec['muralestagio_id']}")
            print()
        else:
            print("All inscricoes records now have a valid aluno_id.\n")

        conn.commit()
        print("Backup and update process completed successfully.\n")
        return True

    except mysql.connector.Error as err:
        print(f"Database Error during backup/update: {err}")
        if conn:
            conn.rollback()
        return False
    except Exception as e:
        print(f"An unexpected error occurred: {e}")
        if conn:
            conn.rollback()
        return False
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()


def verify_and_repair():
    """Verifies the inscricoes table for duplicates and removes them, keeping the oldest valid entry."""
    print("Starting verification of the 'inscricoes' table...")
    try:
        # Establish connection
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        print("Successfully connected to the database.\n")
        
        # 1. Identify duplicates based on aluno_id and muralestagio_id
        query_find_duplicates = """
        SELECT aluno_id, muralestagio_id, COUNT(*) as count 
        FROM inscricoes 
        GROUP BY aluno_id, muralestagio_id 
        HAVING COUNT(*) > 1
        """
        
        cursor.execute(query_find_duplicates)
        duplicates = cursor.fetchall()
        
        if not duplicates:
            print("No duplicates found. The 'inscricoes' table is clean.")
            return

        print(f"Found {len(duplicates)} combinations of aluno_id and muralestagio_id with duplicate entries.")
        print("-" * 60)
        
        total_deleted = 0
        affected_aluno_ids = set()

        # 2. Iterate through duplicates and delete the newest ones, keeping the first (oldest) entry
        for dup in duplicates:
            aluno_id = dup['aluno_id']
            muralestagio_id = dup['muralestagio_id']
            count = dup['count']
            
            print(f"Aluno ID: {aluno_id} | MuralEstagio ID: {muralestagio_id} | Total Entries: {count}")
            
            # Fetch all IDs for this specific duplicate combination, ordered by ID ascending
            # This ensures we keep the oldest record and delete the subsequent duplicates
            query_get_ids = """
            SELECT id FROM inscricoes 
            WHERE aluno_id = %s AND muralestagio_id = %s 
            ORDER BY id ASC
            """
            cursor.execute(query_get_ids, (aluno_id, muralestagio_id))
            records = cursor.fetchall()
            
            # Keep the first record (records[0]), delete the rest
            ids_to_delete = [record['id'] for record in records[1:]]
            
            if ids_to_delete:
                # Prepare the DELETE statement
                format_strings = ','.join(['%s'] * len(ids_to_delete))
                query_delete = f"DELETE FROM inscricoes WHERE id IN ({format_strings})"
                
                # Execute deletion
                cursor.execute(query_delete, tuple(ids_to_delete))
                
                deleted_count = cursor.rowcount
                total_deleted += deleted_count
                affected_aluno_ids.add(aluno_id)
                print(f"  -> Deleted {deleted_count} duplicate(s). Removed IDs: {ids_to_delete}")
        
        print("-" * 60)

        # 3. Update the counter cache in the 'alunos' table
        # Since the `InscricoesTable` has a CounterCache behavior for 'inscricao_count', we need to sync it.
        if affected_aluno_ids:
            print("Updating 'inscricao_count' in 'alunos' table to maintain data integrity...")
            for a_id in affected_aluno_ids:
                query_update_counter = """
                UPDATE alunos 
                SET inscricao_count = (SELECT COUNT(*) FROM inscricoes WHERE aluno_id = %s)
                WHERE id = %s
                """
                cursor.execute(query_update_counter, (a_id, a_id))
            print(f"Successfully updated inscricao_count for {len(affected_aluno_ids)} aluno(s).")

        # Commit changes to the database
        conn.commit()
        print(f"\nRepair complete. Successfully deleted a total of {total_deleted} duplicate records.")
    
    except mysql.connector.Error as err:
        print(f"Database Error: {err}")
        print("Please ensure the MySQL server is running and the database credentials are correct.")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")
    finally:
        # Guarantee closure of connections
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()
            print("Database connection closed.")

if __name__ == '__main__':
    # Check if mysql-connector-python is installed
    try:
        import mysql.connector
    except ImportError:
        print("Error: The 'mysql-connector-python' module is not installed.")
        print("Please install it by running: pip install mysql-connector-python")
        sys.exit(1)

    # Step 1: Backup and update aluno_id
    if not backup_and_update_aluno_id():
        print("Backup and update failed. Aborting.")
        sys.exit(1)

    # Step 2: Verify and remove duplicates
    
    verify_and_repair()
