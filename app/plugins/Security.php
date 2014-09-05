<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

class Security extends Plugin
{

    protected $user;

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {

        // Check whether the "auth" variable exists in session to define the active role
        if ($this->session->has('auth_id')) {
            $user_id = $this->session->get('auth_id');
            $this->user = $user_id ? User::findById($user_id) : null;
        }

        // Take the active controller/action from the dispatcher
        $namespace = strtolower($dispatcher->getNamespaceName());
        $controller = strtolower($dispatcher->getControllerName());
        $action = $dispatcher->getActionName();

        // Skips API routes (for now)
        if ($namespace === 'api') {
            return true;
        }

        // Key check for API
        /*
                // Get the header and look for an auth key
        $header = $this->request->getHeaders();
        $authCode = isset($header['AUTHORIZATION']) ? str_replace('Bearer', '', $header['AUTHORIZATION']) : false;

        // Check we're authorised
        if(!$authCode) {
            $this->respondWith([], 403, "No access key");
            exit;
        }

        // Check for a user with the key
        $user = User::findFirst(array(
            "key" => $authCode
        ));

        // Deny the request if the key isn't active
        if($user->count() < 1) {
            $this->respondWith([], 403, "Access denied");
            exit;
        }
        */

        // Redirect to /login if user is not logged in
        if ($controller !== 'sessions' && ! $this->user) {
            $this->view->disable();
            $this->response->redirect('login');
            return false;
        }

    }

}
