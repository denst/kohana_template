<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Image {
    
    private $errors = array();
    
    public function set_banner($txt, $ext, $files)
    {
        try
        {
            $result = Amazons3::instance()->upload_file($files, $txt.'.'.$ext);
            return $result;
        }
        catch (Exception $ext)
        {
            return false;
        }
    }
    
    public function delete_banner($file)
    {
        try 
        {
            $result = Amazons3::instance()->delete_file($file);
            return $result;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }

    public function check_image($files, $form_name, $ad_zone_size)
    {
        $has_error = true;
        $valid_formats = array("jpg", "jpeg", "png", "gif");
        $name = $files[$form_name]['name'];
        $size = $files[$form_name]['size'];
        $ext = strtolower(pathinfo($files[$form_name]['name'], PATHINFO_EXTENSION));
        switch (true) 
        {
            case (empty($name)):
                $this->errors = "Please select the banner";
                break;
            case (! in_array($ext,$valid_formats)):
                $this->errors = "Invalid banner format";
                break;
            case (! empty($files[$form_name]['error'])):
                $this->errors = "Can't upload this banner";
                break;
            case ($size > Num::bytes('200K')):
                $this->errors = "Banner's max size is 200 KB";
                break;
            case (Image::factory($files[$form_name]['tmp_name'])->width != 
                    $ad_zone_size[0]):
                $this->errors = "Max banner width is ".$ad_zone_size[0];
                break;
            case (Image::factory($files[$form_name]['tmp_name'])->height != 
                    $ad_zone_size[1]):
                $this->errors = "Max banner height is ".$ad_zone_size[1];
                break;
            default:
                $has_error = false;
                break;
        }
        if(! $has_error)
            return true;
        else
            return false;
    }
    
    public function rewrite_banner($file_name)
    {
        try
        {
            $file_path = Kohana::$config->load('amazons3.file_path').$file_name;
            Amazons3::instance()->download_file($file_name, $file_path);
            $txt = Text::random('alpha');
            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $temp_file['tmp_name'] = $file_path;
            $new_banner =  $this->set_banner($txt, $ext, $temp_file);
            unlink($file_path);
            return $new_banner;
        }
        catch (Exception $ext)
        {
            return false;
        }
    }

    public function get_errors()
    {
        return $this->errors;
    }
}