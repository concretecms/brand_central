<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
use Concrete5\AssetLibrary\Results\Formatter\Lightbox;
?>

    <div class="row">
        <div class="col-12">
            <section class="collection-list-container">
                <div class="row">
                    <?php foreach($lightboxes as $entry) {
                        $lightbox = new Lightbox($entry);
                        ?>
                        <div class="col-12 col-md-4">
                            <div class="thumbnail-container lightbox-grid-container">
                                <a href="<?=$lightbox->getMyAccountViewLink()?>" class="thumbnail">
                                    <?php if($lightbox->getCoverImageURL()) { ?>
                                        <img src="<?=$lightbox->getCoverImageURL()?>" />
                                    <?php } ?>
                                </a>
                                <div class="thumbnail-caption">
                                    <h3>
                                        <a href="<?=$lightbox->getMyAccountViewLink()?>"><?=$lightbox->getName()?></a>
                                    </h3>
                                    <p>Created: <?=$lightbox->getDateAdded()?></p>
                                    <p><strong><?= t2('%d Asset','%d Assets', count((array)$lightbox->getAssets())) ?> </strong></p>
                                    <div class="lightbox-menu">
                                        <div class="btn-group">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                                            <div class="dropdown-menu">
                                                <a href="#" class="dropdown-item btn-edit-lightbox" data-lightbox="<?= $lightbox->getId() ?>" data-lightbox-name="<?= $lightbox->getLightboxName() ?>"><i class="fa fa-pencil"></i> Edit</a></>
                                                <a class="dropdown-item" href="<?=URL::to('/account/lightboxes', 'delete', $lightbox->getId())?>"><i class="fa fa-trash"></i> Delete</a></>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
            <div style="min-height:200px;"></div>
        </div>
    </div>

