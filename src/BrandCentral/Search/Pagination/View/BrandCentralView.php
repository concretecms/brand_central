<?php

namespace Concrete5\BrandCentral\Search\Pagination\View;

use Concrete\Core\Search\Pagination\View\ViewInterface;
use Pagerfanta\View\DefaultView;
use Pagerfanta\View\Template\DefaultTemplate;

class BrandCentralView extends DefaultView implements ViewInterface
{

    protected function createDefaultTemplate()
    {
        return new DefaultTemplate();
    }

    public function getArguments()
    {
        return array(
            'prev_message' => '&lt;',
            'next_message' => '&gt;',
            'container_template' => '<nav class="pagination">%pages%</nav>',
        );
    }

}
