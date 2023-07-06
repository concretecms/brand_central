<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

$items = $result->getItems();
$results = [];
foreach($items as $result) {
    $results[] = $result->getEntry();
}

$c = Page::getCurrentPage();

$itemsPerPageOptionUrl = function($itemsPerPage) {
    $url = \League\Url\Url::createFromServer($_SERVER);
    $query = $url->getQuery();
    $query->modify(['itemsPerPage' => $itemsPerPage]);
    $url->setQuery($query);
    $itemsPerPageOptionUrl = (string) $url;
    return $itemsPerPageOptionUrl;
}
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
                <div class="dropdown-menu dropdown-menu-right">
                <h4><?= t('Category') ?></h4>
                <a class="dropdown-item" href="<?= $link ?>"><i
                            class="far <?php if ($isSelected) { ?>fa-dot-circle<?php } else { ?>fa-circle<?php } ?>"></i>
                    <strong><?= t('All') ?></strong></a>
                <?php foreach ($categories as $category) {
                    /** @var $link \Concrete\Core\Url\UrlImmutable */
                    $link = $link->setQuery([$field => $category->getTreeNodeID(), 'search' => 1]);
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

<?php
if ($enableItemsPerPageSelection || $enablePagination) {
?>

<div class="row mt-2 mb-5">
    <?php if ($enableItemsPerPageSelection) { ?>
        <div class="col-sm-4">
            <div class="dropdown">
                <button data-toggle="dropdown" class="btn-round" aria-haspopup="true" aria-expanded="false"><?= h($itemsPerPageSelected) ?> <i class="fa fa-chevron-down"></i></button>
                Per Page
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(12) ?>">12</a></li>
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(24) ?>">24</a></li>
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(48) ?>">48</a></li>
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(96) ?>">96</a></li>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($enablePagination) { ?>
        <div class="col-sm-8 d-flex flex-row-reverse">
            <?php if ($pagination) { ?>
                <?=$pagination ?>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php } ?>