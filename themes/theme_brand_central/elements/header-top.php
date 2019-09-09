<!DOCTYPE html>
<html>
    <head>
        <?php View::element('header_required'); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700" rel="stylesheet">

        <link href="<?= $view->getThemePath() ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <link href="<?= $view->getThemePath() ?>/css/main.css" rel="stylesheet" type="text/css">

        <script src="<?= $view->getThemePath() ?>/js/bootstrap.min.js"></script>

        <script src="<?= $view->getThemePath() ?>/js/main.js"></script>

    </head>

    <body id="page<?= $c->getCollectionID() ?>">

        <div class="<?= $c->getPageWrapperClass() ?>">
