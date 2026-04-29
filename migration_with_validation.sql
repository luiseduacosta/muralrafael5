-- =====================================================
-- MIGRATION SCRIPT FOR MariaDB
-- Compatível com MariaDB 10.2+
-- =====================================================

DELIMITER //

DROP PROCEDURE IF EXISTS RunMigrationWithValidation//
DROP PROCEDURE IF EXISTS DisplayValidationResults//
DROP PROCEDURE IF EXISTS ValidateRequiredTables//
DROP PROCEDURE IF EXISTS ValidateForeignKeyRelationships//
DROP PROCEDURE IF EXISTS SafeRenameColumn//
DROP PROCEDURE IF EXISTS SafeRenameTable//
DROP PROCEDURE IF EXISTS SafeDropTable//
DROP PROCEDURE IF EXISTS SafeDropColumn//
DROP PROCEDURE IF EXISTS SafeAddColumn//
DROP PROCEDURE IF EXISTS SafeModifyColumn//
DROP PROCEDURE IF EXISTS SafeChangeColumn//

-- Helper Procedures for Safe DDL Operations
CREATE PROCEDURE SafeChangeColumn(IN tbl VARCHAR(64), IN old_col VARCHAR(64), IN new_col VARCHAR(64), IN col_def TEXT)
BEGIN
    SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = old_col);
    SET @new_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = new_col);
    IF @col_exists > 0 AND @new_exists = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` CHANGE COLUMN `', old_col, '` `', new_col, '` ', col_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

CREATE PROCEDURE SafeModifyColumn(IN tbl VARCHAR(64), IN col VARCHAR(64), IN col_def TEXT)
BEGIN
    SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col);
    IF @col_exists > 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` MODIFY COLUMN `', col, '` ', col_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

CREATE PROCEDURE SafeAddColumn(IN tbl VARCHAR(64), IN col VARCHAR(64), IN col_def TEXT)
BEGIN
    SET @tbl_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl);
    IF @tbl_exists > 0 THEN
        SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
                           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col);
        IF @col_exists = 0 THEN
            SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', col_def);
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;
    END IF;
END //

CREATE PROCEDURE SafeDropColumn(IN tbl VARCHAR(64), IN col VARCHAR(64))
BEGIN
    SET @tbl_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl);
    IF @tbl_exists > 0 THEN
        SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
                           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col);
        IF @col_exists > 0 THEN
            SET @sql = CONCAT('ALTER TABLE `', tbl, '` DROP COLUMN `', col, '`');
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;
    END IF;
END //

CREATE PROCEDURE SafeRenameTable(IN old_tbl VARCHAR(64), IN new_tbl VARCHAR(64))
BEGIN
    SET @old_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = old_tbl);
    SET @new_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = new_tbl);
    IF @old_exists > 0 AND @new_exists = 0 THEN
        SET @sql = CONCAT('RENAME TABLE `', old_tbl, '` TO `', new_tbl, '`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

CREATE PROCEDURE SafeDropTable(IN tbl VARCHAR(64))
BEGIN
    SET @tbl_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl);
    IF @tbl_exists > 0 THEN
        SET @sql = CONCAT('DROP TABLE `', tbl, '`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

CREATE PROCEDURE SafeRenameColumn(IN tbl VARCHAR(64), IN old_col VARCHAR(64), IN new_col VARCHAR(64))
BEGIN
    DECLARE colExists INT DEFAULT 0;
    DECLARE newExists INT DEFAULT 0;

    DECLARE columnType TEXT;
    DECLARE dataType VARCHAR(64);
    DECLARE isNullable VARCHAR(3);
    DECLARE columnDefault TEXT;
    DECLARE extraInfo TEXT;
    DECLARE columnComment TEXT;
    DECLARE charsetName VARCHAR(64);
    DECLARE collationName VARCHAR(64);

    DECLARE nullSql TEXT;
    DECLARE charsetSql TEXT DEFAULT '';
    DECLARE defaultSql TEXT DEFAULT '';
    DECLARE extraSql TEXT DEFAULT '';
    DECLARE commentSql TEXT DEFAULT '';
    DECLARE colDef TEXT;

    SET colExists = (SELECT COUNT(*) FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = old_col);
    SET newExists = (SELECT COUNT(*) FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = new_col);

    IF colExists > 0 AND newExists = 0 THEN
        SELECT COLUMN_TYPE,
               DATA_TYPE,
               IS_NULLABLE,
               COLUMN_DEFAULT,
               EXTRA,
               COLUMN_COMMENT,
               CHARACTER_SET_NAME,
               COLLATION_NAME
        INTO columnType,
             dataType,
             isNullable,
             columnDefault,
             extraInfo,
             columnComment,
             charsetName,
             collationName
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = tbl
          AND COLUMN_NAME = old_col
        LIMIT 1;

        SET nullSql = IF(isNullable = 'YES', 'NULL', 'NOT NULL');

        IF charsetName IS NOT NULL AND dataType IN ('char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext', 'enum', 'set') THEN
            SET charsetSql = CONCAT(' CHARACTER SET ', charsetName, ' COLLATE ', collationName);
        END IF;

        IF columnDefault IS NOT NULL THEN
            IF LOWER(columnDefault) LIKE 'current_timestamp%' OR UPPER(columnDefault) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()') THEN
                SET defaultSql = CONCAT(' DEFAULT ', columnDefault);
            ELSEIF dataType IN ('tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint', 'decimal', 'numeric', 'float', 'double', 'real', 'bit') THEN
                SET defaultSql = CONCAT(' DEFAULT ', columnDefault);
            ELSE
                SET defaultSql = CONCAT(' DEFAULT ''', REPLACE(columnDefault, '''', ''''''), '''');
            END IF;
        END IF;

        IF extraInfo IS NOT NULL AND LENGTH(extraInfo) > 0 THEN
            SET extraSql = CONCAT(' ', extraInfo);
        END IF;

        IF columnComment IS NOT NULL AND LENGTH(columnComment) > 0 THEN
            SET commentSql = CONCAT(' COMMENT ''', REPLACE(columnComment, '''', ''''''), '''');
        END IF;

        SET colDef = CONCAT(columnType, charsetSql, ' ', nullSql, defaultSql, extraSql, commentSql);

        SET @sql = CONCAT('ALTER TABLE `', tbl, '` CHANGE COLUMN `', old_col, '` `', new_col, '` ', colDef);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

-- Procedure to check foreign key relationships
CREATE PROCEDURE ValidateForeignKeyRelationships()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE constraint_name VARCHAR(255);
    DECLARE table_name VARCHAR(255);
    DECLARE column_name VARCHAR(255);
    DECLARE referenced_table VARCHAR(255);
    DECLARE referenced_column VARCHAR(255);
    DECLARE orphan_count INT;
    
    DECLARE fk_cursor CURSOR FOR
        SELECT kcu.CONSTRAINT_NAME,
               kcu.TABLE_NAME,
               kcu.COLUMN_NAME,
               kcu.REFERENCED_TABLE_NAME,
               kcu.REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE kcu
        JOIN information_schema.TABLES rt
          ON rt.TABLE_SCHEMA = kcu.CONSTRAINT_SCHEMA
         AND rt.TABLE_NAME = kcu.REFERENCED_TABLE_NAME
        JOIN information_schema.COLUMNS rc
          ON rc.TABLE_SCHEMA = kcu.CONSTRAINT_SCHEMA
         AND rc.TABLE_NAME = kcu.REFERENCED_TABLE_NAME
         AND rc.COLUMN_NAME = kcu.REFERENCED_COLUMN_NAME
        WHERE kcu.CONSTRAINT_SCHEMA = DATABASE()
          AND kcu.REFERENCED_TABLE_NAME IS NOT NULL;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Check missing referenced tables
    INSERT INTO validation_errors (error_type, error_message, severity)
    SELECT 'MISSING_REFERENCED_TABLE', 
           CONCAT('Foreign key ',
                  COALESCE(CONSTRAINT_NAME, '(unknown)'),
                  ' references missing table: ',
                  COALESCE(REFERENCED_TABLE_NAME, '(unknown)')), 
           'WARNING'
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES 
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = REFERENCED_TABLE_NAME);
    
    -- Check orphaned records
    OPEN fk_cursor;
    read_loop: LOOP
        FETCH fk_cursor INTO constraint_name, table_name, column_name, referenced_table, referenced_column;
        IF done THEN LEAVE read_loop; END IF;
        
        SET @orphan_count = 0;
        SET @sql_check = '';

        IF NOT EXISTS (
            SELECT 1 FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = referenced_table
        ) THEN
            ITERATE read_loop;
        END IF;

        SET @sql_check = CONCAT('SELECT COUNT(*) INTO @orphan_count FROM `', table_name, '` t1 
                                LEFT JOIN `', referenced_table, '` t2 ON t1.`', column_name, '` = t2.`', referenced_column, '` 
                                WHERE t1.`', column_name, '` IS NOT NULL AND t2.`', referenced_column, '` IS NULL');
        PREPARE stmt FROM @sql_check;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        
        IF @orphan_count > 0 THEN
            INSERT INTO validation_errors (error_type, error_message, severity)
            VALUES ('ORPHAN_RECORDS', 
                    CONCAT('Table `', COALESCE(table_name, '(unknown)'), '`.`', COALESCE(column_name, '(unknown)'),
                           '` has ', @orphan_count, ' orphan records referencing `', COALESCE(referenced_table, '(unknown)'), '`'),
                    'WARNING');
        END IF;
    END LOOP;
    CLOSE fk_cursor;
    
    -- Check data type mismatches
    INSERT INTO validation_errors (error_type, error_message, severity)
    SELECT 'DATA_TYPE_MISMATCH',
           CONCAT('Foreign key ', COALESCE(kcu.CONSTRAINT_NAME, '(unknown)'), ': `',
                  COALESCE(kcu.TABLE_NAME, '(unknown)'), '.', COALESCE(kcu.COLUMN_NAME, '(unknown)'),
                  '` (', COALESCE(c1.DATA_TYPE, '(unknown)'),
                  ') does not match referenced `', COALESCE(kcu.REFERENCED_TABLE_NAME, '(unknown)'),
                  '.', COALESCE(kcu.REFERENCED_COLUMN_NAME, '(unknown)'),
                  '` (', COALESCE(c2.DATA_TYPE, '(unknown)'), ')'),
           'ERROR'
    FROM information_schema.KEY_COLUMN_USAGE kcu
    JOIN information_schema.COLUMNS c1 ON c1.TABLE_SCHEMA = kcu.CONSTRAINT_SCHEMA 
        AND c1.TABLE_NAME = kcu.TABLE_NAME AND c1.COLUMN_NAME = kcu.COLUMN_NAME
    JOIN information_schema.COLUMNS c2 ON c2.TABLE_SCHEMA = kcu.CONSTRAINT_SCHEMA 
        AND c2.TABLE_NAME = kcu.REFERENCED_TABLE_NAME AND c2.COLUMN_NAME = kcu.REFERENCED_COLUMN_NAME
    WHERE kcu.CONSTRAINT_SCHEMA = DATABASE() AND c1.DATA_TYPE != c2.DATA_TYPE;
END //

CREATE PROCEDURE ValidateRequiredTables()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) 
        VALUES ('MISSING_TABLE', 'Required table `users` does not exist', 'ERROR');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) 
        VALUES ('MISSING_TABLE', 'Required table `alunos` does not exist', 'WARNING');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) 
        VALUES ('MISSING_TABLE', 'Required table `professores` does not exist', 'WARNING');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) 
        VALUES ('MISSING_TABLE', 'Required table `supervisores` does not exist', 'WARNING');
    END IF;
END //

CREATE PROCEDURE DisplayValidationResults()
BEGIN
    DECLARE error_count INT DEFAULT 0;
    DECLARE warning_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO error_count FROM validation_errors WHERE severity = 'ERROR';
    SELECT COUNT(*) INTO warning_count FROM validation_errors WHERE severity = 'WARNING';
    
    SELECT '========================================' AS '';
    SELECT 'VALIDATION RESULTS' AS '';
    SELECT '========================================' AS '';
    SELECT CONCAT('Total Errors: ', error_count) AS '';
    SELECT CONCAT('Total Warnings: ', warning_count) AS '';
    SELECT '' AS '';
    
    IF error_count > 0 THEN
        SELECT 'ERRORS FOUND:' AS '';
        SELECT error_message FROM validation_errors WHERE severity = 'ERROR';
        SELECT '' AS '';
    END IF;
    
    IF warning_count > 0 THEN
        SELECT 'WARNINGS FOUND:' AS '';
        SELECT error_message FROM validation_errors WHERE severity = 'WARNING';
        SELECT '' AS '';
    END IF;
    
    IF error_count = 0 AND warning_count = 0 THEN
        SELECT '✓ All validation checks passed!' AS '';
    END IF;
END //

CREATE PROCEDURE RunMigrationWithValidation()
main: BEGIN
    DECLARE errorCount INT DEFAULT 0;
    DECLARE hasCategoria INT DEFAULT 0;
    DECLARE hasEntidadeId INT DEFAULT 0;
    DECLARE hasAlunoId INT DEFAULT 0;
    DECLARE hasProfessorId INT DEFAULT 0;
    DECLARE hasSupervisorId INT DEFAULT 0;
    DECLARE hasTurno INT DEFAULT 0;
    DECLARE hasAlunonovoId INT DEFAULT 0;
    DECLARE hasAlunoIdInscricoes INT DEFAULT 0;
    DECLARE hasProfDddTelefone INT DEFAULT 0;
    DECLARE hasProfDddCelular INT DEFAULT 0;
    DECLARE hasSupCodigoCelOld INT DEFAULT 0;
    DECLARE hasSupCodigoTelOld INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        DECLARE errNo INT DEFAULT 0;
        DECLARE errState CHAR(5) DEFAULT '00000';
        DECLARE errMsg TEXT DEFAULT '';

        GET DIAGNOSTICS CONDITION 1
            errNo = MYSQL_ERRNO,
            errState = RETURNED_SQLSTATE,
            errMsg = MESSAGE_TEXT;

        SELECT '========================================' AS '';
        SELECT '❌ MIGRATION FAILED' AS '';
        SELECT '========================================' AS '';
        SELECT 'STEP' AS '';
        SELECT COALESCE(@migration_step, '') AS '';
        SELECT 'ERROR' AS '';
        SELECT CONCAT(errState, ' ', errNo, ' ', errMsg) AS '';
        SELECT 'LAST SQL (dynamic)' AS '';
        SELECT COALESCE(@sql, '') AS '';
        SELECT 'LAST SQL (validation)' AS '';
        SELECT COALESCE(@sql_check, '') AS '';
        ROLLBACK;
        RESIGNAL;
    END;

    SET @migration_step = 'init';

    CREATE TEMPORARY TABLE IF NOT EXISTS validation_errors (
        error_id INT AUTO_INCREMENT PRIMARY KEY,
        error_type VARCHAR(100),
        error_message TEXT,
        severity VARCHAR(20)
    );

    SET @migration_step = 'truncate_validation_errors';
    TRUNCATE TABLE validation_errors;

    SET @migration_step = 'start_transaction';
    START TRANSACTION;

    SET @migration_step = 'pre_validation_required_tables';
    CALL ValidateRequiredTables();
    SET @migration_step = 'pre_validation_foreign_keys';
    CALL ValidateForeignKeyRelationships();
    SET @migration_step = 'pre_validation_display';
    CALL DisplayValidationResults();

    SET @migration_step = 'check_validation_errors';
    SELECT COUNT(*) INTO errorCount FROM validation_errors WHERE severity = 'ERROR';
    IF errorCount > 0 THEN
        SELECT '❌ Migration aborted due to validation errors.' AS '';
        SELECT error_type, severity, error_message FROM validation_errors WHERE severity = 'ERROR';
        ROLLBACK;
        DROP TEMPORARY TABLE IF EXISTS validation_errors;
        LEAVE main;
    END IF;

    SET @migration_step = 'backup_table';
    CREATE TABLE IF NOT EXISTS migration_backup_20260414 (
        backup_info VARCHAR(100),
        backup_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    SET @migration_step = 'backup_insert';
    INSERT INTO migration_backup_20260414 (backup_info) VALUES ('Migration started at');

    -- Alunos
    SET @migration_step = 'alunos';
    -- Only drop alunos and rename from alunosnovos if alunosnovos exists (migration scenario)
    IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos')
       AND EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunosnovos') THEN
        CALL SafeDropTable('alunos');
        CALL SafeRenameTable('alunosnovos', 'alunos');
    END IF;
    -- Only process alunos if table exists after rename
    IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos') THEN
        CALL SafeAddColumn('alunos', 'turno_id', 'smallint(3) NULL');
        CALL SafeAddColumn('alunos', 'user_id', 'int(11) NULL');
        CALL SafeAddColumn('alunos', 'inscricao_count', 'int(11) NULL COMMENT "Quantidade de inscrições do aluno"');

        CALL SafeModifyColumn('alunos', 'cpf', 'varchar(15) NULL');
        CALL SafeModifyColumn('alunos', 'telefone', 'varchar(15) NULL');
        CALL SafeModifyColumn('alunos', 'celular', 'varchar(15) NULL');

        SET hasTurno = (SELECT COUNT(*) FROM information_schema.COLUMNS
                        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos' AND COLUMN_NAME = 'turno');
        IF hasTurno > 0 THEN
            UPDATE `alunos` a
            INNER JOIN `turnos` t ON t.`turno` = a.`turno`
            SET a.`turno_id` = t.`id`;
        END IF;

        CALL SafeChangeColumn('alunos', 'ddd_telefone', 'codigo_telefone', 'tinyint(2) NULL DEFAULT 21');
        CALL SafeChangeColumn('alunos', 'ddd_celular', 'codigo_celular', 'tinyint(2) NULL DEFAULT 21');

        -- Zerar telefone com menos de 8 dígitos
        UPDATE `alunos`
        set `telefone` = ''
        WHERE char_length(telefone) < 8 OR telefone IS NULL;

        UPDATE `alunos`
            SET `telefone` = CONCAT('(', LPAD(codigo_telefone, 2, '0'), ') ', telefone)
            WHERE codigo_telefone IS NOT NULL
              AND codigo_telefone REGEXP '^[0-9]{2}$'
              AND telefone IS NOT NULL
              AND telefone != ''
              AND LENGTH(telefone) IN (8, 9)
              AND telefone NOT LIKE CONCAT('(', codigo_telefone, ')%');

        -- Zerar celular com menos de 8 dígitos
        UPDATE `alunos`
        set `celular` = ''
        WHERE char_length(celular) < 8 OR celular IS NULL;

        UPDATE `alunos`
            SET `celular` = CONCAT('(', LPAD(codigo_celular, 2, '0'), ') ', celular)
            WHERE codigo_celular IS NOT NULL
              AND codigo_celular REGEXP '^[0-9]{2}$'
              AND celular IS NOT NULL
              AND celular != ''
              AND LENGTH(celular) IN (8, 9, 10)
              AND celular NOT LIKE CONCAT('(', codigo_celular, ')%');
    END IF;

    -- Professores
    SET @migration_step = 'professores';
    CALL SafeAddColumn('professores', 'cress', 'varchar(10) NULL');
    CALL SafeAddColumn('professores', 'regiao', 'varchar(2) NULL');
    CALL SafeAddColumn('professores', 'user_id', 'int(11) NULL');

    CALL SafeDropColumn('professores', 'datanascimento');
    CALL SafeDropColumn('professores', 'localnascimento');
    CALL SafeDropColumn('professores', 'sexo');
    CALL SafeDropColumn('professores', 'homepage');
    CALL SafeDropColumn('professores', 'redesocial');
    CALL SafeDropColumn('professores', 'curriculosigma');
    CALL SafeDropColumn('professores', 'pesquisadordgp');
    CALL SafeDropColumn('professores', 'formacaoprofissional');
    CALL SafeDropColumn('professores', 'universidadedegraduacao');
    CALL SafeDropColumn('professores', 'anoformacao');
    CALL SafeDropColumn('professores', 'mestradoarea');
    CALL SafeDropColumn('professores', 'mestradouniversidade');
    CALL SafeDropColumn('professores', 'mestradoanoconclusao');
    CALL SafeDropColumn('professores', 'doutoradoarea');
    CALL SafeDropColumn('professores', 'doutoradouniversidade');
    CALL SafeDropColumn('professores', 'doutoradoanoconclusao');
    CALL SafeDropColumn('professores', 'formaingresso');
    CALL SafeDropColumn('professores', 'tipocargo');
    CALL SafeDropColumn('professores', 'categoria');
    CALL SafeDropColumn('professores', 'regimetrabalho');

    CALL SafeModifyColumn('professores', 'cpf', 'varchar(15) NULL');
    CALL SafeModifyColumn('professores', 'telefone', 'varchar(15) NULL');
    CALL SafeModifyColumn('professores', 'celular', 'varchar(15) NULL');

    SET hasProfDddTelefone = (SELECT COUNT(*) FROM information_schema.COLUMNS
                             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'ddd_telefone');
    IF hasProfDddTelefone > 0 THEN
        UPDATE `professores`
        SET `ddd_telefone` = NULL
        WHERE TRIM(CAST(`ddd_telefone` AS CHAR)) = '';
    END IF;

    SET hasProfDddCelular = (SELECT COUNT(*) FROM information_schema.COLUMNS
                             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'ddd_celular');
    IF hasProfDddCelular > 0 THEN
        UPDATE `professores`
        SET `ddd_celular` = NULL
        WHERE TRIM(CAST(`ddd_celular` AS CHAR)) = '';
    END IF;

    CALL SafeChangeColumn('professores', 'ddd_telefone', 'codigo_telefone', 'tinyint(2) NULL DEFAULT 21');
    CALL SafeChangeColumn('professores', 'ddd_celular', 'codigo_celular', 'tinyint(2) NULL DEFAULT 21');

    -- Zerar telefone com menos de 8 dígitos
    UPDATE `professores` 
    set `telefone` = '' 
    WHERE char_length(telefone) < 8 OR telefone IS NULL;

    -- Atualizar telefone
    UPDATE `professores`
        SET `telefone` = CONCAT('(', LPAD(codigo_telefone, 2, '0'), ') ', telefone)
        WHERE codigo_telefone IS NOT NULL
          AND codigo_telefone REGEXP '^[0-9]{2}$'
          AND telefone IS NOT NULL
          AND telefone != ''
          AND LENGTH(telefone) IN (8, 9)
          AND telefone NOT LIKE CONCAT('(', codigo_telefone, ')%');

    -- Zerar celular com menos de 8 dígitos
    UPDATE `professores` 
    set `celular` = '' 
    WHERE char_length(celular) < 8 OR celular IS NULL;
    
    -- Professores: Atualizar celular
    UPDATE `professores`
        SET `celular` = CONCAT('(', LPAD(codigo_celular, 2, '0'), ') ', celular)
        WHERE codigo_celular IS NOT NULL
          AND codigo_celular REGEXP '^[0-9]{2}$'
          AND celular IS NOT NULL
          AND celular != ''
          AND LENGTH(celular) IN (8, 9, 10)
          AND celular NOT LIKE CONCAT('(', codigo_celular, ')%');

    -- Supervisores
    SET @migration_step = 'supervisores';
    CALL SafeAddColumn('supervisores', 'user_id', 'int(11) NULL');

    CALL SafeDropColumn('supervisores', 'outros_estudos');
    CALL SafeDropColumn('supervisores', 'area_curso');
    CALL SafeDropColumn('supervisores', 'ano_curso');
    CALL SafeDropColumn('supervisores', 'num_inscricao');
    CALL SafeDropColumn('supervisores', 'curso_turma');
    CALL SafeDropColumn('supervisores', 'endereco');
    CALL SafeDropColumn('supervisores', 'bairro');
    CALL SafeDropColumn('supervisores', 'municipio');
    CALL SafeDropColumn('supervisores', 'cep');

    CALL SafeModifyColumn('supervisores', 'cpf', 'varchar(15) NULL');
    CALL SafeModifyColumn('supervisores', 'telefone', 'varchar(15) NULL');
    CALL SafeModifyColumn('supervisores', 'celular', 'varchar(15) NULL');
    CALL SafeModifyColumn('supervisores', 'escola', 'varchar(70) NULL');

    -- Supervisores: Renomear codigo_tel e codigo_cel para codigo_telefone e codigo_celular
    CALL SafeChangeColumn('supervisores', 'codigo_tel', 'codigo_telefone', 'tinyint(2) NULL DEFAULT 21');
    CALL SafeChangeColumn('supervisores', 'codigo_cel', 'codigo_celular', 'tinyint(2) NULL DEFAULT 21');

    -- Zerar telefone com menos de 8 dígitos
    UPDATE `supervisores` 
    set `telefone` = '' 
    WHERE char_length(telefone) < 8 OR telefone IS NULL;

    -- Atualizar telefone
    UPDATE `supervisores`
        SET `telefone` = CONCAT('(', LPAD(codigo_telefone, 2, '0'), ') ', telefone)
        WHERE codigo_telefone IS NOT NULL
          AND codigo_telefone REGEXP '^[0-9]{2}$'
          AND telefone IS NOT NULL
          AND telefone != ''
          AND LENGTH(telefone) IN (8, 9)
          AND telefone NOT LIKE CONCAT('(', codigo_telefone, ')%');

    -- Zerar celular com menos de 8 dígitos
    UPDATE `supervisores` 
    set `celular` = '' 
    WHERE char_length(celular) < 8 OR celular IS NULL;

    -- Atualizar celular
    UPDATE `supervisores`
        SET `celular` = CONCAT('(', LPAD(codigo_celular, 2, '0'), ') ', celular)
        WHERE codigo_celular IS NOT NULL
          AND codigo_celular REGEXP '^[0-9]{2}$'
          AND celular IS NOT NULL
          AND celular != ''
          AND LENGTH(celular) IN (8, 9, 10)
          AND celular NOT LIKE CONCAT('(', codigo_celular, ')%');

    CALL SafeChangeColumn('supervisores', 'ano_formatura', 'ano_formacao', 'smallint(4) NULL');

    -- Users
    SET @migration_step = 'users';
    SET @migration_step = 'users_add_columns';
    CALL SafeAddColumn('users', 'nome', 'varchar(128) NOT NULL COMMENT ''Nome do usuário''');
    CALL SafeAddColumn('users', 'role', 'enum(''admin'',''aluno'',''professor'',''supervisor'') NOT NULL DEFAULT ''aluno'' COMMENT ''roles''');
    CALL SafeAddColumn('users', 'entidade_id', 'int(11) DEFAULT NULL COMMENT ''id da entidade''');
    CALL SafeAddColumn('users', 'ativo', 'tinyint(1) DEFAULT 1');
    CALL SafeAddColumn('users', 'criado_em', 'timestamp NOT NULL DEFAULT current_timestamp()');

    SET @migration_step = 'users_rename_columns';
    CALL SafeChangeColumn('users', 'numero', 'identificacao', 'int(9) DEFAULT NULL COMMENT ''Registro do aluno, SIAPE do professor ou CRESS do supervisor''');
    CALL SafeChangeColumn('users', 'timestamp', 'atualizado_em', 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()');
    CALL SafeChangeColumn('users', 'estudante_id', 'aluno_id', 'int(11) DEFAULT NULL');
    CALL SafeChangeColumn('users', 'docente_id', 'professor_id', 'int(11) DEFAULT NULL');

    SET @migration_step = 'users_update_roles';
    SET hasCategoria = (SELECT COUNT(*) FROM information_schema.COLUMNS
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'categoria');
    IF hasCategoria > 0 THEN
        UPDATE `users` SET `role` = CASE `categoria`
            WHEN '1' THEN 'admin'
            WHEN '2' THEN 'aluno'
            WHEN '3' THEN 'professor'
            WHEN '4' THEN 'supervisor'
            ELSE `role`
        END WHERE `categoria` IN ('1', '2', '3', '4');
    END IF;

    SET @migration_step = 'users_update_entidade_id';
    SET hasEntidadeId = (SELECT COUNT(*) FROM information_schema.COLUMNS
                         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'entidade_id');
    SET hasAlunoId = (SELECT COUNT(*) FROM information_schema.COLUMNS
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'aluno_id');
    SET hasProfessorId = (SELECT COUNT(*) FROM information_schema.COLUMNS
                          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'professor_id');
    SET hasSupervisorId = (SELECT COUNT(*) FROM information_schema.COLUMNS
                           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'supervisor_id');

    IF hasEntidadeId > 0 AND hasAlunoId > 0 THEN
        UPDATE `users` SET `entidade_id` = `aluno_id` WHERE `role` = 'aluno' AND `aluno_id` IS NOT NULL;
    END IF;
    IF hasEntidadeId > 0 AND hasProfessorId > 0 THEN
        UPDATE `users` SET `entidade_id` = `professor_id` WHERE `role` = 'professor' AND `professor_id` IS NOT NULL;
    END IF;
    IF hasEntidadeId > 0 AND hasSupervisorId > 0 THEN
        UPDATE `users` SET `entidade_id` = `supervisor_id` WHERE `role` = 'supervisor' AND `supervisor_id` IS NOT NULL;
    END IF;

    SET @migration_step = 'users_update_nome';
    IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos')
       AND hasAlunoId > 0 THEN
        UPDATE `users` u
        JOIN `alunos` a ON u.`aluno_id` = a.`id`
        SET u.`nome` = a.`nome`
        WHERE u.`role` = 'aluno';
    END IF;

    IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores')
       AND hasProfessorId > 0 THEN
        UPDATE `users` u
        JOIN `professores` p ON u.`professor_id` = p.`id`
        SET u.`nome` = p.`nome`
        WHERE u.`role` = 'professor';
    END IF;

    IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores')
       AND hasSupervisorId > 0 THEN
        UPDATE `users` u
        JOIN `supervisores` s ON u.`supervisor_id` = s.`id`
        SET u.`nome` = s.`nome`
        WHERE u.`role` = 'supervisor';
    END IF;

    -- Instituicoes
    SET @migration_step = 'instituicoes';
    CALL SafeRenameTable('estagio', 'instituicoes');
    CALL SafeRenameColumn('instituicoes', 'area', 'area_id');
    CALL SafeModifyColumn('instituicoes', 'convenio', 'int(4) NULL');
    CALL SafeDropColumn('instituicoes', 'avaliacao');
    CALL SafeDropColumn('instituicoes', 'localInscricao');
    CALL SafeDropColumn('instituicoes', 'fax');

    -- Inst_super
    SET @migration_step = 'inst_super';
    CALL SafeRenameColumn('inst_super', 'id_supervisor', 'supervisor_id');
    CALL SafeRenameColumn('inst_super', 'id_instituicao', 'instituicao_id');

    -- Configuracoes
    SET @migration_step = 'configuracoes';
    CALL SafeRenameTable('configuracao', 'configuracoes');
    CALL SafeAddColumn('configuracoes', 'instituicao', 'varchar(50) NOT NULL');

    -- Mural_estagios
    SET @migration_step = 'mural_estagios';
    CALL SafeRenameTable('mural_estagio', 'mural_estagios');
    CALL SafeRenameColumn('mural_estagios', 'dataSelecao', 'data_selecao');
    CALL SafeRenameColumn('mural_estagios', 'cargaHoraria', 'carga_horaria');
    CALL SafeRenameColumn('mural_estagios', 'dataInscricao', 'data_inscricao');
    CALL SafeRenameColumn('mural_estagios', 'horarioSelecao', 'horario_selecao');
    CALL SafeRenameColumn('mural_estagios', 'localSelecao', 'local_selecao');
    CALL SafeRenameColumn('mural_estagios', 'formaSelecao', 'forma_selecao');
    CALL SafeRenameColumn('mural_estagios', 'localInscricao', 'local_inscricao');
    CALL SafeRenameColumn('mural_estagios', 'id_estagio', 'instituicao_id');
    CALL SafeDropColumn('mural_estagios', 'area');
    CALL SafeDropColumn('mural_estagios', 'id_area');
    CALL SafeDropColumn('mural_estagios', 'datafax');

    -- Turnos
    SET @migration_step = 'turnos';
    CREATE TABLE IF NOT EXISTS `turnos` (
      `id` smallint(3) NOT NULL AUTO_INCREMENT,
      `turno` varchar(70) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    -- INSERT IGNORE INTO `turnos` (`id`, `turno`) VALUES (1, 'diurno'), (2, 'noturno'), (3, 'integral'), (4, 'outro');

    -- Turmas
    SET @migration_step = 'turmas';
    CALL SafeRenameTable('turma_estagios', 'turmas');
    CALL SafeRenameColumn('turmas', 'turam', 'turma');

    -- Estagiarios
    SET @migration_step = 'estagiarios';
    CALL SafeRenameColumn('estagiarios', 'alunonovo_id', 'aluno_id');
    CALL SafeRenameColumn('estagiarios', 'id_instituicao', 'instituicao_id');
    CALL SafeRenameColumn('estagiarios', 'id_supervisor', 'supervisor_id');
    CALL SafeRenameColumn('estagiarios', 'id_professor', 'professor_id');
    CALL SafeDropColumn('estagiarios', 'id_aluno');
    CALL SafeDropColumn('estagiarios', 'id_area');
    CALL SafeDropColumn('estagiarios', 'turno');

    -- Turma_estagios
    SET @migration_step = 'turma_estagios';
    CALL SafeRenameTable('areas_estagio', 'turma_estagios');

    -- Questionarios
    SET @migration_step = 'questionarios';
    CREATE TABLE IF NOT EXISTS `questionarios` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `created` datetime NOT NULL,
      `modified` datetime NOT NULL,
      `is_active` tinyint(1) NOT NULL,
      `category` varchar(100) NOT NULL,
      `target_user_type` varchar(50) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Questoes
    CREATE TABLE IF NOT EXISTS `questoes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `questionario_id` int(11) NOT NULL,
      `text` text NOT NULL,
      `type` varchar(50) NOT NULL,
      `options` text NOT NULL,
      `created` timestamp NOT NULL DEFAULT current_timestamp(),
      `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `ordem` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `questionnaire_id` (`questionario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Respostas
    CREATE TABLE IF NOT EXISTS `respostas` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `questionario_id` int(11) NOT NULL,
      `estagiario_id` int(11) NOT NULL,
      `response` text NOT NULL,
      `created` timestamp NOT NULL DEFAULT current_timestamp(),
      `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `estagiarios_id` (`estagiario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Visitas
    SET @migration_step = 'visitas';
    CALL SafeRenameTable('visita', 'visitas');
    CALL SafeChangeColumn('visitas', 'estagio_id', 'instituicao_id', 'int(11) NOT NULL');

    -- Inscricoes
    SET @migration_step = 'inscricoes';
    CALL SafeRenameTable('mural_inscricao', 'inscricoes');
    CALL SafeRenameColumn('inscricoes', 'id_aluno', 'registro');
    CALL SafeRenameColumn('inscricoes', 'id_instituicao', 'muralestagio_id');

    CALL SafeAddColumn('inscricoes', 'aluno_id', 'int(11) NULL');
    SET hasAlunonovoId = (SELECT COUNT(*) FROM information_schema.COLUMNS
                          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inscricoes' AND COLUMN_NAME = 'alunonovo_id');
    SET hasAlunoIdInscricoes = (SELECT COUNT(*) FROM information_schema.COLUMNS
                                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inscricoes' AND COLUMN_NAME = 'aluno_id');
    IF hasAlunonovoId > 0 AND hasAlunoIdInscricoes > 0 THEN
        UPDATE `inscricoes`
        SET `aluno_id` = `alunonovo_id`;
    END IF;
    CALL SafeDropColumn('inscricoes', 'alunonovo_id');

    -- Areas
    SET @migration_step = 'areas';
    CALL SafeRenameTable('area_instituicoes', 'areas');

    -- Administradores
    SET @migration_step = 'administradores';
    CREATE TABLE IF NOT EXISTS `administradores` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `nome` VARCHAR(128) NOT NULL,
      `user_id` INT(11) NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Impersonations
    SET @migration_step = 'impersonations';
    CREATE TABLE IF NOT EXISTS `impersonations` (
        `id` int(11) NOT NULL,
        `admin_id` int(11) NOT NULL,
        `impersonated_user_id` int(11) NOT NULL,
        `started_at` timestamp NULL DEFAULT current_timestamp(),
        `ended_at` timestamp NULL DEFAULT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`),
        KEY `admin_id` (`admin_id`),
        KEY `impersonated_user_id` (`impersonated_user_id`),
        CONSTRAINT `impersonations_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
        CONSTRAINT `impersonations_ibfk_2` FOREIGN KEY (`impersonated_user_id`) REFERENCES `users` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    SET @migration_step = 'post_validation';
    SELECT '========================================' AS '';
    SELECT 'POST-MIGRATION VALIDATION' AS '';
    SELECT '========================================' AS '';

    SELECT COUNT(*) AS tables_processed
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME IN ('users', 'instituicoes', 'configuracoes', 'mural_estagios',
                         'turnos', 'estagiarios', 'professores', 'supervisores',
                         'visitas', 'alunos', 'inscricoes', 'areas');

    SELECT 'users' AS table_name, COUNT(*) AS record_count FROM `users`
    UNION ALL SELECT 'alunos', COUNT(*) FROM `alunos`
    UNION ALL SELECT 'professores', COUNT(*) FROM `professores`
    UNION ALL SELECT 'supervisores', COUNT(*) FROM `supervisores`;

    UPDATE migration_backup_20260414
    SET backup_time = CURRENT_TIMESTAMP
    WHERE backup_info = 'Migration started at';

    SELECT '========================================' AS '';
    SELECT '✅ MIGRATION COMPLETED SUCCESSFULLY' AS '';
    SELECT '========================================' AS '';

    COMMIT;
END //

DELIMITER ;

SELECT '========================================' AS '';
SELECT 'DRY RUN - PLANNED DDL (information_schema)' AS '';
SELECT '========================================' AS '';

SELECT ddl
FROM (
    SELECT 10 AS seq, CONCAT('ALTER TABLE `users` ADD COLUMN `nome` varchar(128) NOT NULL COMMENT ''Nome do usuário'';') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'nome')

    UNION ALL
    SELECT 20 AS seq, CONCAT('ALTER TABLE `users` ADD COLUMN `role` enum(''admin'',''aluno'',''professor'',''supervisor'') NOT NULL DEFAULT ''aluno'' COMMENT ''roles'';') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role')

    UNION ALL
    SELECT 30 AS seq, CONCAT('ALTER TABLE `users` ADD COLUMN `entidade_id` int(11) DEFAULT NULL COMMENT ''id da entidade'';') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'entidade_id')

    UNION ALL
    SELECT 40 AS seq, CONCAT('ALTER TABLE `users` ADD COLUMN `ativo` tinyint(1) DEFAULT 1;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'ativo')

    UNION ALL
    SELECT 50 AS seq, CONCAT('ALTER TABLE `users` ADD COLUMN `criado_em` timestamp NOT NULL DEFAULT current_timestamp();') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'criado_em')

    UNION ALL
    SELECT 60 AS seq, CONCAT('ALTER TABLE `users` CHANGE COLUMN `numero` `identificacao` int(9) DEFAULT NULL COMMENT ''Registro do aluno, SIAPE do professor ou CRESS do supervisor'';') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'numero')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'identificacao')

    UNION ALL
    SELECT 70 AS seq, CONCAT('ALTER TABLE `users` CHANGE COLUMN `timestamp` `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'timestamp')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'atualizado_em')

    UNION ALL
    SELECT 80 AS seq, CONCAT('ALTER TABLE `users` CHANGE COLUMN `estudante_id` `aluno_id` int(11) DEFAULT NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'estudante_id')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'aluno_id')

    UNION ALL
    SELECT 90 AS seq, CONCAT('ALTER TABLE `users` CHANGE COLUMN `docente_id` `professor_id` int(11) DEFAULT NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'docente_id')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'professor_id')

    UNION ALL
    SELECT 100 AS seq, CONCAT('RENAME TABLE `estagio` TO `instituicoes`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagio')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'instituicoes')

    UNION ALL
    SELECT 110 AS seq,
           CONCAT(
               'ALTER TABLE `instituicoes` CHANGE COLUMN `area` `area_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('instituicoes', 'estagio')
        ORDER BY FIELD(TABLE_NAME, 'instituicoes', 'estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'area'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'area_id'
    )

    UNION ALL
    SELECT 120 AS seq, CONCAT('ALTER TABLE `instituicoes` DROP COLUMN `avaliacao`;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('instituicoes', 'estagio')
              ORDER BY FIELD(TABLE_NAME, 'instituicoes', 'estagio')
              LIMIT 1
          )
          AND COLUMN_NAME = 'avaliacao'
    )

    UNION ALL
    SELECT 130 AS seq, CONCAT('ALTER TABLE `instituicoes` DROP COLUMN `localInscricao`;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('instituicoes', 'estagio')
              ORDER BY FIELD(TABLE_NAME, 'instituicoes', 'estagio')
              LIMIT 1
          )
          AND COLUMN_NAME = 'localInscricao'
    )

    UNION ALL
    SELECT 140 AS seq, CONCAT('ALTER TABLE `instituicoes` DROP COLUMN `fax`;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('instituicoes', 'estagio')
              ORDER BY FIELD(TABLE_NAME, 'instituicoes', 'estagio')
              LIMIT 1
          )
          AND COLUMN_NAME = 'fax'
    )

    UNION ALL
    SELECT 150 AS seq, CONCAT('RENAME TABLE `configuracao` TO `configuracoes`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'configuracao')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'configuracoes')

    UNION ALL
    SELECT 160 AS seq, CONCAT('ALTER TABLE `configuracoes` ADD COLUMN `instituicao` varchar(50) NOT NULL;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME IN ('configuracoes', 'configuracao')
    )
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('configuracoes', 'configuracao')
              ORDER BY FIELD(TABLE_NAME, 'configuracoes', 'configuracao')
              LIMIT 1
          )
          AND COLUMN_NAME = 'instituicao'
    )

    UNION ALL
    SELECT 170 AS seq, CONCAT('RENAME TABLE `mural_estagio` TO `mural_estagios`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mural_estagio')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mural_estagios')

    UNION ALL
    SELECT 180 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `dataSelecao` `data_selecao` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'dataSelecao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'data_selecao'
    )

    UNION ALL
    SELECT 190 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `cargaHoraria` `carga_horaria` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'cargaHoraria'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'carga_horaria'
    )

    UNION ALL
    SELECT 200 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `dataInscricao` `data_inscricao` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'dataInscricao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'data_inscricao'
    )

    UNION ALL
    SELECT 210 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `horarioSelecao` `horario_selecao` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'horarioSelecao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'horario_selecao'
    )

    UNION ALL
    SELECT 220 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `localSelecao` `local_selecao` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'localSelecao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'local_selecao'
    )

    UNION ALL
    SELECT 230 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `formaSelecao` `forma_selecao` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'formaSelecao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'forma_selecao'
    )

    UNION ALL
    SELECT 240 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `localInscricao` `local_inscricao` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'localInscricao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'local_inscricao'
    )

    UNION ALL
    SELECT 250 AS seq,
           CONCAT(
               'ALTER TABLE `mural_estagios` CHANGE COLUMN `id_estagio` `instituicao_id` ',
               c.COLUMN_TYPE,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
        ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'id_estagio'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'instituicao_id'
    )

    UNION ALL
    SELECT 260 AS seq, CONCAT('ALTER TABLE `mural_estagios` DROP COLUMN `id_area`;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
              ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
              LIMIT 1
          )
          AND COLUMN_NAME = 'id_area'
    )

    UNION ALL
    SELECT 270 AS seq, CONCAT('ALTER TABLE `mural_estagios` DROP COLUMN `datafax`;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('mural_estagios', 'mural_estagio')
              ORDER BY FIELD(TABLE_NAME, 'mural_estagios', 'mural_estagio')
              LIMIT 1
          )
          AND COLUMN_NAME = 'datafax'
    )

    UNION ALL
    SELECT 280 AS seq,
           'CREATE TABLE `turnos` (`id` smallint(3) NOT NULL AUTO_INCREMENT, `turno` varchar(70) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;' AS ddl
    WHERE NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'turnos')

    UNION ALL
    SELECT 290 AS seq,
           CONCAT(
               'ALTER TABLE `estagiarios` CHANGE COLUMN `alunonovo_id` `aluno_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = 'estagiarios'
      AND c.COLUMN_NAME = 'alunonovo_id'
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'aluno_id'
      )

    UNION ALL
    SELECT 300 AS seq,
           CONCAT(
               'ALTER TABLE `estagiarios` CHANGE COLUMN `id_instituicao` `instituicao_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = 'estagiarios'
      AND c.COLUMN_NAME = 'id_instituicao'
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'instituicao_id'
      )

    UNION ALL
    SELECT 310 AS seq,
           CONCAT(
               'ALTER TABLE `estagiarios` CHANGE COLUMN `id_supervisor` `supervisor_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = 'estagiarios'
      AND c.COLUMN_NAME = 'id_supervisor'
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'supervisor_id'
      )

    UNION ALL
    SELECT 320 AS seq,
           CONCAT(
               'ALTER TABLE `estagiarios` CHANGE COLUMN `id_professor` `professor_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = 'estagiarios'
      AND c.COLUMN_NAME = 'id_professor'
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'professor_id'
      )

    UNION ALL
    SELECT 330 AS seq, CONCAT('ALTER TABLE `estagiarios` DROP COLUMN `id_aluno`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'id_aluno')

    UNION ALL
    SELECT 340 AS seq, CONCAT('ALTER TABLE `estagiarios` DROP COLUMN `id_area`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'id_area')

    UNION ALL
    SELECT 350 AS seq, CONCAT('ALTER TABLE `estagiarios` DROP COLUMN `turno`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estagiarios' AND COLUMN_NAME = 'turno')

    UNION ALL
    SELECT 360 AS seq, CONCAT('RENAME TABLE `areas_estagio` TO `turma_estagios`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'areas_estagio')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'turma_estagios')

    UNION ALL
    SELECT 370 AS seq, CONCAT('ALTER TABLE `professores` ADD COLUMN `cress` varchar(10) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'cress')

    UNION ALL
    SELECT 380 AS seq, CONCAT('ALTER TABLE `professores` ADD COLUMN `regiao` varchar(2) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'regiao')

    UNION ALL
    SELECT 390 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `datanascimento`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'datanascimento')

    UNION ALL
    SELECT 400 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `localnascimento`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'localnascimento')

    UNION ALL
    SELECT 410 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `sexo`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'sexo')

    UNION ALL
    SELECT 420 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `homepage`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'homepage')

    UNION ALL
    SELECT 430 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `redesocial`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'redesocial')

    UNION ALL
    SELECT 440 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `curriculosigma`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'curriculosigma')

    UNION ALL
    SELECT 450 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `pesquisadordgp`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'pesquisadordgp')

    UNION ALL
    SELECT 460 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `formacaoprofissional`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'formacaoprofissional')

    UNION ALL
    SELECT 470 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `universidadedegraduacao`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'universidadedegraduacao')

    UNION ALL
    SELECT 480 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `anoformacao`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'anoformacao')

    UNION ALL
    SELECT 490 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `mestradoarea`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'mestradoarea')

    UNION ALL
    SELECT 500 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `mestradouniversidade`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'mestradouniversidade')

    UNION ALL
    SELECT 510 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `mestradoanoconclusao`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'mestradoanoconclusao')

    UNION ALL
    SELECT 520 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `doutoradoarea`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'doutoradoarea')

    UNION ALL
    SELECT 530 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `doutoradouniversidade`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'doutoradouniversidade')

    UNION ALL
    SELECT 540 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `doutoradoanoconclusao`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'doutoradoanoconclusao')

    UNION ALL
    SELECT 550 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `formaingresso`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'formaingresso')

    UNION ALL
    SELECT 560 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `tipocargo`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'tipocargo')

    UNION ALL
    SELECT 570 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `categoria`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'categoria')

    UNION ALL
    SELECT 580 AS seq, CONCAT('ALTER TABLE `professores` DROP COLUMN `regimetrabalho`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'regimetrabalho')

    UNION ALL
    SELECT 590 AS seq, CONCAT('ALTER TABLE `professores` MODIFY COLUMN `cpf` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'cpf')

    UNION ALL
    SELECT 600 AS seq, CONCAT('ALTER TABLE `professores` MODIFY COLUMN `telefone` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'telefone')

    UNION ALL
    SELECT 610 AS seq, CONCAT('ALTER TABLE `professores` MODIFY COLUMN `celular` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'celular')

    UNION ALL
    SELECT 620 AS seq, CONCAT('ALTER TABLE `professores` CHANGE COLUMN `ddd_telefone` `codigo_telefone` tinyint(2) NULL DEFAULT 21;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'ddd_telefone')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'codigo_telefone')

    UNION ALL
    SELECT 630 AS seq, CONCAT('ALTER TABLE `professores` CHANGE COLUMN `ddd_celular` `codigo_celular` tinyint(2) NULL DEFAULT 21;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'ddd_celular')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'professores' AND COLUMN_NAME = 'codigo_celular')

    UNION ALL
    SELECT 640 AS seq,
           'CREATE TABLE `questionarios` (`id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `description` text NOT NULL, `created` datetime NOT NULL, `modified` datetime NOT NULL, `is_active` tinyint(1) NOT NULL, `category` varchar(100) NOT NULL, `target_user_type` varchar(50) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;' AS ddl
    WHERE NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questionarios')

    UNION ALL
    SELECT 650 AS seq,
           'CREATE TABLE `questoes` (`id` int(11) NOT NULL AUTO_INCREMENT, `questionario_id` int(11) NOT NULL, `text` text NOT NULL, `type` varchar(50) NOT NULL, `options` text NOT NULL, `created` timestamp NOT NULL DEFAULT current_timestamp(), `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(), `ordem` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `questionnaire_id` (`questionario_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;' AS ddl
    WHERE NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questoes')

    UNION ALL
    SELECT 660 AS seq,
           'CREATE TABLE `respostas` (`id` int(11) NOT NULL AUTO_INCREMENT, `questionario_id` int(11) NOT NULL, `estagiario_id` int(11) NOT NULL, `response` text NOT NULL, `created` timestamp NOT NULL DEFAULT current_timestamp(), `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(), PRIMARY KEY (`id`), KEY `estagiarios_id` (`estagiario_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;' AS ddl
    WHERE NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'respostas')

    UNION ALL
    SELECT 670 AS seq, CONCAT('RENAME TABLE `visita` TO `visitas`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'visita')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'visitas')

    UNION ALL
    SELECT 680 AS seq, CONCAT('ALTER TABLE `visitas` CHANGE COLUMN `estagio_id` `instituicao_id` int(11) NOT NULL;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('visitas', 'visita')
              ORDER BY FIELD(TABLE_NAME, 'visitas', 'visita')
              LIMIT 1
          )
          AND COLUMN_NAME = 'estagio_id'
    )
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('visitas', 'visita')
              ORDER BY FIELD(TABLE_NAME, 'visitas', 'visita')
              LIMIT 1
          )
          AND COLUMN_NAME = 'instituicao_id'
      )

    UNION ALL
    SELECT 690 AS seq, CONCAT('ALTER TABLE `alunos` ADD COLUMN `turno_id` smallint(3) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos' AND COLUMN_NAME = 'turno_id')

    UNION ALL
    SELECT 700 AS seq, CONCAT('ALTER TABLE `alunos` MODIFY COLUMN `cpf` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos' AND COLUMN_NAME = 'cpf')

    UNION ALL
    SELECT 710 AS seq, CONCAT('ALTER TABLE `alunos` MODIFY COLUMN `telefone` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos' AND COLUMN_NAME = 'telefone')

    UNION ALL
    SELECT 720 AS seq, CONCAT('ALTER TABLE `alunos` MODIFY COLUMN `celular` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alunos' AND COLUMN_NAME = 'celular')

    UNION ALL
    SELECT 730 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `outros_estudos`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'outros_estudos')

    UNION ALL
    SELECT 740 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `area_curso`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'area_curso')

    UNION ALL
    SELECT 750 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `ano_curso`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'ano_curso')

    UNION ALL
    SELECT 760 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `num_inscricao`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'num_inscricao')

    UNION ALL
    SELECT 770 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `curso_turma`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'curso_turma')

    UNION ALL
    SELECT 780 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `endereco`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'endereco')

    UNION ALL
    SELECT 790 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `bairro`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'bairro')

    UNION ALL
    SELECT 800 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `municipio`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'municipio')

    UNION ALL
    SELECT 810 AS seq, CONCAT('ALTER TABLE `supervisores` DROP COLUMN `cep`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'cep')

    UNION ALL
    SELECT 820 AS seq, CONCAT('ALTER TABLE `supervisores` MODIFY COLUMN `cpf` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'cpf')

    UNION ALL
    SELECT 830 AS seq, CONCAT('ALTER TABLE `supervisores` MODIFY COLUMN `telefone` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'telefone')

    UNION ALL
    SELECT 840 AS seq, CONCAT('ALTER TABLE `supervisores` MODIFY COLUMN `celular` varchar(15) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'celular')

    UNION ALL
    SELECT 850 AS seq, CONCAT('ALTER TABLE `supervisores` MODIFY COLUMN `escola` varchar(70) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'escola')

    UNION ALL
    SELECT 860 AS seq, CONCAT('ALTER TABLE `supervisores` CHANGE COLUMN `codigo_cel` `codigo_celular` varchar(2) NOT NULL DEFAULT ''21'';') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'codigo_cel')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'codigo_celular')

    UNION ALL
    SELECT 870 AS seq, CONCAT('ALTER TABLE `supervisores` CHANGE COLUMN `codigo_tel` `codigo_telefone` varchar(2) NOT NULL DEFAULT ''21'';') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'codigo_tel')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'codigo_telefone')

    UNION ALL
    SELECT 880 AS seq, CONCAT('ALTER TABLE `supervisores` CHANGE COLUMN `ano_formatura` `ano_formacao` smallint(4) NULL;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'ano_formatura')
      AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supervisores' AND COLUMN_NAME = 'ano_formacao')

    UNION ALL
    SELECT 890 AS seq,
           CONCAT(
               'ALTER TABLE `inst_super` CHANGE COLUMN `id_supervisor` `supervisor_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = 'inst_super'
      AND c.COLUMN_NAME = 'id_supervisor'
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inst_super' AND COLUMN_NAME = 'supervisor_id'
      )

    UNION ALL
    SELECT 900 AS seq,
           CONCAT(
               'ALTER TABLE `inst_super` CHANGE COLUMN `id_instituicao` `instituicao_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = 'inst_super'
      AND c.COLUMN_NAME = 'id_instituicao'
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inst_super' AND COLUMN_NAME = 'instituicao_id'
      )

    UNION ALL
    SELECT 910 AS seq, CONCAT('RENAME TABLE `mural_inscricao` TO `inscricoes`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mural_inscricao')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inscricoes')

    UNION ALL
    SELECT 920 AS seq,
           CONCAT(
               'ALTER TABLE `inscricoes` CHANGE COLUMN `id_aluno` `registro` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('inscricoes', 'mural_inscricao')
        ORDER BY FIELD(TABLE_NAME, 'inscricoes', 'mural_inscricao')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'id_aluno'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'registro'
    )

    UNION ALL
    SELECT 930 AS seq,
           CONCAT(
               'ALTER TABLE `inscricoes` CHANGE COLUMN `id_instituicao` `muralestagio_id` ',
               c.COLUMN_TYPE,
               CASE
                   WHEN c.CHARACTER_SET_NAME IS NOT NULL AND c.DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','enum','set')
                       THEN CONCAT(' CHARACTER SET ', c.CHARACTER_SET_NAME, ' COLLATE ', c.COLLATION_NAME)
                   ELSE ''
               END,
               ' ',
               IF(c.IS_NULLABLE = 'YES', 'NULL', 'NOT NULL'),
               CASE
                   WHEN c.COLUMN_DEFAULT IS NULL THEN ''
                   WHEN LOWER(c.COLUMN_DEFAULT) LIKE 'current_timestamp%' OR UPPER(c.COLUMN_DEFAULT) IN ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   WHEN c.DATA_TYPE IN ('tinyint','smallint','mediumint','int','integer','bigint','decimal','numeric','float','double','real','bit')
                       THEN CONCAT(' DEFAULT ', c.COLUMN_DEFAULT)
                   ELSE CONCAT(' DEFAULT ''', REPLACE(c.COLUMN_DEFAULT, '''', ''''''), '''')
               END,
               CASE WHEN c.EXTRA IS NULL OR c.EXTRA = '' THEN '' ELSE CONCAT(' ', c.EXTRA) END,
               CASE WHEN c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '' THEN '' ELSE CONCAT(' COMMENT ''', REPLACE(c.COLUMN_COMMENT, '''', ''''''), '''') END,
               ';'
           ) AS ddl
    FROM information_schema.COLUMNS c
    JOIN (
        SELECT TABLE_NAME AS src_table
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('inscricoes', 'mural_inscricao')
        ORDER BY FIELD(TABLE_NAME, 'inscricoes', 'mural_inscricao')
        LIMIT 1
    ) t
      ON c.TABLE_SCHEMA = DATABASE() AND c.TABLE_NAME = t.src_table AND c.COLUMN_NAME = 'id_instituicao'
    WHERE NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = t.src_table AND COLUMN_NAME = 'muralestagio_id'
    )

    UNION ALL
    SELECT 940 AS seq, CONCAT('ALTER TABLE `inscricoes` ADD COLUMN `aluno_id` int(11) NULL;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('inscricoes', 'mural_inscricao')
    )
      AND NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('inscricoes', 'mural_inscricao')
              ORDER BY FIELD(TABLE_NAME, 'inscricoes', 'mural_inscricao')
              LIMIT 1
          )
          AND COLUMN_NAME = 'aluno_id'
    )

    UNION ALL
    SELECT 950 AS seq, CONCAT('ALTER TABLE `inscricoes` DROP COLUMN `alunonovo_id`;') AS ddl
    WHERE EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = (
              SELECT TABLE_NAME FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('inscricoes', 'mural_inscricao')
              ORDER BY FIELD(TABLE_NAME, 'inscricoes', 'mural_inscricao')
              LIMIT 1
          )
          AND COLUMN_NAME = 'alunonovo_id'
    )

    UNION ALL
    SELECT 960 AS seq, CONCAT('RENAME TABLE `area_instituicoes` TO `areas`;') AS ddl
    WHERE EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'area_instituicoes')
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'areas')
) planned_ddls
ORDER BY seq;

CALL RunMigrationWithValidation();

DROP TEMPORARY TABLE IF EXISTS validation_errors;
DROP PROCEDURE IF EXISTS RunMigrationWithValidation;
DROP PROCEDURE IF EXISTS DisplayValidationResults;
DROP PROCEDURE IF EXISTS ValidateRequiredTables;
DROP PROCEDURE IF EXISTS ValidateForeignKeyRelationships;
DROP PROCEDURE IF EXISTS SafeRenameColumn;
DROP PROCEDURE IF EXISTS SafeRenameTable;
DROP PROCEDURE IF EXISTS SafeDropColumn;
DROP PROCEDURE IF EXISTS SafeAddColumn;
DROP PROCEDURE IF EXISTS SafeModifyColumn;
DROP PROCEDURE IF EXISTS SafeChangeColumn;
