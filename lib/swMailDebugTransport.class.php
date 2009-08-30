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
 * @subpackage mail
 * @author     Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * @version    SVN: $Id$
 */
class swMailDebugTransport extends Zend_Mail_Transport_Abstract
{

  protected
    $raw_mail;

  public function __construct()
  {

  }
  
  public function _sendMail()
  {
    
    $this->raw_mail = $this->header . Zend_Mime::LINEEND . $this->body;
    
  }
  
  public function getRawMail()
  {
    return $this->raw_mail;
  }

}