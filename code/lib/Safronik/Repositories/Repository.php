<?php

namespace Safronik\Repositories;

use Safronik\DB\DB;
use Safronik\Helpers\SanitizerHelper;
use Safronik\Helpers\ValidationHelper;
use Safronik\Models\Domains\EntityObject;
use Safronik\Models\SchemaProviders\EntitySchemaProvider;

class Repository implements EntityRepositoryInterface
{
    /** @var DB */
    protected DB $db;
    
    /** @var string|EntityObject Contains classname */
    protected string|EntityObject $entity;
    protected string $table;
    
    /**
     * @param string|EntityObject $entity Entity classname or EntityObject
     * @param DB                  $db
     *
     * @throws \Exception
     */
    public function __construct( DB $db, string|EntityObject $entity )
    {
        $this->db     = $db;
        $this->entity = $entity;
        $this->table  = ( new EntitySchemaProvider( $entity, 'Entities' ) )->getEntityTable();
    }
    
    /**
     * Creates an entity from given data.
     * Validates data by built-in entity rules and entity SQL-schema rules
     *
     * Validates only new entities. No validation if data comes from DB.
     *
     * @param array $data
     * @param bool  $new
     *
     * @return EntityObject[]|EntityObject
     */
    public function create( array $data, bool $new = false ): array|EntityObject
    {
        $rules = $this->entity::$rules;
        
        if( $new ){
            unset( $rules['id'] );
        }
        
        $entities = [];
        foreach( $data as &$datum ){
            
            $datum['id'] ??= null;
            
            ValidationHelper::validate( $datum, $rules );
            SanitizerHelper::sanitize( $datum, $rules );
            
            $entities[] = new $this->entity( ...$datum );
            
        } unset( $datum );
        
        return $entities;
    }
    
    /**
     * Returns an array of entities or single entity
     *
     * @param array    $condition
     * @param int|null $amount
     * @param int|null $offset
     *
     * @return EntityObject|EntityObject[]
     * @throws \Exception
     */
    public function read( array $condition = [], ?int $amount = null, ?int $offset = null ): array|EntityObject
    {
        $entity_data = $this->db
            ->select( $this->table )
            ->where( $condition )
            ->limit( $amount, $offset )
            ->run();
        
        return $this->create( $entity_data );
    }
    
    /**
     * Recursive
     * Saves fully valid entity to the DB
     *
     * @param EntityObject|EntityObject[] $items
     *
     * @return int|string|int[]|string[]
     * @throws \Exception
     */
    public function save( EntityObject|array $items ): int|string|array
    {
        // Recursion
        if( is_array( $items ) ){
            $inserted_ids = [];
            foreach( $items as $item ){
                $inserted_ids[] = $this->save( $item );
            }
            
            return $inserted_ids;
        }
        
        // Base case
        $values = array_filter(
            $items->toArray(),
            static function ( $val ){
                return $val !== null;
            }
        );
        
        $this->db
            ->insert($this->table )
            ->columns( array_keys( $values ) )
            ->values( $values )
            ->onDuplicateKey( 'update', $values )
            ->run();
        
        return $this->db
            ->query('SELECT last_insert_id() as id')
            ->fetch()['id'];
    }
    
    /**
     * Deletes entities or one entity from the SQL
     *
     * @param $condition
     *
     * @return int
     * @throws \Exception
     */
    public function delete( $condition ): ?int
    {
        return $this->db
            ->delete( $this->table )
            ->where( $condition )
            ->run();
    }
    
    /**
     * Counts entities in table
     *
     * @param array $condition
     *
     * @return int
     * @throws \Exception
     */
    public function count( array $condition = [] ): int
    {
        return $this->db
            ->select( $this->table )
            ->where( $condition )
            ->count();
    }
}