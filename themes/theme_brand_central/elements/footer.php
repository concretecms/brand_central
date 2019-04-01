
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
                <div class="col-md-8 footer-copyright">
                    <span>Copyright © <?=date('Y')?> BrandCentral. All Rights Reserved.</span>
                </div>
            </div>
        </div>
    </footer>


    <?php $this->inc('elements/footer-bottom.php'); ?>
