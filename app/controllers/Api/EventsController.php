<?php

namespace Api;

use Event;
use MongoId;

/**
 * @RoutePrefix("/api/events")
 */
class EventsController extends ApiController
{

    /**
     * @Get("/{id:[a-z]+}")
     */
    public function indexAction()
    {
        $events = Event::find();
        $this->respondWith($events);
    }

    /**
     * @Route("/name/{name:[a-z]+}", methods="GET")
     */
    public function idAction($name)
    {
        $events = Event::find("name='{$name}'");
        print_r($events);
    }

    /**
     * @Post("/")
     */
    public function createAction()
    {
        // Get the JSON body
        $payload = $this->request->getJsonRawBody();

        // Check for a name
        if(!isset($payload->name))
            return $this->respondWith([], 400, "You must give your event a name");

        // Check we have a bucket id
        if(!isset($payload->bucket_id))
            return $this->respondWith([], 400, "You must specify a bucket to save data to");

        // Create and store a new event
        $event = new Event;
        $event->name = $payload->name;
        $event->bucket_id = new MongoId($payload->bucket_id);
        $event->data = $payload->data;
        $event->save();
        $this->respondWith($event);
    }
}
