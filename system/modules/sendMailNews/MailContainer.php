<?php

if (!defined('TL_ROOT'))
    die('You cannot access this file directly!');

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
 * @license    GNU/GPL 2
 * @filesource
 */

/**
 * Class MailContainer
 */
class MailContainer extends Backend
{

    protected $_intId         = null;
    protected $_strSubject    = null;
    protected $_strSize       = null;
    protected $_strDate       = null;
    protected $_arrTo         = null;
    protected $_arrCc         = null;
    protected $_arrFrom       = null;
    protected $_arrReplyTo    = null;
    protected $_strBody       = null;
    protected $_arrAttachment = null;

    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getId()
    {
        return $this->_intId;
    }

    public function setId($intId)
    {
        $this->_intId = $intId;
    }

    public function getSubject()
    {
        return $this->_strSubject;
    }

    public function setSubject($strSubject)
    {
        $this->_strSubject = $strSubject;
    }

    public function getSize()
    {
        return $this->_strSize;
    }

    public function setSize($strSize)
    {
        $this->_strSize = $strSize;
    }

    public function getDate()
    {
        return $this->_strDate;
    }

    public function setDate($strDate)
    {
        $this->_strDate = $strDate;
    }

    public function getTo()
    {
        return $this->_arrTo;
    }

    public function setTo($strTo)
    {
        $this->_arrTo = $strTo;
    }

    public function getCc()
    {
        return $this->_arrCc;
    }

    public function setCc($strCc)
    {
        $this->_arrCc = $strCc;
    }

    public function getFrom()
    {
        return $this->_arrFrom;
    }

    public function setFrom($strFrom)
    {
        $this->_arrFrom = $strFrom;
    }

    public function getReplyTo()
    {
        return $this->_arrReplyTo;
    }

    public function setReplyTo($strReplyTo)
    {
        $this->_arrReplyTo = $strReplyTo;
    }

    public function getBody()
    {
        return $this->_strBody;
    }

    public function setBody($strBody)
    {
        $this->_strBody = $strBody;
    }

    public function getAttachment()
    {
        return $this->_arrAttachment;
    }

    public function setAttachment($arrAttachment)
    {
        $this->_arrAttachment = $arrAttachment;
    }

}

?>