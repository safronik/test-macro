<?php

namespace Controllers\Api;

use Entities\Comment;
use Models\Comments;
use Safronik\Helpers\ValidationHelper;

final class CommentController extends ApiController{
    
    public function methodGet(): void
    {
         try{
            ValidationHelper::validate(
                $this->request->parameters,
                [
                    'id' => Comment::$rules['id']
                ]
            );
            
            $comment = Comments::find([ 'id' => $this->request->parameters['id'] ] );
            
            $this->outputSuccess( $comment );
            
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
                    'commenter' => Comment::$rules['commenter'],
                    'user'      => Comment::$rules['user'],
                    'article'   => Comment::$rules['article'],
                    'body'      => Comment::$rules['body'],
                    'approved'  => Comment::$rules['approved'],
            ] );
            
            $inserted_ids = Comments::new(
                [
                    'commenter' => $this->request->body['commenter'],
                    'user'      => $this->request->body['user'] ?? null,
                    'article'   => $this->request->body['article'],
                    'body'      => $this->request->body['body'],
                    'approved'  => $this->request->body['approved'],
                ]
            );
            
            $this->outputSuccess(
                [ 'inserted_ids' => $inserted_ids ],
                'Comments added'
            );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception->getMessage() );
        }
    }
}