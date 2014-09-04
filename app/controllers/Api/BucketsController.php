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
     * @Get('/')
     */
    public function indexAction()
    {
        $events = Bucket::find();
        $this->respondWith($events);
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
            return $this->errorWith(400, "Buckets must have a lovely name!");

        // Check a bucket name doesn't already exist
        if(Bucket::count("name='" . $payload->name . "'") > 0)
            return $this->errorWith(400, "This bucket already exists");

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
            return $this->errorWith(400, "You must specify a bucket by name");

        // Check the bucket id exists
        if(Bucket::count("name='" . $name . "'") < 1)
            return $this->errorWith(400, "The bucket cannot be found");

        // Delete the bucket
        $bucket = Bucket::findFirst("name='" . $name . "'");
        $bucket->delete();
        $this->respondWith("The bucket was deleted");

    }
}