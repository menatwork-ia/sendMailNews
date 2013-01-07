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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_send_mail_news']['title'] = array('Title', 'Hier müssen Sie den Titel des E-Mail Accounts eingeben.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_name'] = array('Server Name', 'Hier müssen Sie den Namen des Posteingangsserver eingeben. (z.B. pop.gmail.com');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_port'] = array('Server Port', 'Hier müssen Sie den Port des Posteingangsserver eingeben. Default ist 0');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_type'] = array('Server Typ', 'Bitte wählen Sie den Typ des Posteingangsserver aus.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security'] = array('Server Sicherheit', 'Bitte wählen Sie die Sicherheitseinstellungen des Posteingangsserver aus.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_user'] = array('Server Benutzername', 'Hier müssen Sie den Loggin-Benutzernamen des E-Mail Accounts eingeben.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_password'] = array('Server Passwort', 'Hier müssen Sie das Loggin-Passwort des E-Mail Accounts eingeben.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_mailbox'] = array('Server Ordner', '');
$GLOBALS['TL_LANG']['tl_send_mail_news']['news_archive'] = array('News Archiv', 'Wählen Sie hier das Archiv aus in dem die E-Mails gespeichert werden sollen.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check'] = array('Cron Job Interval', 'Wählen Sie hier den Interval aus, wann nach neuen E-Mails zum importieren geschaut werden soll.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['enclosure'] = array('Anhänge anfügen', 'Hier klicken um E-Mailanhänge den news anzufügen.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['enclosure_dir'] = array('Anhang Ordner', 'Wählen Sie hier den Ordner aus wo die Anhänge gespeichert werden sollen.');
$GLOBALS['TL_LANG']['tl_send_mail_news']['published'] = array('E-Mail Client veröffentlichen', 'Die E-Mails auf der Webseite anzeigen.');

/**
 * Options
 */
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_type_options']['pop3']               = 'POP3';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_type_options']['imap']               = 'IMAP';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['notls']          = 'None';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['validate']       = 'TLS if available, with certificate validation';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['novalidate']     = 'TLS if available, without certificate validation';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['tls_validate']   = 'TLS with certificate validation';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['tls_novalidate'] = 'TLS without certificate validation';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['ssl_validate']   = 'SSL with certificate validation';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options']['ssl_novalidate'] = 'SSL without certificate validation';
$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check_options']['H']                        = 'Einmal pro Stunde';
$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check_options']['D']                        = 'Einmal pro Tag';
$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check_options']['W']                        = 'Einmal pro Woche';
$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check_options']['M']                        = 'Einmal pro Monat';

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_send_mail_news']['title_legend']       = 'Titel';
$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_legend'] = 'Posteingangsserver Einstellungen';
$GLOBALS['TL_LANG']['tl_send_mail_news']['archive_legend'] = 'News Archiv';
$GLOBALS['TL_LANG']['tl_send_mail_news']['advanced_legend'] = 'Erweiterte Einstellungen';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_send_mail_news']['new'] = array('Neuer E-Mail Account', 'Einen neuen E-Mail Account anlegen');
$GLOBALS['TL_LANG']['tl_send_mail_news']['show'] = array('E-Mail Account details', 'Details des E-Mail Accounts ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_send_mail_news']['edit'] = array('E-Mail Account bearbeiten', 'E-Mail Account ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_send_mail_news']['copy'] = array('E-Mail Account duplizieren', 'E-Mail Account ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_send_mail_news']['cut'] = array('E-Mail Account verschieben', 'E-Mail Account ID %s verschieben');
$GLOBALS['TL_LANG']['tl_send_mail_news']['delete'] = array('E-Mail Account löschen', 'E-Mail Account ID %s löschen');
$GLOBALS['TL_LANG']['tl_send_mail_news']['check'] = array('Neue E-Mails vom Account abrufen.', '');
$GLOBALS['TL_LANG']['tl_send_mail_news']['toggle'] = array('E-Mail Account veröffentlichen/unveröffentlichen', 'E-Mail Account ID %s veröffentlichen/unveröffentlichen');