<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Collection;

$items = $result->getItems();
$results = [];
foreach($items as $result) {
    $results[] = $result->getEntry();
}

$featuredResultEntry1 = null;
$featuredResultEntry2 = null;

if ($results[0]) {
    $featuredResultEntry1 = new Collection($results[0]);
}
if ($results[1]) {
    $featuredResultEntry2 = new Collection($results[1]);
}

$results = array_slice($results, 2);
?>

<div class="row">
    <div class="col-xs-12 col-md-9">
        <?php if ($featuredResultEntry1) { ?>
            <section class="featured-collection">
                <h1><?=$featuredResultEntry1->getTitle()?></h1>

                <div class="clearfix collection-meta">

                    <ul>
                        <li class="collection-meta-author"><span>Added by</span> <?=$featuredResultEntry1->getAuthor()->getUserDisplayName()?></li>
                        <li><?=$featuredResultEntry1->getDateAdded()?></li>
                        <li class="pull-right"><?=$featuredResultEntry1->getContentsDescription()?></li>
                    </ul>

                </div>

                <div class="featured-collection-thumbnail">
                    <a href="<?=$featuredResultEntry1->getPublicViewLink()?>">
                        <img src="<?=$featuredResultEntry1->getCoverImageURL()?>" />
                    </a>
                </div>
                <div class="featured-collection-description">
                    <div class="row">
                        <div class="col-sm-8">
                                <span>
                                    <?=$featuredResultEntry1->getDescription()?>
                                </span>
                        </div>
                        <div class="col-sm-4 text-right">
                            <a href="<?=$featuredResultEntry1->getPublicViewLink()?>">View Collection</a>
                        </div>
                    </div>
                </div>
            </section>
            <hr>

            <?php if ($featuredResultEntry2) { ?>

                <section class="second-featured-collection">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="<?=$featuredResultEntry2->getPublicViewLink()?>" class="thumbnail second-featured-collection-thumbnail">
                                <img src="<?=$featuredResultEntry2->getCoverImageURL()?>" />
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <h3><a href="<?=$featuredResultEntry2->getPublicViewLink()?>"><?=$featuredResultEntry2->getTitle()?></a></h3>
                            <p><?=$featuredResultEntry2->getDescription()?></p>
                            <p>Created: <?=$featuredResultEntry2->getDateAdded()?></p>
                            <p><strong><?=$featuredResultEntry2->getContentsDescription()?></strong></p>
                        </div>
                    </div>
                </section>
                <hr>
            <?php } ?>

        <?php } ?>

        <section class="collection-list-container">
            <div class="row">
                <?php foreach($results as $entry) {
                    $collection = new Collection($entry);
                    ?>
                    <div class="col-xs-12 col-md-6">
                        <div class="thumbnail-container">
                            <a href="<?=$collection->getPublicViewLink()?>" class="thumbnail">
                                <img src="<?=$collection->getCoverImageURL()?>" />
                            </a>
                            <div class="thumbnail-caption">
                                <h3>
                                    <a href="<?=$collection->getPublicViewLink()?>"><?=$collection->getTitle()?></a>
                                </h3>
                                <p>Created: <?=$collection->getDateAdded()?></p>
                                <p><strong><?=$collection->getContentsDescription()?></strong></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </section>
        <div style="min-height:200px;"></div>
    </div>
    <?php
    $c = Page::getCurrentPage();
    $link = URL::to($c);
    $request = Request::createFromGlobals();
    ?>
    <div class="col-xs-12 col-md-2 col-md-offset-1">
        <?php /*
        <div class="row side-bar-search">
            <div class="col-xs-12">
                <form method="get" action="<?=URL::to($c)?>">
                    <input name="keywords"
                    value="<?php print ($request->query->has('keywords') ? h($request->query->get('keywords')) : '')?>" type="text" autocomplete="off"/>
                </form>
            </div>
        </div>
*/ ?>
        <?php /*
        <div class="row side-bar-links">
            <div class="col-xs-12">
                <h4>Recently Updated</h4>
                <ul>
                    <li><a href="#">Vacation</a></li>
                    <li><a href="#">Diverse Soldiers</a></li>
                    <li><a href="#">Recreation</a></li>
                    <li><a href="#">Siblings</a></li>
                </ul>
            </div>
        </div>
        */ ?>
        <?php
        $entity = Express::getObjectByHandle('collection');
        $key = $entity->getAttributeKeyCategory()->getAttributeKeyByHandle('collection_category');
        $tree = Concrete\Core\Tree\Tree::getByID($key->getAttributeKeySettings()->getTopicTreeID());
        if ($tree) {
            $node = $tree->getRootTreeNodeObject();
            $node->populateChildren();
            $categories = $node->getChildNodes();

            if ($categories) { ?>
                <div class="row side-bar-links">
                    <div class="col-xs-12">
                        <h4>Browse by Category</h4>
                        <ul>
                        <?php foreach($categories as $category) {
                            /** @var $link \Concrete\Core\Url\UrlImmutable */
                            $field = $key->getController()->field('treeNodeID');
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
                            <li <?php if ($isSelected) { ?>class="active"<?php } ?>><a href="<?=$link?>"><?=$category->getTreeNodeDisplayName()?></a></li>
                        <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
