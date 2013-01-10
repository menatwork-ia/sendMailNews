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
 * @license    GNU/GPL 2
 * @filesource
 */

/**
 * Class MailContainer
 */
class MailContainer
{

    protected $_intId         = null;
    protected $_strSubject    = null;
    protected $_strDate       = null;
    protected $_objTo         = null;
    protected $_objFrom       = null;
    protected $_objReplyTo    = null;
    protected $_objSender     = null;
    protected $_strBody       = '';
    protected $_strType       = null;
    protected $_arrAttachment = array();

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
        return $this->_objTo;
    }

    public function setTo($objTo)
    {
        $this->_objTo = $objTo;
    }

    public function getFrom()
    {
        return $this->_objFrom;
    }

    public function setFrom($objFrom)
    {
        $this->_objFrom = $objFrom;
    }

    public function getReplyTo()
    {
        return $this->_objReplyTo;
    }

    public function setReplyTo($objReplyTo)
    {
        $this->_objReplyTo = $objReplyTo;
    }

    public function getSender()
    {
        return $this->_objSender;
    }

    public function setSender($objSender)
    {
        $this->_objSender = $objSender;
    }

    public function getBody()
    {
        return $this->_strBody;
    }

    public function setBody($strBody)
    {
        $this->_strBody = $strBody;
    }

    public function getType()
    {
        return $this->_strType;
    }

    public function setType($strType)
    {
        $this->_strType = $strType;
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