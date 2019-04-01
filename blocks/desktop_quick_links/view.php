<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-desktop-waiting-for-me">
    <h3><?=t('Quick Links')?></h3>

    <div class="list-group">
        <a href="<?=URL::to('/account/lightboxes')?>" class="list-group-item">
            <h4 class="list-group-item-heading"><?=t('My Lightboxes')?></h4>
            <p class="list-group-item-text"><?=t('Save photos for later.')?></p>
        </a>

        <?php if ($canAddCollections) { ?>
        <a href="<?=URL::to('/collections/add')?>" class="list-group-item">
            <h4 class="list-group-item-heading"><?=t('Add Collection')?></h4>
            <p class="list-group-item-text"><?=t('Create a public collection for assets.')?></p>
        </a>
        <?php } ?>
        <?php if ($canAddAssets) { ?>
            <a href="<?=URL::to('/assets/create')?>" class="list-group-item">
                <h4 class="list-group-item-heading"><?=t('Add Single Asset')?></h4>
                <p class="list-group-item-text"><?=t('Upload a new file or photo.')?></p>
            </a>
            <a href="<?=URL::to('/assets/bulk_upload')?>" class="list-group-item">
                <h4 class="list-group-item-heading"><?=t('Bulk Upload')?></h4>
                <p class="list-group-item-text"><?=t('Create a bunch of new assets at once.')?></p>
            </a>
        <?php } ?>
        <a class="list-group-item" href="<?=URL::to('/private-pages/training')?>">
            <h4 class="list-group-item-heading"><?=t('Help')?></h4>
            <p class="list-group-item-text"><?=t('Get help editing this website.')?></p>
        </a>
    </div>

</div>
