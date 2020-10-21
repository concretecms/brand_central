<?php

namespace Concrete5\AssetLibrary\API\V1;

use Symfony\Component\HttpFoundation\JsonResponse;

class FileTypes
{
    public function list()
    {
        return new JsonResponse([
            [
                "key" => "",
                "value" => t("** Select All")
            ],
            [
                "key" => "photo",
                "value" => t("Photo"),
            ],
            [
                "key" => "logo",
                "value" => t("Logos"),
            ],
            [
                "key" => "template",
                "value" => t("Templates"),
            ],
            [
                "key" => "video",
                "value" => t("Videos")
            ]
        ]);
    }
}