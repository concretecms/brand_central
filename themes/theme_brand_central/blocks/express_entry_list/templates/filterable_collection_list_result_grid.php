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
                <ul class="dropdown-menu">
                    <li><h4><?= t('Category') ?></h4></li>
                    <li><a href="<?= $link ?>"><i
                                    class="fa <?php if ($isSelected) { ?>fa-dot-circle-o<?php } else { ?>fa-circle-o<?php } ?>"></i>
                            <strong><?= t('All') ?></strong></a></li>
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
                        <li><a href="<?= $link ?>"><i
                                        class="fa <?php if ($isSelected) { ?>fa-dot-circle-o<?php } else { ?>fa-circle-o<?php } ?>"></i> <?= $category->getTreeNodeDisplayName() ?>
                            </a></li>
                    <?php }
                    } ?>
                </ul></div>
            <?php }
            ?>
        </div>
    </div>

        <?php
View::element('collection_grid', ['collections' => $results], 'brand_central');

?>
