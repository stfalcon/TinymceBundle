﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#jbimages_dlg.title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
	
	<link href="css/dialog.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="upload_in_progress" class="upload_infobar"><img src="img/spinner.gif" width="16" height="16" class="spinner" />{#jbimages_dlg.upload_in_progress}&hellip; <div id="upload_additional_info"></div></div>
<div id="upload_infobar" class="upload_infobar"></div>
<div id="upload_form_container"><form id="upl" name="upl" action="ci/index.php/upload/{#jbimages_dlg.lang_id}?sf_environment=<?php echo $_GET['sf_environment']; ?>" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="jbImagesDialog.inProgress();">
	<h1>{#jbimages_dlg.select_an_image}</h1>
	<p><input id="uploader" name="userfile" type="file" class="jbFilebox" onChange="document.upl.submit(); jbImagesDialog.inProgress();" /></p>
	<p><input type="submit" value="{#jbimages_dlg.upload}" class="jbButton" /></p>
	<p id="the_plugin_name"><a href="http://justboil.me/tinymce-images-plugin/" target="_blank" title="JustBoil.me Images - a TinyMCE Images Upload Plugin">JustBoil.me Images Plugin</a></p>
</form></div>
<iframe id="upload_target" name="upload_target" src="ci/index.php/blank"></iframe>
</body>
</html>
