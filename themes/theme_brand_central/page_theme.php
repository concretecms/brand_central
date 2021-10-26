<?php

namespace Concrete\Package\BrandCentral\Theme\ThemeBrandCentral;

use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme
{

    public function registerAssets()
    {
        $this->requireAsset('font-awesome');
        $this->requireAsset('jquery');
        $this->requireAsset('vue');
        $this->requireAsset('moment');
    }

    public function getThemeGridFrameworkHandle(): string
    {
        return 'bootstrap4';
    }

}