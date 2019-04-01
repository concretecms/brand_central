<?php

namespace Concrete5\AssetLibrary\API\V1\Transformer;

use Concrete\Core\Entity\Express\Entry;
use League\Fractal\TransformerAbstract;

class CollectionTransformer extends TransformerAbstract
{

    public function transform(Entry $collection)
    {
        return [
            'id' => $collection->getID(),
            'name' => $collection->getCollectionName()
        ];
    }

}
