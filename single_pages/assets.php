<?php
if (!$error->has()) {
?>
    <div class="asset">
        <div class="row ">
            <div class="col-12">
                <h1><?=$asset_name?>
                    <?php if ($canEditAsset) { ?>
                        <div class="btn-group asset-btn-group">
                            <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?=t('Edit')?> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="<?=URL::to('/assets', 'edit', $asset->getId())?>" class=""><?=t('Edit Details')?></a></li>
                                <?php if ($canDeleteAsset) { ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="<?=URL::to('/assets', 'delete', $asset->getId())?>"><span class="text-danger"><?=t('Delete')?></span></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>

                </h1>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-8 col-sm-12">
                <div class="clearfix asset-meta">
                    <?php
                    $u = new User();
                    if ($u->isRegistered()) {
                    ?>
                        <a href="#" class="add-to-lightbox" data-asset="<?= $asset->getId() ?>">Add to Lightbox</a>
                    <?php } ?>

                    <ul>
                        <li class="asset-meta-author"><span>Added By</span> <?= $asset_author ?></li>
                        <li><?= $asset_date ?></li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-8 col-sm-12">
                <div class="asset-detail-image">
                    <img src="<?=$asset_thumbnail ?>"/>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="asset-description">
                    <h3>Description</h3>
                    <p><?= $asset_desc ?></p>

                </div>

                <?php if($asset_location) { ?>
                    <div class="asset-collections">
                        <h3>Location</h3>
                        <span><?= $asset_location ?></span>
                    </div>
                <?php } ?>

                <div class="asset-files">
                    <ul>
                        <?php
                        $u = new User();
                        foreach($asset_files as $file) {
                            if ($u->isRegistered()) {
                                $fileDownloadURL = $file->getAssetFile()->getForceDownloadURL();
                            } else {
                                $fileDownloadURL = $asset->getDownloadURL();
                            }
                            ?>
                            <li>
                                <a href="<?=$fileDownloadURL?>">
                                    <i class="fa fa-download"></i>
                                    <?=$file->getAssetFileDescription()?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <a href="<?=$asset->getDownloadURL()?>" class="asset-download-bundle">Download All</a>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-12 ex-tags">
                <h2>Tags</h2>
                <hr>
                <?php
                if($asset_tags) {
                ?>

                    <ul>
                        <?php
                            foreach($asset_tags as $tag){ ?>
                                <li>
                                    <a href="<?=URL::to('/search')?>?tags[]=<?=$tag?>">
                                        <?= $tag ?>
                                    </a>
                                </li>
                        <?php
                            }
                        ?>
                    </ul>
                    <?php
                } else { ?>
                    <p><?=t('This asset has no tags.')?></p>
                <?php }
                ?>
            </div>
        </div>

        <div class="row ">
            <div class="col-12 ex-tags">
                <h2>Appears in Collections</h2>
                <hr>

                <?php
                if($asset_collections) {
                    ?>
                    <?php View::element('asset_collection_grid', ['collections' => $asset_collections], 'brand_central'); ?>
                    <?php
                } else { ?>
                    <p><?=t('This asset is not in any collections.')?></p>
                <?php }
                ?>
            </div>
        </div>

    </div>

<?php
    }
?>

