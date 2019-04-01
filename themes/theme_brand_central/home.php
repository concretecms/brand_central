<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @var $dateHelper Concrete\Core\Localization\Service\Date
 */

$this->inc('elements/header.php');

$list = new \Concrete\Core\File\FileList();
$set = \Concrete\Core\File\Set\Set::getByName('Home Page Background');
$list->filterBySet($set);
$files = $list->getResults();
if (count($files)) {
    $r = array_rand($files, 1);
    $bk = $files{$r}->getRelativePath();
}
?>

<section class="home-search" data-bk="<?= $bk ?>">
    <div class="search-container">
        <form data-form="search" class="" method="get" action="<?=URL::to('/search')?>">
            <div class="search input-group">
                <input name="keywords" type="text" placeholder="Search in Brand Central." autocomplete="off"/>
                <input name="filter" type="hidden" value=""/>
                <div class="input-group-btn">
                    <button type="button" class="btn dropdown-toggle search-btn-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="search-btn-label">Anything</span> <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right" style="z-index: 1000">
                        <li><a href="#" class="dropdown-item" data-filter-value="">Anything</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="photo">Photos</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="logo">Logos</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="video">Videos</a></li>
                        <li><a href="#" class="dropdown-item" data-filter-value="template">Templates</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-8" style="min-height: 400px;">

                <?php
                $a = new Area('Main');
                $a->display($c);
                ?>

            </div>
            <div class="col-md-4">

                <?php
                $a = new Area('Sidebar');
                $a->display($c);
                ?>

            </div>
        </div>
    </div>
</section>

<?php $this->inc('elements/footer.php'); ?>