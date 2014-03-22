<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Assets extends Controller {
    
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
            $body = file_get_contents($file);
            // Send the file content as the response
            $this->response->body($body);
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
}