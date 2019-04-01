<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php

if (isset($renderer) && isset($entry) && is_object($entry)) {
    $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($entry);
    ?>
    <section class="collection-asset-list-container">
        <div class="row">
            <div class="col-xs-12 col-md-4">
                <div class="thumbnail-container">
                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail">
                        <img src="<?=$asset->getThumbnailImageURL()?>" class="<?= $asset->getAssetType() ?>"/>
                    </a>
                </div>
            </div>
            <div class="col-xs-12 col-md-8">
                <div class="asset-details">
                    <h3>
                        <?= $asset->getAssetName() ?>
                    </h3>
                    <p>
                        <?= $asset->getAssetDescription() ?>
                    </p>
                    <a href="<?=$asset->getDownloadURL()?>"><i class="fa fa-download"></i> Download Files</a>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
