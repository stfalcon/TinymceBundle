<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Загрузка файла</title>
<link href="css/upload.css" rel="stylesheet" type="text/css"  />
</head>
<body>

<form id="uploadForm" method="post" action="">
                <div class="com-menu">Загрузка файла</div>
                <div class="com-block">

    <label for="file">закачать локальный файл</label>
	<input id="file" name="file" type="file" size="20" class="file required" />
    <label for="name0">закачать картинку по url</label>
    <input id="name0" name="name0" type="text" size="42" maxlength="255" class="text required" />

    <div class="line-bg"></div>
                    </div>
    <div class="prev-post">
        <input type="submit" name="upload" value="загрузить" class="submit default" />
	    <input type="button" id="cancel" name="cancel" value="отмена" class="button cancel" />
    </div>
</form>

</body>
</html>