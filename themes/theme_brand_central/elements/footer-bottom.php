        </div>

        <?php
        /*
         * Note: this is bootstrap 4's JavaScript. BS5 is used by the core, so in edit mode you may see
         * this AND the core's bootstrap.js being included. Hopefully they will not collide.
         */
        ?>
        <script src="<?=$view->getThemePath()?>/js/bootstrap.js"></script>

        <?php View::element('footer_required'); ?>

        <?php if (isset($lightboxApp)) { ?>
            <div id="lightbox-app"></div>
            <script src="<?= $view->getThemePath() ?>/js/lightbox.js"></script>
        <?php } ?>
        <?=View::element('icons', [], 'brand_central')?>
    </body>
</html>
