<?php defined('C5_EXECUTE') or die("Access Denied."); ?>



<form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('perform_reorder', $collection->getId())?>">

    <?=$token->output('perform_reorder')?>

    <h1><?=t('Reorder Assets')?></h1>

    <table class="table" data-table="reorder-assets">
        <thead>
        <tr>
            <th></th>
            <th><?=t('Name')?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($assets as $asset) {

            $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($asset);
            ?>

            <tr>
                <td style="vertical-align: middle"><i class="fa fa-arrows-alt"></i></td>
                <td><img style="width: 100px" src="<?=$asset->getThumbnailImageURL()?>"></td>
                <td style="width: 100%; vertical-align: middle"><input type="hidden" name="asset[]" value="<?=$asset->getId()?>"> <strong><?=$asset->getName()?></strong></td>
            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>

    <br/>
    <div class="form-actions">
        <a class="btn-round pull-left" href="<?=URL::to('/collections', $collection->getId())?>"><?=t('Back')?></a>
        <button type="submit" name="Submit" class="btn-submit pull-right"><?=t('Save')?></button>
    </div>

</form>


<script type="text/javascript">
    $(function() {
        $('table[data-table=reorder-assets] tbody').sortable({
            handle: 'i.fa-arrows-alt',
            cursor: 'move',
            axis: 'y',
            helper: function(e, ui) { // prevent table columns from collapsing
                ui.addClass('active');
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            stop: function(e, ui) {
                ui.item.removeClass('active');
            }
        });
    })
</script>
