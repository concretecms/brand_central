<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

$results = $result->getItemListObject()->getResults();

?>

<div class="row">
    <div class="col-12">
        <section class="collection-asset-list-container">
                <?php foreach($results as $asset) {
                    $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($asset);
                    ?>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="thumbnail-container">
                                <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail">
                                    <img src="<?=$asset->getThumbnailImageURL()?>" class="<?= $asset->getAssetType() ?>"/>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-8">
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
                <?php } ?>
        </section>
        <div style="min-height:200px;"></div>
    </div>
</div>

