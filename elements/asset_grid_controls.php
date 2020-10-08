<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Asset;

?>

<ul class="switch-view">
    <li>
        <a class="icon-search" href="javascript:void(0);" data-tooltip="regular-grid" data-grid-view="regular" title="<?php echo h(t("Regular Grid")); ?>">
            <svg><use xlink:href="#bc-icon-search-grid" /></svg>
        </a>
    </li>

    <li>
        <a class="icon-search" href="javascript:void(0);" data-tooltip="masonry-grid" data-grid-view="masonry" title="<?php echo h(t("Masonry Grid")); ?>">
            <svg><use xlink:href="#bc-icon-search-masonry" /></svg>
        </a>
    </li>
</ul>