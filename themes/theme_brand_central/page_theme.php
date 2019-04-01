<?php

namespace Concrete\Package\BrandCentral\Theme\ThemeBrandCentral;

use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme {

    protected $pThemeGridFrameworkHandle = 'bootstrap3';

    public function registerAssets()
    {
        $this->providesAsset('javascript', 'bootstrap/*');
        $this->providesAsset('css', 'bootstrap/*');
        $this->requireAsset('css', 'font-awesome');
        $this->requireAsset('javascript', 'jquery');

    }

}