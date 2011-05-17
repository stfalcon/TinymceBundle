# TinymceBundle

Bundle for connecting WYSIWYG editor TinyMCE.
By analogy with the bundle https://github.com/ihqs/WysiwygBundle

## Installation

    git submodule add git://github.com/stfalcon/TinymceBundle.git vendor/bundles/Stfalcon/Bundle

Modify your autoloader if you didn't installer another IHQS Bundle yet.
Register namespace :

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Stfalcon'                       => __DIR__.'/../vendor/bundles',
    ));

Instantiate Bundle in your app/AppKernel.hpp file

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
        );
    }

## Configuration

Configure your application

    // app/config.yml
    stfalcon_tinymce: ~

Add script to your templates at the bottom of your page (for faster page display).

    {% render "StfalconTinymceBundle:Script:init" %}