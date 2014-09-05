<?php

namespace Api;

use Phalcon\Mvc\Controller as BaseController;
use User;

class ApiController extends BaseController
{
    /**
     * Send back a nice response
     * 
     * @param $resource mixed
     * @param $status int
     * @param $message string
     */
    public function respondWith($resource, $status = 200, $message = "")
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setStatusCode($status, http_response_code($status));
        $this->response->setJsonContent([
            'meta' => [],
            'resource' => $resource,
            'message' => $message
        ]);
        $this->response->send();
    }
}
