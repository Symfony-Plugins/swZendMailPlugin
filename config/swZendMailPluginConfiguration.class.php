<?php

/*
 * This file is part of the swZendMailPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class swZendMailPluginConfiguration extends sfPluginConfiguration
{
  
  public function initialize()
  {

    // sendMail option
    $this->dispatcher->connect('component.method_not_found', array('swZendMailHelper', 'componentMethodNotFound'));
    $this->dispatcher->connect('configuration.method_not_found', array('swZendMailHelper', 'configurationMethodNotFound'));

  }
}