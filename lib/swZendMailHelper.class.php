<?php

/*
 * This file is part of the swZendMailPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    swZendMailPlugin
 * @author     Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * @version    SVN: $Id$
 */
class swZendMailHelper
{
    
  /**
   * Listen to the configuration.method_not_found to extends 
   * the configuration object
   *
   * @param sfEvent $event
   */
  static function configurationMethodNotFound(sfEvent $event)
  {
    $params = $event->getParameters();
    
    switch($params['method'])
    {
      case 'registerZend':
        self::registerZend($event);
        break;
    }
  }
  
  /**
   * Listen to the component.method_not_found to extends 
   * the configuration object
   *
   * @param sfEvent $event
   */
  static function componentMethodNotFound(sfEvent $event)
  {
    $params = $event->getParameters();
    
    switch($params['method'])
    {
      case 'sendEmail':
      case 'sendMail':
        $event->setReturnValue(self::sendMailFromEvent($event));
        break;
    }

  }
  
  static private $zendLoaded = false;
  
  /**
   * Register zend
   *
   * @param sfEvent $event
   */
  static function registerZend(sfEvent $event)
  {
    $event->setProcessed(true);
    
    if (self::$zendLoaded)
    {
    
      return;
    }
    
    if(sfConfig::get('app_swToolbox_register_zend', true))
    {
      set_include_path(sfConfig::get('sf_lib_dir').'/vendor'.PATH_SEPARATOR.get_include_path());
    }

    if(!sfAutoload::getInstance()->autoload('Zend_Loader'))
    {
      throw new LogicException('Please install Zend Framework Library inside : '.sfConfig::get('sf_lib_dir').'/vendor');
    }
    
    self::$zendLoaded = true;
  }
  
  static public function sendMail($moduleName, $actionName, $vars)
  {
    $config = sfConfig::get('app_swToolbox_mail');
    $context = sfContext::getInstance();
        
    // 2. REGISTER ZEND CLASS
    $context->getConfiguration()->registerZend();
    
    // 3. CREATE THE ACTION
    $action = $context->getController()->getAction($moduleName, $actionName);
    
    // check for a module config.php
    $moduleConfig = sfConfig::get('sf_app_module_dir').'/'.$moduleName.'/config/config.php';
    if (is_readable($moduleConfig))
    {
      require_once($moduleConfig);
    }
    
    // 4. EXECUTE THE ACTION
    $action->getVarHolder()->add($vars);
    if($action->execute($context->getRequest() == sfView::NONE))
    {
    	// someone has cancalled the email rendering
    	return sfView::NONE;
    }

    // 5. RENDER THE MAIL
    $view = new swMailView($context, $moduleName, $actionName, 'swMailView');

    foreach($action->getVarHolder()->getAll() as $name => $value)
    {
      $view->setAttribute($name, $value);
    }
    
    // define decorator
    if($config['decorator']['enabled'])
    {
      $view->setDecorator(true);
      $view->setDecoratorDirectory($config['decorator']['directory']); 
    }
    else
    {
      $view->setDecorator(false);
    }
    
    // text version
    try 
    {
      $template = $actionName.'Success.text.php';
      $template_dir = $context->getConfiguration()->getTemplateDir($moduleName, $template);
      
      $view->setDirectory($template_dir);
      $view->setTemplate($template);
      if($view->isDecorator())
      {
        $view->setDecoratorTemplate($config['decorator']['template'].'.text.php');
      }
      
      $text_version = $view->render($action->getVarHolder()->getAll());
      
      $action->mail->setBodyText($text_version, $config['charset'], $config['encoding']);
    } 
    catch(sfRenderException $e)
    {}

    // html version
    try {
      $template = $actionName.'Success.html.php';
      $template_dir = $context->getConfiguration()->getTemplateDir($moduleName, $template);
      
      $view->setDirectory($template_dir);
      $view->setTemplate($template);
      if($view->isDecorator())
      {
        $view->setDecoratorTemplate($config['decorator']['template'].'.html.php');
      }
      
      $html_version = $view->render($action->getVarHolder()->getAll());
      
      $action->mail->setBodyHtml($html_version, $config['charset'], $config['encoding']);
    }
    catch(sfRenderException $e)
    {}

    // 6. SEND THE MAIL
    $transport_class = $config['transport']['class'];
    $transport_settings = $config['transport']['parameters'];
    
    if(!is_array($transport_settings))
    {
      $transport_settings = array($transport_settings);
    }
    
    if(!sfAutoload::getInstance()->loadClass($transport_class))
    {
      throw new LogicException('Please configure the mail swToolboxPlugin settings');
    }
    
    $reflection_class = new ReflectionClass($transport_class);
    $transport_class = $reflection_class->newInstanceArgs($transport_settings);
    
    $action->mail->send($transport_class);
    
    return $action->mail;
  }
  
  static public function sendMailFromEvent(sfEvent $event)
  {
    $event->setProcessed(true);
    
    $params = $event->getParameters();
    if(count($params['arguments']) == 3)
    {
      list($moduleName, $actionName, $vars) = $params['arguments'];
    }
    else
    {
      list($moduleName, $actionName) = $params['arguments'];
      $vars = array();
    }
    
    return self::sendMail($moduleName, $actionName, $vars);
  }
}