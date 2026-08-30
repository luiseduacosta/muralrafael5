<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class Initial extends BaseMigration
{
    public bool $autoId = false;

    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     *
     * @return void
     */
    public function up(): void
    {
        // instituicoes, professores and visitas are created by the legacy
        // migrations (20260228193002, 20260228192937, 20260228192315) that run
        // before this snapshot, so recreate them from the snapshot definition
        // to keep the final schema identical to production. visitas must be
        // dropped before professores because of the professor_id foreign key
        // added by 20260325200335.
        if ($this->hasTable('visitas')) {
            $this->table('visitas')->drop()->save();
        }
        if ($this->hasTable('professores')) {
            $this->table('professores')->drop()->save();
        }
        if ($this->hasTable('instituicoes')) {
            $this->table('instituicoes')->drop()->save();
        }

        $this->table('acos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('model', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('alias', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('administradores')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addIndex(
                $this->index('user_id')
                    ->setName('user_id')
                    ->setType('unique'),
            )
            ->create();

        $this->table('afastamentos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('carater', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('tipo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 35,
                'null' => true,
            ])
            ->addColumn('inicio', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('final', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('atadirecao', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('alunos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('codigo_telefone', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('telefone', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('codigo_celular', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('celular', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cpf', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('identidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('orgao', 'string', [
                'collation' => 'latin1_general_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('nascimento', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cep', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('municipio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 250,
                'null' => true,
            ])
            ->addColumn('ingresso', 'char', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('nomesocial', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('turno', 'string', [
                'default' => null,
                'limit' => 7,
                'null' => true,
            ])
            ->addColumn('turno_id', 'smallinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('inscricao_count', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addIndex(
                $this->index('registro')
                    ->setName('registro')
                    ->setType('unique'),
            )
            ->create();

        $this->table('alunos_etica')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('nota', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 3,
                'scale' => 1,
                'signed' => true,
            ])
            ->addColumn('nota_por_extenso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->create();

        $this->table('alunos_etica_23042912')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('nota', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 3,
                'scale' => 1,
                'signed' => true,
            ])
            ->addColumn('nota_por_extenso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->create();

        $this->table('alunos_ingresso')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('nota', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 2,
                'scale' => 1,
                'signed' => true,
            ])
            ->addColumn('nota_por_extenso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->create();

        $this->table('alunos_ingresso-bak')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('etica', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->create();

        $this->table('alunos_ingresso_10-09-2014')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('nota', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 2,
                'scale' => 1,
                'signed' => true,
            ])
            ->addColumn('nota_por_extenso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->create();

        $this->table('alunos_ingresso_18042012')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('nota', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 2,
                'scale' => 1,
                'signed' => true,
            ])
            ->addColumn('nota_por_extenso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->create();

        $this->table('alunosestagiarios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('codigo_telefone', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('codigo_celular', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('celular', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cpf', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('identidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('orgao', 'string', [
                'collation' => 'latin1_general_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('nascimento', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cep', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('municipio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 250,
                'null' => true,
            ])
            ->addColumn('ingresso', 'char', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('nomesocial', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('turno', 'string', [
                'default' => null,
                'limit' => 7,
                'null' => true,
            ])
            ->addIndex(
                $this->index('registro')
                    ->setName('registro')
                    ->setType('unique'),
            )
            ->create();

        $this->table('alunosnovos_bak')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('registro', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('codigo_telefone', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('codigo_celular', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('celular', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cpf', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('identidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('orgao', 'string', [
                'collation' => 'latin1_general_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('nascimento', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cep', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('municipio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 250,
                'null' => true,
            ])
            ->addColumn('ingresso', 'char', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('nomesocial', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('turno', 'string', [
                'default' => null,
                'limit' => 7,
                'null' => true,
            ])
            ->addIndex(
                $this->index('registro')
                    ->setName('registro')
                    ->setType('unique'),
            )
            ->create();

        $this->table('areas')
            ->addColumn('id', 'smallinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('area', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 90,
                'null' => false,
            ])
            ->create();

        $this->table('areas_do_professor_mon')
            ->addColumn('numero', 'smallinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['numero'])
            ->addColumn('area', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('areasmonografia')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('areamonografia', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('aros')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('model', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('foreign_key', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('alias', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('rght', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('aros_acos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('aro_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('aco_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('_create', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('_read', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('_update', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('_delete', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->create();

        $this->table('auth_users')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('email', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('password', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 80,
                'null' => true,
            ])
            ->addColumn('categoria', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '2',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('role', 'string', [
                'default' => 'aluno',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('identificacao', 'string', [
                'comment' => 'DRE, SIAPE, CRESS',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('entidade_id', 'integer', [
                'comment' => 'aluno.id, professor.id, supervisor.id',
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('aluno_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('supervisor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('professor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('auth_users-bak')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('identificacao', 'string', [
                'comment' => 'Either value of DRE, Siape ou CRESS',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('role', 'string', [
                'default' => 'aluno',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('entidade_id', 'integer', [
                'comment' => 'Id of the aluno, docente or supervisor table',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                $this->index('email')
                    ->setName('email')
                    ->setType('unique'),
            )
            ->create();

        $this->table('avaliacoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('estagiario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('avaliacao1', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao2', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao3', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao4', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao5', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao6', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao7', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao8', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao9', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao9_1', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('avaliacao10', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao10_1', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('avaliacao11', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao11_1', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('avaliacao12', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao12_1', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('avaliacao13', 'char', [
                'default' => null,
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('avaliacao13_1', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('avaliacao14', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('TIMESTAMP', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('bancas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('tipo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('funcao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('titulo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('aluno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->create();

        $this->table('complementos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('periodo_especial', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addIndex(
                $this->index('id')
                    ->setName('complementos_id_IDX')
                    ->setType('unique'),
            )
            ->create();

        $this->table('configuracoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('instituicao_curso', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('mural_periodo_atual', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('curso_turma_atual', 'smallinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('curso_abertura_inscricoes', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('curso_encerramento_inscricoes', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('termo_compromisso_periodo', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('termo_compromisso_inicio', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('termo_compromisso_final', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo_calendario_academico', 'char', [
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('instituicao', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('configuraplanejamentos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('semestre', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('versao', 'smallinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->create();

        $this->table('curso_inscricao_instituicao')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_estagio', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('area', 'smallinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('natureza', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('instituicao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 75,
                'null' => true,
            ])
            ->addColumn('url', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 105,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('municipio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('cep', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('fax', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('beneficio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('fim_de_semana', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('curso_inscricao_supervisor')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('cpf', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 12,
                'null' => false,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('municipio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('cep', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('codigo_tel', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '21',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('codigo_cel', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '21',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('celular', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('escola', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('ano_formatura', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('cress', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('regiao', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '7',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('outros_estudos', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('area_curso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 40,
                'null' => true,
            ])
            ->addColumn('ano_curso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('cargo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 25,
                'null' => true,
            ])
            ->addColumn('num_inscricao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('curso_turma', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('selecao', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('observacoes', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('curso_inst_super')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_instituicao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('id_supervisor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('dias')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('dia', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->create();

        $this->table('disciplinas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('codigo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('disciplina', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('creditos', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('carga_horaria', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('periodo_diurno', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('periodo_noturno', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('requisitos', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('optativa', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('departamento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 256,
                'null' => true,
            ])
            ->create();

        $this->table('docentes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('cpf', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 14,
                'null' => true,
            ])
            ->addColumn('siape', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('cress', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('regiao', 'string', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('datanascimento', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('localnascimento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('sexo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '2',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('ddd_telefone', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '21',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('ddd_celular', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '21',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('celular', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('homepage', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 120,
                'null' => true,
            ])
            ->addColumn('redesocial', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('curriculolattes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('atualizacaolattes', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('curriculosigma', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 7,
                'null' => true,
            ])
            ->addColumn('pesquisadordgp', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('formacaoprofissional', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('universidadedegraduacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('anoformacao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('mestradoarea', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 40,
                'null' => true,
            ])
            ->addColumn('mestradouniversidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('mestradoanoconclusao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('doutoradoarea', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 40,
                'null' => true,
            ])
            ->addColumn('doutoradouniversidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('doutoradoanoconclusao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('dataingresso', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('formaingresso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('tipocargo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('categoria', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('regimetrabalho', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('departamento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('dataegresso', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('motivoegresso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('observacoes', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('ementas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('configuraplanejamento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('disciplina_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('optativa_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('titulo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('ementa', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 1000,
                'null' => false,
            ])
            ->create();

        $this->table('eprofesores')
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('departamento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('outros', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->create();

        $this->table('essextensoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('tae_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('segmento', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('segmento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('datacongregacao', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('situacaopr5_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('versao', 'tinyinteger', [
                'default' => '1',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('essusuarios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('login', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 25,
                'null' => false,
            ])
            ->addColumn('senha', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 35,
                'null' => false,
            ])
            ->addColumn('uid', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('gui', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('outros_dados', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 250,
                'null' => false,
            ])
            ->addColumn('pasta', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('shell', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('registro', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('setor', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => 'funcionario',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index([
                        'login',
                        'uid',
                    ])
                    ->setName('login')
                    ->setType('unique'),
            )
            ->create();

        $this->table('estagiarios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('registro', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('aluno_id', 'integer', [
                'comment' => 'Ex alunonovo_id',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('nivel', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('tc', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('tc_solicitacao', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('instituicao_id', 'smallinteger', [
                'comment' => 'ex id_instituicao',
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('supervisor_id', 'smallinteger', [
                'comment' => 'id_supervisor',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('professor_id', 'smallinteger', [
                'comment' => 'ex id_professor',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('nota', 'decimal', [
                'default' => '0.00',
                'null' => true,
                'precision' => 4,
                'scale' => 2,
                'signed' => true,
            ])
            ->addColumn('ch', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('complemento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('ajuste2020', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('benetransporte', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('benealimentacao', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('benebolsa', 'string', [
                'default' => null,
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('eusers')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('registro', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('categoria', 'string', [
                'default' => '2',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('eventos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('evento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('tipo_participacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('local', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->create();

        $this->table('extensao_old')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('tae_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('segmento', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('segmento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('datacongregacao', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('situacaopr5_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addIndex(
                $this->index('id')
                    ->setName('extensao_id_IDX')
                    ->setType('unique'),
            )
            ->create();

        $this->table('extensionistas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('extensoes_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('essextensoes_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('cargahoraria', 'char', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('ano', 'char', [
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->create();

        $this->table('extensoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('coordenacao', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('unidade', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('essextensoes_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('universidade_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('folhadeatividades')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('estagiario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('dia', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('inicio', 'time', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('final', 'time', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('horario', 'time', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('atividade', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->create();

        $this->table('historico_professor')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'smallinteger', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('datainicio', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('datafim', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('assunto', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->create();

        $this->table('horarios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ordem', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('horario', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->create();

        $this->table('impersonations')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('admin_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('impersonated_user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('started_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('ended_at', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                $this->index([
                        'admin_id',
                        'is_active',
                    ])
                    ->setName('idx_admin_active'),
            )
            ->addIndex(
                $this->index([
                        'impersonated_user_id',
                        'is_active',
                    ])
                    ->setName('idx_impersonated_active'),
            )
            ->create();

        $this->table('informacoes')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('cabecalho', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('corpo', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('inscricoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('registro', 'integer', [
                'comment' => 'aluno_registro DRE',
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('muralestagio_id', 'smallinteger', [
                'comment' => 'ex id_instituicao
',
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => '0000-00-00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('timestamp', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('aluno_id', 'integer', [
                'comment' => 'ex alunonovo_id',
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('alunoestagiario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('inst_super')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('instituicao_id', 'smallinteger', [
                'comment' => 'ex id_instituicao',
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('supervisor_id', 'smallinteger', [
                'comment' => 'ex supervisor_id',
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->create();

        $this->table('instituicoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('area_id', 'smallinteger', [
                'comment' => 'ex area_instituicoes_id',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('area', 'smallinteger', [
                'comment' => 'area_id',
                'default' => '0',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('natureza', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('instituicao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 120,
                'null' => false,
            ])
            ->addColumn('cnpj', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 18,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 90,
                'null' => true,
            ])
            ->addColumn('url', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 105,
                'null' => true,
            ])
            ->addColumn('bairro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('municipio', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('cep', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('beneficios', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('fim_de_semana', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('convenio', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('expira', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('seguro', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('log_supervisores')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_supervisor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('cress', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('ip', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('data', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('arquivo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->create();

        $this->table('migration_backup_20260414')
            ->addColumn('backup_info', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('backup_time', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('monografia')
            ->addColumn('codigo', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['codigo'])
            ->addColumn('catalogo', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('titulo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 160,
                'null' => false,
            ])
            ->addColumn('resumo', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'date', [
                'default' => '0000-00-00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('num_prof', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('num_co_orienta', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('num_area', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('areamonografia', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data_defesa', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('banca1', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca2', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca3', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('convidado', 'string', [
                'collation' => 'utf8mb3_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('url', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('timestamp', 'timestamp', [
                'default' => '0000-00-00 00:00:00',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('monografia_20120910')
            ->addColumn('codigo', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['codigo'])
            ->addColumn('catalogo', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('titulo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 160,
                'null' => false,
            ])
            ->addColumn('resumo', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'date', [
                'default' => '0000-00-00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('num_prof', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('num_co_orienta', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('num_area', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('areamonografia', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data_defesa', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('banca1', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca2', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca3', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('convidado', 'string', [
                'collation' => 'utf8mb3_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('url', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('timestamp', 'timestamp', [
                'default' => '0000-00-00 00:00:00',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('mural_estagios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('instituicao_id', 'integer', [
                'comment' => 'ex id_estagio',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('instituicao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('convenio', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('vagas', 'tinyinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('beneficios', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('final_de_semana', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('carga_horaria', 'tinyinteger', [
                'comment' => 'ex cargaHoraria',
                'default' => '0',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('requisitos', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 455,
                'null' => true,
            ])
            ->addColumn('horario', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('data_selecao', 'date', [
                'comment' => 'ex dataSelecao',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_inscricao', 'date', [
                'comment' => 'ex dataInscricao',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('horario_selecao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => 'ex horarioSelecao',
                'default' => null,
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('local_selecao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => 'ex horarioSelecao',
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('forma_selecao', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => 'ex formaSelecao',
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('contato', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'latin1_general_ci',
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('local_inscricao', 'string', [
                'collation' => 'latin1_general_ci',
                'comment' => 'ex localInscricao',
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('outras', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('novo_usuarios')
            ->addColumn('usuario', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('senha', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 10,
                'null' => false,
            ])
            ->create();

        $this->table('nucleos_pesquisa')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('sigla', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('nucleo_pesquisa', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 150,
                'null' => true,
            ])
            ->addColumn('descricao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('pagina_web', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('diretoriolattes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('eventos', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('publicacoes', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('optativas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('codigo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('disciplina', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('creditos', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('carga_horaria', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('periodo_diurno', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('periodo_noturno', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('requisitos', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('optativa', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('departamento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 256,
                'null' => true,
            ])
            ->create();

        $this->table('orientacoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('tipo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('data_inicio', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_fim', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_defesa', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('titulo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('aluno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->create();

        $this->table('planejamentos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('configuraplanejamento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('periodo', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('dia_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('horario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('sala_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('disciplina_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('ementa_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('optativa_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('pos_alunos')
            ->addColumn('id', 'smallinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('curso', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('nascimento', 'date', [
                'default' => '0000-00-00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('naturidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 25,
                'null' => true,
            ])
            ->addColumn('cpf', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('rg', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('formacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('endereco', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 25,
                'null' => true,
            ])
            ->addColumn('telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('outro_telefone', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 25,
                'null' => true,
            ])
            ->addColumn('dre', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('bolsa', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('orgao', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('trabalho', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '0',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('instituicao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('funcao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('local', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('telefone_inst', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 12,
                'null' => false,
            ])
            ->addColumn('tema', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('orientador', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 25,
                'null' => false,
            ])
            ->addColumn('exame', 'date', [
                'default' => '0000-00-00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('defesa', 'date', [
                'default' => '0000-00-00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('proficiencia', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('outro_proficiencia', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 15,
                'null' => false,
            ])
            ->addIndex(
                $this->index('nome')
                    ->setName('nome')
                    ->setType('fulltext'),
            )
            ->create();

        $this->table('pos_alunos_disciplinas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('aluno_id', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('disciplina_id', 'tinyinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('professor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('ano_semestre', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('conceito', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->create();

        $this->table('pos_alunos_professores')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('aluno_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('professor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('inicio', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('ata', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->create();

        $this->table('pos_auxiliar')
            ->addColumn('variavel', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('valor', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 30,
                'null' => false,
            ])
            ->create();

        $this->table('pos_disciplinas')
            ->addColumn('id', 'tinyinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('carga_horaria', 'tinyinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('creditos', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('codigo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('obrigatoriedade', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 1,
                'null' => false,
            ])
            ->create();

        $this->table('prof_area')
            ->addColumn('num_prof', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('num_area', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->create();

        $this->table('prof_disciplinas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_disciplina', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('periodo', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('q_alunos', 'smallinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('prof_nucleopesquisa')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('id_nucleopesquisa', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('peso', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->create();

        $this->table('professores')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('cpf', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('siape', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('cress', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('regiao', 'string', [
                'default' => null,
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('codigo_telefone', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('telefone', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('codigo_celular', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('celular', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('curriculolattes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('atualizacaolattes', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('dataingresso', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('departamento', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('dataegresso', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('motivoegresso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('observacoes', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('usser_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('progressoes')
            ->addColumn('id', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('tipo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('pontuacao', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 4,
                'signed' => true,
            ])
            ->addColumn('avaliacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->create();

        $this->table('publicacoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('tipo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('titulo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('outros_dados', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->create();

        $this->table('qualificacoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('id_professor', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('tipo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('area', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('data_inicio', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('data_fim', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('local', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->create();

        $this->table('questionarios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('title', 'string', [
                'comment' => 'The title of the questionnaire',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'A more detailed description of the questionnaire',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Timestamp when the questionnaire was created',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Timestamp when the questionnaire was last modified',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('is_active', 'boolean', [
                'comment' => 'Whether the questionnaire is currently active and available for use',
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('category', 'string', [
                'comment' => 'Optional category for grouping questionnaires '
                    . '(e.g., \"Student Feedback\", \"Course Evaluation\")',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('target_user_type', 'string', [
                'comment' => 'Optional: Specifies the type of user this questionnaire is intended for '
                    . '(e.g., \"student\", \"supervisor\", \"professor\")',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->create();

        $this->table('questoes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('questionario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('text', 'text', [
                'comment' => 'The text of the question',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('type', 'string', [
                'comment' => 'The type of question (text, textarea, select, scale, boolean)',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('options', 'text', [
                'comment' => 'JSON encoded options for select/scale questions',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Timestamp when the question was created',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Timestamp when the question was last modified',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('ordem', 'integer', [
                'comment' => 'The order in which the question should appear in the questionnaire',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addIndex(
                $this->index('questionario_id')
                    ->setName('questionnaire_id'),
            )
            ->create();

        $this->table('respostas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('questionario_id', 'integer', [
                'comment' => 'The questionnaire id',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('estagiario_id', 'integer', [
                'comment' => 'ID of the user who answered the question',
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('response', 'text', [
                'comment' => 'The answer to the question',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Timestamp when the response was created',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Timestamp when the response was last modified',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                $this->index('estagiario_id')
                    ->setName('estagiarios_id'),
            )
            ->create();

        $this->table('roles')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('categoria', 'string', [
                'collation' => 'utf8mb3_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->create();

        $this->table('salas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('sala', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('localizacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('lotacao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('recursos', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->create();

        $this->table('situacaopr5')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('situacao', 'string', [
                'default' => null,
                'limit' => 25,
                'null' => false,
            ])
            ->create();

        $this->table('situacoes')
            ->addColumn('id', 'smallinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('codigo', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('situacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('supervisores')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('cpf', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('codigo_telefone', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('telefone', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('codigo_celular', 'tinyinteger', [
                'default' => '21',
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('celular', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('escola', 'string', [
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('ano_formacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('cress', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('regiao', 'tinyinteger', [
                'default' => '7',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('cargo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 25,
                'null' => true,
            ])
            ->addColumn('observacoes', 'text', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('taes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('siape', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('tcc_alunos')
            ->addColumn('numero', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['numero'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('num_monografia', 'smallinteger', [
                'default' => '0',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('registro', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->create();

        $this->table('test_cast')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('num', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 2,
                'signed' => true,
            ])
            ->create();

        $this->table('test_test')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('turmas')
            ->addColumn('id', 'smallinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('turma', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->create();

        $this->table('turnos')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('id')
                    ->setName('id'),
            )
            ->create();

        $this->table('universidades')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('universidade', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->create();

        $this->table('users')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('email', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('password', 'char', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 80,
                'null' => true,
            ])
            ->addColumn('nome', 'string', [
                'comment' => 'Nome do usuário',
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('role', 'string', [
                'comment' => 'roles',
                'default' => 'aluno',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('categoria', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '2',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('identificacao', 'integer', [
                'comment' => 'Registro do aluno, SIAPE do professor ou CRESS do supervisor',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('entidade_id', 'integer', [
                'comment' => 'id da entidade: aluno, professor ou supervisor',
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('aluno_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('supervisor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('professor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('usuarios')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('usuario', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('senha', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('role', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => 'READ ONLY',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('viradas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('cpf', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 12,
                'null' => false,
            ])
            ->addColumn('dre', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 9,
                'null' => true,
            ])
            ->addColumn('periodo', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('cress', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('regiao', 'tinyinteger', [
                'default' => '7',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('email', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('codigo_telefone', 'smallinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('telefone', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('codigo_celular', 'smallinteger', [
                'default' => '21',
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('celular', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('curso', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => 'Serviço Social',
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('escola', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('universidade', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('anograduacao', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('profissao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('instituicao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('outro', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('situacao', 'string', [
                'collation' => 'utf8mb4_unicode_ci',
                'default' => '1',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('cpf')
                    ->setName('cpf')
                    ->setType('unique'),
            )
            ->create();

        $this->table('visitas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('instituicao_id', 'integer', [
                'comment' => 'ex estagio_id',
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('professor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('motivo', 'string', [
                'default' => null,
                'limit' => 256,
                'null' => false,
            ])
            ->addColumn('responsavel', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('avaliacao', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('visitas')
            ->addForeignKey(
                'professor_id',
                'professores',
                'id',
                [
                    'delete' => 'SET_NULL',
                    'update' => 'CASCADE',
                ],
            )
            ->update();

        $this->table('impersonations')
            ->addForeignKey(
                $this->foreignKey('impersonated_user_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('RESTRICT')
                    ->setName('impersonations_fk2'),
            )
            ->addForeignKey(
                $this->foreignKey('admin_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('RESTRICT')
                    ->setName('impersonations_fk1'),
            )
            ->update();
        // Legacy tables still present in production but absent from the
        // original snapshot; kept so fresh installs match the live schema.
        $this->table('categorias')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('categoria', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('agendamentotccs')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('convidado', 'string', [
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('banca1', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca2', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('horario', 'time', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('sala', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 180,
                'null' => false,
            ])
            ->addColumn('avaliacao', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->create();

        $this->table('areamonografias')
            ->addColumn('id', 'smallinteger', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('area', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('q_monografia', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('areamonografias_docentes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => false,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('areamonografia_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('atendentes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('tae_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('balcao_users')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('email', 'char', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('password', 'char', [
                'default' => null,
                'limit' => 80,
                'null' => true,
            ])
            ->addColumn('categoria', 'enum', [
                'default' => '1',
                'null' => false,
                'values' => ['1', '2', '3', '4'],
            ])
            ->addColumn('numero', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('timestamp', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('supervisor_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->create();

        $this->table('demandas')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('setor', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => false,
            ])
            ->addColumn('datademanda', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('atendente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('assunto', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('descripcao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('parecer', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('encaminhamento', 'string', [
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('encerramento', 'string', [
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('dataencerramento', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('historicos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('demanda_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('datahistorico', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('atendente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('relato', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('encaminhamento', 'string', [
                'default' => null,
                'limit' => 70,
                'null' => true,
            ])
            ->addColumn('observacao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('historicos')
            ->addIndex(['demanda_id'], ['name' => 'historicos_demanda_id'])
            ->addForeignKey(
                'demanda_id',
                'demandas',
                'id',
                [
                    'constraint' => 'historicos_demanda_id',
                    'delete' => 'RESTRICT',
                    'update' => 'RESTRICT',
                ],
            )
            ->update();

        $this->table('anexos')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('demanda_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('historico_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('estudante_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('nome_original', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('arquivo', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('tipo_documento', 'string', [
                'default' => 'Outros',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('mime_type', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('tamanho', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('anexos')
            ->addIndex(['demanda_id'], ['name' => 'anexos_demanda_id'])
            ->addIndex(['historico_id'], ['name' => 'anexos_historico_id'])
            ->addIndex(['estudante_id'], ['name' => 'anexos_estudante_id'])
            ->addForeignKey(
                'demanda_id',
                'demandas',
                'id',
                [
                    'constraint' => 'fk_anexos_demandas',
                    'delete' => 'CASCADE',
                    'update' => 'RESTRICT',
                ],
            )
            ->addForeignKey(
                'historico_id',
                'historicos',
                'id',
                [
                    'constraint' => 'fk_anexos_historicos',
                    'delete' => 'SET_NULL',
                    'update' => 'RESTRICT',
                ],
            )
            ->addForeignKey(
                'estudante_id',
                'alunos',
                'id',
                [
                    'constraint' => 'fk_anexos_estudantes',
                    'delete' => 'CASCADE',
                    'update' => 'RESTRICT',
                ],
            )
            ->update();

        $this->table('monografias')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('catalogo', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('titulo', 'string', [
                'default' => '',
                'limit' => 160,
                'null' => false,
            ])
            ->addColumn('resumo', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('data', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('periodo', 'string', [
                'default' => '',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('professor_id', 'smallinteger', [
                'default' => 0,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('num_co_orienta', 'smallinteger', [
                'default' => 0,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('areamonografia_id', 'smallinteger', [
                'default' => 0,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('areamonografia', 'integer', [
                'default' => 0,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('data_defesa', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('banca1', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca2', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('banca3', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('convidado', 'string', [
                'default' => null,
                'limit' => 70,
                'null' => false,
            ])
            ->addColumn('url', 'string', [
                'default' => null,
                'limit' => 15,
                'null' => true,
            ])
            ->addColumn('timestamp', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('tccestudantes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('nome', 'string', [
                'default' => '',
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('monografia_id', 'smallinteger', [
                'default' => 0,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('registro', 'char', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addIndex(['id'], ['name' => 'id', 'unique' => true])
            ->create();

        $this->table('turmaotps')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('configuraplanejamento_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('turno', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('periodo', 'tinyinteger', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('turmaotp', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('docente_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('dia_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('horario_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('sala_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'signed' => true,
            ])
            ->addColumn('observacoes', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     *
     * @return void
     */
    public function down(): void
    {
        $this->table('impersonations')
            ->dropForeignKey(
                'impersonated_user_id',
            )
            ->dropForeignKey(
                'admin_id',
            )->save();

        $this->table('acos')->drop()->save();
        $this->table('administradores')->drop()->save();
        $this->table('afastamentos')->drop()->save();
        $this->table('alunos')->drop()->save();
        $this->table('alunos_etica')->drop()->save();
        $this->table('alunos_etica_23042912')->drop()->save();
        $this->table('alunos_ingresso')->drop()->save();
        $this->table('alunos_ingresso-bak')->drop()->save();
        $this->table('alunos_ingresso_10-09-2014')->drop()->save();
        $this->table('alunos_ingresso_18042012')->drop()->save();
        $this->table('alunosestagiarios')->drop()->save();
        $this->table('alunosnovos_bak')->drop()->save();
        $this->table('areas')->drop()->save();
        $this->table('areas_do_professor_mon')->drop()->save();
        $this->table('areasmonografia')->drop()->save();
        $this->table('aros')->drop()->save();
        $this->table('aros_acos')->drop()->save();
        $this->table('auth_users')->drop()->save();
        $this->table('auth_users-bak')->drop()->save();
        $this->table('avaliacoes')->drop()->save();
        $this->table('bancas')->drop()->save();
        $this->table('complementos')->drop()->save();
        $this->table('configuracoes')->drop()->save();
        $this->table('configuraplanejamentos')->drop()->save();
        $this->table('curso_inscricao_instituicao')->drop()->save();
        $this->table('curso_inscricao_supervisor')->drop()->save();
        $this->table('curso_inst_super')->drop()->save();
        $this->table('dias')->drop()->save();
        $this->table('disciplinas')->drop()->save();
        $this->table('docentes')->drop()->save();
        $this->table('ementas')->drop()->save();
        $this->table('eprofesores')->drop()->save();
        $this->table('essextensoes')->drop()->save();
        $this->table('essusuarios')->drop()->save();
        $this->table('estagiarios')->drop()->save();
        $this->table('eusers')->drop()->save();
        $this->table('eventos')->drop()->save();
        $this->table('extensao_old')->drop()->save();
        $this->table('extensionistas')->drop()->save();
        $this->table('extensoes')->drop()->save();
        $this->table('folhadeatividades')->drop()->save();
        $this->table('historico_professor')->drop()->save();
        $this->table('horarios')->drop()->save();
        $this->table('impersonations')->drop()->save();
        $this->table('informacoes')->drop()->save();
        $this->table('inscricoes')->drop()->save();
        $this->table('inst_super')->drop()->save();
        $this->table('instituicoes')->drop()->save();
        $this->table('log_supervisores')->drop()->save();
        $this->table('migration_backup_20260414')->drop()->save();
        $this->table('monografia')->drop()->save();
        $this->table('monografia_20120910')->drop()->save();
        $this->table('mural_estagios')->drop()->save();
        $this->table('novo_usuarios')->drop()->save();
        $this->table('nucleos_pesquisa')->drop()->save();
        $this->table('optativas')->drop()->save();
        $this->table('orientacoes')->drop()->save();
        $this->table('planejamentos')->drop()->save();
        $this->table('pos_alunos')->drop()->save();
        $this->table('pos_alunos_disciplinas')->drop()->save();
        $this->table('pos_alunos_professores')->drop()->save();
        $this->table('pos_auxiliar')->drop()->save();
        $this->table('pos_disciplinas')->drop()->save();
        $this->table('prof_area')->drop()->save();
        $this->table('prof_disciplinas')->drop()->save();
        $this->table('prof_nucleopesquisa')->drop()->save();
        $this->table('professores')->drop()->save();
        $this->table('progressoes')->drop()->save();
        $this->table('publicacoes')->drop()->save();
        $this->table('qualificacoes')->drop()->save();
        $this->table('questionarios')->drop()->save();
        $this->table('questoes')->drop()->save();
        $this->table('respostas')->drop()->save();
        $this->table('roles')->drop()->save();
        $this->table('salas')->drop()->save();
        $this->table('situacaopr5')->drop()->save();
        $this->table('situacoes')->drop()->save();
        $this->table('supervisores')->drop()->save();
        $this->table('taes')->drop()->save();
        $this->table('tcc_alunos')->drop()->save();
        $this->table('test_cast')->drop()->save();
        $this->table('test_test')->drop()->save();
        $this->table('agendamentotccs')->drop()->save();
        $this->table('anexos')->drop()->save();
        $this->table('historicos')->drop()->save();
        $this->table('demandas')->drop()->save();
        $this->table('areamonografias')->drop()->save();
        $this->table('areamonografias_docentes')->drop()->save();
        $this->table('atendentes')->drop()->save();
        $this->table('balcao_users')->drop()->save();
        $this->table('categorias')->drop()->save();
        $this->table('monografias')->drop()->save();
        $this->table('tccestudantes')->drop()->save();
        $this->table('turmaotps')->drop()->save();
        $this->table('turmas')->drop()->save();
        $this->table('turnos')->drop()->save();
        $this->table('universidades')->drop()->save();
        $this->table('users')->drop()->save();
        $this->table('usuarios')->drop()->save();
        $this->table('viradas')->drop()->save();
        $this->table('visitas')->drop()->save();
    }
}
