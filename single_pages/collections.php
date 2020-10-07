<?php

use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete5\AssetLibrary\Results\Formatter\Asset;

if ($collection) {
?>
    <div class="collection-container clearfix">
        <div class="row">
            <div class="col-12">
                <div class="float-right mb-2">
                    <?php if ($canEditCollection) { ?>
                        <div class="btn-group">
                            <button type="button" class="btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?=t('Edit')?> <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?=URL::to('/collections', 'edit', $collection->getId())?>"><?=t('Edit Details')?></a>
                                <a class="dropdown-item" href="<?=URL::to('/collections', 'reorder', $collection->getId())?>"><?=t('Reorder Assets')?></a>
                                <?php if ($canDeleteCollection) { ?>
                                    <a class="dropdown-item" href="<?=URL::to('/collections', 'delete', $collection->getId())?>"><span class="text-danger"><?=t('Delete Collection')?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <a href="<?=URL::to('/collections', 'download', $collection->getId())?>" class="btn-round"><?=t('Download All')?></a>
                </div>

                <h1><?= $collection_name ?></h1>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php if($collection->getCollectionDescription()) { ?>
                    <p style="margin-bottom: 30px;"><?= nl2br($collection->getCollectionDescription()) ?></p>
                <?php } ?>
                
                <ul class="switch-view">
                    <li>
                        <a href="javascript:void(0);" data-tooltip="regular-grid" data-grid-view="regular" title="<?php echo h(t("Regular Grid")); ?>">
                            <i class="fa fa-th-large"></i>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-tooltip="masonry-grid" data-grid-view="masonry" title="<?php echo h(t("Masonry Grid")); ?>">
                            <i class="fa fa-th"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row grid-view grid-view-regular d-none">
            <div class="col-md-12">
                <div class="row assets">
                    <?php foreach($collection_assets as $asset) {
                        $asset = new Asset($asset);
                        ?>
                        <div class="col-12 col-sm-6 col-lg-4 thumbnail-container">
                            <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail">
                                <img src="<?=$asset->getThumbnailImageURL()?>" class="<?= $asset->getAssetType() ?>"/>
                            </a>
                            <div class="thumbnail-caption">
                                <h3>
                                    <span class="float-right">
                                        <?php
                                        $u = new User();
                                        if ($u->isRegistered()) {
                                            ?>
                                            <a href="#" data-tooltip="lightbox" title="<?=t('Add to Lightbox')?>" data-asset="<?= $asset->getId() ?>"><i class="fa fa-plus"></i></a>
                                        <?php } ?>
                                        <a href="<?=$asset->getDownloadURL()?>" data-tooltip="download" title="<?=t('Download')?>"><i class="fa fa-download"></i></a>
                                    </span>
                                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="asset-link"><?=$asset->getAssetName()?></a>
                                </h3>
                            </div>

                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
        <div class="row grid-view grid-view-masonry d-none">
            <div class="">
                <div class="grid">
                    <?php foreach($collection_assets as $asset) { ?>
                        <?php $asset = new Asset($asset); ?>

                        <div class="grid-item">
                            <a href="<?php echo Url::to('/assets', $asset->getId()) ?>" class="thumbnail">
                                <img src="<?php echo h($asset->getThumbnailImageURL()) ?>" class="<?php echo h($asset->getAssetType()) ?>"/>
                            </a>

                            <div class="overlay">
                                <a href="<?php echo Url::to('/assets', $asset->getId()) ?>" class="description">
                                            <span class="title">
                                                <?php echo $asset->getAssetName()?>
                                            </span>

                                    <?php echo $asset->getAssetDescription(); ?>
                                </a>

                                <?php $u = new User(); ?>

                                <?php if ($u->isRegistered()) { ?>
                                    <a href="#" class="add-to-lightbox" data-tooltip="lightbox" title="<?php echo h(t('Add to Lightbox')) ?>" data-asset="<?php echo h($asset->getId()) ?>">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                <?php } ?>

                                <a href="<?php echo h($asset->getDownloadURL()) ?>" data-tooltip="download" title="<?php echo h(t('Download')) ?>" class="download">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php
    } else {

    View::element('all_collection_grid', ['collections' => $collections, 'pagination' => $pagination], 'brand_central');

}
?>


