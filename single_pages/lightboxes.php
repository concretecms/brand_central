<?php if ($lightbox) { ?>

    <div class="row asset">
        <div class="col-md-8">
            <h1><?= $lightbox_name ?></h1>
        </div>
        <div class="col-md-4">
            <div class="asset-files pull-right">
                <a href="<?= \URL::to('/lightboxes/download', $lightbox->getId()) ?>" class="asset-download-bundle">Download All Assets</a>
            </div>
        </div>
    </div>
    <hr>
    <div class="row collection-container">
        <div class="col-xs-12">
            <div class="row assets">
            <?php foreach ($lightbox_assets as $asset) {
                $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($asset);
                ?>
                <div class="col-xs-12 col-md-4 thumbnail-container">
                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail" style="max-height: 200px;">
                        <img src="<?= $asset->getThumbnailImageURL() ?>" class="<?= $asset->getAssetType() ?>"/>
                    </a>
                    <div class="thumbnail-caption">
                        <h3>
                            <span class="pull-right">
                                <a href="<?=$asset->getDownloadURL()?>" data-tooltip="download" title="<?=t('Download')?>"><i class="fa fa-download"></i></a>
                            </span>
                            <a href="<?= \URL::to('/assets', $asset->getId()) ?>"
                               class="asset-link"><?= $asset->getAssetName() ?></a>
                        </h3>
                    </div>

                </div>
            <?php } ?>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="row">
        <div class="col-xs-12">
            <h1>Lightboxes</h1>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <?php View::element('public_lightbox_grid', ['lightboxes' => $lightboxes], 'brand_central'); ?>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
