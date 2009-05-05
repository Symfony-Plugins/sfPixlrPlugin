<?php

/**
 * Base actions for the sfPixlrPlugin sfPixlr module.
 * 
 * @package     sfPixlrPlugin
 * @subpackage  sfPixlr
 * @author      Dragan Bosnjak
 * @version     SVN: $Id: BasesfPixlrActions.class.php 12628 2008-11-04 14:43:36Z Kris.Wallsmith $
 */
abstract class BasesfPixlrActions extends sfActions
{

  public function executeSave(sfWebRequest $request)
  {
  	$options = base64_decode($request->getParameter("options"));
  	$options = unserialize($options);

    $options = array_merge(array(
      'save_to' => null,
      'target_vars' => true,
    ), $options);
   
    $state = $request->getParameter("state");
    $url = $request->getParameter("image");
    $extension = $request->getParameter("type");
   
    if($state!="fetched")
    {
      throw new sfException("Unknown pixlr state: {$state}");
    }
 
    if(substr($url, 0, strlen(sfPixlrTools::PIXLR_URL))!=sfPixlrTools::PIXLR_URL)
    {
      throw new sfException("Unrecognized url: {$url}");
    }

    if($options['target_vars'])
    {
    	$target_parts = explode("#", $options['target'], 2);
    	$options['target'] = $target_parts[0].(strpos($options['target'], "?")===FALSE?"?":"&").http_build_query($request->getGetParameters(), '', '&');
    	if(isset($target_parts[1]))
    	{
    		$options['target'] .= "#".$target_parts[1];
    	}
    }

    if($options['save_to'])
    {

    	$full_path = sfConfig::get('app_pixlr_upload_dir', sfConfig::get('sf_upload_dir'));
    	if(is_string($options['save_to']))
    	{
        $options['save_to'] = preg_replace('/(^|[\/\\\\]??)([\\.\\s]+)($|[\/\\\\])/', '/', $options['save_to']);
    		$full_path .= "/".$options['save_to'];
    	}
    	$name = $this->getUniqueFilename($request->getParameter("title").".".$extension, $full_path);

      $this->copyFromUrl($url, "{$full_path}/{$name}");
    }
    
    $this->redirect($options['target']);

    
  }

  public function executePost(sfWebRequest $request)
  {
    $file = $request->getParameter('file');

    if(!file_exists($file) || !is_file($file))
    {
    	throw new sfException("File \"{$file}\" not found");
    }
    
    $pixlr_vars = array();
    $pixlr_vars['title'] = $request->getParameter('title', basename($file));
    $pixlr_vars['referrer'] = $request->getParameter('referrer', $_SERVER['HTTP_HOST']);
    $pixlr_vars['exit'] = $request->getParameter('exit', sfContext::getInstance()->getRequest()->getReferer());
    $pixlr_vars['loc'] = $request->getParameter('loc', 'en');
    $pixlr_vars['target'] = urldecode($request->getParameter('target'));
    
    $options = array();
    
    $options['app'] = sfPixlrTools::getPixlrApp($request->getParameter('app', null));
  
    $post = $this->buildRequest($file, $pixlr_vars, $options);

    $f = fsockopen(sfPixlrTools::PIXLR_HOST, 80);
    fputs($f, $post);
    $response = "";
    while (!feof($f))
    {
      $response .= fread($f, 1024);
    }

    $location = array();
 
    if( 1 != preg_match('/Location: ([^\\r\\n]+)/', $response, $location) )
    {
      throw new sfException("Unexpected response headers.");
    }
    
    $this->redirect($location[1]);

  }

  private function buildRequest($file, $pixlr_vars, $options)
  {
  	
  	$file_data = file_get_contents($file);
  	$path_info = pathinfo($file);
  	$content_type = "image/".$path_info['extension'];

    $boundary = "---------------------------".md5(uniqid());
      
    // Build the header
    $header = "POST ".sfPixlrTools::getPixlrAppUrl($options['app'])." HTTP/1.0\r\n";
    $header .= "Host: ".sfPixlrTools::PIXLR_HOST."\r\n";
    $header .= "Content-Type: multipart/form-data; boundary=$boundary\r\n";
    
    $data = "";
    
    foreach($pixlr_vars AS $index => $value){
        $data .="--$boundary\r\n";
        $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
        $data .= "\r\n".$value."\r\n";
        //$data .="--$boundary\r\n";
    }

    $data .= "--$boundary\r\n";

    $data.="Content-Disposition: file; name=\"image\"; filename=\"{$path_info['basename']}\"\r\n";
    $data .= "Content-Type: $content_type\r\n\r\n";
    $data .= "".$file_data."\r\n";
    
    $data .="--$boundary\r\n";

    $data .="--$boundary--\r\n";
    

    $header .= "Content-length: " . strlen($data) . "\r\n\r\n"; 
        
    return $header.$data;
    
  }

  private function getUniqueFilename($name, $full_path)
  {
    if(!file_exists("{$full_path}/{$name}"))
    {
      return $name;
    }
    $pathinfo = pathinfo($name);
    return $pathinfo['filename']."-".md5(uniqid()).".".$pathinfo['extension'];
  }

  private function copyFromUrl($url, $full_path)
  {
    $file = file_get_contents($url);
    if($file===FALSE)
    {
      throw new sfException("File {$url} could not be retrieved.");
    }
    
    $success = file_put_contents($full_path, $file);
    if($success===FALSE)
    {
      throw new sfException("File {$full_path} could not be saved.");
    }
    
    $permission = sfConfig::get('app_pixlr_upload_permissions');
    if($permission===null)
    {
      $permission = fileperms(sfConfig::get('app_pixlr_upload_dir', sfConfig::get('sf_upload_dir')));
    }
    
    chmod($full_path, $permission);

    return $success;
  }
  
  
}
