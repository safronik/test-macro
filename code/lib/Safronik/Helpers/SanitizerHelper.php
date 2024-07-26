<?php

namespace Safronik\Helpers;

class SanitizerHelper
{
    public static function sanitize( &$data, ...$rule_set ): void
    {
        foreach( $rule_set as $rules ){
            foreach( $rules as $field => $rule ){
                self::setMissingOptionalToNull( $data, $field, $rule );
                self::setEmptyFieldsToDefault( $data, $field, $rule );
            }
        }
    }
    
    private static function setMissingOptionalToNull( &$data, $field, $rule ): void
    {
        $data[ $field ] = ! in_array( 'required', $rule, true ) && ! isset( $data[ $field ] )
            ? null
            : $data[ $field ];
    }
    
    private static function setEmptyFieldsToDefault( array &$data, string $field, $rule ): void
    {
        $data[ $field ] = ! isset( $data[ $field ] ) && isset( $rule['default'] )
            ? $rule['default']
            : $data[ $field ];
    }

}