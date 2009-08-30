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
class swMail extends Zend_Mail
{

  public function __construct($charset = null) {
    if(is_null($charset)) {
      $config = sfConfig::get('app_swToolbox_mail');
      $charset = $config['charset'];
    }

    parent::__construct($charset);
  }

  public function quickView() {
    $mail = "";
    
    foreach($this->getParts() as $part) {
      $mail .= $part->getContent();
    }

    return $mail;
  }

  public function __toString() {

    return $this->quickView();
  }

  public function getRecipientsTo() {
    
    return $this->_to;
  }

  public function getRecipientsBcc() {

    return $this->_to;
  }

  public function getRecipientsCc() {

    return $this->_to;
  }

  public function getHeader($name) {
    if(isset($this->_headers[$name])) {
      
      return $this->_headers[$name];
    }

    return null;
  }

  public function getPrintableTo()
  {
    
    return $this->_getPrintable($this->getHeader('To'));
  }

  private function _getPrintable($tos)
  {
    if(!is_array($tos))
    {

      return '';
    }

    $to = array();
    foreach($tos as $name => $email)
    {

      if($name === 'append')
      {

        continue;
      }

      $to[] = $email;
    }

    return implode(", ", $to);
  }
  
  public function getPrintableBcc()
  {

    return $this->_getPrintable($this->getHeader('Bcc'));
  }

  public function getPrintableCc()
  {

    return $this->_getPrintable($this->getHeader('Cc'));
  }

  public function getPrintableReplyTo()
  {

    return $this->_getPrintable($this->getHeader('ReplyTo'));
  }
}