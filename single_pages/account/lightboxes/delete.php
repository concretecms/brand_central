<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


    <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('perform_delete', $lightbox->getId())?>">
        <?=$token->output('perform_delete')?>

        <h1><?=t('Delete Lightbox')?></h1>

        <p><?=t("Are you sure you want to delete this lightbox? The assets will not be removed.")?></p>

        <br/>

        <div class="form-actions">
            <a class="btn-round pull-left" href="<?=URL::to('/account/lightboxes', $lightbox->getId())?>"><?=t('Back')?></a>
            <button type="submit" name="Submit" class="btn-submit-danger pull-right"><?=t('Delete Lightbox')?></button>
        </div>

    </form>
