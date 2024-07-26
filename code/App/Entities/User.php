<?php

namespace Entities;

use Safronik\Models\Domains\EntityObject;

class User extends EntityObject{
    
    protected mixed  $id;
    protected string $username;
    protected string $first_name;
    protected string $last_name;
    protected string $email;
    
    public static array $rules = [
        'id'         => [ 'required', 'type' => 'integer', 'length' => 11, 'content' => '@^[0-9]+$@',                         'extra' => 'AUTO_INCREMENT' ],
        'username'   => [ 'required', 'type' => 'string',  'length' => 64, 'content' => '@^[a-z0-9_-]+$@', ],
        'first_name' => [             'type' => 'string',  'length' => 64, 'content' => '@^[A-Za-z]+$@',      'default' => '', ],
        'last_name'  => [             'type' => 'string',  'length' => 64, 'content' => '@^[A-Za-z]+$@',      'default' => '', ],
        'email'      => [ 'required', 'type' => 'string',  'length' => 64, 'content' => '/^.+@.+\..{2,10}$/', ],
    ];
    
    public function __construct( ?int $id, string $username, ?string $first_name, ?string $last_name, string $email )
    {
        $this->id =         $id;
        $this->username =   $username;
        $this->first_name = $first_name;
        $this->last_name =  $last_name;
        $this->email =      $email;
    }
}