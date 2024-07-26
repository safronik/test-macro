<?php

namespace Safronik\Models\SchemaProviders;

use Safronik\DBMigrator\Objects\Column;
use Safronik\DBMigrator\Objects\Constraint;
use Safronik\DBMigrator\Objects\Index;
use Safronik\DBMigrator\Objects\Table;
use Safronik\Models\Domains\EntityObject;
use Safronik\Models\Domains\ValueObject;

class EntitySchemaProvider
{
    
    private string $entity_root_namespace;
    private string $entity_slug;
    private array  $entity_route;
    private int    $default_object_length = 2048;
    private int    $default_string_length = 64;
    private int    $default_integer_length = 11;
    private ValueObject|string $entity_classname;
    
    public function __construct( string|object $entity, string $entity_root_namespace = 'Safronik\Models\Domains' )
    {
        $this->entity_root_namespace = $entity_root_namespace;
        $this->entity_slug           = $this->getEntitySlug( $entity );
        $this->entity_classname      = is_object( $entity ) ? $entity::class : $entity;
        $this->entity_route          = $this->getEntityRoute( $this->entity_slug );
    }
    
    /**
     * Get entity path from the entity root path
     *
     * @param string|object $entity
     *
     * @return string
     * @throws \Exception
     */
    private function getEntitySlug( string|object $entity ): string
    {
        $entity_classname = is_object( $entity )
            ? $entity::class
            : $entity;
        
        class_exists( $entity_classname )
            || throw new \Exception( "Entity: '$entity_classname' is missing " );
        
        return str_replace( $this->entity_root_namespace. '\\', '', $entity_classname );
    }
    
    /**
     * Breaks entity path into route
     *
     * @param string|null $entity_slug
     *
     * @return array
     */
    private function getEntityRoute( string $entity_slug = null ): array
    {
        return array_map(
            static function( $val ){ return strtolower( $val ); },
            explode( '\\', $entity_slug )
        );
    }
    
    /**
     * Returns entity table name
     *
     * @param $entity_route
     *
     * @return string
     */
    public function getEntityTable( $entity_route = null ): string
    {
        $entity_route = $entity_route ?? $this->entity_route;
        
        return implode( '_', $entity_route );
    }
    
    /**
     * Returns entity table schema without secondary tables
     *
     * @return Table
     * @throws \Exception
     */
    public function getEntitySchema(): Table
    {
        return new Table(
            $this->getEntityTable(),
            $this->getEntityColumns(),
            $this->getEntityIndexes()
        );
    }
    
    /**
     * Get indexes for the entity
     *
     * @return Index[]
     * @throws \Exception
     */
    public function getEntityIndexes(): array
    {
        $indexes = [];
        if( isset( $this->entity_classname::$rules['id'] ) ){
            $indexes[] = new Index( [ 'key_name' => 'PRIMARY', 'columns' => [ 'id' ], 'comment' => 'Primary ID' ] );
        }
        
        return $indexes;
    }
    
    /**
     * Creates secondary tables to support the structure
     *
     * @return Table[]
     * @throws \Exception
     */
    public function getRelationTablesSchemas(): array
    {
        $schemas = [];
        foreach( $this->entity_classname::$rules as $rule ){
            
            if(
                class_exists( $rule['type'] ) &&
                is_subclass_of( $rule['type'], EntityObject::class ) &&
                isset( $rule['length'] ) && $rule['length'] > 1
            ){
                $entity_table        = $this->getEntityTable();
                $entity_table_column = $entity_table . '_id';
                
                $sub_entity_table        = $this->getEntityTable( $this->getEntityRoute( $this->getEntitySlug( $rule['type'] ) ) ); // todo refactor
                $sub_entity_table_column = $sub_entity_table . '_id';
                
                $schemas[] = new Table(
                    $entity_table . '__' . $sub_entity_table,
                    [
                        new Column( ['field' => $entity_table_column,     'type' => 'VARCHAR(64)', 'null' => 'NO' ], ),
                        new Column( ['field' => $sub_entity_table_column, 'type' => 'VARCHAR(64)', 'null' => 'NO' ], ),
                    ],
                    [
                        new Index( [
                            'key_name' => $entity_table_column,
                            'columns'  => [ $entity_table_column, $sub_entity_table_column, ],
                            'unique'   => true,
                        ] )
                    ],
                    [
                        new Constraint([
                            'name'             => "FK_$entity_table" . '_' . $sub_entity_table,
                            'column'           => $entity_table_column,
                            'reference_table'  => $entity_table,
                            'reference_column' => 'id',
                        ]),
                        new Constraint( [
                            'name'             => "FK_$sub_entity_table" . '_' . $entity_table,
                            'column'           => $sub_entity_table_column,
                            'reference_table'  => $sub_entity_table,
                            'reference_column' => 'id',
                        ]),
                    ],
                );
            }
        }
        
        return $schemas;
    }
    
    /**
     * Returns entity columns SQL-scheme
     *
     * @return Column[]
     * @throws \Exception
     */
    public function getEntityColumns(): array
    {
        $columns = [];
        foreach( $this->entity_classname::$rules as $field => $rule ){
            
            $type = $this->convertRuleTypeToSQLType( $rule );
            
            if( $type === 'entity' || in_array( 'entity', $rule, true ) ){
                $rule['type'] = 'integer';
                $columns[] = new Column( [
                    'field' => $field,
                    'type'  => $this->convertRuleTypeToSQLType( $rule ),
                    'null'  => in_array( 'required', $rule, true ) ? 'NO' : 'YES',
                    'default' => $rule['default'] ?? null,
                ] );
            
            // Self reference. Hierarchic construction. Adding service field 'parent'
            }elseif( $type === 'self' ){
                $columns[] = new Column( [
                    'field' => 'parent',
                    'type'  => $this->convertRuleTypeToSQLType( $this->entity_classname::$rules['id'] ),
                    'null'  => 'YES',
                ] );
                
            // Usual field. Direct representation into SQL-schema
            }else{
                $columns[] = new Column( [
                    'field' => $field,
                    'type'  => $type,
                    'null'  => in_array( 'required', $rule, true ) ? 'NO' : 'YES',
                    'default' => $rule['default'] ?? null,
                    'extra'   => $rule['extra']   ?? null,
                ] );
            }
        }
        
        return $columns;
    }
    
    /**
     *
     *
     * @param array $rule
     *
     * @return string
     */
    private function convertRuleTypeToSQLType( array $rule ): string
    {
        $type = $rule['type'];
        
        // Object type
        if( class_exists( $type ) ){
            
            // Other EntityObject
            if( is_subclass_of($type, EntityObject::class ) ){
                $type = 'entity';
            
            // Self
            }elseif( is_subclass_of($type, $this->entity_classname ) ){
                $type = 'self';
            
            // Single or multiple ValueObject
            }elseif( is_subclass_of($type, ValueObject::class ) ){
                $length = ( $rule['length'] ?? 1 ) * $this->default_object_length;
                $type = $length < 16383
                    ? 'VARCHAR(' . $length . ')'
                    : 'TEXT';
            }
            
        // Scalar type
        }elseif( is_scalar( $type ) ){
            
            $type = match( $type ){
                'string' => isset( $rule['content'] ) && is_array( $rule['content'])
                    ? 'ENUM(\'' . implode( "','", $rule['content'] ) . '\')'
                    : 'VARCHAR(' . ( $rule['length'] ?? $this->default_string_length ) . ')',
                'integer' => 'INT(' . ( $rule['length'] ?? $this->default_integer_length ) . ')',
            };
            
        }
        
        return $type;
    }
}