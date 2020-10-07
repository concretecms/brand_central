<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
use Concrete5\AssetLibrary\Results\Formatter\Lightbox;
?>

    <div class="row">
        <div class="col-12">
            <section class="all-collection-list-container">
                <div class="row">
                    <?php foreach($lightboxes as $entry) {
                        $lightbox = new Lightbox($entry);
                        ?>
                        <div class="col-12 col-md-4">
                            <div class="thumbnail-container lightbox-grid-container">
                                <a href="<?=$lightbox->getPublicViewLink()?>" class="thumbnail">
                                    <?php if($lightbox->getCoverImageURL()) { ?>
                                        <img src="<?=$lightbox->getCoverImageURL()?>" />
                                    <?php } ?>
                                </a>
                                <div class="thumbnail-caption">
                                    <h3>
                                        <a href="<?=$lightbox->getPublicViewLink()?>"><?=$lightbox->getName()?></a>
                                    </h3>
                                    <p>Created <?= $lightbox->getAuthor() ? 'by <strong>'.$lightbox->getAuthor().'</strong>' : null  ?> on <?=$lightbox->getDateAdded()?>  </p>
                                    <p><strong><?= t2('%d Asset','%d Assets', count((array)$lightbox->getAssets())) ?> </strong></p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
            <div style="min-height:200px;"></div>
        </div>
    </div>

