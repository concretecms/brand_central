<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Collection;

?>

<div class="row">
    <div class="col-12">
        <section class="collection-list-container">
            <div class="row">
                <?php foreach ($collections as $entry) {
                    $collection = new Collection($entry);
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="thumbnail-container">
                            <a href="<?= $collection->getPublicViewLink() ?>" class="thumbnail">
                                <img src="<?= $collection->getThumbnailImageURL() ?>"/>
                            </a>
                            <div class="thumbnail-caption">
                                <h3>
                                    <a href="<?= $collection->getPublicViewLink() ?>"><?= $collection->getTitle() ?></a>
                                </h3>
                                <p><?= $collection->getDateAdded() ?></p>
                                <p><strong><?= $collection->getContentsDescription() ?></strong></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
</div>

