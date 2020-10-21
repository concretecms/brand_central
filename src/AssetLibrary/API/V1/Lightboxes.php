<?php

namespace Concrete5\AssetLibrary\API\V1;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use League\Fractal\Resource\Collection;
use Concrete5\AssetLibrary\API\V1\Transformer\LightboxTransformer;
use Concrete5\AssetLibrary\Results\Formatter\Lightbox;
use Symfony\Component\HttpFoundation\JsonResponse;

class Lightboxes
{

    protected $objectManager;

    protected $request;

    protected $entityManager;

    protected function getLightbox($id)
    {
        $entry = $this->objectManager->getEntry($id);
        if (!is_object($entry) || $entry->getEntity()->getHandle() !== 'lightbox') {
            throw new ResourceNotFoundException(t('Invalid LightBox.'));
        }

        $lightbox = new Lightbox($entry);

        return $lightbox;
    }

    protected function saveEntry($lightbox, $data)
    {
        $currentAssets = [];

        $assetExists = false;

        if ($lightbox->getAssets() !== null) {
            foreach ($lightbox->getAssets() as $asset) {
                $assetEntry = $this->objectManager->getEntry($asset->getID());
                $currentAssets[] = $assetEntry;
                if ($asset->getID() === $data->asset) {
                    $assetExists = true;
                }
            }
        }

        if (!$assetExists) {
            $entry = $this->objectManager->getEntry($data->asset);
            $currentAssets[] = $entry;
            $lightbox->associateEntries()->setAssets($currentAssets);
        }

        return $lightbox;
    }

    public function __construct(ObjectManager $objectManager, Request $request, EntityManager $entityManager)
    {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    public function list()
    {
        $app = Facade::getFacadeApplication();
        $entity = $app->make('express')->getObjectByHandle('lightbox');
        $u = new User();
        $userID = $u->getUserID();
        $list = new EntryList($entity);
        $list->ignorePermissions();
        $list->filterByAuthorUserID($userID);
        return new Collection($list->getResults(), new LightboxTransformer());
    }

    public function add()
    {
        $errors = new ErrorList();
        $u = new User();
        if (!$u->isRegistered()) {
            $errors->add(t('You must be logged in to create a lightbox.'));
        }

        if (!$errors->has()) {
            $lightboxRequest = json_decode($this->request->getContent());

            $lightboxName = $lightboxRequest->lightbox;

            $lightbox = $this->objectManager->buildEntry('lightbox')->save();
            $lightbox->setLightboxName($lightboxName);

            $this->objectManager->refresh($lightbox);

            $this->saveEntry($lightbox, $lightboxRequest);

            return new JsonResponse(['lightbox' => $lightbox->getId()]);
        } else {
            return $errors->createResponse();
        }
    }

    public function set()
    {
        $lightboxRequest = json_decode($this->request->getContent());

        $entry = $this->objectManager->getEntry($lightboxRequest->id);
        if (!is_object($entry) || $entry->getEntity()->getHandle() !== 'lightbox') {
            throw new ResourceNotFoundException(t('Invalid LightBox.'));
        }

        $lightbox = new Lightbox($entry);

        if (!$lightbox) {
            throw new \Exception(t('You must specify a valid id for this lightbox.'));
        }

        $u = new User();
        if ((int) $entry->getAuthor()->getUserID() !== (int) $u->getUserID()) {
            throw new \Exception(t('Access Denied.'));
        }

        $this->saveEntry($lightbox, $lightboxRequest);

        return new JsonResponse([200]);
    }
}
