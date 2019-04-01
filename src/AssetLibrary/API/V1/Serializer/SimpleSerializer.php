<?php

namespace Concrete5\AssetLibrary\API\V1\Serializer;

use League\Fractal\Serializer\DataArraySerializer;

class SimpleSerializer extends DataArraySerializer
{

    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }

}
