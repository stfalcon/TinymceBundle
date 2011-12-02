(function($){
	window.UploadDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args;

			t.args = args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			t.fileListTpl = $.templateFromScript('#filelist_item_template');

			$('.uploadtype').html($.translate('{#upload.basic_upload}', 0, {a : '<a id="singleupload" href="#basic">', '/a' : '</a>'}));
			$('#createin').html(args.visual_path);
			$('form input[name=path]').val(args.path);
			$('form input[name=file0]').change(function(e) {
				$('form input[name=name0]').val(t.cleanName(/([^\/\\]+)$/.exec(e.target.value)[0].replace(/\.[^\.]+$/, '')));
			});

			$('form').submit(function() {
				$.WindowManager.showProgress({message : $.translate('{#upload.progress}')}); 
			});

			if (document.location.hostname != document.domain)
				$('form input[name=domain]').val(document.domain);

			t.path = args.path;

			$('#singleupload').click(function(e) {
				$('#multiupload_view').hide();
				$('#singleupload_view').show();
			});

			RPC.exec('im.getConfig', {path : args.path}, function(data) {
				var config = data.result, maxSize, upExt, fsExt, outExt = [], i, x, found;

				maxSize = config['upload.maxsize'];
				fsExt = config['filesystem.extensions'].split(',');
				upExt = config['upload.extensions'].split(',');
				t.debug = config['general.debug'] == "true";
				t.shouldCleanNames = config['filesystem.clean_names'] == "true";
				t.chunkSize = config['upload.chunk_size'] || '1mb';

				$('#content').show();

				// Disabled upload
				if (config['upload.multiple_upload'] != "true") {
					$('#multiupload_view').hide();
					$('#singleupload_view').show();
				}

				maxSize = maxSize.replace(/\s+/, '');
				maxSize = maxSize.replace(/([0-9]+)/g, '$1 ');

				if (upExt[0] == '*')
					upExt = fsExt;

				if (fsExt[0] == '*')
					fsExt = upExt;

				for (i = 0; i < upExt.length; i++) {
					upExt[i] = $.trim(upExt[i].toLowerCase());
					found = false;

					for (x = 0; x < fsExt.length; x++) {
						fsExt[x] = $.trim(fsExt[x]).toLowerCase();

						if (upExt[i] == fsExt[x]) {
							found = true;
							break;
						}
					}

					if (found)
						outExt.push(upExt[i]);
				}

				t.validExtensions = outExt;
				t.maxSize = maxSize;

				$('#facts').html($.templateFromScript('#facts_template'), {extensions : outExt.join(', '), maxsize : maxSize, path : args.visual_path});

				if (config['upload.multiple_upload'] == "true")
					t.initPlupload();
			});

			$('#cancel').click(function() {t.currentWin.close();});
		},

		cleanName : function(s) {
			if (this.shouldCleanNames)
				s = $.cleanName(s);

			return s;
		},

		handleSingleUploadResponse : function(data) {
			var t = this, args = t.currentWin.getArgs();

			$.WindowManager.hideProgress();

			if (!RPC.handleError({message : '{#error.upload_failed}', visual_path : t.args.visual_path, response : data})) {
				var res = RPC.toArray(data.result);

				$.WindowManager.info($.translate('{#message.upload_ok}'));
				$('#file0, #name0').val('');

				t.insertFiles([res[0].file]);
			}
		},

		initPlupload : function() {
			var initialDone, uploader, self = this, startTime;

			uploader = new plupload.Uploader({
				runtimes : 'gears,silverlight,flash',
				url : '../../stream/index.php?cmd=im.upload&path=' + escape(self.path),
				browse_button : 'addshim',
				container : 'multiupload_view',
				chunk_size : self.chunkSize,
				max_size : self.maxSize,
				//shim_bgcolor : 'red',
				flash_swf_url : 'js/plupload/plupload.flash.swf',
				silverlight_xap_url : '../../stream/index.php?theme=im&package=static_files&file=multiupload_xap',
				filters : [
					{title : "Files", extensions : self.validExtensions.join(',')}
				]
			});

			function calc() {
				if (!uploader.files.length) {
					$('#selectview').css('top', 0);
					$('#selectview').show();
					$('#fileblock').css({position : 'relative', top : 400});

					moveShim('#add');
					uploader.refresh();
					initialDone = false;

					return;
				}

				$('#progressinfo').html($.translate('{#upload.progressinfo}', 1, {
					loaded : plupload.formatSize(uploader.total.loaded),
					total : plupload.formatSize(uploader.total.size),
					speed : plupload.formatSize(uploader.total.bytesPerSec)
				}));

				$('#progressbar').css('width', uploader.total.percent + '%');

				$('#stats').html($.translate('{#upload.statusrow}', 1, {files : uploader.files.length, size : plupload.formatSize(uploader.total.size)}));
			};

			$('#multiupload_view').show();

			uploader.bind('Init', function(up, res) {
				var addOffs, viewOffs;

				$('#singleupload_view').hide();
				$('#add').removeClass('hidden');

				// Reposition shim ontop of add
				moveShim('#add');
				$('#add').click(function(e) {
					e.preventDefault();
				});

				$('div.headline').attr('title', res.runtime);
			});

			uploader.bind('Error', function(uploader, err) {
				if (err.code == plupload.INIT_ERROR) {
					$('#multiupload_view').hide();
					$('#singleupload_view').show();
					return;
				}
			});

			uploader.init();

			$('#abortupload').click(function() {
				uploader.stop();
			});

			uploader.bind('StateChanged', function() {
				var fileList = [];

				if (uploader.state == plupload.STOPPED) {
					$('#abortupload').hide();

					$(uploader.files).each(function(i, file) {
						if (file.status == plupload.DONE)
							fileList.push(self.path + '/' + file.name);
					});

					self.insertFiles(fileList, function() {
						// All files uploaded 100% ok
						if (!uploader.total.failed)
							self.currentWin.close();
					});
				}
			});

			function moveShim(shim) {
				// Reposition shim ontop of addmore
				var addOffs = $(shim).offset();
				var viewOffs = $('#multiupload_view').offset();

				$('#addshim').css({
					top: addOffs.top - viewOffs.top,
					left: addOffs.left - viewOffs.left,
					width: $(shim).width(),
					height: $(shim).height()
				});
			};

			uploader.bind('FilesAdded', function(uploader, files) {
				if (!files.length)
					return;

				// Run animation once
				if (!initialDone) {
					$('#selectview').animate({
						top: '-150px'
					}, 1000);

					$('#fileblock').animate({
						top:'-60px'
					}, 1000, 'linear', function() {
						$('#fileblock').css('position', 'static');
						$('#selectview').hide();

						// Reposition shim ontop of addmore
						moveShim('#addmore');
						uploader.refresh();
					});

					initialDone = 1;
				}

				// Update file list
				$(files).each(function(i, file) {
					file.name = self.cleanName(file.name);

					$('#files').show();
					$('#files tbody').append(self.fileListTpl, {id : file.id, name : file.name, size : file.size});

					// Add remove handler
					$('#' + file.id + ' a.remove').click(function(e) {
						$('#' + file.id).remove();
						uploader.removeFile(uploader.getFile(file.id));
						calc();

						e.preventDefault();
						return false;
					});

					// Add rename handler
					$('#' + file.id + ' a.rename').click(function(e) {
						var a = $(e.target), inp, parts;

						if (!a.hasClass('disabled')) {
							parts = /^(.+)(\.[^\.]+)$/.exec(file.name);
							a.hide();
							$(e.target).parent().append('<input id="rename" type="text" class="text" />');
							inp = $('#rename').val(parts[1]);
							self.renameEnabled = 1;

							inp.focus().blur(function() {
								self.endRename();
							}).keydown(function(e) {
								var c = e.keyCode;

								if (c == 13 || c == 27) {
									if (c == 13) {
										file.name = self.cleanName(inp.val()) + parts[2];
										a.html(file.name);
									}

									self.endRename();
								}
							});
						}

						e.preventDefault();
						return false;
					});
				});

				moveShim('#addmore');
				uploader.refresh();
			});

			uploader.bind('QueueChanged', function(uploader) {
				calc();

				moveShim('#addmore');
				uploader.refresh();
			});

			uploader.bind('UploadProgress', function(uploader, file) {
				if (file.status != plupload.FAILED) {
					if (!file.scroll) {
						$('#filelist').scrollTo($('#' + file.id), 50);
						file.scroll = 1;
					}

					$('#' + file.id + ' td.status').html(Math.round(file.loaded / file.size * 100.0) + '%');
					calc();
				}
			});

			uploader.bind('Error', function(uploader, err) {
				if (err.file) {
					calc(uploader);
					$('#' + err.file.id).addClass('failed');
					$('#' + err.file.id + ' td.status').html(err.message);
				}
			});

			uploader.bind('ChunkUploaded', function(uploader, file, res) {
				var res = $.parseJSON(res.response), data = RPC.toArray(res.result);

				if (data[0]["status"] != 'OK') {
					uploader.trigger('Error', {
						code : 100,
						message : $.translate(data[0]["message"]),
						file : file
					});
				}
			});

			uploader.bind('FileUploaded', function(uploader, file, res) {
				var res = $.parseJSON(res.response), data = RPC.toArray(res.result);

				if (data[0]["status"] != 'OK') {
					uploader.trigger('Error', {
						code : 100,
						message : $.translate(data[0]["message"]),
						file : file
					});
				}
			});

			$('#uploadstart').click(function(e) {
				$('#uploadstart').parent().hide();
				$('#status').show();
				$('#statsrow').hide();
				$('#files .status').html('-');
				$('#files .fname a').addClass('disabled');

				startTime = new Date().getTime();
				uploader.start();

				e.preventDefault();
				return false;
			});
		},

		insertFiles : function(pa, cb) {
			var s = this.currentWin.getArgs();

			// Insert file
			if (s.onupload) {
				RPC.insertFiles({
					relative_urls : s.relative_urls,
					document_base_url : s.document_base_url,
					default_base_url : s.default_base_url,
					no_host : s.remove_script_host || s.no_host,
					paths : pa,
					insert_filter : s.insert_filter,
					oninsert : function(o) {
						$.restoreTinySelection();
						s.onupload(o);

						if (cb)
							cb();
					}
				});
			}
		},

		isDemo : function() {
			if (this.currentWin.getArgs().is_demo) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		},

		endRename : function() {
			if (this.renameEnabled) {
				$('#files input').remove();
				$('#files a').show();
				this.renameEnabled = 0;
			}
		}
	};

	// JSON handler
	window.handleJSON = function(data) {
		window.focus();
		UploadDialog.handleSingleUploadResponse(data);
	};

	$(function(e) {
		UploadDialog.init();
	});
})(jQuery);
