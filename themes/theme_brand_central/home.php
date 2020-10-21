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
        <form data-form="home-search" class="" method="get" action="<?=URL::to('/search')?>">
            <input name="filter" type="hidden" value="all"/>
            <div class="search input-group">
                <input name="keywords" class="form-control" type="text" placeholder="Search in Brand Central." autocomplete="off"/>
                <div class="input-group-append">
                    <button type="button" class="btn dropdown-toggle search-btn-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="search-btn-label">Anything</span> <span class="caret"></span></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item" data-filter-value="all">Anything</a>
                        <a href="#" class="dropdown-item" data-filter-value="photo">Photos</a>
                        <a href="#" class="dropdown-item" data-filter-value="logo">Logos</a>
                        <a href="#" class="dropdown-item" data-filter-value="video">Videos</a>
                        <a href="#" class="dropdown-item" data-filter-value="template">Templates</a>
                    </div>
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