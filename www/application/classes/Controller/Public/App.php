<?php // defined('SYSPATH') or die('No direct script access.');

class Controller_Public_App extends Controller {
        
        private $styles;
        private $scripts;
	/**
	 * @var string Filename of the template file.
	 */
	public $template = 'template/index';

	/**
	 * @var boolean Whether the template file should be rendered automatically.
	 * 
	 * If set, then the template view set above will be created before the controller action begins.
	 * You then need to just set $this->template->content to your content, without needing to worry about the containing template.
	 *
	 **/
	public $auto_render = TRUE;

	/**
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified
	 *
	 * Can be set to a string or an array, for example array('login', 'admin') or 'login'
	 */
	public $auth_required = FALSE;

	/** Controls access for separate actions
	 * 
	 * Examples:
	 * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	 * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	 */
	public $secure_actions = FALSE;

        protected $session;

	/**
	 * The before() method is called before your controller action.
	 * In our template controller we override this method so that we can
	 * set up default values. These variables are then available to our
	 * controllers if they need to be modified.
	 *
	 * @return  void
	 */
        public function before()
        {
            if ($this->auto_render)
            {
                // only load the template if the template has not been set..
                $this->template = View::factory($this->template);
                // Initialize empty values
                // Page title
                // Page content
                $this->template->content = '';
                
//                $this->template->site_name = Settings::instance()->get_setting('site_name');
                // Styles in header
                $this->template->styles = array();
                // Scripts in header
                $this->template->scripts = array();
                // ControllerName will contain the name of the Controller in the Template
                $this->template->controllerName = $this->request->controller();
                // ActionName will contain the name of the Action in the Template
                $this->template->actionName = $this->request->action();
                
                // next, it is expected that $this->template->content is set e.g. by rendering a view into it.
//                $this->template->menus = $this->set_menus();
//                $this->template->settings = Settings::instance()->get_settings();
//                $this->template->title = Text::ucfirst($this->request->controller()).' - '.Text::ucfirst(Settings::instance()->get_setting('site_name'));
            }
        }
        
	public function after()
	{
            if ($this->auto_render === TRUE)
            {
                $this->template->scripts = $this->get_media('js');
                $this->template->styles = $this->get_media('css');
                
                $this->template->content->form = new Helper_Appform();
                if(isset($this->template->content->errors))
                {
                    $this->template->content->form->post = $this->template->content->post;
                    $this->template->content->form->errors = $this->template->content->errors;
                }
                
                $this->response->body($this->template);
            }
            parent::after();
	}

        public function action_media()
	{
		// prevent auto render
		$this->auto_render = FALSE;
		// Generate and check the ETag for this file
		//		$this->request->check_cache(sha1($this->request->uri));
		// Get the file path from the request
		$file = Request::current()->param('file');
		$dir = Request::current()->param('dir');
		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		// Remove the extension from the filename
		$file = substr($file, 0, - ( strlen($ext) + 1 ));
		$file = Kohana::find_file('assets', $dir . '/' . $file, $ext);
		if ($file)
		{
                    // Send the file content as the response
                    $this->response->body(file_get_contents($file));
		}
		else
		{
                    // Return a 404 status
                    $this->response->status(404);
		}
		// Set the proper headers to allow caching
		$this->response->headers('Content-Type', File::mime_by_ext($ext));
		$this->response->headers('Content-Length', (string) filesize($file));
		$this->response->headers('Last-Modified', date('r', filemtime($file)));
	}
        
        public function page_title($title)
        {
            $this->template->title = Text::ucfirst($title).' - '.Text::ucfirst(Settings::instance()->get_setting('site_name'));
        }
        
        private function get_media($type)
        {
            $result = array();
            $part_path = Kohana::$config->load('minify.path.media');
            if($type == 'css')
                $media_array = 
                    array_keys($this->get_media_files(Kohana::$config->load('minify.path.css'),
                            'css'));
            else
                $media_array =      
                    $this->get_media_files(Kohana::$config->load('minify.path.js'), 'js');
            $min_media = Minify::factory($type)->minify($media_array);
            foreach ($min_media as $key => $value)
            {
                if(substr_count($value, $part_path))
                    $value = str_replace ($part_path, 'media/', $value);
                if($type == 'css')
                    $result[$value] = 'screen';
                else
                    $result[] = $value;
            }
            return $result;
        }
        
        private function get_media_files($path, $type)
        {
            $result = array();
            if(($objs = glob($path."/*")))
            {
               foreach($objs as $obj) 
               {
                   if(is_file($obj))
                   {
                       if($type == 'css')
                            $result['css/'.pathinfo($obj, PATHINFO_FILENAME).'.css'] = 'screen';
                       elseif($type == 'js')
                            $result[] = 'js/'.pathinfo($obj, PATHINFO_FILENAME).'.js';
                   }
               }
            }
            return $result;
        }
}