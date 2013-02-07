# TinymceBundle

This bundle makes it very easy to add the TinyMCE WYSIWYG editor to your Symfony2 project.

## Installation

### Installation by Composer

If you use composer, add TinyMCE bundle as a dependency to the composer.json of your application

#### For Symfony 2.1

    "require": {
        ...
        "stfalcon/tinymce-bundle": "dev-master"
        ...
    },

#### For Symfony 2.0

    "require": {
        ...
        "stfalcon/tinymce-bundle": "2.0.x-dev"
        ...
    },

### Installation via git submodule

```bash
    git submodule add git://github.com/stfalcon/TinymceBundle.git vendor/bundles/Stfalcon/Bundle/TinymceBundle
```

## Modify autoloader and update kernel

If you are not using Composer, modify your autoloader file.

```php
// app/autoload.php
<?php
    // ...
    $loader->registerNamespaces(array(
        // ...
        'Stfalcon'                       => __DIR__.'/../vendor/bundles',
    ));
```

Add StfalconTinymceBundle to your application kernel.

```php
// app/AppKernel.php
<?php
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
        );
    }
```

The bundle needs to copy the resources necessary to the web folder. You can use the command below:

```bash
    php app/console assets:install web/
```

## Include in template

This bundle comes with an extension for Twig. This makes it very easy to include the TinyMCE Javascript into your pages. Add the tag below to the places where you want to use TinyMCE. It will output the complete Javascript, including `<script>` tags. Add it to the bottom of your page for optimized performance.

```twig
    {{ tinymce_init() }}
```

## Base configuration

By default, tinymce is enabled for all textareas on the page. If you want to customize it, do the following:

Add class "tinymce" to textarea field to initialize TinyMCE.

```html
    <textarea class="tinymce"></textarea>
```

If you want to use jQuery version of the editor set the following parameters:

```yaml
    stfalcon_tinymce:
        include_jquery: true
        tinymce_jquery: true
        ...
```

The option `include_jquery` allows you to load external jQuery library from the Google CDN. Set it to `true` if you haven't included jQuery on your page.

If you are using FormBuilder, use an array to add the class, you can also use the `theme` option to change the
used theme to something other than 'simple' (i.e. on of the other defined themes in your config - the example above
defined 'medium'). e.g.:

```php
<?php
    $builder->add('introtext', 'textarea', array(
        'attr' => array(
            'class' => 'tinymce',
            'data-theme' => 'medium' // simple, advanced, bbcode
        )
    ));
```

## Localization

You can change the language of your TinyMCE editor by adding language selector into top level of configuration, something like:

```yaml
    // app/config/config.yml
    stfalcon_tinymce:
        include_jquery: true
        tinymce_jquery: true
        textarea_class: "tinymce"
        language: %locale%
        theme:
            simple:
                mode: "textareas"
                theme: "advanced"
        ...

```

> NOTE! As there is no way to set custom language for each instance of editor, this option set on language for all instances.

In the example we set default language from the parameters.ini. Of course you can set your default language passing the language code (`ru` or `ru_RU`, `en` or `en_US`)

If language parameter isn't set, the default language will be get from the session.

## Custom configurations

According to the TinyMCE documentation you can configure your editor as you wish. Below is an almost full list of available parameters that you can configure by yourself:

```yaml
    // app/config/config.yml
    stfalcon_tinymce:
        include_jquery: true
        tinymce_jquery: true
        textarea_class: "tinymce"
        base_url: "http://yourdomain.com/" # this parameter may be included if you need to override the assets_base_urls for your template engine (to override a CDN base url)
        # Get current language from the parameters.ini
        language: %locale%
        # Custom buttons
        tinymce_buttons:
            stfalcon: # Id of the first button
                title: "Stfalcon"
                image: "http://stfalcon.com/favicon.ico"
        theme:
            # Simple theme: same as default theme
            simple:
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
            # Advanced theme with almost all enabled plugins
            advanced:
                theme: "advanced"
                plugins: "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template"
                theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect"
                theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor"
                theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen"
                theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak"
                theme_advanced_toolbar_location: "top"
                theme_advanced_toolbar_align: "left"
                theme_advanced_statusbar_location: "bottom"
                theme_advanced_resizing: true
            # Medium number of enabled plugins + spellchecker
            medium:
                mode: "textareas"
                theme: "advanced"
                plugins: "table,advhr,advlink,paste,xhtmlxtras,spellchecker"
                theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,hr,removeformat,|,sub,sup,|,spellchecker"
                theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,cleanup,code,|,tablecontrols"
                theme_advanced_buttons3: ""
                theme_advanced_toolbar_location: "top"
                theme_advanced_toolbar_align: "left"
                theme_advanced_statusbar_location: ""
                paste_auto_cleanup_on_paste: true
                spellchecker_languages: "+English=en,Dutch=nl"
            # BBCode tag compatible theme (see http://www.bbcode.org/reference.php)
            bbcode:
                mode: "none"
                theme: "advanced"
                plugins: "bbcode"
                theme_advanced_buttons1: "bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code"
                theme_advanced_buttons2: ""
                theme_advanced_buttons3: ""
                theme_advanced_toolbar_location: "bottom"
                theme_advanced_toolbar_align: "center"
                theme_advanced_styles: "Code=codeStyle;Quote=quoteStyle"
                entity_encoding: "raw"
                add_unload_trigger: false
                remove_linebreaks: false
                inline_styles: false
                convert_fonts_to_spans: false
```

### External plugins support

If you want to load some external plugins which are situated in your bundle, you should configure it as in the example:

```yaml
    stfalcon_tinymce:
        external_plugins:
            filemanager:
                url: "asset[bundles/acmedemo/js/tinymce-plugin/filemanager/editor_plugin.js]"
            imagemanager:
                url: "asset[bundles/acmedemo/js/tinymce-plugin/imagemanager/editor_plugin.js]"
        ...
        theme:
            simple:
                mode: "textareas"
                theme: "advanced"
                plugins: "-filemanager, -imagemanager, table,advhr,advlink,paste,xhtmlxtras,spellchecker...
```

> NOTE! Make sure that your plugin contain dash (-YOUREXTPLUGIN) before name on theme plugins section

### Custom buttons

You can add some custom buttons to editor's toolbar (See: http://www.tinymce.com/tryit/custom_toolbar_button.php)

First of all you should describe it in your config:

```yaml
    stfalcon_tinymce:
        tinymce_buttons:
            stfalcon: # Id of the first button
                title: "Stfalcon"
                image: "http://stfalcon.com/favicon.ico"
            hello_world: # Id of the second button
                title: "Google"
                image: "http://google.com/favicon.ico"
                ...
                or for the local images
                ...
                image: "asset[bundles/somebundle/images/icon.ico]"

        theme:
            simple:
                mode: "textareas"
                theme: "advanced"
```

And you should create a callback functions `tinymce_button_` for your buttons, based on their button ID:


```javascript

function tinymce_button_stfalcon(ed) {
    ed.focus();
    ed.selection.setContent("Hello from stfalcon.com :)");
}

function tinymce_button_hello_world(ed) {
    ed.focus();
    ed.selection.setContent("Hello world!");
}

```

### Custom CSS

This option enables you to specify a custom CSS file that extends the theme content CSS. This CSS file is the one used within the editor (the editable area). This option can also be a comma separated list of URLs.

If you specify a relative path, it is resolved in relation to the URL of the (HTML) file that includes TinyMCE, NOT relative to TinyMCE itself.

```yaml
    stfalcon_tinymce:
        ...
        theme:
            simple:
                content_css: "/bundles/mybundle/css/tinymce-content.css"
                mode: "textareas"
                ...
```

> NOTE! Read Official TinyMCE documentation for more details: http://www.tinymce.com/wiki.php/Configuration:content_css
