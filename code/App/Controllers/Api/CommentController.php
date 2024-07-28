<?php

namespace Controllers\Api;

use Entities\Comment;
use Models\ApiCallLimit;
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
            $this->outputError( $exception );
        }
    }
    
    public function methodPost(): void
    {
        try{
            
            // Control call limits
            // The parameters could be taken from somewhere, from config, for instance
            $api_limit = new ApiCallLimit(
                60,
                1,
                [ 'commenter' => $this->request->body['commenter'] ]
            );
            $api_limit->controlCallLimit();
            
            ValidationHelper::validate(
                $this->request->body,
                [
                    'commenter' => Comment::$rules['commenter'],
                    'user'      => Comment::$rules['user'],
                    'article'   => Comment::$rules['article'],
                    'body'      => Comment::$rules['body'],
                    'approved'  => Comment::$rules['approved'],
            ] );
            
            $inserted_id = Comments::new( [
                    'commenter' => $this->request->body['commenter'],
                    'user'      => $this->request->body['user'] ?? null,
                    'article'   => $this->request->body['article'],
                    'body'      => $this->request->body['body'],
                    'approved'  => $this->request->body['approved'],
                ] )[0];
            
            $this->outputSuccess(
                [ 'inserted_id' => $inserted_id ],
                'Comments added'
            );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception );
        }
    }
}