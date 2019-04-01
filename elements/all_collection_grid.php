<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Collection;

?>

<div class="row">
    <div class="col-xs-12">
        <section class="all-collection-list-container">
            <div class="row">
                <?php foreach ($collections as $entry) {
                    $collection = new Collection($entry);
                    ?>
                    <div class="col-xs-12 col-sm-6 col-lg-4 thumbnail-container">
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
                <?php } ?>
            </div>
        </section>
    </div>
</div>

