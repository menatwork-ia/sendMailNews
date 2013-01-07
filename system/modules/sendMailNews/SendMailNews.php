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
 * @copyright  MEN AT WORK 2012
 * @package    sendMailNews
 * @license    GNU/GPL 2
 * @filesource
 */

/**
 * Class SendMailNews
 */
class SendMailNews extends Backend
{

    /**
     *
     * @var type 
     */
    protected $_objConnectionController = null;
    protected $_objMailConfig           = null;
    protected $_arrFilterTags           = array('style');

    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();

        $this->import('String');
    }

    public function checkMailsNow()
    {
        $intId = $this->Input->get('id');

        $this->checkForMails($intId);

        $this->redirect($this->getReferer());
    }

    /**
     * Check for new mails on mail server and update the news
     * 
     * @param integer $intId
     * @return null
     */
    public function checkForMails($intId)
    {
        if (strlen($intId) > 0)
        {
            $this->_objMailConfig = $this->_getMailConfig($intId);

            if (is_null($this->_objMailConfig))
                return null;

            $this->_objConnectionController = new ConnectionController($this->_objMailConfig);
            $this->_objConnectionController->connectInbox();

            $this->_writeMailsToNews($this->_objConnectionController->getMails());

            $this->_objConnectionController->closeInbox();
        }
    }

    /**
     * Write given mails to the news
     * 
     * @param array $arrMails
     */
    protected function _writeMailsToNews($arrMails)
    {
        if (count($arrMails) < 1)
            return;

        foreach ($arrMails as $objMail)
        {
            /* @var $objMail MailContainer */
            $time = time();

            $arrFrom = $objMail->getFrom();

            $mixedAuthor = $this->_getUserIdForMail($arrFrom[0]['address']);
            if (is_null($mixedAuthor))
                continue;

            $arrNews = array(
                'pid'         => $this->_objMailConfig->news_archive,
                'tstamp'      => $time,
                'date'        => $time,
                'time'        => $time,
                'headline'    => $this->String->substr($objMail->getSubject(), 230),
                'author'      => $mixedAuthor,
                'subheadline' => $this->String->substr('', 230),
                'text'        => $this->_validateHtml($objMail->getBody()),
                'teaser'      => '',
                'published'   => '1',
                'source'      => 'default'
            );

            if ($this->_objMailConfig->enclosure)
            {
                $arrAttachmentList = array();
                $arrAttachment = $objMail->getAttachment();

                if (is_array($arrAttachment) && count($arrAttachment) > 0)
                {
                    foreach ($arrAttachment as $objAttachmentController)
                    {
                        /* @var $objAttachmentController AttachmentController */
                        $strFilePath = $objAttachmentController->saveToDirectory($this->_objMailConfig->enclosure_dir);

                        if (strlen($strFilePath) > 0)
                            $arrAttachmentList[] = $strFilePath;
                    }
                }

                if (count($arrAttachmentList) > 0)
                {
                    $arrNews['addEnclosure'] = '1';
                    $arrNews['enclosure']    = serialize($arrAttachmentList);
                }
            }

            $this->_insertNews($arrNews);
            $this->_objConnectionController->delete($objMail);
        }
    }

    /* CRONJOB -------------------------------------------------------------- */

    /**
     * Function used by the hourly cron job.
     */
    public function cronJobHourly()
    {
        $this->cronJob('H');
    }

    /**
     * Function used by the dayly cron job.
     */
    public function cronJobDaily()
    {
        $this->cronJob('D');
    }

    /**
     * Function used by the weekly cron job.
     */
    public function cronJobWeekly()
    {
        $this->cronJob('W');
    }

    /**
     * Function used by the monthly cron job.
     */
    public function cronJobMonthly()
    {
        $this->cronJob('M');
    }

    /**
     * Main function used by the cron job.
     */
    public function cronJob($strType)
    {
        $objResult = $this->Database
                ->prepare("SELECT * FROM tl_send_mail_news WHERE published = '1' AND time_check = ?")
                ->execute($strType);

        if ($objResult->numRows > 0)
        {
            while ($objResult->next())
            {
                $this->checkForMails($objResult->id);
            }
        }
    }

    /* HELPER --------------------------------------------------------------- */

    protected function _validateHtml($strValue)
    {
        $strAllowedTags = '<span><br><p><b><i><strong>';
        foreach ($this->_arrFilterTags as $strTag)
        {
            $strAllowedTags = str_replace('<' . $strTag . '>', '', $strAllowedTags);
        }

        $strText = trim(strip_tags($strValue, $strAllowedTags));

        $arrAttrPattern = array(
            // Remove style attribute
            'style' => '/ ?style=\"[^>]*\" ?/i',
            // Remove class attribute
            'class' => '/ ?class=\"[^>]*\" ?/i'
        );

        foreach ($arrAttrPattern as $strPattern)
        {
            $strText = preg_replace($strPattern, '', $strText);
        }

        $arrTagPattern = array(
            // Remove empty span tags
            'span' => '/<span[^>]*>[\s|&nbsp;]*<\/span>/',
            // Remove empty p tags
            'p'    => '/<p[^>]*>[\s|&nbsp;]*<\/p>/',
            // Remove empty div tags
            'span' => '/<span[^>]*>[\s|&nbsp;]*<\/span>/'
        );

        $strCleanText = $strText;
        $blnClean     = false;
        while (!$blnClean)
        {
            $strNotClean = $strCleanText;
            foreach ($arrTagPattern as $strPattern)
            {
                $strCleanText = preg_replace($strPattern, '', $strCleanText);
            }

            if ($strNotClean === $strCleanText)
                $blnClean = true;
        }

        return $strCleanText;
    }

    protected function _getUserIdForMail($strMail)
    {
        $objResult = $this->Database
                ->prepare("SELECT * FROM tl_user WHERE email = ?")
                ->limit(1)
                ->execute($strMail);

        return ($objResult->numRows > 0) ? $objResult->id : null;
    }

    /**
     * Return the the active mail config
     * 
     * @param integer $intId
     * @return Database_Result
     */
    protected function _getMailConfig($intId)
    {
        $objMailConf = $this->Database
                ->prepare("SELECT * FROM tl_send_mail_news WHERE id = ? AND published = '1'")
                ->limit(1)
                ->execute($intId);

        return ($objMailConf->numRows > 0) ? $objMailConf : null;
    }

    /**
     * Insert the given news entry
     * 
     * @param array $arrNews
     */
    protected function _insertNews($arrNews)
    {
        $intId = $this->Database->prepare("INSERT INTO tl_news %s")->set($arrNews)->execute()->insertId;

        $this->_updadeAlias($intId, $arrNews['headline']);
    }

    /**
     * Update the alias
     * 
     * @param integer $intId
     * @param string $strHeadline
     */
    protected function _updadeAlias($intId, $strHeadline)
    {
        $strAlias = standardize($this->restoreBasicEntities($strHeadline));

        $objAlias = $this->Database
                ->prepare("SELECT id FROM tl_news WHERE alias = ?")
                ->execute($strAlias);

        // Add ID to alias
        if ($objAlias->numRows)
        {
            $strAlias .= '-' . $intId;
        }

        $this->Database
                ->prepare("UPDATE tl_news SET alias = ? WHERE id = ?")
                ->execute($strAlias, $intId);
    }

}

?>