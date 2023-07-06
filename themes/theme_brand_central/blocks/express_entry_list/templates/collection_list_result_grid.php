<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

$items = $result->getItems();
$results = [];
foreach($items as $result) {
    $results[] = $result->getEntry();
}

View::element('collection_grid', ['collections' => $results], 'brand_central');

$itemsPerPageOptionUrl = function($itemsPerPage) {
    $url = \League\Url\Url::createFromServer($_SERVER);
    $query = $url->getQuery();
    $query->modify(['itemsPerPage' => $itemsPerPage]);
    $url->setQuery($query);
    $itemsPerPageOptionUrl = (string) $url;
    return $itemsPerPageOptionUrl;
}
?>

<?php
if ($enableItemsPerPageSelection || $enablePagination) {
?>

<div class="row mt-2 mb-5">
    <?php if ($enableItemsPerPageSelection) { ?>
        <div class="col-sm-4">
            <div class="dropdown">
                <button data-toggle="dropdown" class="btn-round" aria-haspopup="true" aria-expanded="false"><?= h($itemsPerPageSelected) ?> <i class="fa fa-chevron-down"></i></button>
                Per Page
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(12) ?>">12</a></li>
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(24) ?>">24</a></li>
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(48) ?>">48</a></li>
                    <a class="dropdown-item" href="<?= $itemsPerPageOptionUrl(96) ?>">96</a></li>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($enablePagination) { ?>
        <div class="col-sm-8 d-flex flex-row-reverse">
            <?php if ($pagination) { ?>
                <?=$pagination ?>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php } ?>

