<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="container">
    <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('submit')?>">

    <?php
    if (isset($collection)) { ?>
        <input type="hidden" name="collection_id" value="<?=$collection->getID()?>">
    <?php }

    $renderer->render($collection);
    ?>

    <div class="form-actions">
        <?php if ($collection) { ?>
            <a class="btn-round pull-left" href="<?=URL::to('/collections', $collection->getId())?>"><?=t('Back')?></a>
        <?php } ?>

        <button type="submit" name="Submit" class="btn-submit pull-right"><?=$buttonText?></button>
    </div>

    </form>
</div>