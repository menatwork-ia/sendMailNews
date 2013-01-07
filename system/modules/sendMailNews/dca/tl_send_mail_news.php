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
 * Table tl_send_mail_news
 */
$GLOBALS['TL_DCA']['tl_send_mail_news'] = array(
    // Config
    'config' => array(
        'dataContainer'    => 'Table',
        'switchToEdit'     => true,
        'enableVersioning' => true
    ),
    // List
    'list'             => array(
        'sorting' => array(
            'mode'   => 1,
            'fields' => array('title'),
            'flag'        => 1,
            'panelLayout' => 'filter,search,limit'
        ),
        'label'       => array(
            'fields' => array('title', 'mail_server_name'),
            'format'            => '%s [%s]'
        ),
        'global_operations' => array(
            'all' => array(
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array(
            'edit' => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_send_mail_news']['edit'],
                'href'       => 'act=edit',
                'icon'       => 'edit.gif',
                'attributes' => 'class="contextmenu"'
            ),
            'copy'       => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_send_mail_news']['copy'],
                'href'       => 'act=paste&amp;mode=copy',
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ),
            'cut'        => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_send_mail_news']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ),
            'delete'     => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_send_mail_news']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'check'      => array(
                'label' => &$GLOBALS['TL_LANG']['tl_send_mail_news']['check'],
                'href'  => 'key=check',
                'icon'  => 'system/modules/zad_sendnews/html/check.gif'
            ),
//            'toggle'     => array(
//                'label'      => &$GLOBALS['TL_LANG']['tl_send_mail_news']['toggle'],
//                'icon'       => 'visible.gif',
//                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"'
//            ),
            'show'  => array(
                'label'    => &$GLOBALS['TL_LANG']['tl_send_mail_news']['show'],
                'href'     => 'act=show',
                'icon'     => 'show.gif'
            )
        )
    ),
    // Palettes
    'palettes' => array(
        '__selector__' => array('enclosure'),
        'default'     => '{title_legend},title;{mail_server_legend},mail_server_name,mail_server_port,mail_server_type,mail_server_security,mail_server_user,mail_server_password,mail_server_mailbox;{archive_legend},news_archive;{advanced_legend},time_check,enclosure,published;'
    ),
    // Subpalettes
    'subpalettes' => array(
        'enclosure' => 'enclosure_dir'
    ),
    // Fields
    'fields'    => array(
        'title' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory'        => true,
                'maxlength'        => 255,
                'decodeEntities'   => true
            )
        ),
        'mail_server_name' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_name'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory'        => true,
                'maxlength'        => 255,
                'tl_class'         => 'w50'
            )
        ),
        'mail_server_port' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_port'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0',
            'eval'      => array(
                'rgxp'             => 'digit',
                'tl_class'         => 'w50'
            ),
        ),
        'mail_server_type' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_type'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('pop3', 'imap'),
            'default'   => 'pop3',
            'reference' => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_type_options'],
            'eval'      => array(
                'mandatory'            => true,
                'tl_class'             => 'w50'
            )
        ),
        'mail_server_security' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('notls', 'validate', 'novalidate', 'tls_validate', 'tls_novalidate', 'ssl_validate', 'ssl_novalidate'),
            'default'   => 'novalidate',
            'reference' => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_security_options'],
            'eval'      => array(
                'mandatory'        => true,
                'tl_class'         => 'w50'
            )
        ),
        'mail_server_user' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_user'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory'     => true,
                'maxlength'     => 255,
                'tl_class'      => 'w50'
            ),
            'load_callback' => array(
                array('tl_send_mail_news', 'decryptLoadCallback')
            ),
            'save_callback' => array
                (
                array('tl_send_mail_news', 'encryptSaveCallback')
            )
        ),
        'mail_server_password' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_password'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory'     => true,
                'maxlength'     => 255,
                'hideInput'     => true,
                'tl_class'      => 'w50'
            ),
            'load_callback' => array(
                array('tl_send_mail_news', 'decryptLoadCallback')
            ),
            'save_callback' => array
                (
                array('tl_send_mail_news', 'encryptSaveCallback')
            )
        ),
        'mail_server_mailbox' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['mail_server_mailbox'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('maxlength'    => 255)
        ),
        'news_archive' => array(
            'label'      => &$GLOBALS['TL_LANG']['tl_send_mail_news']['news_archive'],
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_news_archive.title',
            'eval'       => array(
                'doNotCopy'          => true,
                'mandatory'          => true,
                'chosen'             => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50'
            )
        ),
        'time_check'         => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('H', 'D', 'W', 'M'),
            'default'   => 'H',
            'reference' => &$GLOBALS['TL_LANG']['tl_send_mail_news']['time_check_options'],
            'eval'      => array(
                'mandatory'          => true,
                'includeBlankOption' => true
            )
        ),
        'enclosure'          => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['enclosure'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '',
            'eval'      => array(
                'isBoolean'      => true,
                'submitOnChange' => true,
                'tl_class'       => 'clr'
            )
        ),
        'enclosure_dir'  => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['enclosure_dir'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                'fieldType' => 'radio',
                'files'     => false,
                'filesOnly' => false,
                'mandatory' => true
            )
        ),
        'published' => array
            (
            'label'     => &$GLOBALS['TL_LANG']['tl_send_mail_news']['published'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array(
                'doNotCopy' => true
            )
        ),
    )
);

/**
 * Class tl_send_mail_news
 * 
 * PHP version 5
 * @copyright  MEN AT WORK 2013
 * @package    sendMailNews
 * @license    GNU/LGPL
 * @filesource
 */
class tl_send_mail_news extends Backend
{

    /**
     * Load the encryption object
     */
    protected function __construct()
    {
        parent::__construct();

        $this->import('Encryption');
    }

    /**
     * 
     * @param mixed $varValue
     * @param DataContainer $dc
     */
    public function decryptLoadCallback($varValue, DataContainer $dc)
    {
        return $this->Encryption->decrypt($varValue);
    }

    /**
     * 
     * @param mixed $varValue
     * @param DataContainer $dc
     */
    public function encryptSaveCallback($varValue, DataContainer $dc)
    {
        return $this->Encryption->encrypt($varValue);
    }

}

