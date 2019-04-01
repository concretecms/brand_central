
<div class="container row top-search">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
        <form data-form="search" class="" method="get" action="<?=URL::to('/search')?>">
            <div class="search input-group">
                <a href="#" class="fa fa-close search-clear"></a>
                <input name="keywords" type="text" placeholder="Search in Brand Central." autocomplete="off" value="<?= $input_search ?>"/>
                <input name="filter" type="hidden" value="<?= $filter ?>"/>
                <div class="input-group-btn">
                    <button type="button" class="btn dropdown-toggle search-btn-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="search-btn-label"><?= $filter_label?></span> <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right" style="z-index: 1000">
                        <li><a href="#" class="dropdown-item" data-filter-value="">Anything</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="photo">Photos</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="logo">Logos</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="video">Videos</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="template">Templates</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="container search-results">
    <div class="row">
        <div class="col-xs-12 collection-container">
            <?php if($count_results) { ?>
                <div class="row">
                    <div class="col-sm-8">
                        <h1><?= $input_search?> <small>Results <strong><?=t2('%s item', '%s items', $count_results)?>.</strong></small></h1>
                    </div>
                    <div class="col-sm-4 text-right">
                        <div class="btn-group search-filter-dropdown">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sort Results <img src="<?=$view->getThemePath()?>/images/dropdown_filter.png"></button>
                            <ul class="dropdown-menu">
                                <li><h4>Sort By</h4></li>
                                <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;ipp=<?=h($items_per_page)?>&amp;sortBy=recent"><i class="fa <?= $sortBy == 'recent' ? 'fa-dot-circle-o' : 'fa-circle-o' ?>"></i> Recent First</a></li>
                                <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;ipp=<?=h($items_per_page)?>&amp;sortBy=oldest"><i class="fa <?= $sortBy == 'oldest' ? 'fa-dot-circle-o' : 'fa-circle-o' ?>"></i> Oldest First</a></li>
                                <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;ipp=<?=h($items_per_page)?>&amp;sortBy=name"><i class="fa <?= $sortBy == 'name'? 'fa-dot-circle-o' : 'fa-circle-o' ?>"></i> Name</a></li>
                            </ul>
                        </div>
                    </div>
                </div>


                    <?php if(count($keywords)>0) { ?>
                        <div class="search-keywords">
                            <?php foreach ($keywords as $keyword) {
                                $tagUrl = $view->controller->getRemoveTagUrl($keyword);
                                ?>
                                <span><?= $keyword ?> <a href="<?=$tagUrl?>"><i class="fa fa-close"></i></a></span>
                            <?php } ?>
                        </div>
                    <?php } ?>

                <div class="row assets">
                    <?php foreach($search_assets as $asset) {
                        $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($asset);
                        ?>
                        <div class="col-xs-12 col-sm-6 col-lg-4 thumbnail-container">
                            <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail">
                                <img src="<?=$asset->getThumbnailImageURL()?>" class="<?= $asset->getAssetType() ?>"/>
                            </a>
                            <div class="thumbnail-caption">
                                <h3>
                                    <span class="pull-right">
                                        <?php
                                        $u = new User();
                                        if ($u->isRegistered()) {
                                            ?>
                                            <a href="#" class="add-to-lightbox" data-tooltip="lightbox" title="<?=t('Add to Lightbox')?>" data-asset="<?= $asset->getId() ?>"></a>
                                        <?php } ?>
                                        <a href="<?=$asset->getDownloadURL()?>" data-tooltip="download" title="<?=t('Download')?>"><i class="fa fa-download"></i></a>
                                    </span>
                                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="asset-link"><?= trim(h($asset->getAssetName())) ?: '&nbsp;' ?></a>
                                </h3>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php
                if (isset($pagination) && $pagination->getTotalPages() > 1) { ?>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="dropdown search-page-results" style="">
                                <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="current-option"><?= h($items_per_page) ?> </span> <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;sortBy=<?=h($sortBy)?>&amp;ipp=12">12</a></li>
                                    <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;sortBy=<?=h($sortBy)?>&amp;ipp=24">24</a></li>
                                    <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;sortBy=<?=h($sortBy)?>&amp;ipp=48">48</a></li>
                                    <li><a href="<?= URL::to('/search')?>?filter=<?=$filter?>&amp;keywords=<?=h($qkeywords)?>&amp;sortBy=<?=h($sortBy)?>&amp;ipp=96">96</a></li>
                                </ul>
                            </div>
                            Per Page
                        </div>
                        <div class="col-sm-8">
                            <?php print $pagination->renderDefaultView(); ?>
                        </div>
                    </div>

                <?php } ?>

                <?php if ($showCollectionResults) { ?>
                    <h3><?=t('Also check out...')?></h3>
                    <?php
                    View::element('all_collection_grid', ['collections' => $collectionResults], 'brand_central');
                    ?>
                <?php } ?>
                <div style="min-height: 200px;"></div>
            <?php } else { ?>
                <div style="min-height: 600px;">
                </div>
            <?php } ?>
        </div>
    </div>
</div>
