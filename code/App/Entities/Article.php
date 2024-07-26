<?php

namespace Entities;

use Safronik\Models\Domains\EntityObject;

class Article extends EntityObject{
    
    protected mixed  $id;
    protected mixed  $author;
    protected string $heading;
    protected string $body;
    
    /** @var array Comment[] */
    protected array  $comments;
    
    public static array $rules = [
        'id'      => [ 'required', 'type' => 'integer',   'length' => 11,   'content' => '@^[0-9]+$@', 'extra' => 'AUTO_INCREMENT' ],
        'author'  => [ 'required', 'type' => User::class, 'length' => 11, ],
        'heading' => [ 'required', 'type' => 'string',    'length' => 255,  'content' => '@^.+$@', ],
        'body'    => [ 'required', 'type' => 'string',    'length' => 5000, 'content' => '@^[\s\S]+$@', ],
    ];
    
    public function __construct( ?int $id, int $author, string $heading, string $body )
    {
        $this->id      = $id;
        $this->author  = $author;
        $this->heading = $heading;
        $this->body    = $body;
    }
    
    /**
     * @param Comment[] $comments
     */
    public function setComments( array $comments): void
    {
        $this->comments = $comments;
    }
}