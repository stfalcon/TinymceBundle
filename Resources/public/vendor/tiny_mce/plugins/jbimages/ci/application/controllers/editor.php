<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editor extends CI_Controller {
	
	/* Constructor */
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('jbimages','language'));
		
		if (is_allowed() === FALSE)
		{
			exit;
		}
		
		$this->config->load('uploader_settings', TRUE);
	}
	
	/* Helper */
	
	private function _lang_set($lang)
	{
		// We do say hello to any language set as lang_id in **_dlg.js
		/*$langs = array('russian','english');
		if (!in_array($lang, $langs))
		{
			$lang = 'english';
		}*/
		
		$this->config->set_item('language', $lang);
		$this->lang->load('jbstrings', $lang);
	}
	
	/* Default upload routine */
		
	public function upload($lang='english')
	{
		$this->_lang_set($lang);
		
		$conf['img_path'] = $this->config->item('img_path', 'uploader_settings');
		$conf['allow_resize'] = $this->config->item('allow_resize', 'uploader_settings');
		//$conf['latinize_name'] = $this->config->item('latinize_name', 'uploader_settings'); //not used
		
		$config['allowed_types'] = $this->config->item('allowed_types', 'uploader_settings');
		$config['max_size'] = $this->config->item('max_size', 'uploader_settings');
		$config['encrypt_name'] = $this->config->item('encrypt_name', 'uploader_settings');
		$config['overwrite'] = $this->config->item('overwrite', 'uploader_settings');
		$config['upload_path'] = $this->config->item('upload_path', 'uploader_settings');
		
		if (!$conf['allow_resize'])
		{
			$config['max_width'] = $this->config->item('max_width', 'uploader_settings');
			$config['max_height'] = $this->config->item('max_height', 'uploader_settings');
		}
		else
		{
			$conf['max_width'] = $this->config->item('max_width', 'uploader_settings');
			$conf['max_height'] = $this->config->item('max_height', 'uploader_settings');
			
			if ($conf['max_width'] == 0 and $conf['max_height'] == 0)
			{
				$conf['allow_resize'] = FALSE;
			}
		}
		
		$this->load->library('upload', $config);
		
		if ($this->upload->do_upload())
		{
			$result = $this->upload->data();
			
			if ($conf['allow_resize'] and $conf['max_width'] > 0 and $conf['max_height'] > 0 and (($result['image_width'] > $conf['max_width']) or ($result['image_height'] > $conf['max_height'])))
			{				
				$resizeParams = array
				(
					'source_image'=>$result['full_path'],
					'new_image'=>$result['full_path'],
					'width'=>$conf['max_width'],
					'height'=>$conf['max_height']
				);
				
				$this->load->library('image_lib', $resizeParams);
				$this->image_lib->resize();
			}
			
			
			/* //The old resize routine DEPRECATED
			
			if ($conf['allow_resize'] and (($result['image_width'] > $conf['max_width'] and $conf['max_width'] > 0) or ($result['image_height'] > $conf['max_height'] and $conf['max_height'] > 0)))
			{
				if ($conf['max_height'] == 0)
				{				
					$aspect_ratio = $result['image_width'] / $result['image_height'];
					$new_width = $conf['max_width'];
					$new_height = floor($new_width / $aspect_ratio);
				}
				elseif ($conf['max_width'] == 0)
				{				
					$aspect_ratio = $result['image_height'] / $result['image_width'];
					$new_height = $conf['max_height'];
					$new_width = floor($new_height / $aspect_ratio);
				}
				else
				{
					$new_width = $conf['max_width'];
					$new_height = $conf['max_height'];
				}
				
				$resizeParams = array
				(
					'source_image'=>$result['full_path'],
					'width'=>$new_width,
					'height'=>$new_height
				);
				$this->load->library('image_lib', $resizeParams);
				$this->image_lib->resize();
			}*/
			
			$result['result'] = "file_uploaded";
			$result['resultcode'] = 'ok';
			$result['file_name'] = $conf['img_path'] . '/' . $result['file_name'];
			$this->load->view('ajax_upload_result', $result);
		}
		else
		{
			$result['result'] = $this->upload->display_errors(' ', '<br />');
			$result['resultcode'] = 'failed';
			$this->load->view('ajax_upload_result', $result);
		}
	}
	
	/* Blank Page (default source for iframe) */
	
	public function blank($lang='english')
	{
		$this->_lang_set($lang);
		$this->load->view('blank');
	}
	
	public function index($lang='english')
	{
		$this->blank($lang);
	}
}

/* End of file editor.php */
/* Location: ./application/controllers/editor.php */
