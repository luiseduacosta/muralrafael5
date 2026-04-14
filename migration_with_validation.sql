DELIMITER //
CREATE PROCEDURE SafeChangeColumn(IN tbl VARCHAR(64), IN old_col VARCHAR(64), IN new_col VARCHAR(64), IN col_def TEXT)
BEGIN
    SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = old_col);
    IF @col_exists > 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` CHANGE COLUMN `', old_col, '` `', new_col, '` ', col_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

CREATE PROCEDURE SafeModifyColumn(IN tbl VARCHAR(64), IN col VARCHAR(64), IN col_def TEXT)
BEGIN
    SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col);
    IF @col_exists > 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` MODIFY COLUMN `', col, '` ', col_def);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

-- Create temporary table before transaction (Temp tables DDL auto-commits)
CREATE TEMPORARY TABLE IF NOT EXISTS validation_errors (
    error_id INT AUTO_INCREMENT PRIMARY KEY,
    error_type VARCHAR(100),
    error_message TEXT,
    severity VARCHAR(20)
);

START TRANSACTION;

-- =====================================================
-- PART 1: PRE-MIGRATION VALIDATION CHECKS
-- =====================================================

DELIMITER //

-- Procedure to check foreign key relationships
CREATE PROCEDURE ValidateForeignKeyRelationships()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE constraint_name VARCHAR(255);
    DECLARE table_name VARCHAR(255);
    DECLARE column_name VARCHAR(255);
    DECLARE referenced_table VARCHAR(255);
    DECLARE referenced_column VARCHAR(255);
    
    DECLARE fk_cursor CURSOR FOR
        SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE CONSTRAINT_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    INSERT INTO validation_errors (error_type, error_message, severity)
    SELECT 'MISSING_REFERENCED_TABLE', CONCAT('Foreign key ', CONSTRAINT_NAME, ' references missing table: ', REFERENCED_TABLE_NAME), 'ERROR'
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL
      AND NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = REFERENCED_TABLE_NAME);
    
    OPEN fk_cursor;
    read_loop: LOOP
        FETCH fk_cursor INTO constraint_name, table_name, column_name, referenced_table, referenced_column;
        IF done THEN LEAVE read_loop; END IF;
        
        IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = table_name)
           AND EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = referenced_table) THEN
            SET @sql = CONCAT('SELECT COUNT(*) INTO @orphan_count FROM `', table_name, '` t1 
                              LEFT JOIN `', referenced_table, '` t2 ON t1.`', column_name, '` = t2.`', referenced_column, '` 
                              WHERE t1.`', column_name, '` IS NOT NULL AND t2.`', referenced_column, '` IS NULL');
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            IF @orphan_count > 0 THEN
                INSERT INTO validation_errors (error_type, error_message, severity)
                VALUES ('ORPHAN_RECORDS', CONCAT('Table `', table_name, '`.`', column_name, '` has ', @orphan_count, ' orphan records referencing `', referenced_table, '`'), 'WARNING');
            END IF;
        END IF;
    END LOOP;
    CLOSE fk_cursor;
    
    INSERT INTO validation_errors (error_type, error_message, severity)
    SELECT 'DATA_TYPE_MISMATCH',
           CONCAT('Foreign key ', kcu.CONSTRAINT_NAME, ': `', kcu.TABLE_NAME, '.', kcu.COLUMN_NAME, '` (', c1.DATA_TYPE, 
                  ') does not match referenced `', kcu.REFERENCED_TABLE_NAME, '.', kcu.REFERENCED_COLUMN_NAME, '` (', c2.DATA_TYPE, ')'), 'ERROR'
    FROM information_schema.KEY_COLUMN_USAGE kcu
    JOIN information_schema.COLUMNS c1 ON c1.TABLE_SCHEMA = kcu.CONSTRAINT_SCHEMA AND c1.TABLE_NAME = kcu.TABLE_NAME AND c1.COLUMN_NAME = kcu.COLUMN_NAME
    JOIN information_schema.COLUMNS c2 ON c2.TABLE_SCHEMA = kcu.CONSTRAINT_SCHEMA AND c2.TABLE_NAME = kcu.REFERENCED_TABLE_NAME AND c2.COLUMN_NAME = kcu.REFERENCED_COLUMN_NAME
    WHERE kcu.CONSTRAINT_SCHEMA = DATABASE() AND c1.DATA_TYPE != c2.DATA_TYPE;
END //

CREATE PROCEDURE ValidateRequiredTables()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = 'users') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) VALUES ('MISSING_TABLE', 'Required table `users` does not exist', 'ERROR');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = 'alunos') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) VALUES ('MISSING_TABLE', 'Required table `alunos` does not exist', 'WARNING');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = 'professores') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) VALUES ('MISSING_TABLE', 'Required table `professores` does not exist', 'WARNING');
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_NAME = 'supervisores') THEN
        INSERT INTO validation_errors (error_type, error_message, severity) VALUES ('MISSING_TABLE', 'Required table `supervisores` does not exist', 'WARNING');
    END IF;
END //

CREATE PROCEDURE DisplayValidationResults()
BEGIN
    DECLARE error_count INT;
    DECLARE warning_count INT;
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
    END IF;
    IF warning_count > 0 THEN
        SELECT 'WARNINGS FOUND:' AS '';
        SELECT error_message FROM validation_errors WHERE severity = 'WARNING';
    END IF;
    IF error_count = 0 AND warning_count = 0 THEN
        SELECT '✓ All validation checks passed!' AS '';
    END IF;
END //

DELIMITER ;

CALL ValidateRequiredTables();
CALL ValidateForeignKeyRelationships();
CALL DisplayValidationResults();

-- SAFE ABORT: Throws an error to stop the script and implicitly rolls back the transaction if errors are found
SELECT COUNT(*) INTO @error_count FROM validation_errors WHERE severity = 'ERROR';
SET @sql = IF(@error_count > 0, 
    'SIGNAL SQLSTATE ''45000'' SET MESSAGE_TEXT = ''Migration aborted due to validation errors.''',
    'SELECT ''Validation passed, proceeding...'' AS Status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PART 2: BACKUP EXISTING DATA
-- =====================================================

CREATE TABLE IF NOT EXISTS migration_backup_20260414 AS 
SELECT 'Migration started at ' AS backup_time, NOW() AS timestamp;

-- =====================================================
-- PART 3: MAIN MIGRATION SCRIPT
-- =====================================================

-- Table USERS: Add new columns
ALTER TABLE IF EXISTS `users`
  ADD COLUMN IF NOT EXISTS `nome` varchar(128) NOT NULL COMMENT 'Nome do usuário' AFTER `password`,
  ADD COLUMN IF NOT EXISTS `role` enum('admin','aluno','professor','supervisor') NOT NULL DEFAULT 'aluno' COMMENT 'roles' AFTER `nome`,
  ADD COLUMN IF NOT EXISTS `entidade_id` int(11) DEFAULT NULL COMMENT 'id da entidade: aluno, professor ou supervisor' AFTER `identificacao`,
  ADD COLUMN IF NOT EXISTS `ativo` tinyint(1) DEFAULT 1 AFTER `entidade_id`,
  ADD COLUMN IF NOT EXISTS `criado_em` timestamp NOT NULL DEFAULT current_timestamp() AFTER `ativo`;

-- Safely rename users columns (Using our Helper Procedure)
CALL SafeChangeColumn('users', 'numero', 'identificacao', 'int(9) DEFAULT NULL COMMENT "Registro do aluno, SIAPE do professor ou CRESS do supervisor"');
CALL SafeChangeColumn('users', 'timestamp', 'atualizado_em', 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()');
CALL SafeChangeColumn('users', 'estudante_id', 'aluno_id', 'INT'); -- Note: Adjust 'INT' if the original type was different
CALL SafeChangeColumn('users', 'docente_id', 'professor_id', 'INT'); -- Note: Adjust 'INT' if the original type was different

-- Update user roles
UPDATE `users` SET `role` = CASE `categoria` 
    WHEN '1' THEN 'admin' WHEN '2' THEN 'aluno' WHEN '3' THEN 'professor' WHEN '4' THEN 'supervisor' ELSE `role` 
END WHERE `categoria` IN ('1','2','3','4');

UPDATE `users` SET `entidade_id` = `aluno_id` WHERE `role` = 'aluno';
UPDATE `users` SET `entidade_id` = `professor_id` WHERE `role` = 'professor';
UPDATE `users` SET `entidade_id` = `supervisor_id` WHERE `role` = 'supervisor';

-- Safer UPDATE JOIN syntax for MariaDB
UPDATE `users` u JOIN `alunos` a ON u.`aluno_id` = a.`id` SET u.`nome` = a.`nome`;
UPDATE `users` u JOIN `professores` p ON u.`professor_id` = p.`id` SET u.`nome` = p.`nome`;
UPDATE `users` u JOIN `supervisores` s ON u.`supervisor_id` = s.`id` SET u.`nome` = s.`nome`;

-- Table INSTITUICOES
ALTER TABLE IF EXISTS `estagio` RENAME TO `instituicoes`;
ALTER TABLE `instituicoes`
    RENAME COLUMN IF EXISTS `area` TO `area_id`,
    DROP COLUMN IF EXISTS `avaliacao`,
    DROP COLUMN IF EXISTS `localInscricao`,
    DROP COLUMN IF EXISTS `fax`;

-- Table CONFIGURACOES
ALTER TABLE IF EXISTS `configuracao` RENAME TO `configuracoes`;
ALTER TABLE `configuracoes` ADD COLUMN IF NOT EXISTS `instituicao` varchar(50) NOT NULL;

-- Table MURAL_ESTAGIOS
ALTER TABLE IF EXISTS `mural_estagio` RENAME TO `mural_estagios`;
ALTER TABLE `mural_estagios` 
    RENAME COLUMN IF EXISTS `dataSelecao` TO `data_selecao`,
    RENAME COLUMN IF EXISTS `cargaHoraria` TO `carga_horaria`,
    RENAME COLUMN IF EXISTS `dataInscricao` TO `data_inscricao`,
    RENAME COLUMN IF EXISTS `horarioSelecao` TO `horario_selecao`,
    RENAME COLUMN IF EXISTS `localSelecao` TO `local_selecao`,
    RENAME COLUMN IF EXISTS `formaSelecao` TO `forma_selecao`,
    RENAME COLUMN IF EXISTS `localInscricao` TO `local_inscricao`,
    RENAME COLUMN IF EXISTS `id_estagio` TO `instituicao_id`,
    DROP COLUMN IF EXISTS `id_area`,
    DROP COLUMN IF EXISTS `datafax`;

-- Table TURNOS
CREATE TABLE IF NOT EXISTS `turnos` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `turno` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `turnos` (`turno`) VALUES ('diurno'), ('noturno'), ('integral'), ('outro');

-- Table ESTAGIARIOS
ALTER TABLE `estagiarios` 
    RENAME COLUMN IF EXISTS `alunonovo_id` TO `aluno_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `instituicao_id`,
    RENAME COLUMN IF EXISTS `id_supervisor` TO `supervisor_id`,
    RENAME COLUMN IF EXISTS `id_professor` TO `professor_id`,
    DROP COLUMN IF EXISTS `id_aluno`,
    DROP COLUMN IF EXISTS `id_area`,
    DROP COLUMN IF EXISTS `turno`;

-- Table TURMA_ESTAGIOS
ALTER TABLE IF EXISTS `areas_estagio` RENAME TO `turma_estagios`;

-- Table PROFESSORES
ALTER TABLE IF EXISTS `professores`
    ADD COLUMN IF NOT EXISTS `cress` varchar(10) NULL AFTER `siape`,
    ADD COLUMN IF NOT EXISTS `regiao` varchar(2) NULL AFTER `cress`,
    DROP COLUMN IF EXISTS `datanascimento`,
    DROP COLUMN IF EXISTS `localnascimento`,
    DROP COLUMN IF EXISTS `sexo`,
    DROP COLUMN IF EXISTS `homepage`,
    DROP COLUMN IF EXISTS `redesocial`,
    DROP COLUMN IF EXISTS `curriculosigma`,
    DROP COLUMN IF EXISTS `pesquisadordgp`,
    DROP COLUMN IF EXISTS `formacaoprofissional`,
    DROP COLUMN IF EXISTS `universidadedegraduacao`,
    DROP COLUMN IF EXISTS `anoformacao`,
    DROP COLUMN IF EXISTS `mestradoarea`,
    DROP COLUMN IF EXISTS `mestradouniversidade`,
    DROP COLUMN IF EXISTS `mestradoanoconclusao`,
    DROP COLUMN IF EXISTS `doutoradoarea`,
    DROP COLUMN IF EXISTS `doutoradouniversidade`,
    DROP COLUMN IF EXISTS `doutoradoanoconclusao`,
    DROP COLUMN IF EXISTS `formaingresso`,
    DROP COLUMN IF EXISTS `tipocargo`,
    DROP COLUMN IF EXISTS `categoria`,
    DROP COLUMN IF EXISTS `regimetrabalho`;

-- Safely modify and change professors columns
CALL SafeModifyColumn('professores', 'cpf', 'varchar(15) NULL');
CALL SafeModifyColumn('professores', 'telefone', 'varchar(15) NULL');
CALL SafeModifyColumn('professores', 'celular', 'varchar(15) NULL');
CALL SafeChangeColumn('professores', 'ddd_telefone', 'codigo_telefone', 'tinyint(2) NULL DEFAULT 21 AFTER `telefone`');
CALL SafeChangeColumn('professores', 'ddd_celular', 'codigo_celular', 'tinyint(2) NULL DEFAULT 21 AFTER `celular`');

-- Update professors phone numbers
UPDATE `professores` SET `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, ''))
WHERE codigo_telefone IS NOT NULL AND telefone IS NOT NULL;
UPDATE `professores` SET `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE codigo_celular IS NOT NULL AND celular IS NOT NULL;

-- Table Questionários
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Questões
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table Respostas
CREATE TABLE IF NOT EXISTS `respostas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionario_id` int(11) NOT NULL,
  `estagiario_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `estagiarios_id` (`estagiario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table VISITAS
ALTER TABLE IF EXISTS `visita` RENAME TO `visitas`;
CALL SafeChangeColumn('visitas', 'estagio_id', 'instituicao_id', 'INT(11) NOT NULL');

-- Table ALUNOS
ALTER TABLE IF EXISTS `alunos` 
    ADD COLUMN IF NOT EXISTS `turno_id` SMALLINT(3) NOT NULL AFTER `turno`;

CALL SafeModifyColumn('alunos', 'cpf', 'VARCHAR(15) NULL AFTER `registro`');
CALL SafeModifyColumn('alunos', 'telefone', 'VARCHAR(15) NULL AFTER `codigo_telefone`');
CALL SafeModifyColumn('alunos', 'celular', 'VARCHAR(15) NULL AFTER `codigo_celular`');

-- Update turno_id
UPDATE `alunos` a INNER JOIN `turnos` t ON t.`turno` = a.`turno` SET a.`turno_id` = t.`id`;

-- Update alunos phone numbers
UPDATE `alunos` 
SET `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, '')),
    `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE (codigo_telefone IS NOT NULL AND telefone IS NOT NULL)
   OR (codigo_celular IS NOT NULL AND celular IS NOT NULL);

-- Table SUPERVISORES
ALTER TABLE IF EXISTS `supervisores` 
    DROP COLUMN IF EXISTS `outros_estudos`,
    DROP COLUMN IF EXISTS `area_curso`,
    DROP COLUMN IF EXISTS `ano_curso`,
    DROP COLUMN IF EXISTS `num_inscricao`,
    DROP COLUMN IF EXISTS `curso_turma`,
    DROP COLUMN IF EXISTS `endereco`,
    DROP COLUMN IF EXISTS `bairro`,
    DROP COLUMN IF EXISTS `municipio`,
    DROP COLUMN IF EXISTS `cep`;

CALL SafeModifyColumn('supervisores', 'cpf', 'VARCHAR(15) NULL');
CALL SafeModifyColumn('supervisores', 'telefone', 'VARCHAR(15) NULL');
CALL SafeModifyColumn('supervisores', 'celular', 'VARCHAR(15) NULL');
CALL SafeModifyColumn('supervisores', 'escola', 'VARCHAR(70) NULL');
CALL SafeChangeColumn('supervisores', 'codigo_cel', 'codigo_celular', 'VARCHAR(2) NOT NULL DEFAULT "21"');
CALL SafeChangeColumn('supervisores', 'codigo_tel', 'codigo_telefone', 'VARCHAR(2) NOT NULL DEFAULT "21"');
CALL SafeChangeColumn('supervisores', 'ano_formatura', 'ano_formacao', 'SMALLINT(4) NULL');

-- Update supervisores phone numbers
UPDATE `supervisores` SET `telefone` = CONCAT('(', COALESCE(codigo_telefone, ''), ') ', COALESCE(telefone, ''))
WHERE codigo_telefone IS NOT NULL AND telefone IS NOT NULL AND telefone NOT LIKE CONCAT('(', codigo_telefone, ')%');
UPDATE `supervisores` SET `celular` = CONCAT('(', COALESCE(codigo_celular, ''), ') ', COALESCE(celular, ''))
WHERE codigo_celular IS NOT NULL AND celular IS NOT NULL AND celular NOT LIKE CONCAT('(', codigo_celular, ')%');

-- Table INST_SUPER
ALTER TABLE `inst_super` 
    RENAME COLUMN IF EXISTS `id_supervisor` TO `supervisor_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `instituicao_id`;

-- Table INSCRICOES
ALTER TABLE IF EXISTS `mural_inscricao` RENAME TO `inscricoes`;
ALTER TABLE `inscricoes` 
    RENAME COLUMN IF EXISTS `id_aluno` TO `aluno_id`,
    RENAME COLUMN IF EXISTS `id_instituicao` TO `muralestagio_id`,
    DROP COLUMN IF EXISTS `alunonovo_id`;

-- Table AREA_INSTITUICOES
ALTER TABLE IF EXISTS `area_instituicoes` RENAME TO `areas`;

-- =====================================================
-- PART 4: POST-MIGRATION VALIDATION
-- =====================================================

DELIMITER //
CREATE PROCEDURE PostMigrationValidation()
BEGIN
    DECLARE table_count INT;
    
    SELECT '========================================' AS '';
    SELECT 'POST-MIGRATION VALIDATION' AS '';
    SELECT '========================================' AS '';
    
    SELECT COUNT(*) INTO table_count 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME IN ('users', 'instituicoes', 'configuracoes', 'mural_estagios', 
                         'turnos', 'estagiarios', 'professores', 'supervisores', 
                         'visitas', 'alunos', 'inscricoes', 'areas');
    
    SELECT CONCAT('✓ Tables created/renamed successfully: ', table_count, '/12') AS '';
    
    SELECT 
        'users' AS table_name, COUNT(*) AS record_count FROM `users`
    UNION ALL SELECT 'alunos', COUNT(*) FROM `alunos`
    UNION ALL SELECT 'professores', COUNT(*) FROM `professores`
    UNION ALL SELECT 'supervisores', COUNT(*) FROM `supervisores`;
END //
DELIMITER ;

CALL PostMigrationValidation();

-- =====================================================
-- PART 5: FINALIZE MIGRATION
-- =====================================================

UPDATE migration_backup_20260414 SET backup_time = 'Migration completed at ' WHERE 1=1;

SELECT '========================================' AS '';
SELECT '✅ MIGRATION COMPLETED SUCCESSFULLY' AS '';
SELECT '========================================' AS '';

COMMIT;

-- =====================================================
-- PART 6: CLEAN UP
-- =====================================================
DROP TEMPORARY TABLE IF EXISTS validation_errors;
DROP PROCEDURE IF EXISTS ValidateForeignKeyRelationships;
DROP PROCEDURE IF EXISTS ValidateRequiredTables;
DROP PROCEDURE IF EXISTS DisplayValidationResults;
DROP PROCEDURE IF EXISTS PostMigrationValidation;
DROP PROCEDURE IF EXISTS SafeChangeColumn;
DROP PROCEDURE IF EXISTS SafeModifyColumn;

-- Uncomment below to drop backup table after confirming migration
-- DROP TABLE IF EXISTS migration_backup_20260414;
