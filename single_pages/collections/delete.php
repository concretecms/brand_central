<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="container">
    <div class="row">

        <div class="col-xs-8 col-xs-offset-2">

            <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('perform_delete', $collection->getId())?>">
                <?=$token->output('perform_delete')?>

                <h1><?=t('Delete Collection')?></h1>

                <p><?=t("Are you sure you want to delete this collection? The assets will not be removed.")?></p>

                <br/>

                <div class="form-actions">
                    <a class="btn-round pull-left" href="<?=URL::to('/collections', $collection->getId())?>"><?=t('Back')?></a>
                    <button type="submit" name="Submit" class="btn-submit-danger pull-right"><?=t('Delete Collection')?></button>
                </div>

            </form>

        </div>
    </div>
</div>
