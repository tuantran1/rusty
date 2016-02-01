<?php

/**
 * @package     Joomla.Site
 * @subpackage  module_googleads
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
require_once JPATH_ADMINISTRATOR . '/includes/app.class.php';

$doc = JFactory::getDocument();

$db = & JFactory::getDBO();
$getAdv = modGoogleadsHelper::getAdv_list($params);

$apps = new App();
$getAdvs = $apps->getAdv();

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

$setTargeting = '';
$targetCity = '';
$targetCountry = '';
$getView = strip_tags(JRequest::getVar('view'));
$getOption = strip_tags(JRequest::getVar('option'));
$right_adv = $getAdvs[$prefix . 'gpt_adv_path_right'];
$right_adv_id = $getAdvs[$prefix . 'gpt_adv_id_right'];
$top_adv = $getAdvs[$prefix . 'gpt_adv_path_topbanner'];
$top_adv_id = $getAdvs[$prefix . 'gpt_adv_id_topbanner'];

if ($getOption == "com_blog") {
    $category = 'blog';
    $setTargeting = "googletag.pubads().setTargeting('category','" . $category . "');";
    $articleId = JRequest::getVar('id', 0, '', 'int');

    $query = 'SELECT co.title AS contry_name, ci.title AS city_name FROM #__blog AS c
        INNER JOIN #__destination_countries as co ON c.country_id=co.id
        INNER JOIN #__destination_cities as ci ON c.city_id = ci.id
        WHERE c.id=' . $articleId;
    $db->setQuery($query);
    $result = $db->loadObject();

    if (isset($result->city_name)) {
        $city_name = strtolower($result->city_name);
        $targetCity = '.setTargeting("city","' . $city_name . '")';
    }

    if (isset($result->contry_name)) {
        $country_name = strtolower($result->contry_name);
        $targetCountry = '.setTargeting("country","' . $country_name . '")';
    }
    $right_adv = $getAdvs[$prefix . 'gpt_adv_path_right'];
    $right_adv_id = $getAdvs[$prefix . 'gpt_adv_id_right'];
    $top_adv = $getAdvs[$prefix . 'gpt_adv_path_topbanner'];
    $top_adv_id = $getAdvs[$prefix . 'gpt_adv_id_topbanner'];
}
if ($getOption == "com_destination") {

    switch ($getView) {

        case ($getView == 'categoryintro' || $getView == 'map'):

            $getCityId = JRequest::getVar('city', '', 'int');
            $getCountryId = JRequest::getVar('country', '', 'int');
            break;

        case 'destinationintro':

            $getId = JRequest::getVar('id', '', 'int');
            $stype = JRequest::getVar('stype', '', 'string');
            if ($stype == "city_intro") {
                $getCityId = $getId;
            } else {
                $getCountryId = $getId;
            }
            break;

        case ($getView == 'bestof' || $getView == 'galleryvideo'):
            $getId = JRequest::getVar('id', '', 'int');
            $stype = JRequest::getVar('stype', '', 'string');
            if ($stype == "city_intro") {
                $getCityId = $getId;
            } else {
                $getCountryId = $getId;
            }
            break;

        case 'detail':

            $getCityId = JRequest::getVar('city', '', 'int');
            $getCountryId = JRequest::getVar('country', '', 'int');
            break;
    }

    if ($getCityId) {
        $query = 'SELECT co.title AS contry_name, ci.title AS city_name FROM #__destination_cities AS ci
        INNER JOIN #__destination_countries as co ON ci.country_id=co.id
        WHERE ci.id =' . $getCityId;
        $db->setQuery($query);
        $result = $db->loadObject();
        $city_name = $result->city_name;
        $country_name = $result->contry_name;
    }
    if ($getCountryId) {
        $query = 'SELECT co.title AS contry_name FROM #__destination_countries AS co
        WHERE co.id =' . $getCountryId;
        $db->setQuery($query);
        $result = $db->loadObject();
        $city_name = "";
        $country_name = $result->contry_name;
    }

    if (!empty($city_name)) {
        $targetCity = '.setTargeting("city","' . $city_name . '")';
    }

    if (!empty($country_name)) {
        $targetCountry = '.setTargeting("country","' . $country_name . '")';
    }
    $right_adv = $getAdvs[$prefix . 'gpt_adv_path_right'];
    $right_adv_id = $getAdvs[$prefix . 'gpt_adv_id_right'];
    $top_adv = $getAdvs[$prefix . 'gpt_adv_path_skyscraper'];
    $top_adv_id = $getAdvs[$prefix . 'gpt_adv_id_topbanner'];
}
if ($getOption == "com_video" || $getOption == "com_gallery") {
    $city_name = '';
    $country_name = '';
    $uri = $_SERVER['REQUEST_URI'];
    $uriParameter = explode('/', $uri);

    if (!empty($uriParameter[2])) {
        $country_name = str_replace('-', ' ', $uriParameter[2]);
    }

    if (!empty($uriParameter[3])) {
        $city_name = str_replace('-', ' ', $uriParameter[3]);
    }

    if ($getOption == 'com_video') {
        $category = 'videos';
        $table = "#__video";
    } else {
        $category = 'gallery';
        $table = "#__gallery";
    }

    $setTargeting = "googletag.pubads().setTargeting('category','" . $category . "');";

    if ($getView == 'detail') {
        $articleId = JRequest::getVar('id', '', 'int');
        $query = 'SELECT co.title AS contry_name, ci.title AS city_name
            FROM ' . $table . ' AS c
        INNER JOIN #__destination_countries as co ON c.country_id=co.id
        INNER JOIN #__destination_cities as ci ON c.city_id = ci.id
        WHERE c.id=' . $articleId;
        $query .= ($category == "gallery") ? " AND photo_gallery = 0" : "";
        $db->setQuery($query);
        $result = $db->loadObject();

        if (isset($result->city_name)) {
            $city_name = strtolower(str_replace(' ', '', $result->city_name));
            $targetCity = '.setTargeting("city","' . $city_name . '")';
        }

        if (isset($result->contry_name)) {
            $country_name = strtolower(str_replace(' ', '', $result->contry_name));
            $targetCountry = '.setTargeting("country","' . $country_name . '")';
        }
    }
    if (!empty($city_name)) {
        $targetCity = '.setTargeting("city","' . $city_name . '")';
    }

    if (!empty($country_name)) {
        $targetCountry = '.setTargeting("country","' . $country_name . '")';
    }
    $right_adv = $getAdvs[$prefix . 'gpt_adv_path_gallery'];
    $right_adv_id = $getAdvs[$prefix . 'gpt_adv_id_gallery'];
    $top_adv = $getAdvs[$prefix . 'gpt_adv_path_gallery_video'];
    $top_adv_id = $getAdvs[$prefix . 'gpt_adv_id_gallery_video'];
}

//$script = "
//    googletag.cmd.push(function() {
//        googletag.defineSlot('" . $right_adv . "', [300, 250], '" . $right_adv_id . "').addService(googletag.pubads())" . $targetCity . $targetCountry . ";
//        googletag.defineSlot('" . $top_adv . "', [728, 90], '" . $top_adv_id . "').addService(googletag.pubads())" . $targetCity . $targetCountry . ";
//        " . $setTargeting . "
//        googletag.pubads().enableSingleRequest();
//        googletag.enableServices();
//    });
//";

$tmp_googletag_define = $params->get('googletag-define');
$googletag_target = 'googletag.pubads().setTargeting("keywords", "'. strtolower($doc->getMetaData('keywords')) .'");';
$googletag_define = substr_replace($tmp_googletag_define, $googletag_target, strripos($tmp_googletag_define, "googletag.pubads().enableSingleRequest();"), 0);

$script = $googletag_define;

$doc = JFactory::getDocument();
$doc->addScriptDeclaration($script);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_googleads', $params->get('layout', 'default'));
