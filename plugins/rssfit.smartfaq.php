<?php
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2006 NS Tai (aka tuff)                ##
##                       <http://www.brandycoke.com/>                        ##
###############################################################################
##                    XOOPS - PHP Content Management System                  ##
##                       Copyright (c) 2000 XOOPS.org                        ##
##                          <http://www.xoops.org/>                          ##
###############################################################################
##  This program is free software; you can redistribute it and/or modify     ##
##  it under the terms of the GNU General Public License as published by     ##
##  the Free Software Foundation; either version 2 of the License, or        ##
##  (at your option) any later version.                                      ##
##                                                                           ##
##  You may not change or alter any portion of this comment or credits       ##
##  of supporting developers from this source code or any supporting         ##
##  source code which is considered copyrighted (c) material of the          ##
##  original comment or credit authors.                                      ##
##                                                                           ##
##  This program is distributed in the hope that it will be useful,          ##
##  but WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            ##
##  GNU General Public License for more details.                             ##
##                                                                           ##
##  You should have received a copy of the GNU General Public License        ##
##  along with this program; if not, write to the Free Software              ##
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA ##
###############################################################################
/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com/>
* Requirements (Tested with):
*  Module: SmartFAQ <http://www.smartfactory.ca/>
*  Version: 1.04 / 1.1 dev
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}
class RssfitSmartfaq
{
    public $dirname = 'smartfaq';
    public $modname;
    public $grab;

    public function loadModule()
    {
        $mod = $GLOBALS['moduleHandler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        return $mod;
    }

    public function &grabEntries(&$obj)
    {
        $ret = false;
        @require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
        $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
        $faqs       = $faqHandler->getAllPublished($this->grab, 0);
        if (false !== $faqs && count($faqs) > 0) {
            /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
            $answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');
            for ($i=0, $iMax = count($faqs); $i < $iMax; $i++) {
                if (!$answer = $answerHandler->getOfficialAnswer($faqs[$i]->faqid())) {
                    continue;
                }
                $ret[$i]['link'] = $ret[$i]['guid'] = XOOPS_URL.'/modules/smartfaq/faq.php?faqid='.$faqs[$i]->faqid();
                $q = $faqs[$i]->getVar('howdoi', 'n');
                $q = empty($q) ? $faqs[$i]->getVar('question', 'n') : $q;
                $ret[$i]['title'] = $q;
                $ret[$i]['timestamp'] = $faqs[$i]->getVar('datesub');
                $ret[$i]['description'] = $answer->getVar('answer');
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
            }
        }
        return $ret;
    }
}
