<?php

namespace Concrete5\AssetLibrary\API\V1\Transformer;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{

    public function transform(SelectValueOption $tag)
    {
        return [
            'id' => $tag->getSelectAttributeOptionID(),
            'name' => $tag->getSelectAttributeOptionValue()
        ];
    }

}
