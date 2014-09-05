<?php

namespace Api;

use User;
use MongoId;

/**
 * @RoutePrefix("/api/users")
 */
class UsersController extends ApiController
{

    /**
     * @Get("/")
     */
    public function indexAction()
    {
        $users = User::find();
        $this->respondWith($users);
    }

    /**
     * @Get("/{id:[a-zA-Z0-9]+}")
     */
    public function showAction($id)
    {
        $users = User::findById($id);
        $this->respondWith($users);
    }

    /**
     * @Post("/")
     */
    public function createAction()
    {
        // Get the JSON body
        $payload = $this->request->getJsonRawBody();

        // Check we have a bucket id
        if(!isset($payload->username))
            return $this->respondWith([], 400, "You must specify a username");

        // Check we have a bucket id
        if(!isset($payload->email))
            return $this->respondWith([], 400, "You must specify an email");

        // Check we have a bucket id
        if(!isset($payload->password))
            return $this->respondWith([], 400, "You must specify a password");

        // Create a new user
        $user = new User;
        $user->getConnection()->drop();
        $user->_id = new MongoId();
        $user->username = $payload->username;
        $user->email = $payload->email;
        $user->password = password_hash($payload->password, PASSWORD_BCRYPT, ['cost' => 13]);
        $user->save();

        $this->respondWith($user);
    }
}
