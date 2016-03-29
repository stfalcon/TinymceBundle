# TinymceBundle

This bundle makes it very easy to add the TinyMCE WYSIWYG editor to your Symfony2 project.

## Installation

### Choose the appropriate version

| Bundle Version (X.Y) | PHP     | Symfony            | Comment                                  |
|:--------------------:|:-------:|:------------------:|------------------------------------------|
| 2.0                  | >= 5.4  | >= 3.0             | Actual version                           |
| 1.0                  | >= 5.4  | >= 2.1 and <= 2.8  |                                          |

> NOTE! To upgrade your configuration, please read UPGRADE.md

### Add TinyMCE bundle as a dependency of your application via composer

```
$ php composer.phar require stfalcon/tinymce-bundle='X.Y'
```

### Add StfalconTinymceBundle to your application kernel.

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

### The bundle needs to copy the resources necessary to the web folder. You can use the command below:

```
$ php app/console assets:install web/
```

## Include in template

This bundle comes with an extension for Twig. This makes it very easy to include the TinyMCE Javascript into your pages. Add the tag below to the places where you want to use TinyMCE. It will output the complete Javascript, including `<script>` tags. Add it to the bottom of your page for optimized performance.

```twig
    {{ tinymce_init() }}
```

You can also override the default configuration by passing an option like this:

```twig
    {{ tinymce_init({'use_callback_tinymce_init': true, 'theme': {'simple': {'menubar': false}}}) }}
```

   or

```
    {{ tinymce_init({
        theme: {'simple':{'language': app.request.locale, 'height': 500 }},
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        autosave_ask_before_unload: false,
        asset_package_name: 'backend'})
    }}
```


***NEW !*** Added posibility to specify asset package [doc](http://symfony.com/doc/current/components/templating/helpers/assetshelper.html#multiple-packages) to generate proper js links, see above, parameter: asset_package_name


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
defined 'bbcode'). e.g.:

```php
<?php
    $builder->add('introtext', 'textarea', array(
        'attr' => array(
            'class' => 'tinymce',
            'data-theme' => 'bbcode' // Skip it if you want to use default theme
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
        selector: ".tinymce"
        language: %locale%
        theme:
            simple:
                theme: "modern"
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
        selector: ".tinymce"
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
            simple: ~
            # Advanced theme with almost all enabled plugins
            advanced:
                 plugins:
                     - "advlist autolink lists link image charmap print preview hr anchor pagebreak"
                     - "searchreplace wordcount visualblocks visualchars code fullscreen"
                     - "insertdatetime media nonbreaking save table contextmenu directionality"
                     - "emoticons template paste textcolor"
                 toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                 toolbar2: "print preview media | forecolor backcolor emoticons | stfalcon | example"
                 image_advtab: true
                 templates:
                     - {title: 'Test template 1', content: 'Test 1'}
                     - {title: 'Test template 2', content: 'Test 2'}
            # BBCode tag compatible theme (see http://www.bbcode.org/reference.php)
            bbcode:
                 plugins: ["bbcode, code, link, preview"]
                 menubar: false
                 toolbar1: "bold,italic,underline,undo,redo,link,unlink,removeformat,cleanup,code,preview"
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
                theme: "modern"
                ...
```

### Custom buttons

You can add some custom buttons to editor's toolbar (See: http://www.tinymce.com/tryit/button.php, http://www.tinymce.com/wiki.php/api4:method.tinymce.Editor.addButton)

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
                     ...
                 toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                 toolbar2: "print preview media | forecolor backcolor emoticons | stfalcon | hello_world"
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
                content_css: "asset[bundles/mybundle/css/tinymce-content.css]"
                ...
```

> NOTE! Read Official TinyMCE documentation for more details: http://www.tinymce.com/wiki.php/Configuration:content_css

## Init Event

As $(document).ready() in jQuery you can listen to the init event as well in Tinymce.

To do so you must edit your config and set `use_callback_tinymce_init` to true.

`app/config/config.yml`:

```yaml
    stfalcon_tinymce:
        ...
        use_callback_tinymce_init: true
        ...

```

And then create a javascript callback function named `callback_tinymce_init` as follow

```javascript

function callback_tinymce_init(editor) {
    // execute your best script ever
}

```

## How to init TinyMCE for dynamically loaded elements

To initialize TinyMCE for new loaded textareas you should just call `initTinyMCE()` function.

#### Example for Sonata Admin Bundle

```javascript
    jQuery(document).ready(function() {
        $('form').on('sonata.add_element', function(){
            initTinyMCE();
        });
    });
```
