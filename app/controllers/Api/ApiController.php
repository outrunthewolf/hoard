<?php

namespace Api;

use Phalcon\Mvc\Controller as BaseController;

class ApiController extends BaseController
{
    /**
     * Send back a nice response
     * 
     * @param $resource mixed
     */
    public function respondWith($resource)
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent([
            'meta' => [],
            'resource' => $resource,
        ]);
        $this->response->send();
    }

    /**
     * Send back an error response bitches
     * 
     * @param $status number
     * @param $message mixed
     */
    public function errorWith($status, $message)
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setStatusCode($status, http_response_code($status));
        $this->response->setJsonContent([
            'status' => $status,
            'message' => $message
        ]);
        $this->response->send();
    }
}
