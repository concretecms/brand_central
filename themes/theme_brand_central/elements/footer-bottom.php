        </div>
        <?php View::element('footer_required'); ?>

        <?php if ($lightboxApp) { ?>
            <div id="lightbox-app"></div>
            <script src="<?= $view->getThemePath() ?>/js/lightbox.js"></script>
        <?php } ?>
    </body>
</html>
