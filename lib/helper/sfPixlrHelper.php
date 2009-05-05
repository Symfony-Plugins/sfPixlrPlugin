<?php

/**
 * Returns a routed URL to pixlr.com image editor.
 * Image URL is sent by GET method to http://www.pixlr.com/editor/
 * Options:
 *  - save_to: path to folder on server to which file will be saved.
 *    * this parameter is appended to app_pixlr_upload_dir or sf_upload_dir
 *    * if TRUE, default symfony upload folder is used
 *    * if FALSE or not specified file will not be automatically saved
 *  - title: default name for new file (default: original file name)
 *  - referrer: displayed in save dialog (default: HTTP_HOST)
 *  - exit: where to send the visitor when he/she clicks on the File->Exit
 *  - loc: pixlr localization (default: en)
 *
 * @param string $absolute_url  absolute url to image, must be accessible over internet
 * @param string $target        url that will browser be redirected to on save
 * @param array $options        options
 * @return string
 */
function pixlr_get_url($absolute_url, $target, $options=array())
{
  
  $query_vars = array("image"=>$absolute_url, "target"=>$target);
  
  $query_vars['title'] = isset($options['title']) ? $options['title'] : basename($absolute_url);
  $query_vars['referrer'] = isset($options['referrer']) ? $options['referrer'] :$_SERVER['HTTP_HOST'];
  $query_vars['exit'] = isset($options['exit']) ? $options['exit'] : sfContext::getInstance()->getRequest()->getUri();
  $query_vars['loc'] = isset($options['loc']) ? $options['loc'] : 'en';
  $pixlr_vars = $query_vars;

  if(!isset($options['skip_default']) || $options['skip_default']==FALSE)
  {
    $query_vars['save_to'] = isset($options['save_to']) ? $options['save_to'] : null;
    $query_vars['target_vars'] = isset($options['target_vars']) ? $options['target_vars'] : true;
    $pixlr_vars['target'] = url_for("@sf_pixlr_save?options=".base64_encode(serialize($query_vars)), true);
  }

  return sfPixlrTools::getPixlrAppUrl(isset($options['app'])?$options['app']:null)."?".http_build_query($pixlr_vars);
 
}


/**
 * Returns a routed URL to pixlr.com image editor.
 * Image is sent by POST method via sfPixlr/post action.
 * Options:
 *  - save_to: path to folder on server to which file will be saved.
 *    * this parameter is appended to app_pixlr_upload_dir or sf_upload_dir
 *    * if TRUE, default symfony upload folder is used
 *    * if FALSE or not specified file will not be automatically saved
 *  - title: default name for new file (default: original file name)
 *  - referrer: displayed in save dialog (default: HTTP_HOST)
 *  - exit: where to send the visitor when he/she clicks on the File->Exit
 *  - loc: pixlr localization (default: en)
 *
 * @param string $file         path to file on server
 * @param string $target       url that will browser be redirected to on save
 * @param array $options       options
 * @return string
 */
function pixlr_post_url($file, $target, $options=array())
{
	$query_vars = array("file"=>$file, "target"=>$target);
	foreach(array('referrer','title','exit','loc','app','target_vars','save_to') as $key)
	{
		if(isset($options[$key]))
	  {
	    $query_vars[$key] = $options[$key];
	  }
	}

  if(!isset($options['skip_default']) || $options['skip_default']==FALSE)
  {
    $query_vars['target'] = url_for("@sf_pixlr_save?options=".base64_encode(serialize($query_vars)), true);
  }

  return url_for("@sf_pixlr_post?".http_build_query($query_vars, '', '&'));
}


/**
 * Returns a routed URL to pixlr.com image editor.
 * Image URL is sent by GET method to http://www.pixlr.com/editor/
 * Options:
 *  - save_to: path to folder on server to which file will be saved.
 *    * this parameter is appended to app_pixlr_upload_dir or sf_upload_dir
 *    * if TRUE, default symfony upload folder is used
 *    * if FALSE or not specified file will not be automatically saved
 *  - title: default name for new file (default: original file name)
 *  - referrer: displayed in save dialog (default: HTTP_HOST)
 *  - exit: where to send the visitor when he/she clicks on the File->Exit
 *  - loc: pixlr localization (default: en)
 *
 * @param string $absolute_url  absolute url to image, must be accessible over internet
 * @param string $target        url that will browser be redirected to on save
 * @param array $options        options
 * @return string
 */
function pixlr_editor_get_url($absolute_url, $target, $options=array())
{
  return pixlr_get_url($absolute_url, $target, array_merge($options, array("app"=>"editor")));
}


/**
 * Returns a routed URL to pixlr.com image editor.
 * Image is sent by POST method via sfPixlr/post action.
 * Options:
 *  - save_to: path to folder on server to which file will be saved.
 *    * this parameter is appended to app_pixlr_upload_dir or sf_upload_dir
 *    * if TRUE, default symfony upload folder is used
 *    * if FALSE or not specified file will not be automatically saved
 *  - title: default name for new file (default: original file name)
 *  - referrer: displayed in save dialog (default: HTTP_HOST)
 *  - exit: where to send the visitor when he/she clicks on the File->Exit
 *  - loc: pixlr localization (default: en)
 *
 * @param string $file         path to file on server
 * @param string $target       url that will browser be redirected to on save
 * @param array $options       options
 * @return string
 */
function pixlr_editor_post_url($file, $target, $options=array())
{
  return pixlr_post_url($file, $target, array_merge($options, array("app"=>"editor")));
}



/**
 * Returns a routed URL to pixlr.com image editor.
 * Image URL is sent by GET method to http://www.pixlr.com/editor/
 * Options:
 *  - save_to: path to folder on server to which file will be saved.
 *    * this parameter is appended to app_pixlr_upload_dir or sf_upload_dir
 *    * if TRUE, default symfony upload folder is used
 *    * if FALSE or not specified file will not be automatically saved
 *  - title: default name for new file (default: original file name)
 *  - referrer: displayed in save dialog (default: HTTP_HOST)
 *  - exit: where to send the visitor when he/she clicks on the File->Exit
 *  - loc: pixlr localization (default: en)
 *
 * @param string $absolute_url  absolute url to image, must be accessible over internet
 * @param string $target        url that will browser be redirected to on save
 * @param array $options        options
 * @return string
 */
function pixlr_express_get_url($absolute_url, $target, $options=array())
{
  return pixlr_get_url($absolute_url, $target, array_merge($options, array("app"=>"express")));
}


/**
 * Returns a routed URL to pixlr.com image editor.
 * Image is sent by POST method via sfPixlr/post action.
 * Options:
 *  - save_to: path to folder on server to which file will be saved.
 *    * this parameter is appended to app_pixlr_upload_dir or sf_upload_dir
 *    * if TRUE, default symfony upload folder is used
 *    * if FALSE or not specified file will not be automatically saved
 *  - title: default name for new file (default: original file name)
 *  - referrer: displayed in save dialog (default: HTTP_HOST)
 *  - exit: where to send the visitor when he/she clicks on the File->Exit
 *  - loc: pixlr localization (default: en)
 *
 * @param string $file         path to file on server
 * @param string $target       url that will browser be redirected to on save
 * @param array $options       options
 * @return string
 */
function pixlr_express_post_url($file, $target, $options=array())
{
  return pixlr_post_url($file, $target, array_merge($options, array("app"=>"express")));
}


