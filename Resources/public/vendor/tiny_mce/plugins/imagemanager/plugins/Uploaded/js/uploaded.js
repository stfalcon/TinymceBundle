(function($){
	var man = window.FileManager || window.ImageManager;

	man.addSpecialFolder({title : '{#uploaded.special_folder_title}', path : 'uploaded:///', type : 'uploaded'});

	$().bind('filelist:changed', function() {
		if (man.path.indexOf('uploaded://') != -1) {
			$(man.tools).each(function(i, v) {
				man.setDisabled(v, 1);
			});

			$(['insert', 'download', 'view']).each(function(i, v) {
				man.setDisabled(v, 0);
			});
		}
	});
})(jQuery);
