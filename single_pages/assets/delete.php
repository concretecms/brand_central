<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="container">
    <div class="row">

        <div class="col-8 col-offset-2">

            <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('perform_delete', $asset->getId())?>">
                <?=$token->output('perform_delete')?>

                <h1><?=t('Delete Asset')?></h1>

                <p><?=t("Are you sure you want to delete this asset?")?></p>

                <br/>

                <div class="form-actions">
                    <a class="btn-round pull-left" href="<?=URL::to('/assets', $asset->getId())?>"><?=t('Back')?></a>
                    <button type="submit" name="Submit" class="btn-submit-danger pull-right"><?=t('Delete Asset')?></button>
                </div>

            </form>

        </div>
    </div>
</div>
