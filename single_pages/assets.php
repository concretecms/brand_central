<div class="container">
    <?php
    /** @var bool $canDownload */

    use Concrete\Core\Support\Facade\Url;

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
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?=URL::to('/assets', 'edit', $asset->getId())?>"><?=t('Edit Details')?></a>
                                <?php if ($canDeleteAsset) { ?>
                                    <a class="dropdown-item" href="<?=URL::to('/assets', 'delete', $asset->getId())?>"><span class="text-danger"><?=t('Delete')?></span></a>
                                <?php } ?>
                            </div>
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
                    <h3><?=t2('File', 'Files', count($asset_files))?></h3>
                    <ul>
                        <?php
                        $u = new User();
                        foreach($asset_files as $file) {
                            $assetFile = $file->getAssetFile();
                            if ($u->isRegistered()) {
                                $fileDownloadURL = $assetFile->getForceDownloadURL();
                            } else {
                                $fileDownloadURL = $asset->getDownloadURL();
                            }
                            ?>
                            <li>
                                <?php
                                $mime = $assetFile->getMimeType();
                                if ($mime === 'application/pdf') { ?>

                                <a href="#" class="text-dark" data-toggle="modal" data-target="#pdf-viewer-<?=$assetFile->getFileID()?>">
                                    <?=$file->getAssetFileDescription()?>
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                                <a href="<?=$fileDownloadURL?>" class="text-dark<?php echo $canDownload ? "" : " request-download-opt-in" ?>">
                                    <i class="fa fa-file-download"></i>
                                </a>
                                <div class="modal fade modal-pdf" id="pdf-viewer-<?=$assetFile->getFileID()?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true"><i class="fa fa-times"></i></span>
                                        </button>
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div
                                                        class="modal-pdf-viewer"
                                                        data-viewer="pdf"
                                                        id="pdf-viewer-content-<?=$assetFile->getFileID()?>"
                                                        data-pdf-url="<?=$assetFile->getURL()?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php } else { ?>
                                 <a href="<?=$fileDownloadURL?>" class="text-dark<?php echo $canDownload ? "" : " request-download-opt-in" ?>">
                                     <?=$file->getAssetFileDescription()?>
                                     <i class="fa fa-file-download"></i>
                                 </a>
                                <?php } ?>
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

        <div class="modal" id="download-opt-in-modal" role="dialog" tabindex="-1">
          <div class="modal-dialog" style="margin-top: 200px;">
            <div class="modal-content">
              <form enctype="multipart/form-data" action="#" method="post" id="opt-in-form">
                <div class="modal-body">
                  <?php
                    // render the modal content form the global "Download Agreement" Stack.
                    $stack = \Concrete\Core\Page\Stack\Stack::getByName('Download Agreement');
                    if (is_object($stack)) {
                        $stack->display();
                    }
                  ?>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-default" data-dismiss="modal">
                      <?php echo t("Cancel"); ?>
                  </button>
                  <button class="btn btn-primary" type="submit">
                      <?php echo t("Agree"); ?>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

    </div>

<?php
    }
?>

</div>