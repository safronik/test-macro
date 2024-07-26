<?php

namespace Controllers\Api;

use Entities\Article;
use Models\Articles;
use Safronik\Helpers\ValidationHelper;

final class ArticleController extends ApiController{
    
    public function methodGet(): void
    {
         try{
            ValidationHelper::validate(
                $this->request->parameters,
                [
                    'id' => Article::$rules['id']
                ]
            );
            
            $this->outputSuccess(
                Articles::find(
                    ['id' => $this->request->parameters['id'] ]
                )
            );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception->getMessage() );
        }
    }
    
    public function methodPost(): void
    {
        try{
            ValidationHelper::validate(
                $this->request->body,
                [
                    'author'  => Article::$rules['author'],
                    'heading' => Article::$rules['heading'],
                    'body'    => Article::$rules['body'],
                ]
            );
            
            $inserted_ids = Articles::new( [
                'author'  => $this->request->body['author'],
                'heading' => $this->request->body['heading'],
                'body'    => $this->request->body['body'],
            ] );
            
            $this->outputSuccess(
                [ 'inserted_ids' => $inserted_ids ],
                'Articles added'
            );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception->getMessage() );
        }
    }
    
    public function actionList(): void
    {
        try{
            ValidationHelper::validate(
                $this->request->parameters,
                [
                    // 'id'          => Article::$rules['id'],
                    'page_number' => [ 'type' => 'integer' ],
                ]
            );
            
            $this->outputSuccess(
                ( new Articles() )
                    ->getByPage( $this->request->parameters['page_number'] )
            );
            
        }catch( \Exception $exception){
            $this->outputError( $exception->getMessage() );
        }
    }

    public function actionComments(): void
    {
        try{
            ValidationHelper::validate(
                $this->request->parameters,
                [
                    'id'          => Article::$rules['id'],
                    'offset'      => [ 'type' => 'integer' ],
                    'amount'      => [ 'type' => 'integer' ],
                    'page_number' => [ 'type' => 'integer' ],
                ]
            );
            
            $comments = ( new Articles() )
                ->getComments(
                    $this->request->parameters['id'],
                    $this->request->parameters['amount'] ?? null,
                    $this->request->parameters['offset'] ?? null,
                    $this->request->parameters['page_number'] ?? null
                );
            
            $this->outputSuccess( $comments );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception->getMessage() );
        }
    }
}