<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete5\AssetLibrary\Results\Formatter\Asset;

?>

<ul class="switch-view">
    <li>
        <a href="javascript:void(0);" data-tooltip="regular-grid" data-grid-view="regular" title="<?php echo h(t("Regular Grid")); ?>">
            <i class="fa fa-th-large"></i>
        </a>
    </li>

    <li>
        <a href="javascript:void(0);" data-tooltip="masonry-grid" data-grid-view="masonry" title="<?php echo h(t("Masonry Grid")); ?>">
            <i class="fa fa-th"></i>
        </a>
    </li>
</ul>