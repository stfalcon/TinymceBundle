<? foreach ($files as $name=>$fileinfo): ?>
	<? if (strpos($name, 't_') === 0): ?>
		<img src="<? echo $img_path ?>/<? echo $name ?>" width="25" height="25" alt="<? echo $name ?>" />
	<? endif; ?>
<? endforeach; ?>