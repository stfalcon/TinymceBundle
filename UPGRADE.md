# TinymceBundle UPGRADE FROM 0.2.1 to 0.3.0
=======================

When upgrading TinyMCE bundle from 0.2.1 to 0.3.0, you need to do the following changes in your configuration:


### Selector and theme

   Before:

```yaml
    // app/config/config.yml
    stfalcon_tinymce:
        ...
        textarea_class: "tinymce"
        theme:
            simple:
                mode: "textareas"
                theme: "advanced"
        ...

```

   After:

```yaml
    // app/config/config.yml
    stfalcon_tinymce:
        ...
        selector: ".tinymce" # with leading dot for a class or hash tag for ID of the textarea
        theme:
            simple:
                theme: "modern" # or just remove this line
        ...

```

> NOTE! `textarea_class` parameter has been removed since v.0.3.4. Please use `selector` one.

### Buttons and plugins

    Before

```yaml
    // app/config/config.yml
    stfalcon_tinymce:
        include_jquery: true
        tinymce_jquery: true
        ...
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
```

    After

```yaml
    // app/config/config.yml
    stfalcon_tinymce:
        include_jquery: true
        tinymce_jquery: true
        ...
        theme:
            # Simple theme: same as default theme
            simple: ~
            # Advanced theme with almost all enabled plugins
            advanced:
                 plugins:
                     - advlist autolink lists link image charmap print preview hr anchor pagebreak
                     - searchreplace wordcount visualblocks visualchars code fullscreen
                     - insertdatetime media nonbreaking save table contextmenu directionality
                     - emoticons template paste textcolor
                 toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                 toolbar2: "print preview media | forecolor backcolor emoticons | stfalcon | example"
                 image_advtab: true
                 templates:
                     - {title: 'Test template 1', content: 'Test 1'}
                     - {title: 'Test template 2', content: 'Test 2'}

```

> NOTE! Read Official TinyMCE documentation for more details: http://www.tinymce.com/wiki.php/Tutorial:Migration_guide_from_3.x
