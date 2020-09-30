<div class="text-center">
    <h3><?= t('Your download will begin shortly.') ?></h3>
    <span class="help-block">
        <?= t('If not, %sclick here%s to download.', '<a id="trigger_download" href="' . $downloadUrl . '" download="' . $downloadUrl . '" target="_blank">', '</a>') ?>
    </span>
</div>

<script>
    $(function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'download', {
                'event_category': 'collection_download',
                'value': <?= json_encode($collection->getID()) ?>

            });
        }
    })
</script>
<script src="<?= $view->getThemePath() ?>/js/collection-download.js"></script>
