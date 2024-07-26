<?php

namespace Safronik\Models\Domains;

abstract class EntityObject extends ValueObject{
    
    protected mixed $id;
    public static array $rules;
    
    public function getId()
    {
        return $this->id;
    }
    
    // public function __isset( string $name ): bool
    // {
    //     return isset( $this->$name );
    // }
    //
    // public function __get( string $name )
    // {
    //     return $this->$name;
    // }
    //
    // public function __set( string $name, $value ): void
    // {
    //     property_exists( static::class, $name)
    //         && throw new \Exception("No property $name exists for entity " . static::class );
    //
    //     $this->$name = $value;
    // }
    
    public static function fabric( $data ): array
    {
        foreach( $data as &$datum ){
            $datum = new static( ...$datum );
        }
        
        return $data;
    }
}