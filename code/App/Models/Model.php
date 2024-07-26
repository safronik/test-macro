<?php

namespace Models;

use Entities\User;
use Safronik\DB\DB;
use Safronik\Repositories\Repository;

abstract class Model{

    protected $db;
    
    public function __construct()
    {
        $this->db = DB::getInstance();
    }
    
    /**
     * @param $condition
     *
     * @return array|\Safronik\Models\Domains\EntityObject
     * @throws \Exception
     */
    public static function find( $condition = [] ): array|\Safronik\Models\Domains\EntityObject
    {
        return ( new Repository( DB::getInstance(), static::$entity ) )
            ->read( $condition );
    }
    
    /**
     * @param ...$data
     *
     * @return int|string|array
     * @throws \Exception
     */
    public static function new( ...$data ): int|string|array
    {
        $repo = new Repository( DB::getInstance(), static::$entity );
        $entities = $repo->create(
            $data,
            true
        );
        
        return $repo->save( $entities );
    }
    
    /**
     * @param $condition
     *
     * @return int
     * @throws \Exception
     */
    public static function remove( $condition ): int
    {
        return ( new Repository( DB::getInstance(), static::$entity ) )
            ->delete( $condition );
    }
}