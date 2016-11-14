<?php
/**
 * Products simple filter: module for PrestaShop 1.4
 *
 * @author     zapalm <zapalm@ya.ru>
 * @copyright (c) 2012-2016, zapalm
 * @link      http://prestashop.modulez.ru/en/frontend-features/44-products-simple-filter.html The module's homepage
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once _PS_ROOT_DIR_ . '/header.php';

global $smarty;

$moduleName = 'simplefilter';
if (Module::isInstalled($moduleName) && ($module = Module::getInstanceByName($moduleName)) !== false && $module->active) {
    $conf           = Configuration::getMultiple(array_keys($module::$conf));
    $blockWidth     = (int)$conf['SIMPLEFILTER_WIDTH_ADJUST'];
    $nbItemsPerLine = (int)$conf['SIMPLEFILTER_COLS'];
    $blockLiWidth   = ceil($blockWidth / $nbItemsPerLine) - 1;

    $smarty->assign(array(
        'conf'              => $conf,
        'nbItemsPerLine'    => $nbItemsPerLine,
        'blockLiWidth'      => $blockLiWidth,
    ));

    $smarty->display(_PS_ROOT_DIR_ . '/modules/simplefilter/simplefilter.tpl');
}

include(dirname(__FILE__) . '/../../footer.php');