<?php
if ($collection) {
?>
    <div class="collection-container clearfix">
        <div class="row">
            <div class="col-xs-12">
                <?php if ($canEditCollection) { ?>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?=t('Edit Collection')?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="<?=URL::to('/collections', 'edit', $collection->getId())?>"><?=t('Edit Details')?></a></li>
                            <li><a href="<?=URL::to('/collections', 'reorder', $collection->getId())?>"><?=t('Reorder Assets')?></a></li>
                            <?php if ($canDeleteCollection) { ?>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?=URL::to('/collections', 'delete', $collection->getId())?>"><span class="text-danger"><?=t('Delete Collection')?></span></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php /*

                    <a href="" class="pull-right btn-round"><?=t('Edit Collection')?></a>

                    */ ?>
                <?php } ?>

                <h1><?= $collection_name ?></h1>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <?php if($collection->getCollectionDescription()) { ?>
                    <p style="margin-bottom: 30px;"><?= nl2br($collection->getCollectionDescription()) ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="row assets">
                    <?php foreach($collection_assets as $asset) {
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
                                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="asset-link"><?=$asset->getAssetName()?></a>
                                </h3>
                            </div>

                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
<?php
    } else {

    View::element('all_collection_grid', ['collections' => $collections], 'brand_central');

}
?>


