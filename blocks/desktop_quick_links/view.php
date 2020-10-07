<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-desktop-waiting-for-me">
    <h3><?=t('Quick Links')?></h3>

    <div class="list-group">
        <a href="<?=URL::to('/account/lightboxes')?>" class="list-group-item list-group-item-action">
            <h5 class="mb-1"><?=t('My Lightboxes')?></h5>
            <?=t('Save photos for later.')?>
        </a>

        <?php if ($canAddCollections) { ?>
        <a href="<?=URL::to('/collections/add')?>" class="list-group-item list-group-item-action">
            <h5 class="mb-1"><?=t('Add Collection')?></h5>
            <?=t('Create a public collection for assets.')?>
        </a>
        <?php } ?>
        <?php if ($canAddAssets) { ?>
            <a href="<?=URL::to('/assets/create')?>" class="list-group-item list-group-item-action">
                <h5 class="mb-1"><?=t('Add Single Asset')?></h5>
                <?=t('Upload a new file or photo.')?>
            </a>
            <a href="<?=URL::to('/assets/bulk_upload')?>" class="list-group-item list-group-item-action">
                <h5 class="mb-1"><?=t('Bulk Upload')?></h5>
                <?=t('Create a bunch of new assets at once.')?>
            </a>
        <?php } ?>
        <a class="list-group-item list-group-item-action" href="<?=URL::to('/private-pages/training')?>">
            <h5 class="mb-1"><?=t('Help')?></h5>
            <?=t('Get help editing this website.')?>
        </a>
    </div>

</div>
