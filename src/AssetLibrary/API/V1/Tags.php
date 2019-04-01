<?php

namespace Concrete5\AssetLibrary\API\V1;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Http\Request;
use Doctrine\ORM\EntityManager;
use Exception;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Concrete5\AssetLibrary\API\V1\Transformer\TagTransformer;

class Tags
{

    protected $objectManager;

    protected $request;

    protected $entityManager;

    public function __construct(ObjectManager $objectManager, Request $request, EntityManager $entityManager)
    {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    protected function getAttributeKey()
    {
        $asset = $this->objectManager->getObjectByHandle('asset');
        $category = $asset->getAttributeKeyCategory();
        $key = $category->getAttributeKeyByHandle('asset_tags');
        return $key;

    }

    public function list()
    {
        $controller = $this->getAttributeKey()->getController();
        $search = $this->request->query->get('search');
        $tags = (array)$controller->getOptions($search);
        return new Collection($tags, new TagTransformer());
    }

    public function add()
    {
        $label = $this->request->request->get('tag');
        if (!$label) {
            throw new Exception(t('You must specify a valid label for this tag.'));
        }

        $key = $this->getAttributeKey();
        $list = $key->getAttributeKeySettings()->getOptionList();

        $displayOrder = 0;
        if ($list) {
            $displayOrder = count($list->getOptions());
        }

        $tag = new SelectValueOption();
        $tag->setIsEndUserAdded(true);
        $tag->setDisplayOrder($displayOrder);
        $tag->setSelectAttributeOptionValue($label);
        $tag->setOptionList($list);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return new Item($tag, new TagTransformer());
    }
}
