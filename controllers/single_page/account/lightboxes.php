<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage\Account;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\Validation\CSRF\Token;
use Express;

class Lightboxes extends AccountPageController
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

    public function delete($lightboxID = null)
    {
        $express = $this->app->make('express');
        $lightbox = $express->getEntry($lightboxID);
        $u = new \User();
        if ($lightbox) {
            if ($u->getUserID() == $lightbox->getAuthor()->getUserID()) {
                $this->set('lightbox', $lightbox);
                $this->set('token', new Token());
                $this->render('/account/lightboxes/delete');
            } else {
                throw new \Exception(t('Access Denied.'));
            }
        }
        $this->view($lightboxID);
    }

    public function perform_delete($lightboxID = null)
    {
        $express = $this->app->make('express');
        $lightbox = $express->getEntry($lightboxID);

        $token = new Token();

        $u = new \User();
        if ($u->getUserID() != $lightbox->getAuthor()->getUserID()) {
            throw new \Exception(t('Access Denied'));
        }

        if (!$token->validate('perform_delete')) {
            $this->error->add($token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $express->deleteEntry($lightbox->getId());
            $this->flash('success', t('Lightbox deleted successfully.'));
            return $this->redirect('/account/lightboxes');
        }

        $this->set('lightbox', $lightbox);
        $this->set('token', $token);
        $this->render('/account/lightboxes/delete');
    }

    public function perform_remove_asset($lightboxID = null)
    {

        $express = $this->app->make('express');
        $lightbox = $express->getEntry($lightboxID);

        $token = new Token();

        $u = new \User();
        if ($u->getUserID() != $lightbox->getAuthor()->getUserID()) {
            throw new \Exception(t('Access Denied'));
        }

        if (!$token->validate('perform_remove_asset')) {
            $this->error->add($token->getErrorMessage());
        }

        if (!$this->error->has()) {

            //Asset ID is being submitted through via post: <input name='asset'/>
            $assetId = $this->request->request->get('asset');
            $asset = $express->getEntry($assetId);
            if ($asset && $asset->is('asset')) {
                $lightbox->associateEntries()->removeAssets([$asset]);
                return $this->redirect('/account/lightboxes', $lightbox->getId());
            }
        }

        //returns to selected lightbox view.
        return $this->redirect('/account/lightboxes', $lightbox->getId());

    }

    public function rename()
    {
        $lightboxID = $this->request->request->get('lightbox');

        $express = $this->app->make('express');
        $lightbox = $express->getEntry($lightboxID);

        $token = new Token();

        $u = new \User();
        if ($u->getUserID() != $lightbox->getAuthor()->getUserID()) {
            throw new \Exception(t('Access Denied'));
        }

        if (!$token->validate('perform_rename')) {
            $this->error->add($token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $lightboxName = $this->request->request->get('name');
            $lightbox->setLightboxName($lightboxName);

            return $this->redirect('/account/lightboxes');
        }

    }

    public function view($lightboxID = null)
    {

        $u = new \User();
        $userID = $u->getUserID();
        $express = $this->app->make('express');
        $lightboxObj = $express->getObjectByHandle('lightbox');
        $list = new EntryList($lightboxObj);
        $list->filterByAuthorUserID($userID);

        $this->set('token', new Token());

        $lightbox = Express::getEntry($lightboxID);

        if ($lightbox && $lightbox->getEntity()->getHandle() == 'lightbox') {
            if ($lightbox->getAuthor()->getUserID() == $u->getUserID()) {
                $this->set('lightbox', $lightbox);
                $this->set('lightbox_assets', (array)$lightbox->getAssets());
                $this->set('lightbox_name', ucwords($lightbox->getLightboxName()));
                $this->set('lightboxes', $list->getResults());
            } else {
                throw new \Exception('Access Denied.');
            }
        } else {
            $this->set('lightboxes', $list->getResults());

        }
    }

}
