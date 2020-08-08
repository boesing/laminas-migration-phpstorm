# PHPStorm META informations for migrated zendframework libraries

This tool can create a phpstorm meta information with all used `laminas`, `mezzio` or `api-tools` aliases.


## Installation

```bash
$ composer require --dev boesing/laminas-migration-phpstorm
``` 

## Configuration

If you are using `laminas/laminas-component-installer`, you should be asked if you want to add this package to your project.

If not, add the `ConfigProvider` (mezzio) or `Module` (MVC) of this package to your applications `development` configuration.

**WARNING:** If you've added this package as a dev-dependency of your `composer.json`, make sure you are using `laminas/laminas-development-mode` aswell. This is necessary as the `ConfigProvider` of this package wont be available if you remove dev-dependencies.


## Usage

```bash
mkdir -p .phpstorm.meta.php/

vendor/bin/laminas-cli migration:phpstorm-extended-meta vendor/ .phpstorm.meta.php/zendframework.meta.php
```


