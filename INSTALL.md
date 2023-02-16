# System requirements

Brand Central is an extension that runs within a Concrete CMS installation. 

Currently, the following combination of Concrete CMS, PHP, and Brand Central should work together:

| Component            | Version          | Notes                                                     |
|----------------------|------------------|-----------------------------------------------------------|
| PHP                  | 7.3.33 -- 7.4.3  | Not fully tested with PHP 8.x yet                         |
| MySQL                | 5.7.12           |                                                           |
| Concrete CMS         | 9.0.0a3 -- 9.0.2 | Not fully tested with the latest Concrete CMS release yet |
| Brand Central        | 1.0.3            | (this package)                                            |
| doctrine/annotations | 1.14.x           | Specific version of Concrete dependancy                   |
| doctrine/orm         | 2.13.x           | Specific version of Concrete dependancy                   |
| doctrine/persistance | 2.5.x            | Specific version of Concrete dependancy                   |


# Example install:

Using this `composer.json` should provide a working combination to run
under PHP 7.4. After running `composer install` with this
`composer.json`, Concrete CMS can be installed. After that, the Brand
Central extension can be installed with the full content swap option
selected.

```
{
  "name": "concretecms/brand_central_example",
  "description": "Example working install of Brand Central",
  "type": "project",
  "license": "MIT",
  "prefer-stable": true,
  "autoload": {
    "psr-4": {
      "ConcreteComposer\\" : "./src"
    },
    "files": [
      "src/helpers.php"
    ]
  },
  "autoload-dev": {
    "psr-4": {
      "ConcreteComposer\\" : "./tests"
    }
  },
  "require": {
    "composer/installers": "^1.3",
    "concrete5/core": "9.0.1",
    "concrete5/dependency-patches": "^1.4.0",
    "vlucas/phpdotenv": "^2.4",
    "concretecms/brand_central": "dev-9.0rc",
    "doctrine/annotations": "^1.14",
    "doctrine/orm": "~2.13.0",
    "doctrine/persistence": "^2.5.1"
  },
  "require-dev": {
    "phpunit/phpunit": "~4.3|^8.0",
    "mockery/mockery": "^0.9.9|^1.2"
  },
  "config": {
    "preferred-install": "dist",
    "platform": {
      "php": "7.4"
    },
    "allow-plugins": {
      "composer/installers": true,
      "mlocati/composer-patcher": true
    }
  },
  "extra": {
    "allow-subpatches": [
      "concrete5/dependency-patches"
    ],
    "branch-alias": {
      "dev-8.x": "8.x-dev"
    },
    "installer-paths": {
      "public/concrete": ["type:concrete5-core"],
      "public/application/themes/{$name}": ["type:concrete5-theme"],
      "public/packages/{$name}": ["type:concrete5-package"],
      "public/application/blocks/{$name}": ["type:concrete5-block"]
    }
  },
  "scripts": {
    "test": "phpunit"
  }
}
```
