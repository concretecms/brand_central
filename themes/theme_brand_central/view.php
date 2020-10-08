<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @var $dateHelper Concrete\Core\Localization\Service\Date
 */
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Formatter\StandardFormatter;
$this->inc('elements/header.php');
?>

<content>

    <?php if (isset($error) && $error instanceof ErrorList && $error->has()) { ?>
        <div class='error-message alert alert-danger'>
            <i class='fa fa-exclamation-triangle fa-2x'></i>

                <?php
                $formatter = new StandardFormatter($error);
                print $formatter->render();
                ?>
        </div>
    <?php } ?>
    <?= $innerContent; ?>
</content>

<br/>

<?php $this->inc('elements/footer.php'); ?>
