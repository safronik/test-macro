<?php

namespace Safronik\Repositories;

use Safronik\Models\Domains\EntityObject;

interface EntityRepositoryInterface{
    
    /** Additional methods */
    // public function setEntity( $entity_classname ): void;
    
    /** CRUD */
    public function create( array $data ): array|EntityObject;
    public function read( array $condition = [], ?int $amount = null, ?int $offset = null ): array|EntityObject;
    public function save( EntityObject $items ): int|string|array;
    public function delete( $condition ): ?int;
    
    /** Additional */
    public function count( array $condition ): int;
    
}