
<?php

 if ($breadcrumbList) { ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbList as $list){ ?>
                        <li><a href="<?=$list['url']?>"><?= $list['label'] ?></a></li>
                    <?php } ?>
                    <li class="active"><?= $pageName ?></li>
                </ol>
            </div>
        </div>
    </div>
<?php } ?>