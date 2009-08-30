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
class swMailView extends sfPHPView
{
  /**
   * Retrieves the template engine associated with this view.
   *
   * @return string sfMail
   */
  public function getEngine()
  {
    return 'swMail';
  }
}
