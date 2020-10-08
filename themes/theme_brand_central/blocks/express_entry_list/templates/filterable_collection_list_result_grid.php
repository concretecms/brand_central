<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

$items = $result->getItems();
$results = [];
foreach($items as $result) {
    $results[] = $result->getEntry();
}

$c = Page::getCurrentPage();
?>


    <div class="row">
        <div class="col-sm-8">
            <h1><?=$c->getCollectionName()?></h1>
        </div>
        <div class="col-sm-4 text-right">
            <?php
            $link = URL::to($c);
            $request = Request::createFromGlobals();

            $entity = Express::getObjectByHandle('collection');
            $key = $entity->getAttributeKeyCategory()->getAttributeKeyByHandle('collection_category');
            $tree = Concrete\Core\Tree\Tree::getByID($key->getAttributeKeySettings()->getTopicTreeID());
            $field = $key->getController()->field('treeNodeID');
            if ($tree) {
                $node = $tree->getRootTreeNodeObject();
                $node->populateChildren();
                $categories = $node->getChildNodes();
                $link = $link->setQuery([$field => null]);
                $querykeyID = $request->query->get('akID');
                $isSelected = empty($querykeyID[$key->getAttributeKeyID()]);

                if ($categories) { ?>
            <div class="btn-group search-filter-dropdown">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"><?= t('Filter Results') ?> <img
                            src="<?= $view->getThemePath() ?>/images/dropdown_filter.png"></button>
                <div class="dropdown-menu">
                    <h4><?= t('Category') ?></h4>
                    <a class="dropdown-item" href="<?= $link ?>"><i
                                    class="far <?php if ($isSelected) { ?>fa-dot-circle<?php } else { ?>fa-circle<?php } ?>"></i>
                            <strong><?= t('All') ?></strong></a>
                    <?php foreach ($categories as $category) {
                        /** @var $link \Concrete\Core\Url\UrlImmutable */
                        $link = $link->setQuery([$field => $category->getTreeNodeID()]);
                        $isSelected = false;
                        if ($request->query->has('akID')) {
                            $querykeyID = $request->query->get('akID');
                            if (!empty($querykeyID[$key->getAttributeKeyID()])) {
                                $akID = $querykeyID[$key->getAttributeKeyID()]['treeNodeID'];
                                if ($akID == $category->getTreeNodeID()) {
                                    $isSelected = true;
                                }
                            }

                        }
                        ?>
                        <a class="dropdown-item" href="<?= $link ?>"><i
                                        class="far <?php if ($isSelected) { ?>fa-dot-circle<?php } else { ?>fa-circle<?php } ?>"></i> <?= $category->getTreeNodeDisplayName() ?>
                            </a>
                    <?php }
                    } ?>
                </div></div>
            <?php }
            ?>
        </div>
    </div>

        <?php
View::element('collection_grid', ['collections' => $results], 'brand_central');

?>

<?php if ($pagination) { ?>
    <div class="row mb-5">
        <div class="col-12 d-flex flex-column-reverse">
            <?=$pagination ?>
        </div>
    </div>
<?php } ?>


