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
     * @Get("/")
     */
    public function indexAction()
    {
        $events = Event::find();
        $this->respondWith($events);
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
            return $this->errorWith(400, "You must give your event a name");

        // Check we have a bucket id, We don't want erroneous data filling the halllways
        if(!isset($this->bucket_id))
            return $this->errorWith(400, "You must specify a bucket to save data to");

        // Create and store a new event
        $event = new Event;
        $event->name = $payload->name;
        $event->bucket_id = "ballask";//$payload->bucket_id;
        $event->data = $payload->data;
        $event->save();
        $this->respondWith($event);
    }
}
