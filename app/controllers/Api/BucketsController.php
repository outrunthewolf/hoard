<?php

namespace Api;

use Bucket;
use MongoId;

/**
 * @RoutePrefix('/api/buckets')
 */
class BucketsController extends ApiController
{
    /**
     * @Get("/")
     */
    public function indexAction()
    {
        $buckets = Bucket::find();
        $this->respondWith($buckets);
    }

    /**
     * @Get("/{id:[a-zA-Z0-9]+}")
     */
    public function showAction($id)
    {
        $bucket = Bucket::findById($id);
        $this->respondWith($bucket);
    }

    /**
     * @Get("/{id:[a-zA-Z0-9]+}/events")
     */
    public function eventAction($id)
    {
        $bucket = Bucket::findById($id);
        $this->respondWith($bucket->getEvents());
    }

    /**
     * @Post('/')
     */
    public function createAction()
    {
        // Get some data
        $payload = $this->request->getJsonRawBody();

        // Validate a bucket name
        if(!isset($payload->name)) 
            return $this->respondWith([], 400, "Buckets must have a lovely name!");

        // Create a new event
        $bucket = new Bucket;
        $bucket->name = $payload->name;
        $bucket->description = $payload->description;
        $bucket->save();
        $this->respondWith($bucket);
    }

    /**
    * @Route("/delete/{id:[a-z]+}", methods="DELETE")
    */
    public function deleteAction($name)
    {
        // Check the bucket id is being sent
        if(!isset($name))
            return $this->respondWith([], 400, "You must specify a bucket by name");

        // Delete the bucket
        $bucket = Bucket::findFirst("name='" . $name . "'");
        $bucket->delete();
        $this->respondWith("The bucket was deleted");
    }
}