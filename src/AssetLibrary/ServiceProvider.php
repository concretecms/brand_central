<?php

namespace Concrete5\AssetLibrary;

use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Express\Controller\Manager as ExpressControllerManager;
use Concrete\Core\File\Event\FileVersion;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Http\Middleware\OAuthAuthenticationMiddleware;
use Concrete\Core\Http\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete5\AssetLibrary\API\V1\FileTypes;
use Concrete5\AssetLibrary\API\V1\Lightboxes;
use Concrete5\AssetLibrary\API\V1\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Routing\Router;
use Concrete5\AssetLibrary\API\V1\Assets;
use Concrete5\AssetLibrary\API\V1\Collections;
use Concrete5\AssetLibrary\API\V1\Tags;
use Concrete5\AssetLibrary\API\V1\TagsGenerator;
use Concrete5\AssetLibrary\Express\Controller\AssetController;
use Concrete5\AssetLibrary\Metadata\FileMetadata;
use Concrete5\AssetLibrary\Metadata\Reader\IptcReader;
use Concrete5\AssetLibrary\Metadata\Reader\XmpReader;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->registerAPI();
        $this->registerExpressControllers();
        $this->registerThemePaths();
        $this->registerFileEvents();
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

        $router->buildGroup()
            ->setPrefix('/public_api/v1')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(OAuthAuthenticationMiddleware::class)
            ->routes(function($groupRouter) {
                /**
                 * @var $groupRouter Router
                 */
                $groupRouter->get('/file_types', [FileTypes::class, 'list']);
                $groupRouter->get('/assets/search', [Assets::class, 'search']);
                $groupRouter->get('/assets/{assetID}', [Assets::class, 'read']);
                $groupRouter->get('/assets/get_file/{assetFileId}', [Assets::class, 'getAssetFile']);
            });

    }

    private function registerFileEvents()
    {
        $this->app->make(EventDispatcher::class)->addListener('on_file_version_add', function (FileVersion $event) {
            $loaded = null;
            $version = $event->getFileVersionObject();

            // Try loading XMP data
            try {
                $metadata = $this->app->make(XmpReader::class)->read($version->getFileResource()->readStream());
            } catch (\Throwable $e) {
                \Log::emergency(
                    sprintf(
                        'Failed parsing XMP data for file version: %d v %d: %s',
                        $version->getFileID(),
                        $version->getFileVersionID(),
                        $e
                    )
                );
            }

            if (!$loaded) {
                // Fall back to trying to load IPTC data
                try {
                    $loaded = $this->app->make(IptcReader::class)->read($version->getFileResource()->readStream());
                } catch (\Throwable $e) {
                    \Log::emergency(
                        sprintf(
                            'Failed parsing IPTC data for file version: %d v %d: %s',
                            $version->getFileID(),
                            $version->getFileVersionID(),
                            $e
                        )
                    );
                }
            }

            if ($loaded instanceof FileMetadata) {
                if ($loaded->title) {
                    $version->updateTitle($loaded->title);
                }
                if ($loaded->description) {
                    $version->updateDescription($loaded->description);
                }
                if ($loaded->keywords) {
                    $version->updateTags(implode("\n", $loaded->keywords));
                }
            }
        });
    }

}
