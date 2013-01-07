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

        for ($i = 1; $i <= $this->_getMailsCount(); $i++)
        {
            $objMail = new MailContainer();

            $this->_loadMessage($i, $objMail);

            $this->_arrMails[] = $objMail;
        }

        return $this->_arrMails;
    }

    // IMAP HELPER -------------------------------------------------------------

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
     * 
     * @param integer $intId
     * @param MailContainer $objMail
     */
    protected function _loadMessage($intId, &$objMail)
    {
        if (isset($this->plaintextMessage))
            unset($this->plaintextMessage);
        if (isset($this->htmlMessage))
            unset($this->htmlMessage);
        if (isset($this->attachments))
            unset($this->attachments);

        $objMail->setId($intId);

        /* First load the message overview information */

        $objMessageOverview = $this->_getOverview($intId);

        $objMail->setSubject($objMessageOverview->subject);
        $objMail->setDate($objMessageOverview->date);
        $objMail->setSize($objMessageOverview->size);

        /* Next load in all of the header information */

        $headers = $this->_getHeaders($intId);

        if (isset($headers->to))
            $objMail->setTo($this->_processAddressObject($headers->to));

        if (isset($headers->cc))
            $objMail->setCc($this->_processAddressObject($headers->cc));

        $objMail->setFrom($this->_processAddressObject($headers->from));
        $objMail->setReplyTo(isset($headers->reply_to) ? $this->_processAddressObject($headers->reply_to) : $this->from);

        /* Finally load the structure itself */

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

        $objMail->setBody($this->_getMessageBody(true));

        if (isset($this->attachments))
            $objMail->setAttachment($this->attachments);
    }

    protected function _getOverview($intId)
    {
        $arrMessageOverview = imap_fetch_overview($this->_objInbox, $intId);
        return array_shift($arrMessageOverview);
    }

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

    protected function _processAddressObject($addresses)
    {
        $outputAddresses = array();
        if (is_array($addresses))
            foreach ($addresses as $address)
            {
                $currentAddress = array();
                $currentAddress['address'] = $address->mailbox . '@' . $address->host;
                if (isset($address->personal))
                    $currentAddress['name']    = $address->personal;
                $outputAddresses[]         = $currentAddress;
            }
        return $outputAddresses;
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
            $attachment          = new AttachmentController($this, $intId, $objStructure, $partIdentifier);
            $this->attachments[] = $attachment;
        }
        elseif ($objStructure->type == 0 || $objStructure->type == 1)
        {

            $messageBody = isset($partIdentifier) ?
                    imap_fetchbody($this->_objInbox, $intId, $partIdentifier) : imap_body($this->_objInbox, $intId);

            $messageBody = self::decode($messageBody, $objStructure->encoding);

            if ($arrParameters['CHARSET'] !== 'UTF-8//TRANSLIT')
                $messageBody = iconv($parameters['CHARSET'], 'UTF-8//TRANSLIT', $messageBody);

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

    public function getParametersFromStructure($objStructure)
    {
        $arrParameters = array();
        if (isset($objStructure->parameters))
            foreach ($objStructure->parameters as $parameter)
                $arrParameters[$parameter->attribute] = $parameter->value;

        if (isset($objStructure->dparameters))
            foreach ($objStructure->dparameters as $parameter)
                $arrParameters[$parameter->attribute] = $parameter->value;

        return $arrParameters;
    }

    public static function decode($data, $encoding)
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

    protected function _getMessageBody($html = false)
    {
        if ($html)
        {
            if (!isset($this->htmlMessage) && isset($this->plaintextMessage))
            {
                $output = nl2br_html5($this->plaintextMessage);
                return $output;
            }
            elseif (isset($this->htmlMessage))
            {
                return $this->htmlMessage;
            }
        }
        else
        {
            if (!isset($this->plaintextMessage) && isset($this->htmlMessage))
            {
                $output = strip_tags($this->htmlMessage);
                return $output;
            }
            elseif (isset($this->plaintextMessage))
            {
                return $this->plaintextMessage;
            }
        }
        return false;
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

    // HELPER ------------------------------------------------------------------

    public function getInboxObject()
    {
        return $this->_objInbox;
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