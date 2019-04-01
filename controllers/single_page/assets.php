<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Validation\CSRF\Token;
use Express;
use Concrete5\AssetLibrary\Results\Formatter\Asset;

class Assets extends PageController
{

    protected $error;

    public function on_start()
    {
        $this->error = new ErrorList();
    }

    public function on_before_render()
    {
        $this->set('error', $this->error);
    }

    private function getAssetCollection($asset, $collectionID)
    {
        if (isset($collectionID)) {
            return $collectionObj = Express::getEntry($collectionID);
        } else {
            return $collectionObj = $asset->getCollections()[0];
        }
    }

    public function delete($assetID = null)
    {
        $express = $this->app->make('express');
        $asset = $express->getEntry($assetID);
        if ($asset && $asset->is('asset')) {

            $permissions = new \Permissions($asset);
            if (!$permissions->canDeleteExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            $this->set('asset', $asset);
            $this->set('token', new Token());
            $this->render('/assets/delete');
        }
        $this->view($assetID);
    }

    public function perform_delete($assetID = null)
    {
        $express = $this->app->make('express');
        $asset = $express->getEntry($assetID);
        $token = new Token();
        if ($asset && $asset->is('asset')) {

            $permissions = new \Permissions($asset);
            if (!$permissions->canDeleteExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            if (!$token->validate('perform_delete')) {
                $this->error->add($token->getErrorMessage());
            }

            if (!$this->error->has()) {
                $express->deleteEntry($asset->getId());
                $this->flash('success', t('Asset deleted successfully.'));
                return $this->redirect('/collections');
            }

            $this->set('asset', $asset);
            $this->set('token', $token);
            $this->render('/assets/delete');
        }
    }

    public function view($assetID = null, $collectionID = null)
    {
        $asset = Express::getEntry($assetID);

        if ($asset) {

            $asset = new Asset($asset);

            $checker = new Checker($asset);
            $authorName = t('Admin');
            if ($author = $asset->getAuthor()) {
                $authorName = ucfirst($author->getUserInfoObject()->getUserDisplayName());
            }
            $this->set('canEditAsset', $checker->canEditExpressEntry());
            $this->set('canDeleteAsset', $checker->canDeleteExpressEntry());
            $this->set('asset_name', $asset->getAssetName());
            $this->set('asset_date', $asset->getDateCreated()->format('F d, Y'));
            $this->set('asset_desc', $asset->getAssetDescription());
            $this->set('asset_location', $asset->getAssetLocation());
            $this->set('asset_thumbnail', $asset->getDetailImageURL());
            $this->set('asset_author', $authorName);
            $this->set('asset_files', (array)$asset->getAssetFiles());
            $this->set('asset_collections', $asset->getCollections());

            $assetTags = [];
            if (is_object($asset->getAssetTags())) {
                foreach ($asset->getAssetTags() as $tag) {
                    $assetTags[] = $tag->getSelectAttributeOptionValue();
                }
            }
            $this->set('asset_tags', $assetTags);

            $this->set('assetCollection', $this->getAssetCollection($asset, $collectionID));

            $this->set("asset", $asset);

            $this->set('lightboxApp', true);

        } else {
            $this->error->add('Asset not found.');
        }

    }

    public function create()
    {
        $asset = \Express::getObjectByHandle('asset');
        $permissions = new \Permissions($asset);
        if (!$permissions->canAddExpressEntries()) {
            throw new \Exception(t('Access Denied'));
        }

        $collection = \Express::getObjectByHandle('collection');
        $permissions = new \Permissions($collection);
        $this->set('canAddCollections', $permissions->canAddExpressEntries());
        $this->render('asset_app');
    }

    public function edit($assetID = null, $collectionID = null)
    {
        $asset = Express::getEntry($assetID);
        if ($asset) {
            $this->set("asset", $asset);
            $this->set('assetCollection', $this->getAssetCollection($asset, $collectionID));

            $this->render('asset_app_edit');
        }
        $this->view($assetID);
    }

    public function bulk_upload()
    {
        $asset = \Express::getObjectByHandle('asset');
        $permissions = new \Permissions($asset);
        if (!$permissions->canAddExpressEntries()) {
            throw new \Exception(t('Access Denied'));
        }

        $collection = \Express::getObjectByHandle('collection');
        $permissions = new \Permissions($collection);
        $this->set('canAddCollections', $permissions->canAddExpressEntries());
        $this->set('lightboxApp', false);
        $this->render('asset_bulk_upload');
    }
}
