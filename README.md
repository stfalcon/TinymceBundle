# TinymceBundle

Bundle for connecting WYSIWYG editor TinyMCE to your Symfony2 project.
By analogy with the bundle https://github.com/ihqs/WysiwygBundle

## Installation

    git submodule add git://github.com/stfalcon/TinymceBundle.git vendor/bundles/Stfalcon/Bundle

Modify your autoloader if you didn't installer another Stfalcon Bundle yet.
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
    stfalcon_tinymce:
        mode: "textareas"
        theme: "advanced"
        theme_advanced_buttons1: "mylistbox,mysplitbutton,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink"
        theme_advanced_buttons2: ""
        theme_advanced_buttons3: ""
        theme_advanced_toolbar_location: "top"
        theme_advanced_toolbar_align: "left"
        theme_advanced_statusbar_location: "bottom"
        plugins: "fullscreen"
        theme_advanced_buttons1_add: "fullscreen"

Add script to your templates at the bottom of your page (for faster page display).

    {% render "StfalconTinymceBundle:Script:init" %}