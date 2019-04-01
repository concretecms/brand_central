<?php

namespace Concrete5\AssetLibrary\API\V1;

use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Facade;
use League\Fractal\Resource\Collection;
use Concrete5\AssetLibrary\API\V1\Transformer\CollectionTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;

class Collections
{
    protected $objectManager;

    protected $request;

    public function __construct(ObjectManager $objectManager, Request $request)
    {
        $this->objectManager = $objectManager;
        $this->request = $request;
    }

    public function list()
    {
        $app = Facade::getFacadeApplication();
        $entity = $app->make('express')->getObjectByHandle('collection');
        $list = new \Concrete\Core\Express\EntryList($entity);
        $list->sortByDateAddedDescending();
        if ($this->request->query->has('search')) {
            $list->filterByKeywords($this->request->query->get('search'));
        }
        return new Collection($list->getResults(), new CollectionTransformer());
    }

    public function create()
    {

        $requestCollection = json_decode($this->request->getContent());

        $collectionName = $requestCollection->collection;

        $collection = $this->objectManager->buildEntry('collection')->save();
        $collection->setCollectionName($collectionName);

        $this->objectManager->refresh($collection);

        return new JsonResponse(['collection' => $collection->getId()]);
    }

}
