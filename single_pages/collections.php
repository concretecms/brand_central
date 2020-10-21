<?php

use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete5\AssetLibrary\Results\Formatter\Asset;

if ($collection) {
?>
    <div class="container collection-container clearfix">
        <div class="row">
            <div class="col-12">
                <div class="float-right mb-2">
                    <?php if ($canEditCollection) { ?>
                        <div class="btn-group">
                            <button type="button" class="btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?=t('Edit')?> <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?=URL::to('/collections', 'edit', $collection->getId())?>"><?=t('Edit Details')?></a>
                                <a class="dropdown-item" href="<?=URL::to('/collections', 'reorder', $collection->getId())?>"><?=t('Reorder Assets')?></a>
                                <?php if ($canDeleteCollection) { ?>
                                    <a class="dropdown-item" href="<?=URL::to('/collections', 'delete', $collection->getId())?>"><span class="text-danger"><?=t('Delete Collection')?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <a href="<?=URL::to('/collections', 'download', $collection->getId())?>" class="btn-round"><?=t('Download All')?></a>
                </div>

                <h1><?= $collection_name ?></h1>
            </div>
        </div>

        <?php if($collection->getCollectionDescription()) { ?>
            <div class="row">
                <div class="col-12">
                    <p style="margin-bottom: 30px;"><?= nl2br($collection->getCollectionDescription()) ?></p>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-12">
                <?php
                Element::get('asset_grid_controls', 'brand_central')->render();
                ?>
            </div>
        </div>

        <?php
        Element::get('asset_grid', [
                'assets' => $collection_assets,
        ], 'brand_central')->render();
        ?>

    </div>
<?php
    } else {
    ?>
<div class="container">
    <?php
    View::element('all_collection_grid', ['collections' => $collections, 'pagination' => $pagination], 'brand_central');
    ?>
</div>
<?php
}
?>


