<?php

namespace Safronik\Gateways;

use Safronik\DB\DB;
use Safronik\DBMigrator\DBMigratorGatewayInterface;
use Safronik\DBMigrator\Objects\Column;
use Safronik\DBMigrator\Objects\Constraint;
use Safronik\DBMigrator\Objects\Index;
use Safronik\DBMigrator\Objects\Table;

final class DBMigratorGateway implements DBMigratorGatewayInterface
{
    protected DB $db;
    
    public function __construct( DB $db )
    {
        $this->db = $db;
    }
    
    public function isTableExists( $table ): bool
    {
        return $this->db->isTableExists( $table );
    }
    
    public function createTable( Table $table, bool $if_not_exists = true ): bool
    {
        $columns     = array_map( static fn( $column )     => (string)$column,     $table->getColumns() ?? [] );
        $indexes     = array_map( static fn( $index )      => (string)$index,      $table->getIndexes() ?? [] );
        $constraints = array_map( static fn( $constraint ) => (string)$constraint, $table->getConstraints() ?? [] );
        
        return $this->db->createTable(
            $table->getTableName(),
            $columns,
            $indexes,
            $constraints,
            $if_not_exists
        );
    }
    
    public function dropTable( $table ): bool
    {
        return $this->db->dropTable( $table );
    }
    
    public function alterTable($table, array $columns = [], array $indexes = [], array $constraints = [] ): bool
    {
        /** @var Column $column */
        $sql_columns = [];
        foreach( $columns['update'] as $column ){
            $sql_columns[] = 'CHANGE COLUMN `' . $column->getField() . '` ' . $column;
        }
        foreach( $columns['create'] as $column ){
            $sql_columns[] = 'ADD COLUMN ' . $column;
        }
        foreach( $columns['delete'] as $column ){
            $sql_columns[] = 'DROP COLUMN `' . $column->getField() . '`';
        }
        
        /** @var Index $index */
        $sql_indexes = [];
        foreach( $indexes['create'] as $index ){
            $sql_indexes[] = 'ADD ' . $index;
        }
        foreach( $indexes['update'] as $index ){
            $sql_indexes[] = 'DROP `' . $index->getKeyName() . '`'; // @todo
            $sql_indexes[] = 'ADD ' . $index;
        }
        foreach( $indexes['delete'] as $index ){
            $sql_indexes[] = 'DROP `' . $index->getKeyName() . '`'; // @todo
        }
        
        /** @var Constraint $index */
        $sql_constraints = [];
        foreach( $constraints['create'] as $constraint ){
            $sql_constraints[] = 'ADD ' . $constraint;
        }
        foreach( $constraints['update'] as $constraint ){
            $sql_constraints[] = 'ADD ' . $constraint;
        }
        foreach( $constraints['delete'] as $constraint ){
            $sql_constraints[] = 'ADD ' . $constraint;
        }
        
        return $this->db->alterTable( $table, $sql_columns, $sql_indexes, $sql_constraints );
    }
    
    public function getTableColumns( string $table ): array
    {
        return $this->db
            ->setResponseMode( 'array' )
            ->prepare(
                'SHOW COLUMNS FROM :table;',
                [ ':table' => [ $table, 'table' ] ] )
            ->query()
            ->fetchAll();
    }
    
    public function getTableIndexes( string $table ): array
    {
        return $this->db
            ->setResponseMode( 'array' )
            ->prepare(
                'SHOW INDEXES FROM :table;',
                [ ':table' => [ $table, 'table' ] ] )
            ->query()
            ->fetchAll();
    }
    
    public function getTableConstraints( string $table ): array
    {
        return $this->db
            ->setResponseMode( 'array' )
            ->select( 'information_schema.key_column_usage' )
                ->columns([
                    'constraint_name',
                    'column_name',
                    'referenced_table_name',
                    'referenced_column_name',
                ])
                ->where( [ 'referenced_table_name' => [ 'is', [ 'NOT NULL', 'serve_word' ] ] ] )
                ->and( [ 'table_schema' => [ '=', [ 'DATABASE()', 'function' ] ] ] )
                ->and( [ 'table_name' => $table ] )
                ->run();
    }
    
    public function getTablesNames(): array
    {
        $table_names     = [];
        $table_names_raw = $this->db
            ->query('SHOW TABLES')
            ->fetchAll();
        
        foreach($table_names_raw as $table_name){
            $table_names[] = current($table_name );
        }
        
        return $table_names;
    }
}