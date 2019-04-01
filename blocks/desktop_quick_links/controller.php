<?php

namespace Concrete\Package\BrandCentral\Block\DesktopQuickLinks;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Permission\Checker;
use PortlandLabs\Liberta\Site\Selector;

class Controller extends BlockController
{

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;

    public function getBlockTypeDescription()
    {
        return t('Displays BrandCentral Quick Links.');
    }

    public function getBlockTypeName()
    {
        return t('Quick Links');
    }

    public function view()
    {
        $express = $this->app->make('express');
        $asset = $express->getObjectByHandle('asset');

        $checker = new Checker($asset);
        $this->set('canAddAssets', $checker->canAddExpressEntries());
        $collection = $express->getObjectByHandle('collection');

        $checker = new Checker($collection);
        $this->set('canAddCollections', $checker->canAddExpressEntries());
    }

}
