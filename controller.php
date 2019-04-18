<?php

namespace Concrete\Package\BrandCentral;

use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Concrete5\AssetLibrary\ServiceProvider;
use Concrete5\BrandCentral\Search\Pagination\View\Manager;

class Controller extends Package implements ProviderAggregateInterface
{

    protected $appVersionRequired = '8.5.0';
    protected $pkgVersion = '0.6.1';
    protected $pkgHandle = 'brand_central';
    protected $pkgAllowsFullContentSwap = true;
    protected $pkgAutoloaderRegistries = array(
        'src/AssetLibrary' => '\Concrete5\AssetLibrary',
        'src/BrandCentral' => '\Concrete5\BrandCentral'
    );

    public function getPackageDescription()
    {
        return t('Asset management system');
    }

    public function getPackageName()
    {
        return t('Brand Central');
    }

    public function install()
    {
        parent::install();
        $this->installContentFile('data.xml');
        $this->installFileSets();
        $this->clearWelcomePages();
        $this->installFolders();
    }

    public function on_start()
    {
        $list = $this->app->make(ProviderList::class);
        $list->registerProvider(ServiceProvider::class);

        // Some theme specific code
        $this->app->bind('manager/view/pagination', function($app) {
            return new Manager($app);
        });

        // Extend the ServerInterface binding so that when concrete5 creates the http server we can add our middleware
        $this->app->extend(ServerInterface::class, function(ServerInterface $server) {
            // Add our custom middleware
            return $server->addMiddleware($this->app->make(Middleware::class));
        });
    }

    protected function clearWelcomePages()
    {
        // Delete old pages
        Page::getByPath('/dashboard/welcome')->delete();
        Page::getByPath('/dashboard/welcome/me')->delete();
        Page::getByPath('/account/welcome')->delete();

        // Install our new content
        $this->installContentFile('desktop.xml');

        // Move the new welcome page to the top
        Page::getByPath('/account/welcome')->movePageDisplayOrderToTop();
    }

    protected function installFolders()
    {
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();

        $add = ['Assets', 'Incoming', 'Deleted Assets'];

        foreach ($add as $name) {
            $folder = $root->getNodeByName($name);
            if (!$folder) {
                $filesystem->addFolder($root, $name);
            }
        }
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installFileSets();
        $this->installFolders();

        $service = $this->app->make(PackageService::class);
        $this->installSinglePages($service->getByHandle($this->pkgHandle));
    }

    protected function installSinglePages($pkg)
    {
        $pages = [
            '/account/lightboxes',
            '/assets/download',
            '/lightboxes'
        ];

        // It is really stupid we have to do this, but unfortunately we can't put
        // these single pages in data.xml because they get killed when the content is cleared on full install
        // and we can't put them in content.xml because we only load content.xml once. So we're putting
        // them in BOTH.
        foreach ($pages as $path) {
            if (Page::getByPath($path)->getCollectionID() <= 0) {
                Single::add($path, $pkg);
            }
        }

        $c = Page::getByPath('/lightboxes');
        $c->setAttribute('exclude_nav', true);
    }

    protected function installFileSets()
    {
        $set = Set::getByName('Home Page Background');
        if (!$set) {
            $set = Set::add('Home Page Background');
        }
    }

    /**
     * @return ProviderInterface
     */
    public function getEntityManagerProvider()
    {
        $locations = [
            'src/BrandCentral' => 'Concrete5\\BrandCentral\\Entity'
        ];

        return $this->app->make(StandardPackageProvider::class, ['pkg' => $this, 'locations' => $locations]);
    }

}
