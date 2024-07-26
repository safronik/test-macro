<?php

namespace Models;

use Entities\Article;
use Entities\Comment;
use Safronik\DB\DB;
use Safronik\Repositories\Repository;

class Articles extends Model{
    
    use Pagination;
    
    protected static string $entity = Article::class;
    
    /**
     * Returns Articles considering pagination
     *
     * @param int $page_number
     *
     * @return Article[]
     * @throws \Exception
     */
    public function getByPage( int $page_number = 1 ): array
    {
        $this->calculatePagination( $page_number );
        
        $repo = new Repository( $this->db, static::$entity );
        /** @var Article $article */
        $articles = $repo->read(
            [],
            $this->amount,
            $this->offset,
        );
        
        $comments_repo = new Repository( DB::getInstance(), Comment::class );
        foreach( $articles as $article ){
            $article->setComments(
                $comments_repo->read(
                    [ 'article' => $article->getId() ],
                    3
                )
            );
        }
        
        return $articles;
    }
    
    /**
     * Returns comment of the article
     *
     * @param mixed    $article_id
     * @param int|null $offset
     * @param int|null $amount
     * @param int|null $page_number
     *
     * @return \Safronik\Models\Domains\EntityObject|\Safronik\Models\Domains\EntityObject[]
     * @throws \Exception
     */
    public function getComments( mixed $article_id, ?int $offset = null, ?int $amount = null, ?int $page_number = null ): array|\Safronik\Models\Domains\EntityObject
    {
        if( $page_number ){
            $this->calculatePagination( $page_number );
        }else{
            $this->setOffset( (int)$offset );
            $this->setAmount( (int)$amount );
        }
        
        return ( new Repository( DB::getInstance(), Comment::class ) )
            ->read(
                [ 'article' => $article_id ],
                $this->amount,
                $this->offset
            );
    }
}