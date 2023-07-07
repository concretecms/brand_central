        </div>
        <?php View::element('footer_required'); ?>

<?php if (isset($lightboxApp)) { ?>
            <div id="lightbox-app"></div>
            <script src="<?= $view->getThemePath() ?>/js/lightbox.js"></script>
        <?php } ?>
        <?=View::element('icons', [], 'brand_central')?>
    </body>
</html>
