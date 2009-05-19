<?php

class sfPixlrTools
{
  const PIXLR_HOST = "www.pixlr.com";
  const PIXLR_URL = "http://www.pixlr.com/";

  static protected $pixlr_apps = array("editor", "express");
  
  /**
   * Returns array with Pixlr application names.
   */
  static public function getPixlrApps()
  {
    return sfPixlrTools::$pixlr_apps;
  }

  /**
   * If $app is valid Pixlr application, returns $app,
   * Otherwise returns default Pixlr application.
   */
  static public function getPixlrApp($app)
  {
    if(sfPixlrTools::isPixlrApp($app))
    {
      return $app;
    }
    else
    {
      return sfConfig::get('app_pixlr_default_app');
    }
  }
  
  /**
   * Check if $app is valid Pixlr application.
   */
  static public function isPixlrApp($app)
  {
    return in_array($app, sfPixlrTools::getPixlrApps());
  }
  
  /**
   * Returns URL for Pixlr application $app, or if $app is
   * not valid application, URL to default Pixlr application.
   */
  static public function getPixlrAppUrl($app)
  {
    return "http://www.pixlr.com/".sfPixlrTools::getPixlrApp($app)."/";
  }

}

?>