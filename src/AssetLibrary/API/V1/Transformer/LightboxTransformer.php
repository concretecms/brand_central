<?php

namespace Concrete5\AssetLibrary\API\V1\Transformer;

use Concrete\Core\Entity\Express\Entry;
use League\Fractal\TransformerAbstract;

class LightboxTransformer extends TransformerAbstract
{

    public function transform(Entry $lightbox)
    {
        return [
            'id' => $lightbox->getID(),
            'name' => $lightbox->getLightboxName()
        ];
    }

}
