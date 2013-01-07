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
 * Class AttachmentController
 */
class AttachmentController extends Backend
{

    protected $structure;
    protected $messageId;
    protected $_objInbox;
    protected $partId;
    protected $filename;
    protected $size;
    protected $data;

    /**
     * Initialize the object
     */
    public function __construct(ConnectionController $objController, $intId, $objStructure, $partIdentifier = null)
    {
        parent::__construct();

        $this->messageId = $intId;
        $this->_objInbox = $objController->getInboxObject();
        $this->structure = $objStructure;

        if (isset($partIdentifier))
            $this->partId = $partIdentifier;

        $arrParameters = $objController->getParametersFromStructure($objStructure);

        if (isset($arrParameters['FILENAME']))
        {
            $this->filename = $arrParameters['FILENAME'];
        }
        elseif (isset($arrParameters['NAME']))
        {
            $this->filename = $arrParameters['NAME'];
        }

        $this->size = $objStructure->bytes;

        $this->mimeType = $objController->typeIdToString($objStructure->type);

        if (isset($objStructure->subtype))
            $this->mimeType .= '/' . strtolower($objStructure->subtype);

        $this->encoding = $objStructure->encoding;
    }

    public function getData()
    {
        if (!isset($this->data))
        {
            $messageBody = isset($this->partId) ?
                    imap_fetchbody($this->_objInbox, $this->messageId, $this->partId) : imap_body($this->_objInbox, $this->messageId);

            $messageBody = ConnectionController::decode($messageBody, $this->encoding);
            $this->data  = $messageBody;
        }
        return $this->data;
    }

    public function getFileName()
    {
        return (isset($this->filename)) ? $this->filename : false;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function saveToDirectory($strPath)
    {
        $strFilePath = $strPath . '/' . $this->getFileName();

        if (file_exists(TL_ROOT . '/' . $strFilePath))
        {
            $blnExist = true;
            $intIndex = 1;
            while ($blnExist)
            {
                $arrFileInfo    = pathinfo($this->getFileName());
                $strTmpFilePath = $strPath . '/' . $arrFileInfo['filename'] . '_' . $intIndex . '.' . $arrFileInfo['extension'];

                if (!file_exists(TL_ROOT . '/' . $strTmpFilePath))
                {
                    $strFilePath = $strTmpFilePath;
                    $blnExist    = false;
                }

                $intIndex++;
            }
        }

        if ($this->_saveFile($strFilePath))
        {
            return $strFilePath;
        }

        return FALSE;
    }

    protected function _saveFile($strFilePath)
    {
        $objFile = new File($strFilePath);
        if (!$objFile->write($this->getData()))
        {
            $objFile->delete();
            return false;
        }

        return true;
    }

}

?>