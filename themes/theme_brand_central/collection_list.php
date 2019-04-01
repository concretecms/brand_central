<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @var $dateHelper Concrete\Core\Localization\Service\Date
 */

$view->inc('elements/header.php');
?>

<?php
$a = new Area('Main');
$a->enableGridContainer();
$a->display($c);
?>


<?php $view->inc('elements/footer.php'); ?>
