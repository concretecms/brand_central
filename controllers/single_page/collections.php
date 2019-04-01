<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Form\Context\FrontendFormContext;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use Express;

class Collections extends PageController
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

    public function submit()
    {
        $express = $this->app->make('express');
        $entity = $express->getObjectByHandle('collection');
        $controller = $express->getEntityController($entity);
        $processor = $controller->getFormProcessor();
        $validator = $processor->getValidator($this->request);
        $form = $entity->getForms()[0];

        $collection = false;
        if ($this->request->request->has('collection_id')) {
            $collection = $express->getEntry($this->request->request->get('collection_id'));
            if (!$collection || !$collection->is('collection')) {
                $this->error->add(t('Invalid collection object.'));
                unset($collection);
            }
        }

        if ($collection) {
            $validator->validate($form, ProcessorInterface::REQUEST_TYPE_UPDATE);
        } else {
            $validator->validate($form, ProcessorInterface::REQUEST_TYPE_ADD);
        }

        $this->error = $validator->getErrorList();

        if (!$this->error->has()) {

            $manager = $controller->getEntryManager($this->request);
            if (!$collection) {
                $collection = $manager->addEntry($entity);
            }
            $collection = $manager->saveEntryAttributesForm($form, $collection);
            $this->flash('success', t('Collection added successfully.'));
            return $this->redirect('/collections', $collection->getId());
        }

        $this->add();
    }

    public function add()
    {
        $express = $this->app->make('express');
        $entity = $express->getObjectByHandle('collection');
        $permissions = new \Permissions($entity);
        if (!$permissions->canAddExpressEntries()) {
            throw new \Exception(t('Access Denied'));
        }

        $controller = $express->getEntityController($entity);
        $factory = new ContextFactory($controller);
        $form = $entity->getForms()[0];
        $context = $factory->getContext(new FrontendFormContext());
        $renderer = new Renderer(
            $context,
            $form
        );
        $this->set('collection', null);
        $this->set("renderer", $renderer);
        $this->set("form", $form);
        $this->set('formMode', 'add');
        $this->set('buttonText', t('Add Collection'));
        $this->render('/collections/form');
    }

    public function edit($collectionID = null)
    {
        $express = $this->app->make('express');

        $collection = Express::getEntry($collectionID);
        if ($collection) {

            $permissions = new \Permissions($collection);
            if (!$permissions->canEditExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            $entity = $collection->getEntity();

            $controller = $express->getEntityController($entity);
            $factory = new ContextFactory($controller);
            $context = $factory->getContext(new FrontendFormContext());
            $form = $entity->getForm('Frontend');
            $renderer = new Renderer(
                $context,
                $form
            );

            $this->set('collection', $collection);
            $this->set('renderer', $renderer);
            $this->set('formMode', 'edit');
            $this->set('buttonText', t('Save Collection'));
            $this->render('/collections/form');
        }
    }

    public function delete($collectionID = null)
    {
        $express = $this->app->make('express');
        $collection = $express->getEntry($collectionID);
        if ($collection) {

            $permissions = new \Permissions($collection);
            if (!$permissions->canDeleteExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            $this->set('collection', $collection);
            $this->set('token', new Token());
            $this->render('/collections/delete');
        }
    }

    public function perform_delete($collectionID = null)
    {
        $express = $this->app->make('express');
        $collection = $express->getEntry($collectionID);
        $token = new Token();
        if ($collection) {

            $permissions = new \Permissions($collection);
            if (!$permissions->canDeleteExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            if (!$token->validate('perform_delete')) {
                $this->error->add($token->getErrorMessage());
            }

            if (!$this->error->has()) {
                $express->deleteEntry($collection->getId());
                $this->flash('success', t('Collection deleted successfully.'));
                return $this->redirect('/collections');
            }

            $this->set('collection', $collection);
            $this->set('token', $token);
            $this->render('/collections/delete');
        }
    }

    public function view($collectionID = null)
    {
        $collection = Express::getEntry($collectionID);

        if ($collection && $collection->getEntity()->getHandle() == 'collection') {

            $checker = new Checker($collection);
            $this->set('canEditCollection', $checker->canEditExpressEntry());
            $this->set('canDeleteCollection', $checker->canDeleteExpressEntry());
            $this->set('collection_name', $collection->getCollectionName());
            $this->set('collection_assets', (array)$collection->getAssets());
            $this->set("collection", $collection);
        } else {
            $express = $this->app->make('express');
            $collection = $express->getObjectByHandle('collection');
            $list = new EntryList($collection);
            $this->set('collections', $list->getResults());
        }
        $this->set('lightboxApp', true);
    }

    public function reorder($collectionID = null)
    {
        $express = $this->app->make('express');
        $collection = $express->getEntry($collectionID);
        $this->requireAsset('jquery/ui');

        if ($collection) {

            $permissions = new \Permissions($collection);
            if (!$permissions->canEditExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            $this->set('assets', (array)$collection->getAssets());
            $this->set('collection', $collection);
            $this->set('token', new Token());
            $this->render('/collections/reorder');
        }
    }

    public function perform_reorder($collectionID = null)
    {
        $express = $this->app->make('express');
        $collection = $express->getEntry($collectionID);
        $em = $this->app->make(EntityManager::class);
        $token = new Token();
        if ($collection) {

            $permissions = new \Permissions($collection);
            if (!$permissions->canEditExpressEntry()) {
                throw new \Exception(t('Access Denied'));
            }

            if (!$token->validate('perform_reorder')) {
                $this->error->add($token->getErrorMessage());
            }

            if (!$this->error->has()) {
                $association = $collection->getAssociation('assets');
                $displayOrder = 0;
                foreach ($this->request->request->get('asset') as $assetId) {
                    $asset = $express->getEntry($assetId);
                    if ($asset && $asset->getEntity()->getHandle() == 'asset') {
                        $associationAsset = $association->getAssociationEntry($asset);
                        $associationAsset->setDisplayOrder($displayOrder);
                        $em->persist($associationAsset);
                    }
                    $displayOrder++;
                }
                $em->flush();
                $this->flash('success', t('Asset order updated successfully.'));
                return $this->redirect('/collections', $collectionID);
            }

            $this->set('collection', $collection);
            $this->set('token', $token);
            $this->render('/collections/reorder');
        }
    }

}
