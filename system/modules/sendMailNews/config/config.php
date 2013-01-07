<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2013
 * @package    sendMailNews
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['sendMailNews'] = array(
    'tables' => array('tl_send_mail_news'),
    'check' => array('SendMailNews', 'checkMailsNow')
);

/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['hourly'][] = array('SendMailNews', 'cronJobHourly');
$GLOBALS['TL_CRON']['daily'][] = array('SendMailNews', 'cronJobDaily');
$GLOBALS['TL_CRON']['weekly'][] = array('SendMailNews', 'cronJobWeekly');
$GLOBALS['TL_CRON']['monthly'][] = array('SendMailNews', 'cronJobMonthly');

