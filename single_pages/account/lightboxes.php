<?php
    defined('C5_EXECUTE') or die("Access Denied.");
    use Concrete5\AssetLibrary\Results\Formatter\Lightbox;


    if($lightbox) {
?>

    <div class="row">
        <div class="col-xs-12">
            <h1><?= $lightbox_name ?></h1>
        </div>
    </div>
    <div class="row collection-container">
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-12">

                    <div class="row assets">
                        <?php foreach($lightbox_assets as $asset) {
                            $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($asset);
                            ?>
                            <div class="col-xs-12 col-md-4 thumbnail-container">
                                <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail" style="max-height: 200px;">
                                    <img src="<?=$asset->getThumbnailImageURL()?>" class="<?= $asset->getAssetType() ?>"/>
                                </a>
                                <div class="thumbnail-caption">
                                    <h3>
                                        <span class="pull-right">
                                        <a href="#" class="btn-remove-asset" data-asset="<?= $asset->getId() ?>" style="color:red; margin-right: 5px;"><i class="fa fa-trash"></i></a>

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
        <div class="col-md-2">
            <?php View::element('lightbox_sidenav', ['lightboxes' => $lightboxes], 'brand_central'); ?>
        </div>
    </div>

    <div class="modal" id="remove-asset-modal" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="margin-top: 200px;">
            <div class="modal-content">
                <form enctype="multipart/form-data" action="<?=$view->action('perform_remove_asset', $lightbox->getId())?>" method="post">
                    <?=$token->output('perform_remove_asset')?>
                    <div class="modal-body">
                        <h2>Do you wan't to remove the asset from "<?= $lightbox_name ?>"?</h2>
                        <input type="hidden" id="modal-asset-id" name="asset" />
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" type="submit">Remove Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php }

else {
?>
    <div class="row">
        <div class="col-xs-12">
            <h1>My Lightboxes</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <?php View::element('lightbox_grid', ['lightboxes' => $lightboxes], 'brand_central'); ?>
        </div>
        <div class="col-md-2">
            <?php View::element('lightbox_sidenav', ['lightboxes' => $lightboxes], 'brand_central'); ?>
        </div>
    </div>

    <div class="modal" id="rename-lightbox-modal" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="margin-top: 200px;">
            <div class="modal-content">
                <form enctype="multipart/form-data" action="<?=$view->action('rename') ?>" method="post">
                    <?=$token->output('perform_rename')?>
                    <div class="modal-header">
                        <h3>Edit Lightbox</h3>
                    </div>
                    <div class="modal-body">
                        <label>Lightbox Name</label>
                        <input type="text" name="name" id="modal-lightbox-name" autocomplete="off"/>
                        <input type="hidden" name="lightbox" id="modal-lightbox-id"/>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-success" type="submit">Rename Lightbox</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php } ?>
