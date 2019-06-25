<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fixboproductname extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fixboproductname';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Saeed Sattar Beglou';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fixed BackOffice product name');
        $this->description = $this->l('Fixed BackOffice product name for disabled languages');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $this->fixAllProductName();

        return parent::install() &&
            $this->registerHook('actionProductUpdate');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookActionProductUpdate($params)
    {
        if (empty($params['id_product'])) {
            return;
        }

        $id_shop = $this->context->shop->id;
        $id_default_lang = Configuration::get('PS_LANG_DEFAULT', null, null, $id_shop);
        $this->fixProductName($params['id_product'], $id_default_lang, $id_shop);
    }

    public function fixProductName($id_product, $id_default_lang, $id_shop)
    {
        $disabled_lang_id = $this->getDisabledLangs($id_shop);

        $sql = "UPDATE `" . _DB_PREFIX_ . "product_lang` a
                INNER JOIN (SELECT b.name, b.id_lang FROM `" . _DB_PREFIX_ . "product_lang` b WHERE b.id_lang = ". $id_default_lang ." AND b.id_product = ". $id_product ." AND id_shop = " . $id_shop . ") t ON
                a.id_product = " . $id_product . " AND a.id_lang IN ('". $disabled_lang_id ."') AND id_shop = " . $id_shop . "
                SET a.name=t.name";

        return Db::getInstance()->Execute($sql);
    }

    public function getDisabledLangs($id_shop)
    {
        $id_langs = Language::getLanguages(false, $id_shop);

        $disabledLangs = array();
        foreach ($id_langs as $id_lang) {
            if ($id_lang['active'] == 0) {
                array_push($disabledLangs, $id_lang['id_lang']);
            }
        }

        return implode("','", $disabledLangs);
    }

    public function fixAllProductName()
    {
        $id_shop = $this->context->shop->id;
        $id_default_lang = Configuration::get('PS_LANG_DEFAULT', null, null, $id_shop);

        $products = Product::getProducts($id_default_lang, 1, 0, 'name', 'ASC', false, false);
        foreach ($products as $key) {
            $this->fixProductName($key['id_product'], $id_default_lang ,$id_shop);
        }
    }
}
