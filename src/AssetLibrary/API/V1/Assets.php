<?php

namespace Concrete5\AssetLibrary\API\V1;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Importer;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Utility\Service\Validation\Numbers;
use League\Fractal\Resource\Item;
use Concrete5\AssetLibrary\API\V1\Transformer\AssetTransformer;
use Concrete5\AssetLibrary\Results\Formatter\Asset;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Assets
{

    protected $objectManager;

    protected $session;

    public function __construct(ObjectManager $objectManager, Session $session)
    {
        $this->objectManager = $objectManager;
        $this->session = $session;
    }

    protected function getAsset($assetID)
    {
        $entry = $this->objectManager->getEntry($assetID);
        if (!is_object($entry) || $entry->getEntity()->getHandle() != 'asset') {
            throw new ResourceNotFoundException(t('Invalid asset.'));
        }
        return new Asset($entry);
    }

    public function read($assetID)
    {
        $entry = $this->getAsset($assetID);
        return new Item($entry, new AssetTransformer());
    }

    public function upload()
    {

        $errors = new ErrorList();
        $fp = FilePermissions::getGlobal();
        if (!$fp->canAddFiles()) {
            $errors->add(t('Unable to add files.'));
        }

        if ($errors->has()) {
            return $errors->createResponse();
        } else {
            $request = Request::createFromGlobals();
            $file = $request->files->get('file');

            $filesystem = new Filesystem();
            $incoming = $filesystem->getRootFolder()->getChildFolderByName('Incoming');

            $importer = new \Concrete\Core\File\Importer();
            $importedFile = $importer->import($file->getPathname(), $file->getClientOriginalName(), $incoming);

            if (!$importedFile instanceof Version) {
                $errors->add(Importer::getErrorMessage($importedFile));
                return $errors->createResponse();
            }

            $result = [
                'id' => $importedFile->getFileID(),
                'filename' => $importedFile->getFileName(),
                'url' => $importedFile->getURL(),
                'desc' => $importedFile->getFileName()
            ];
            return new JsonResponse($result);
        }
    }

    private function parseRequest()
    {
        $request = Request::createFromGlobals();
        $errors = new ErrorList();
        $data = null;
        try {
            $data = json_decode($request->getContent());
        } catch (\Exception $e) {
            $errors->add('Unable to parse asset into JSON object');
        }
        return [$errors, $data];
    }

    public function update($assetID)
    {
        $asset = $this->getAsset($assetID);

        list($errors, $data) = $this->parseRequest();

        $permissions = new \Permissions($asset);
        if (!$permissions->canEditExpressEntry()) {
            $errors->add(t('You do not have access to edit this asset.'));
        }

        if ($errors->has()) {
            return $errors->createResponse();
        } else {
            $folder = FileFolder::getByID($asset->getAssetFolderId());
            $this->saveEntry($asset, $folder, $data);
            $this->session->getFlashBag()->add('page_message', ['success' => t('Asset updated successfully.')]);
            return $this->read($asset->getId());

        }
    }

    protected function saveEntry($asset, $folder, $data)
    {
        $thumbnail = null;
        if ($data->asset->thumbnailId) {
            $thumbnail = File::getByID($data->asset->thumbnailId);
            $sourceNode = $thumbnail->getFileNodeObject();
            if ($folder) {
                $sourceNode->move($folder);
            }
        }

        $asset->setAssetName($data->asset->name);
        $asset->setAssetType($data->asset->type);
        $asset->setAssetDescription($data->asset->desc);
        $asset->setAssetLocation($data->asset->location);
        $asset->setAssetThumbnail($thumbnail);
        $asset->save();

        $fileEntries = [];
        if (isset($data->asset->files)) {
            foreach ($data->asset->files as $file) {
                $f = File::getByID($file->fid);
                if ($f) {
                    $sourceNode = $f->getFileNodeObject();
                    if ($folder) {
                        $sourceNode->move($folder);
                    }
                    $assetFile = $this->objectManager->buildEntry('asset_file')
                        ->setAssetFile($f)
                        ->setAssetFileDescription($file->desc ? $file->desc : $f->getFilename())
                        ->save();
                    $fileEntries[] = $assetFile;
                }
            }
        }

        if (count($fileEntries)) {
            $asset->associateEntries()->setAssetFiles($fileEntries);
        }

        $collections = $data->asset->collections;
        if (count($this->getEntryCollections($collections))) {
            $asset->associateEntries()->setCollections($this->getEntryCollections($collections));
        }

        // Set and/or add new tags.
        $entity = $this->objectManager->getObjectByHandle('asset');
        $key = $entity->getAttributeKeyCategory()->getAttributeKeyByHandle('asset_tags');
        $controller = $key->getController();

        $tagEntries = [];
        $numberValidator = new Numbers();
        if (isset($data->asset->tags)) {
            foreach ($data->asset->tags as $tag) {
                $option = null;
                if ($numberValidator->integer($tag->id)) {
                    $option = $controller->getOptionByID($tag->id);
                }
                if (!$option) {
                    $option = $controller->getOptionByValue($tag->name, $key);
                }
                if ($option) {
                    $tagEntries[] = $option;
                } else {
                    // We have to add a new option
                    $settings = $controller->getAttributeKeySettings();
                    $optionList = $settings->getOptionList();
                    $option = new SelectValueOption();
                    $option->setOptionList($optionList);
                    $option->setIsEndUserAdded(true);
                    $option->setDisplayOrder(0);
                    $option->setSelectAttributeOptionValue($tag->name);
                    $tagEntries[] = $option;
                }
            }
        }

        $asset->setAssetTags($tagEntries);

        $em = \ORM::entityManager();
        $em->clear();

        return $asset;
    }

    public function getEntryCollections($data)
    {
        $collections = [];
        if (isset($data)) {
            foreach ($data as $collection) {
                $entry = $this->objectManager->getEntry($collection->id);
                if ($entry && $entry->is('collection')) {
                    $this->objectManager->refresh($entry);
                    $collections[] = $entry;
                }
            }
            return $collections;
        }
    }

    public function create()
    {

        list($errors, $data) = $this->parseRequest();

        $entity = $this->objectManager->getObjectByHandle('asset');
        $permissions = new \Permissions($entity);
        if (!$permissions->canAddExpressEntries()) {
            $errors->add(t('You do not have access to add assets.'));
        }

        if ($errors->has()) {
            return $errors->createResponse();
        } else {

            // Set basic attributes

            $asset = $this->objectManager->buildEntry('asset')->save();
            $folder = $this->createAssetFolder($asset, $asset->asset->name);

            $asset->setAssetFolderId($folder->getTreeNodeID());

            $this->saveEntry($asset, $folder, $data);

            // Return the asset
            $this->session->getFlashBag()->add('page_message', ['success' => t('Asset created successfully.')]);
            return $this->read($asset->getId());

        }
    }

    public function createAssetFolder($asset, $folderName)
    {
        if (!$folderName) {
            $folderName = $asset->getId();
        }

        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $assets = $root->getChildFolderByName('Assets');
        $folder = $filesystem->addFolder($assets, $folderName);
        return $folder;

    }

    public function bulkCreation()
    {
        \Cache::disableAll();
        list($errors, $data) = $this->parseRequest();
        $entity = $this->objectManager->getObjectByHandle('asset');
        $permissions = new \Permissions($entity);
        if (!$permissions->canAddExpressEntries()) {
            $errors->add(t('You do not have access to add assets.'));
        }
        if ($errors->has()) {
            return $errors->createResponse();
        } else {
            foreach ($data->assets as $assetItem) {
                $assetData = [
                    'asset' => (object)[
                        'name' => $assetItem->name,
                        'desc' => $assetItem->desc,
                        'type' => $assetItem->type,
                        'tags' => $assetItem->tags,
                        'files' => [(object)['fid' => $assetItem->fid]],
                        'collections' => (object)$data->collections
                    ]
                ];

                $assetObj = $this->objectManager->buildEntry('asset')->save();
                $folder = $this->createAssetFolder($assetObj, $assetItem->name);

                $assetObj->setAssetFolderId($folder->getTreeNodeID());

                $this->saveEntry($assetObj, $folder, (object)$assetData);
            }

            return new JsonResponse(['collection' => $data->collections{0}->id]);
        }
    }

}
