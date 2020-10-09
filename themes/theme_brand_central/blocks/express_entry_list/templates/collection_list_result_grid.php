<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

$items = $result->getItems();
$results = [];
foreach($items as $result) {
    $results[] = $result->getEntry();
}

View::element('collection_grid', ['collections' => $results], 'brand_central');

?>

<?php if ($pagination) { ?>
    <div class="row mb-5">
        <div class="col-12 d-flex flex-column-reverse">
            <?=$pagination ?>
        </div>
    </div>
<?php } ?>

