<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Collection;

$url = Concrete\Core\Support\Facade\Url::to('/collections');

$defaultQuery = array_filter([
    'ipp' => (int) $items_per_page ?? null
]);
$searchUrl = function($data = []) use ($url, $defaultQuery) {
    $query = $url->getQuery();
    $query->set($data + $defaultQuery);
    return $url->setQuery($query);
};
?>

<div class="row">
    <div class="col-12">
        <section class="all-collection-list-container">
            <div class="row">
                <?php foreach ($collections as $entry) {
                    $collection = new Collection($entry);
                    ?>
                    <div class="col-12 col-sm-6 col-lg-4 thumbnail-container">
                        <a href="<?= $collection->getPublicViewLink() ?>" class="thumbnail">
                            <img src="<?= $collection->getThumbnailImageURL() ?>"/>
                        </a>
                        <div class="thumbnail-caption">
                            <h3>
                                <a href="<?= $collection->getPublicViewLink() ?>"><?= $collection->getTitle() ?></a>
                            </h3>
                            <p><?= $collection->getDateAdded() ?></p>
                            <p><strong><?= $collection->getContentsDescription() ?></strong></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
</div>

<?php
if (isset($pagination) && $pagination->getTotalPages() > 1) { ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="dropdown search-page-results" style="">
                <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="current-option"><?= h($items_per_page) ?> </span> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="<?= $searchUrl(['ipp' => 12]) ?>">12</a></li>
                    <li><a href="<?= $searchUrl(['ipp' => 24]) ?>">24</a></li>
                    <li><a href="<?= $searchUrl(['ipp' => 48]) ?>">48</a></li>
                    <li><a href="<?= $searchUrl(['ipp' => 96]) ?>">96</a></li>
                </ul>
            </div>
            Per Page
        </div>
        <div class="col-sm-8">
            <?php print $pagination->renderDefaultView(); ?>
        </div>
    </div>

<?php } ?>

