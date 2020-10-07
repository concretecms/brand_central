<?php

namespace Concrete5\AssetLibrary\Express\Controller;

use Concrete\Core\Express\Controller\StandardController;
use Doctrine\ORM\EntityManager;
use Concrete5\AssetLibrary\Express\Entry\Manager as ExpressEntryManager;
use Symfony\Component\HttpFoundation\Request;

class AssetController extends StandardController
{

    public function getEntryManager(Request $request)
    {
        return $this->app->make(ExpressEntryManager::class, ['request' => $request]);
    }

}
