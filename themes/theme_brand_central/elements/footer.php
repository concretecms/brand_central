
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    $a = new GlobalArea('Footer_Side_A');
                    $a->display($c);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                    $a = new GlobalArea('Footer_Side_B');
                    $a->display($c);
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                    $a = new GlobalArea('Footer_Side_C');
                    $a->display($c);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <?php
                    $a = new GlobalArea('Footer_Legal');
                    $a->display($c);
                    ?>
                </div>
            </div>
        <?php View::element('footer_logos', [], 'brand_central') ?>
            <div class="row">
            <div class="col-md-12 col-12 text-center">
                        <span>
                        <?php
                        $page = \Concrete\Core\Page\Page::getCurrentPage();
                        /** @var \Concrete\Core\Page\Collection\Version\Version $version */
                        $version = $page->getVersionObject();
                        echo t('Date updated: ') . date('d M Y', strtotime($version->getVersionDateCreated()));
                        ?>
                        </span>
                    </div>
                </div>
            <?php View::element('footer_copyright', [], 'brand_central') ?>
        </div>
    </footer>


    <?php $this->inc('elements/footer-bottom.php'); ?>
