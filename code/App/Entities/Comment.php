<?php

namespace Entities;

use Safronik\DB\DB;
use Safronik\Models\Domains\EntityObject;
use Safronik\Repositories\Repository;

class Comment extends EntityObject{
    
    protected mixed  $id;
    protected string $commenter;
    protected mixed  $user;
    protected mixed  $article;
    protected string $body;
    protected string $approved;
    
    public static array $rules = [
        'id'        => [ 'required', 'type' => 'integer',      'length' => 11,                                         'extra' => 'AUTO_INCREMENT' ],
        'commenter' => [ 'required', 'type' => 'string',       'length' => 255, 'content' => '@^[A-Za-z.-_\s]+$@', ],
        'user'      => [             'type' => User::class,    'length' => 11, ],
        'article'   => [ 'required', 'type' => Article::class, 'length' => 11, ],
        'body'      => [ 'required', 'type' => 'string',       'length' => 200, 'content' => '@^.+$@', 'default' => '', ],
        'approved'  => [ 'required', 'type' => 'integer',      'length' => 1,                          'default' => 0, ],
    ];
    
    public function __construct( ?int $id, string $commenter, ?int $user, int $article, string $body, string $approved )
    {
        $this->id        = $id;
        $this->commenter = $commenter;
        $this->user      = $user;
        $this->article   = $article;
        $this->body      = $body;
        $this->approved  = $approved;
    }
    
    public static function fabricRandom( int $amount ): array
    {
        $user_amount = ( new Repository( DB::getInstance(), User::class ) )
            ->count();
        
        $article_amount = ( new Repository( DB::getInstance(), Article::class ) )
            ->count();
        
        $names = [
            'Vanya',
            'Petya',
            'Sanya',
            'Vova',
            'Liza',
        ];
        
        for( $i = 1, $data = []; $i !== $amount; $i++ ){
            $data[] = [
                'id'        => $i,
                'commenter' => $names[ random_int(0, count( $names ) - 1 ) ],
                'user'      => random_int(0, $user_amount) ?: null,
                'article'   => random_int(1, $article_amount),
                'body'      => hash( 'sha256', random_int(1, 9999999)),
                'approved'  => 1,
            ];
        }
     
        return parent::fabric( $data ); // TODO: Change the autogenerated stub
    }
}