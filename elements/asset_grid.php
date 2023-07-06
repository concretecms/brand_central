<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Asset;

?>

<div class="row assets grid-view grid-view-regular">
    <?php
    foreach($assets as $asset) {
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
                                    <a href="#" data-action="add-to-lightbox" data-tooltip="lightbox" title="<?=t('Add to Lightbox')?>" data-asset="<?= $asset->getId() ?>"><i class="fa fa-plus"></i></a>
                                <?php } ?>
                                <a href="<?=$asset->getDownloadURL()?>" data-tooltip="download" title="<?=t('Download')?>"><i class="fa fa-download"></i></a>
                            </span>
                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="asset-link"><?=$asset->getAssetName()?></a>
                </h3>
            </div>

        </div>
    <?php

    } ?>
</div>

<div class="row assets grid-view grid-view-masonry">
    <div class="col-12">
        <div class="grid">
            <?php foreach($assets as $asset) { ?>
                <?php $asset = new Asset($asset); ?>

                <div class="grid-item">
                    <a href="<?php echo Url::to('/assets', $asset->getId()) ?>" class="thumbnail">
                        <img src="<?php echo h($asset->getThumbnailImageURL()) ?>" class="<?php echo h($asset->getAssetType()) ?>"/>
                    </a>

                    <div class="overlay">
                        <a href="<?php echo Url::to('/assets', $asset->getId()) ?>">
                            <span class="description-wrapper">
                                <span class="title">
                                    <?php echo $asset->getAssetName()?>
                                </span>

                                <span class="description">
                                    <?php echo $asset->getAssetDescription(); ?>
                                </span>
                            </span>
                        </a>

                        <?php $u = new User(); ?>

                        <?php if ($u->isRegistered()) { ?>
                            <a href="#" data-action="add-to-lightbox" class="add-to-lightbox" data-tooltip="lightbox" title="<?php echo h(t('Add to Lightbox')) ?>" data-asset="<?php echo h($asset->getId()) ?>">
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
