<?php

namespace Concrete5\AssetLibrary\API\V1\Middleware;

use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware as CoreFractalNegotiatorMiddleware;
use Concrete5\AssetLibrary\API\V1\Serializer\SimpleSerializer;

class FractalNegotiatorMiddleware extends CoreFractalNegotiatorMiddleware
{

    public function getSerializer()
    {
        return new SimpleSerializer();
    }

}
