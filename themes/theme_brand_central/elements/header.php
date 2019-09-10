<?php
    $this->inc('elements/header-top.php');

    $list = new \Concrete\Core\Page\PageList();
    $list->filterByParentID(1);
    $list->filterByExcludeNav(false);
    $list->sortByDisplayOrder();
    $pages = $list->getResults();

    $u = new User();

    $express = Core::make('express');
    $asset = $express->getObjectByHandle('asset');
    $checker = new Permissions($asset);
    $canAddAssets = $checker->canAddExpressEntries();
    $collection = $express->getObjectByHandle('collection');
    $checker = new Permissions($collection);
    $canAddCollections = $checker->canAddExpressEntries();


?>
<header>
    <div class="top-nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-right">
                    <nav>
                        <ul>
                            <?php if ($u->isRegistered()) {
                                $ui = $u->getUserInfoObject();?>
                                <li><a href="<?=URL::to('/account/welcome')?>" class="btn"><?= t('Currently signed in as %s', $ui->getUserDisplayName()) ?> <i class="fa fa-user"></i></a></li>
                                <li><a href="<?=URL::to('/account/lightboxes')?>"><?= t('My Lightboxes') ?></a></li>
                                <?php if ($canAddCollections) { ?>
                                    <li><a href="<?=URL::to('/collections/add')?>"><?= t('Add Collection') ?></a></li>
                                <?php } ?>
                                <?php if ($canAddAssets) { ?>
                                    <li><a href="<?=URL::to('/assets/create')?>"><?= t('Add Asset') ?></a></li>
                                    <li><a href="<?=URL::to('/assets/bulk_upload')?>"><?= t('Bulk Upload') ?></a></li>
                                <?php } ?>
                                <li><?php echo Core::make('helper/navigation')->getLogInOutLink() ?></li>
                            <?php } else { ?>
                                <li><a href="<?=URL::to('/login')?>" class="btn"><?= t('Log In') ?> <i class="fa fa-user"></i></a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="header">
        <div class="container">
            <div class="row">
                <?php View::element('header_logo', ['path'=>$this->getThemePath()], 'brand_central') ?>
                <div class="col-md-9 col-xs-10 main-nav-menu text-center">
                    <div class="hamburger-icon">
                        <div id="nav-icon" class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>

                    <ul>
                    <?php foreach ($pages as $page){ ?>
                        <li><a href="<?=URL::to("{$page->getCollectionPath()}")?>" class="btn"><?= $page->getCollectionName()?></a></li>
                    <?php } ?>
                    </ul>
                </div>

            </div>

        </div>
        <div class="main-nav-menu-mobil">
            <div class="col-xs-12">
                <form method="post" action="<?=URL::to('/search')?>">
                    <input name="search-input" type="text" placeholder="Search in Brand Central." autocomplete="off" class="search-input"/>
                </form>
                <ul>
                    <?php foreach ($pages as $page){ ?>
                        <li><a href="<?=URL::to("{$page->getCollectionPath()}")?>" class="btn"><?= $page->getCollectionName()?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>


</header>

<?php
/*
 * They currently do not want this
$breadcrumb = new \Concrete\Package\BrandCentral\Controller\Element\Breadcrumb($view);
$breadcrumb->render();
*/
?>
