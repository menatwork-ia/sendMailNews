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
 * Class ConnectionController
 */
class ConnectionController extends Backend
{

    protected $_objMailConfig = null;
    protected $_strMailbox    = null;
    protected $_strUsername   = null;
    protected $_strPasswort   = null;
    protected $_objInbox      = null;
    protected $_arrMails      = array();

    /**
     * Initialize the object
     */
    public function __construct($objMailConfig)
    {
        parent::__construct();

        $this->import('Encryption');

        $this->_objMailConfig = $objMailConfig;

        $this->_convertVars();
    }

    public function getMails()
    {
        if (is_null($this->_objInbox))
            $this->_connectInbox();

        for ($intId = 1; $intId <= $this->_getMailsCount(); $intId++)
        {
            $objMail = new MailContainer();

            if (isset($this->plaintextMessage))
                unset($this->plaintextMessage);
            if (isset($this->htmlMessage))
                unset($this->htmlMessage);
            if (isset($this->attachments))
                unset($this->attachments);

            $objMail->setId($intId);

            $this->_setMailHeaderInfo($this->_getHeaders($intId), $objMail);

            $objStructure = $this->_getStructure($intId);

            if (!isset($objStructure->parts))
            {
                // Not multipart
                $this->processStructure($intId, $objStructure);
            }
            else
            {
                // Multipart
                foreach ($objStructure->parts as $id => $part)
                {
                    $this->processStructure($intId, $part, $id + 1);
                }
            }

            $this->_setMailBodyInfo($objMail, true);

            if (isset($this->attachments))
                $objMail->setAttachment($this->attachments);

            $this->_arrMails[] = $objMail;
        }

        return $this->_arrMails;
    }

    // SET MAIL CONTAINER INFORMATION ------------------------------------------

    /**
     * Set information from given header object to given mail container
     * (subject, date, to, from, reply_to, sender)
     * 
     * @param \stdClass $objHeaders
     * @param \MailContainer $objMail
     */
    protected function _setMailHeaderInfo($objHeaders, $objMail)
    {
        $objMail->setSubject($objHeaders->subject);
        $objMail->setDate($objHeaders->date);

        $arrAddrTypes = array(
            'to'       => 'setTo',
            'from'     => 'setFrom',
            'reply_to' => 'setReplyTo',
            'sender'   => 'setSender'
        );

        foreach ($arrAddrTypes as $strAddrType => $strFunc)
        {
            if (isset($objHeaders->$strAddrType))
            {
                $objMail->$strFunc($this->_processAddressObject($objHeaders->$strAddrType));
            }
        }
    }

    /**
     * Set body message infromation to given mail container
     * 
     * @param \MailContainer $objMail
     * @param boolean $blnHtml
     */
    protected function _setMailBodyInfo($objMail, $blnHtml = false)
    {
        $objMail->setType('plain');
        if ($blnHtml)
        {
            $objMail->setType('html');
            if (!isset($this->htmlMessage) && isset($this->plaintextMessage))
            {
                $objMail->setBody(nl2br_html5($this->plaintextMessage));
            }
            elseif (isset($this->htmlMessage))
            {
                $objMail->setBody($this->htmlMessage);
            }
        }
        else
        {
            if (!isset($this->plaintextMessage) && isset($this->htmlMessage))
            {
                $objMail->setBody(strip_tags($this->htmlMessage));
            }
            elseif (isset($this->plaintextMessage))
            {
                $objMail->setBody($this->plaintextMessage);
            }
        }
    }

    // GET INFO FROM IMAP ------------------------------------------------------

    public function connectInbox()
    {
        $this->_objInbox = imap_open($this->_strMailbox, $this->_strUsername, $this->_strPasswort);
    }

    /**
     * Get Total Number off Unread Email In Mailbox
     * 
     * @return integer
     */
    protected function _getMailsCount()
    {
        $headers = imap_headers($this->_objInbox);
        return count($headers);
    }

    /**
     * Return the header object for the given id in the open imap stream
     * 
     * @param interger $intId
     * @return \stdClass
     */
    protected function _getHeaders($intId)
    {
        // raw headers
        $rawHeaders = imap_fetchheader($this->_objInbox, $intId);

        // convert raw header string into a usable object
        $headerObject = imap_rfc822_parse_headers($rawHeaders);

        // to keep this object as close as possible to the original header object we add the udate property
        $headerObject->udate = strtotime($headerObject->date);

        return $headerObject;
    }

    protected function _getStructure($intId)
    {
        return imap_fetchstructure($this->_objInbox, $intId);
    }

    protected function processStructure($intId, $objStructure, $partIdentifier = null)
    {

        $arrParameters = $this->getParametersFromStructure($objStructure);

        if (isset($arrParameters['NAME']) || isset($arrParameters['FILENAME']))
        {
            $objAttachment                         = $this->_getAttachmentInformation($intId, $objStructure, $partIdentifier);
            ($objAttachment->ifid) ? $this->attachments[$objAttachment->id] = $objAttachment : $this->attachments[]                   = $objAttachment;
        }
        elseif ($objStructure->type == 0 || $objStructure->type == 1)
        {

            $messageBody = isset($partIdentifier) ?
                    imap_fetchbody($this->_objInbox, $intId, $partIdentifier) : imap_body($this->_objInbox, $intId);

            $messageBody = $this->_decode($messageBody, $objStructure->encoding);

            if ($arrParameters['CHARSET'] !== $GLOBALS['EXT_SEND_MAIL_NEWS']['charset'])
            {
                $messageBody = iconv($arrParameters['CHARSET'], $GLOBALS['EXT_SEND_MAIL_NEWS']['charset'], $messageBody);
            }

            if (strtolower($objStructure->subtype) == 'plain' || $objStructure->type == 1)
            {
                if (isset($this->plaintextMessage))
                {
                    $this->plaintextMessage .= PHP_EOL . PHP_EOL;
                }
                else
                {
                    $this->plaintextMessage = '';
                }

                $this->plaintextMessage .= trim($messageBody);
            }
            else
            {
                if (isset($this->htmlMessage))
                {
                    $this->htmlMessage .= '<br><br>';
                }
                else
                {
                    $this->htmlMessage = '';
                }

                $this->htmlMessage .= $messageBody;
            }
        }

        if (isset($objStructure->parts))
        {  // multipart: iterate through each part
            foreach ($objStructure->parts as $partIndex => $part)
            {
                $partId = $partIndex + 1;

                if (isset($partIdentifier))
                    $partId = $partIdentifier . '.' . $partId;

                $this->processStructure($intId, $part, $partId);
            }
        }
    }

    /**
     * 
     * @param MailContainer $objMail
     * @return boolean
     */
    public function delete($objMail)
    {
        return imap_delete($this->_objInbox, $objMail->getId());
    }

    /**
     * Close inbox
     */
    public function closeInbox()
    {
        imap_close($this->_objInbox, CL_EXPUNGE);
    }

    // IMAP ATTACHMENT ---------------------------------------------------------

    /**
     * Create an object with all nessesary information about the attachment
     * 
     * @param integer $intId
     * @param \stdClass $objStructure
     * @param null|integer $partIdentifier
     * @return \stdClass
     */
    protected function _getAttachmentInformation($intId, $objStructure, $partIdentifier = null)
    {
        $objFile = new stdClass();

        $arrParameters = $this->getParametersFromStructure($objStructure);

        if (isset($arrParameters['FILENAME']))
        {
            $objFile->filename = $arrParameters['FILENAME'];
        }
        elseif (isset($arrParameters['NAME']))
        {
            $objFile->filename = $arrParameters['NAME'];
        }

        $objFile->ifid = $objStructure->ifid;
        $objFile->id   = str_replace(array('<', '>'), '', $objStructure->id);
        $objFile->size     = $objStructure->bytes;
        $objFile->mimeType = $this->typeIdToString($objStructure->type);

        if (isset($objStructure->subtype))
            $objFile->mimeType .= '/' . strtolower($objStructure->subtype);

        $objFile->encoding    = $objStructure->encoding;
        $objFile->disposition = strtolower($objStructure->disposition);
        $objFile->data        = $this->_getAttachmentData($intId, $objStructure, $partIdentifier);

        return $objFile;
    }

    /**
     * Return the encoded attachment file body as raw data
     * 
     * @param integer $intId
     * @param \stdClass $objStructure
     * @param null|integer $partIdentifier
     * @return string
     */
    protected function _getAttachmentData($intId, $objStructure, $partIdentifier)
    {
        $attachmentBody = isset($partIdentifier) ?
                imap_fetchbody($this->_objInbox, $intId, $partIdentifier) : imap_body($this->_objInbox, $intId);

        $attachmentBody = $this->_decode($attachmentBody, $objStructure->encoding);
        return $attachmentBody;
    }

    /**
     * Save given attachment object to the given location. 
     * Count int value on filename if exists
     * 
     * @param string $strPath
     * @param \stdClass $objAttachment
     * @return string|boolean
     */
    public function saveAttachmentToDir($strPath, $objAttachment)
    {
        $strMd5 = md5($objAttachment->data);
        $arrFileInfo    = pathinfo($objAttachment->filename);
        $strFilePath = $strPath . '/' . $strMd5 . '.' . $arrFileInfo['extension'];

        if (file_exists(TL_ROOT . '/' . $strFilePath))
        {
            $objAttachment->fileSystemImage;
            return $strFilePath;
        }

        if ($this->_saveAttachmentFile($strFilePath, $objAttachment))
        {
            return $strFilePath;
        }

        return false;
    }

    /**
     * Try to save the given file in the given location and return result
     * 
     * @param string $strFilePath
     * @param \stdClass $objAttachment
     * @return boolean
     */
    protected function _saveAttachmentFile($strFilePath, $objAttachment)
    {
        $objFile = new File($strFilePath);
        if (!$objFile->write($objAttachment->data))
        {
            $objFile->delete();
            return false;
        }

        return true;
    }

    // HELPER ------------------------------------------------------------------

    public function getInboxObject()
    {
        return $this->_objInbox;
    }

    /**
     * Create an object with all nessesary information and return it
     * 
     * @param type $arrAddr
     * @return null|\stdClass
     */
    protected function _processAddressObject($arrAddr)
    {
        if (!is_array($arrAddr))
            return NULL;

        $objAddr = $arrAddr[0];

        $objFormedAddr           = new stdClass();
        $objFormedAddr->personal = $objAddr->personal;
        $objFormedAddr->mailbox  = $objAddr->mailbox;
        $objFormedAddr->host     = $objAddr->host;
        $objFormedAddr->address  = $objAddr->mailbox . '@' . $objAddr->host;

        return $objFormedAddr;
    }

    public function getParametersFromStructure($objStructure)
    {
        $arrParameters = array();
        if (isset($objStructure->parameters))
            foreach ($objStructure->parameters as $parameter)
                $arrParameters[strtoupper($parameter->attribute)] = $parameter->value;

        if (isset($objStructure->dparameters))
            foreach ($objStructure->dparameters as $parameter)
                $arrParameters[strtoupper($parameter->attribute)] = $parameter->value;

        return $arrParameters;
    }

    protected function _decode($data, $encoding)
    {
        if (!is_numeric($encoding))
            $encoding = strtolower($encoding);

        switch ($encoding)
        {
            case 'quoted-printable':
            case 4:
                return quoted_printable_decode($data);

            case 'base64':
            case 3:
                return base64_decode($data);

            default:
                return $data;
        }
    }

    public function typeIdToString($intId)
    {
        switch ($intId)
        {
            case 0:
                return 'text';

            case 1:
                return 'multipart';

            case 2:
                return 'message';

            case 3:
                return 'application';

            case 4:
                return 'audio';

            case 5:
                return 'image';

            case 6:
                return 'video';

            default:
            case 7:
                return 'other';
        }
    }

    /**
     * Convert all values to php imape 
     */
    protected function _convertVars()
    {
        $this->_strMailbox  = $this->_createMailbox();
        $this->_strUsername = $this->Encryption->decrypt($this->_objMailConfig->mail_server_user);
        $this->_strPasswort = $this->Encryption->decrypt($this->_objMailConfig->mail_server_password);
    }

    protected function _createMailbox()
    {
        $arrSecurityOptions = explode('_', $this->_objMailConfig->mail_server_security);

        foreach ($arrSecurityOptions as $key => $value)
        {
            switch ($value)
            {
                case 'validate':
                    unset($arrSecurityOptions[$key]);
                    break;

                case 'novalidate':
                    $arrSecurityOptions[$key] = 'novalidate-cert';
                    break;
            }
        }

        return vsprintf('{%s:%s/%s/%s}%s', array(
                    $this->_objMailConfig->mail_server_name,
                    $this->_getPort(),
                    $this->_objMailConfig->mail_server_type,
                    implode('/', $arrSecurityOptions),
                    $this->_objMailConfig->mail_server_mailbox
                ));
    }

    protected function _getPort()
    {
        if ($this->_objMailConfig->mail_server_port == 0)
        {
            if ($this->_objMailConfig->mail_server_type == 'pop3')
            {
                return ($this->_isSsl()) ? 995 : 110;
            }
            else
            {
                return ($this->_isSsl()) ? 993 : 143;
            }
        }

        return $this->_objMailConfig->mail_server_port;
    }

    protected function _isSsl()
    {
        return (strpos($this->_objMailConfig->mail_server_security, 'ssl') !== false) ? true : false;
    }

}

?>