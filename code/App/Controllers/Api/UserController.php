<?php

namespace Controllers\Api;

use Entities\User;
use Models\Users;
use Safronik\Helpers\ValidationHelper;
use Views\JsonView;

final class UserController extends ApiController{
    
    public function methodGet(): void
    {
        try{
            ValidationHelper::validate(
                $this->request->parameters,
                [
                    'id' => User::$rules['id']
                ]
            );
            
            $user = Users::find([ 'id' => $this->request->parameters['id'] ] );
            
            $this->outputSuccess( $user );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception );
        }
    }
    
    public function methodPost(): void
    {
        try{
            ValidationHelper::validate(
                $this->request->body,
                [
                    'username'   => User::$rules['username'],
                    'first_name' => User::$rules['first_name'],
                    'last_name'  => User::$rules['last_name'],
                    'email'      => User::$rules['email'],
                ]
            );
            
            $inserted_id = Users::new( [
                    'username'   => $this->request->body['username'],
                    'first_name' => $this->request->body['first_name'] ?? null,
                    'last_name'  => $this->request->body['last_name']  ?? null,
                    'email'      => $this->request->body['email'],
                ] )[0];
            
            $this->outputSuccess(
                [ 'inserted_id' => $inserted_id ],
                'Users added'
            );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception );
        }
    }
    
    public function methodDelete(): void
    {
        try{
            ValidationHelper::validate(
                $this->request->parameters,
                [
                    'id' => User::$rules['id']
                ]
            );
            
            Users::remove( ['id' => $this->request->parameters['id'] ] );
            
            $this->outputSuccess(
                [],
                "User with id {$this->request->parameters['id']} is deleted"
            );
            
        }catch( \Exception $exception ){
            $this->outputError( $exception );
        }
    }
}