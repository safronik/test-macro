<?php

namespace Safronik\Models\Domains;

use Safronik\CodePatterns\Structural\Hydrator;

abstract class ValueObject{
    
    use Hydrator;
    
    public static array $rules = [];
    
    public function getRules()
    {
        return static::$rules;
    }
    
    public function toArray(): array
    {
        $output = [];
        foreach( $this as $key => $value ){
            
            if( $value instanceof ValueObject ){
                $output[ $key ] = $value->toArray();
                
            }elseif( is_array( $value)  && current( $value ) instanceof ValueObject ){
                
                foreach( $value as $value_key => $value_item ){
                    $output[ $key ][ $value_key ] = $value_item->toArray();
                }
                
            }else{
                $output[ $key ] = $value;
            }
        }
        
        return $output;
    }
    
    // public function __construct( $params = [] )
    // {
    //     $params
    //         && $this->hydrate( (array) $params );
    //
    //     method_exists( $this, '_init')
    //         && $this->_init( $params );
    //
    //     method_exists( $this, 'init')
    //         && $this->init();
    // }
    //
    // public function toArray(): array
    // {
    //     return (array) $this->storage;
    // }
    //
    // /**
    //  * Get changed values
    //  *
    //  * @return array
    //  */
    // public function getChanges(): array
    // {
    //     // Get rid of dynamic properties
    //     $intersection = array_uintersect_assoc(
    //         $this->storage,
    //         $this->_initial_storage,
    //         function($a, $b){
    //             return 0;
    //         }
    //     );
    //
    //     $initial = $this->_initial_storage;
    //
    //     // Returns only difference
    //     return array_filter(
    //         $intersection,
    //         function( $val, $key ){
    //
    //             // Convert to string if valueObject provide such opportunity
    //             $val = ! is_scalar( $val ) && ! is_null( $val ) && method_exists( get_class( $val ), '_serialize' )
    //                 ? $val->_serialize()
    //                 : $val;
    //
    //             return array_key_exists( $key, $this->_initial_storage ) && $val != $this->_initial_storage[ $key ];
    //         },
    //         ARRAY_FILTER_USE_BOTH
    //     );
    // }
    //
    // public function toObject(): object
    // {
    //     return (object) $this->storage;
    // }
    //
    // public function __get( $name )
    // {
    //     return $this->storage[$name] ?? null;
    // }
    //
    // public function __set( $name, $value )
    // {
    //     $this->storage[$name] = $value;
    // }
    //
    // public function __isset( $name )
    // {
    //     return isset( $this->storage[ $name ] );
    // }
}