<div class="container">
        <div class="text-center">
        <h3><?= t('Your download will begin shortly.') ?></h3>
        <span class="help-block">
            <?= t('If not, %sclick here%s to download.', '<a id="trigger_download" href="' . $downloadUrl . '" download="' . $downloadUrl . '" target="_blank">', '</a>') ?>
        </span>
    </div>
</div>

<script>
    $(function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'download', {
                'event_category': 'asset_download',
                'value': <?= json_encode($asset->getID()) ?>

            });
        }
    })
</script>
<script src="<?= $view->getThemePath() ?>/js/asset-download.js"></script>
