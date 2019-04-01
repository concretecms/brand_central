<?php

namespace Concrete\Package\BrandCentral\Controller\Element;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\View\PageView;

class Breadcrumb extends ElementController
{

    protected $pkgHandle = 'brand_central';

    /**
     * @var PageView
     */
    protected $pageView;

    public function getElement()
    {
        return 'breadcrumb';
    }

    public function __construct(PageView $view)
    {
        $this->pageView = $view;
    }

    public function view()
    {
        $c = $this->pageView->getPageObject();
        $breadcrumbList = [];
        switch ($c->getCollectionPath()) {
            case '/assets':

                $home = Page::getByID(Page::getHomePageID(), 'ACTIVE');
                $breadcrumbList[] = ['label' => $home->getCollectionName(), 'url' => $home->getCollectionLink()];

                switch ($this->getRequest()->getPathInfo()) {
                    case '/assets/create':
                        $pageName = 'Create New Asset';
                        break;
                    case '/assets/bulk_upload':
                        $pageName = 'Bulk Upload';
                        break;
                    default:
                        $collection = $this->pageView->controller->get('assetCollection');
                        $asset = $this->pageView->controller->get('asset');

                        if ($collection) {
                            $breadcrumbList[] = [
                                'label' => $collection->getCollectionName(),
                                'url' => \URL::to('/collections', $collection->getId())
                            ];
                        }
                        if ($asset) {
                            $pageName = $asset->getAssetName();
                        }
                }
                break;
            case '/account/lightboxes':
                $lightbox = $this->pageView->controller->get('lightbox');

                $breadcrumbList[] = [
                    'label' => 'My Account',
                    'url' => \URL::to('/account'),
                ];

                if ($lightbox) {
                    $breadcrumbList[] = [
                        'label' => 'My Lightboxes',
                        'url' => \URL::to('/account/lightboxes'),
                    ];

                    $pageName = $lightbox->getLightboxName();

                } else {
                    $pageName = 'My Lightboxes';
                }

                break;
            case '/collections':
                $home = Page::getByID(Page::getHomePageID(), 'ACTIVE');
                $controller = $this->pageView->controller;
                $breadcrumbList[] = ['label' => $home->getCollectionName(), 'url' => $home->getCollectionLink()];
                $breadcrumbList[] = ['label' => t('All Collections'), 'url' => \URL::to('/collections')];
                if ($controller->get('formMode') == 'add') {
                    $pageName = t('Add Collection');
                } else {
                    if ($controller->get('formMode') == 'edit') {
                        $pageName = t('Edit Collection');
                    } else {
                        $collection = $controller->get('collection');
                        if ($collection) {
                            $pageName = $collection->getCollectionName();
                        }
                    }
                }
                break;
            default:
                $trail = new Navigation();
                $trailReversed = array_reverse($trail->getTrailToCollection($c));
                foreach ($trailReversed as $breadcrumb) {
                    $list = [];
                    $list['label'] = $breadcrumb->getCollectionName();
                    $list['url'] = $breadcrumb->getCollectionLink();

                    $breadcrumbList[] = $list;
                }
                $pageName = $c->getCollectionName();
        }

        $this->set('pageName', $pageName);
        $this->set('breadcrumbList', $breadcrumbList);

    }

}
