<?php

namespace Concrete5\AssetLibrary;

use Concrete\Core\Express\Controller\Manager as ExpressControllerManager;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete5\AssetLibrary\API\V1\Lightboxes;
use Concrete5\AssetLibrary\API\V1\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Routing\Router;
use Concrete5\AssetLibrary\API\V1\Assets;
use Concrete5\AssetLibrary\API\V1\Collections;
use Concrete5\AssetLibrary\API\V1\Tags;
use Concrete5\AssetLibrary\API\V1\TagsGenerator;
use Concrete5\AssetLibrary\Express\Controller\AssetController;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->registerAPI();
        $this->registerExpressControllers();
        $this->registerThemePaths();
    }

    protected function registerExpressControllers()
    {
        $controllerManager = $this->app->make(ExpressControllerManager::class);
        $controllerManager->extend('asset', function ($app) {
            return new AssetController($app);
        });
    }

    protected function registerThemePaths()
    {
        $collection = $this->app->make(ThemeRouteCollection::class);
        $collection->setThemeByRoute('/account/*', 'theme_brand_central');
        $collection->setThemeByRoute('/register', 'theme_brand_central');
    }

    protected function registerAPI()
    {
        /**
         * @var $router Router
         */
        $router = $this->app->make('router');
        $router->buildGroup()
            ->setPrefix('/api/v1')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function($groupRouter) {
                /**
                * @var $groupRouter Router
                */
                $groupRouter->post('/tags', [Tags::class, 'add']);
                $groupRouter->post('/assets/upload', [Assets::class, 'upload']);
                $groupRouter->post('/assets', [Assets::class, 'create']);
                $groupRouter->post('/assets/bulk', [Assets::class, 'bulkCreation']);

                $groupRouter->post('/tags/google-vision/process/images', [TagsGenerator::class, 'processImages']);
                $groupRouter->post('/tags/google-vision/process', [TagsGenerator::class, 'processImageById']);

                $groupRouter->put('/assets/{assetID}', [Assets::class, 'update']);

                $groupRouter->get('/collections', [Collections::class, 'list']);
                $groupRouter->post('/collections/create', [Collections::class, 'create']);

                $groupRouter->get('/tags', [Tags::class, 'list']);
                $groupRouter->get('/assets/{assetID}', [Assets::class, 'read']);

                $groupRouter->get('/lightboxes', [Lightboxes::class, 'list']);
                $groupRouter->post('/lightboxes/create', [Lightboxes::class, 'add']);
                $groupRouter->put('/lightboxes/set', [Lightboxes::class, 'set']);
            });

    }

}
