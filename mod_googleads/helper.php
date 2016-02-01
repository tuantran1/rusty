<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_googleads
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Helper for mod_googleads
 *
 * @package     Joomla.Site
 * @subpackage  mod_googleads
 * @since       1.5
 */
class modGoogleadsHelper {

    function getAdv_list($params) {
        $getOption = strip_tags(JRequest::getVar('option'));
        $app = new App();
        $getAdvs = $app->getAdv();
        $getUrl = JURI::base();
        if (strpos($getUrl, 'local') !== false) {
            $prefix = 'local_';
        } elseif (strpos($getUrl, 'staging2') !== false) {
            $prefix = 'staging_';
        } elseif (strpos($getUrl, 'live') !== false) {
            $prefix = '';
        } else {
            $prefix = '';
        }
        if($params->get("layout") == "_:default"){
            if ($getOption == "com_video" || $getOption == "com_gallery") {
                return $getAdvs[$prefix . 'gpt_adv_id_gallery_video'];
            }
            return $getAdvs[$prefix . 'gpt_adv_id_topbanner'];
        }elseif($params->get("layout") == "_:right"){
            if ($getOption == "com_video" || $getOption == "com_gallery") {
                return $getAdvs[$prefix . 'gpt_adv_id_gallery'];
            }
            return $getAdvs[$prefix . 'gpt_adv_id_right'];
        }
    }
}
