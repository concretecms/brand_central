<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
use Concrete5\AssetLibrary\Results\Formatter\Lightbox;
?>

<p><strong>My Lightboxes</strong></p>
<ul class="lightbox-sidenav">
    <?php foreach($lightboxes as $entry) {
        $lightbox = new Lightbox($entry);
        ?>
        <li><a href="<?=$lightbox->getMyAccountViewLink()?>"><?= $lightbox->getName() ?></a></li>
    <?php } ?>
</ul>
