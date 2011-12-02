
Installing MCFileManager & MCImageManager for Wordpress 2.5+
------------------------------------------------------------

The procedure to install MCFilemanager and MCImageManager are exactly the same, this readme will assume you have MCImageManager.


1. Download the latest version of the MCImageManager using your account at Moxiecode.com.

2. Unzip the contents of the zipfile into this folder:

/wp-includes/js/tinymce/plugins/

The folder structure should look like this since a folder is included in the file:

/wp-includes/js/tinymce/plugins/imagemanager

3. Edit /wp-includes/js/tinymce/plugins/imagemanager/config.php with notepad or any other text editor (not Office).

4. Find this line around the middle of the config file:

$mcImageManagerConfig['authenticator'] = "BaseAuthenticator";

Change it to:

$mcImageManagerConfig['authenticator'] = "SessionAuthenticator";

5. Save and close the file.

6. Download the Wordpress package by logging into your account at www.moxiecode.com/shop.

7. Unzip the contents of the zipfile to /wp-content/plugins/

Folder structure should look like this:

/wp-content/plugins/mce/

The only files in that folder should be mce.php and this readme file.

8. Login to Wordpress as admin, activate the plugin in the plugins interface.

9. You are done. Check for 2 additional icons in the top right corner of the editor button toolbar.