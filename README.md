# TinymceBundle

Bundle for connecting WYSIWYG editor TinyMCE to your Symfony2 project.
By analogy with the bundle https://github.com/ihqs/WysiwygBundle

## Installation

Download the code by adding the git module or editing the deps file in the root project.

### Download via git submodule

    git submodule add git://github.com/stfalcon/TinymceBundle.git vendor/bundles/Stfalcon/Bundle/TinymceBundle

### Download by editing deps file

    [TinymceBundle]
        git=git://github.com/stfalcon/TinymceBundle.git
        target=/bundles/Stfalcon/Bundle/TinymceBundle


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
        include_jquery: true
        textarea_class: "tinymce"
        theme:
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

run the command

    php app/console assets:install web/

to copy the resources to the projects web directory"

On default, tinymce is enabled for all textarea on page, but if you want customize it, do it following:
 
Add class "tinymce" into textarea field to initialize TinyMCE.

    <textarea  class="tinymce"></textarea>

and add the parameter textarea_class to tinymce confgig, something like that:
	stfalcon_tinymce:
			...
		    textarea_class: "tinymce"
			...
	
If you are using FormBuilder, use an array to add the class, you can also use the 'theme' option to change the
used theme to something other than 'simple' (i.e. on of the other defined themes in your config - the example above
defined 'advanced' and 'medium').  e.g.:

        $builder->add('introtext', 'textarea', array(
            'attr'  => array('class' => 'tinymce')
            'theme' => 'medium',
        ))

Add script to your templates/layout at the bottom of your page (for faster page display).

    {{ tinymce_init() }}

## Localization

You can change language of your tiny_mce by adding language selector into theme, something like

    stfalcon_tinymce:
        include_jquery: true
        theme:
            advanced:
                language: ru
