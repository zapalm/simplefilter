<?php
/**
 * Products simple filter: module for PrestaShop 1.4
 *
 * @author     zapalm <zapalm@ya.ru>
 * @copyright (c) 2012-2016, zapalm
 * @link      http://prestashop.modulez.ru/en/frontend-features/44-products-simple-filter.html The module's homepage
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @version v0.8 alpha - development version!
 */
class SimpleFilter extends Module
{
    public static $conf = array(
        'SIMPLEFILTER_NBR'           => 10,
        'SIMPLEFILTER_CATALOG'       => 1,
        'SIMPLEFILTER_TITLE'         => 1,
        'SIMPLEFILTER_DESCR'         => 1,
        'SIMPLEFILTER_VIEW'          => 1,
        'SIMPLEFILTER_CART'          => 1,
        'SIMPLEFILTER_PRICE'         => 1,
        'SIMPLEFILTER_COLS'          => 4,
        'SIMPLEFILTER_MAN'           => 1,
        'SIMPLEFILTER_STOCK'         => 1,
        'SIMPLEFILTER_WIDTH_ADJUST'  => 535, // ширина для блока из 4 колонок для стандартной темы Prestashop
        'SIMPLEFILTER_HEIGHT_ADJUST' => 343, // высота для блока из 4 колонок в 1 ряд для стандартной темы Prestashop
    );

    public function __construct() {
        $this->name             = 'simplefilter';
        $this->version          = '0.8';
        $this->tab              = 'front_office_features';
        $this->author           = 'zapalm';
        $this->need_instance    = 0;

        parent::__construct();

        $this->displayName = $this->l('Products simple filter');
        $this->description = $this->l('Allows filtering products by attributes and by other features.');
    }

    public function install() {
        foreach (self::$conf as $c => $v) {
            Configuration::updateValue($c, $v);
        }

        return parent::install()
            && $this->registerHook('leftColumn')
            && $this->registerHook('header')
        ;
    }

    public function uninstall() {
        foreach (self::$conf as $c => $v) {
            Configuration::deleteByName($c);
        }

        return parent::uninstall();
    }

    public function getContent() {
        global $cookie;

        $output = '<h2>' . $this->displayName . '</h2>';

        if (Tools::isSubmit('submit_save')) {
            $result = 1;
            foreach (self::$conf as $c => $v) {
                $result &= Configuration::updateValue($c, (int)Tools::getValue($c));
            }

            $output .= ($result
                ? $this->displayConfirmation($this->l('Settings updated'))
                : $this->displayError($this->l('Some setting not updated'))
            );
        }

        $cols           = Configuration::getMultiple(array_keys(self::$conf));
        $categories     = Category::getHomeCategories($cookie->id_lang, false);
        $rootCategory   = Category::getRootCategory($cookie->id_lang);
        $output        .= '
            <fieldset style="width:900px">
                <legend><img src="' . _PS_ADMIN_IMG_ . 'cog.gif" alt="" title="" />' . $this->l('Settings') . '</legend>
                <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                    <label>' . $this->l('The filter page URL') . '</label>
                    <div class="margin-form">
                        ' . $this->getFilterResetUri() .'
                        <p class="clear">' . $this->l('Use this URI to create a link to the filter page.') . '</p>
                    </div>
                    <label>' . $this->l('Number of product to display') . '</label>
                    <div class="margin-form">
                        <input type="text" size="5" name="SIMPLEFILTER_NBR" value="' . ($cols['SIMPLEFILTER_NBR'] ? $cols['SIMPLEFILTER_NBR'] : '10') . '" />
                        <p class="clear">' . $this->l('The number of products to display on each page (default: 10).') . '</p>
                    </div>
                    <label>' . $this->l('Category of products to display') . '</label>
                    <div class="margin-form">
                        <select name="SIMPLEFILTER_CATALOG">
                            <option value="' . $rootCategory->id . '" ' . ($cols['SIMPLEFILTER_CATALOG'] == $rootCategory->id ? 'selected=1' : '') . '>' . $rootCategory->name . '</option>';
                                foreach ($categories as $i => $c) {
                                    $output .= '<option value="' . $c['id_category'] . '" ' . ($cols['SIMPLEFILTER_CATALOG'] == $c['id_category'] ? 'selected=1' : '') . '>' . $c['name'] . '</option>';
                                }
                                $output .= '
                        </select>
                        <p class="clear">' . $this->l('Choose category of products, which will be shown (default : Home category).') . '</p>
                    </div>
                    <label>' . $this->l('Show title of a product') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_TITLE" value="1" ' . ($cols['SIMPLEFILTER_TITLE'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want to show a product title.') . '</p>
                    </div>
                    <label>' . $this->l('Show description of a product') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_DESCR" value="1" ' . ($cols['SIMPLEFILTER_DESCR'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want to show a product description.') . '</p>
                    </div>
                    <label>' . $this->l('Show a "View" button') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_VIEW" value="1" ' . ($cols['SIMPLEFILTER_VIEW'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want to show a "View" button.') . '</p>
                    </div>
                    <label>' . $this->l('Show a "Add to cart" button') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_CART" value="1" ' . ($cols['SIMPLEFILTER_CART'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want to show a "Add to cart" button. If prestashop catalog mode is enable than the button will not display.') . '</p>
                    </div>
                    <label>' . $this->l('Show product price') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_PRICE" value="1" ' . ($cols['SIMPLEFILTER_PRICE'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want to show product price.') . '</p>
                    </div>
                    <label>' . $this->l('Number of columns to display') . '</label>
                    <div class="margin-form">
                        <input type="text" size="1" name="SIMPLEFILTER_COLS" value="' . ($cols['SIMPLEFILTER_COLS'] ? $cols['SIMPLEFILTER_COLS'] : '4') . '" />
                        <p class="clear">' . $this->l('The number of columns of products to display (default: 4).') . '</p>
                    </div>
                    <label>' . $this->l('Block module height adjust') . '</label>
                    <div class="margin-form">
                        <input type="text" size="3" name="SIMPLEFILTER_HEIGHT_ADJUST" value="' . ($cols['SIMPLEFILTER_HEIGHT_ADJUST'] ? $cols['SIMPLEFILTER_HEIGHT_ADJUST'] : '0') . '" /> px.
                        <p class="clear">' . $this->l('You should input number of pixels to adjust height of the block.') . '</p>
                    </div>
                    <label>' . $this->l('Block module width adjust') . '</label>
                    <div class="margin-form">
                        <input type="text" size="3" name="SIMPLEFILTER_WIDTH_ADJUST" value="' . ($cols['SIMPLEFILTER_WIDTH_ADJUST'] ? $cols['SIMPLEFILTER_WIDTH_ADJUST'] : '0') . '" /> px.
                        <p class="clear">' . $this->l('You should input number of pixels to adjust width of the block.') . '</p>
                    </div>
                    <label>' . $this->l('Filter by manufacturer') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_MAN" value="1" ' . ($cols['SIMPLEFILTER_MAN'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want to filter by manufacturers.') . '</p>
                    </div>
                    <label>' . $this->l('Filter by stock available') . '</label>
                    <div class="margin-form">
                        <input type="checkbox" name="SIMPLEFILTER_STOCK" value="1" ' . ($cols['SIMPLEFILTER_STOCK'] ? 'checked="checked"' : '') . ' />
                        <p class="clear">' . $this->l('Check it, if you want show only products that is stock available.') . '</p>
                    </div>

                    <center><input type="submit" name="submit_save" value="' . $this->l('Save') . '" class="button" /></center>
                </form>
            </fieldset>
            <br class="clear">
        ';

        return $output;
    }

    public function getFilterOptionUrl($key = null, $value = null) {
        $query = array();
        foreach ($_GET as $k => $v) {
            $p = explode('_', $k);
            if (count($p) == 2 && $p[0] == 'flt' && $p[1] != 'page') {
                $query[$k] = $v;
            }
        }

        if ($key !== null && $value !== null) {
            $query[$key] = $value;
        }

        return $this->getFilterResetUri() . (count($query) === 0 ? '' : '?' . http_build_query($query));
    }

    public function getFilterResetUri() {
        return __PS_BASE_URI__ . 'modules/' . $this->name . '/index.php';
    }

    private function filterByRequest() {
        global $cookie;

        $conf    = Configuration::getMultiple(array_keys(SimpleFilter::$conf));
        $link    = new Link();
        $nbPages = 1;

        // id категории товаров по которой производится фильтрация
        $flt_category = Configuration::get('SIMPLEFILTER_CATALOG') ? Configuration::get('SIMPLEFILTER_CATALOG') : $conf['SIMPLEFILTER_CATALOG'];

        // парсим get-запрос и формируем массив [id атрибутной группы => id атрибута]
        // параметр flt_price пропускаем
        $exist_groups = array();
        foreach ($_GET as $k => $v) {
            $p = explode('_', $k);
            if (sizeof($p) == 2 && $p[1] != 'price' && $p[1] != 'man' && $p[1] != 'page' && $p[0] == 'flt') {
                $exist_groups[(int)$p[1]] = (int)$v;
            }
        }

        $flt_price = isset($_GET['flt_price']) ? (float)Tools::getvalue('flt_price') : false;
        $flt_man   = (int)Tools::getvalue('flt_man');
        $flt_page  = (int)Tools::getValue('flt_page', 1);

        // выборка товаров, входящих в фильтр по категории
        $sql = '
            SELECT c.id_category, cl.name as category_name, p.`id_product`, pl.`name` as product_name
            FROM `' . _DB_PREFIX_ . 'category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = c.`id_category`)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`id_category` = cp.`id_category` AND cl.`id_lang` = ' . intval($cookie->id_lang) . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_lang` = ' . intval($cookie->id_lang) . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (pl.`id_product` = p.`id_product`)
            WHERE p.`id_product` = cp.`id_product` AND c.`id_category` = ' . intval($flt_category);
        $product_category = Db::getInstance()->executeS($sql);

        // формируем массив товаров из категории, к которой применяется фильтр
        $cats = array();
        if (count($product_category)) {
            $cats_incl = '';
            $first = true;
            foreach ($product_category as $pc) {
                $cats[$pc['id_product']]['id_category'] = $pc['id_category'];
                $cats[$pc['id_product']]['category_name'] = $pc['category_name'];
                $cats_incl .= ($first ? '' : ',') . $pc['id_product'];
                $first = false;
            }
        }

        // выбираем все товары с комбинациями, которые принадлежат категории, к которой применяется фильтр
        $sql = '
        SELECT i.`id_image`, pai.id_image as attribute_image, pl.`name` as product_name, pl.`link_rewrite`, pl.`description_short`, p.`customizable`, p.`out_of_stock`, p.`price` as product_price, pa.`price` as impact_price, p.`reference` as product_reference, pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, a.`color` as color_val, m.`name` as manufacturer, m.`id_manufacturer`
        FROM `' . _DB_PREFIX_ . 'product_attribute` pa
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . intval($cookie->id_lang) . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . intval($cookie->id_lang) . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (pa.`id_product` = p.`id_product`)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON (pai.`id_product_attribute` = pa.`id_product_attribute`)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pa.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . intval($cookie->id_lang) . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (p.`id_product` = i.`id_product` AND i.`cover` = 1)
        LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
        WHERE pa.`id_product` in(' . ($cats_incl ? $cats_incl : 0) . ') ORDER BY p.`id_product`
    ';
        $comb = Db::getInstance()->executeS($sql);

        // формируем массив товаров $attr_vals с атрибутами в удобном для отображения в шаблоне виде
        $prices = $manufacturers = $flt_products_attrs = $attr_vals = $flt_attr_vals = $attr_groups = $attr_group_ids = $attr_colors = $attr_colors_groups = array();
        if (count($comb)) {
            foreach ($comb as $c) {
                if ($cats[$c['id_product']]['id_category']) {
                    // формируем массив [id атрибутной группы => id комбинации]
                    if (isset($exist_groups[$c['id_attribute_group']]) && $exist_groups[$c['id_attribute_group']] == $c['id_attribute']) {
                        $flt_products_attrs[$c['id_attribute_group']][] = $c['id_product_attribute'];
                    }
                    $price = Product::getPriceStatic($c['id_product'], false, $c['id_product_attribute']);
                    $attr_vals[$c['id_product_attribute']]['id_product'] = $c['id_product'];
                    $attr_vals[$c['id_product_attribute']][$c['group_name']] = $c['attribute_name'];
                    $attr_vals[$c['id_product_attribute']]['price'] = $price;
                    $attr_vals[$c['id_product_attribute']]['name'] = $c['product_name'];
                    $attr_vals[$c['id_product_attribute']]['id_manufacturer'] = $c['id_manufacturer'];
                    $attr_vals[$c['id_product_attribute']]['reference'] = $c['reference'] ? $c['reference'] : $c['product_reference'];
                    $attr_vals[$c['id_product_attribute']]['id_image'] = $c['id_image'];
                    $attr_vals[$c['id_product_attribute']]['attribute_image'] = $c['attribute_image'];
                    $attr_vals[$c['id_product_attribute']]['link_rewrite'] = $c['link_rewrite'];
                    $attr_vals[$c['id_product_attribute']]['quantity'] = Product::getQuantity($c['id_product'], $c['id_product_attribute']);
                    $attr_vals[$c['id_product_attribute']]['allow_oosp'] = Product::isAvailableWhenOutOfStock($c['out_of_stock']);
                    $attr_vals[$c['id_product_attribute']]['customizable'] = $c['customizable'];
                    $attr_vals[$c['id_product_attribute']]['description_short'] = $c['description_short'];
                    $attr_vals[$c['id_product_attribute']]['link'] = $link->getProductLink($c['id_product'], $c['link_rewrite']);
                    $attr_vals[$c['id_product_attribute']]['default_on'] = $c['default_on'];
                    $products_ids[$c['id_product']] = $c['id_product_attribute'];
                    $attr_colors[$c['group_name']][$c['attribute_name']] = $c['color_val'];
                    $attr_colors_groups[$c['group_name']] = $c['is_color_group'];
                    $prices[] = $price;
                    if ($c['manufacturer']) {
                        $manufacturers[$c['id_manufacturer']] = $c['manufacturer'];
                    }
                    $attr_groups[$c['group_name']][$c['attribute_name']] = $c['id_attribute'];
                    $attr_group_ids[$c['group_name']] = $c['id_attribute_group'];

                    if (intval($c['is_color_group'])) {
                        $attr_vals[$c['id_product_attribute']]['color_val'] = $c['color_val'];
                    }
                }
            }

            // сформируем уникальный массив цен
            if ($prices) {
                $prices = array_unique($prices);
            }

            if ($manufacturers) {
                $manufacturers = array_unique($manufacturers);
            }

            // формируем массив отфильтрованных товаров $flt_attr_vals
            $flt_attr_vals_tmp = $flt_attr_vals = array();
            if ($flt_products_attrs) {
                foreach ($flt_products_attrs as $group => $combs) {
                    foreach ($combs as $k => $comb) {
                        $flt_attr_vals_tmp[$group][$comb] = $attr_vals[$comb];
                    }
                }

                $first = true;
                foreach ($flt_attr_vals_tmp as $group => $combs) {
                    if ($first) {
                        $flt_attr_vals = $combs;
                        $first = false;
                        continue;
                    }
                    $flt_attr_vals = array_intersect_assoc($combs, $flt_attr_vals);
                }
            }
            // если параметры фильтрации были переданы (цена не учитывается, так как ее нет в $exist_groups),
            // но $flt_products_attrs пустой, то нет товаров соответствующих этим параметрам фильтрации
            elseif ($exist_groups) {
                $flt_attr_vals = array();
            }
            else { //если параметров фильтрации не было передано, то результат - все товары
                $flt_attr_vals = $attr_vals;
            }

            // фильтруем по цене, если она задана
            if ($flt_price !== false) {
                // очистим, используемую ранее переменную
                $flt_attr_vals_tmp = array();

                // в зависимости от переданного get-запроса, у нас может быть массив
                // с отфильтрованными товарами или же нет
                $flt_attr_vals = $flt_attr_vals ? $flt_attr_vals : $attr_vals;

                // выбираем только те, где цена товара соответствует, переданному в запросе
                foreach ($flt_attr_vals as $group => $comb) {
                    if ($flt_attr_vals[$group]['price'] == $flt_price) {
                        $flt_attr_vals_tmp[$group] = $flt_attr_vals[$group];
                    }
                }
                $flt_attr_vals = $flt_attr_vals_tmp;
            }

            // фильтруем по производителю, если он задан
            if ($flt_man) {
                // очистим, используемую ранее переменную
                $flt_attr_vals_tmp = array();

                // в зависимости от переданного get-запроса, у нас может быть массив
                // с отфильтрованными товарами или же нет
                $flt_attr_vals = $flt_attr_vals ? $flt_attr_vals : $attr_vals;

                // выбираем только те, где производитель соответствует, переданному в запросе
                if ($flt_attr_vals) {
                    foreach ($flt_attr_vals as $group => $comb) {
                        if ($flt_attr_vals[$group]['id_manufacturer'] == $flt_man) {
                            $flt_attr_vals_tmp[$group] = $flt_attr_vals[$group];
                        }
                    }
                    $flt_attr_vals = $flt_attr_vals_tmp;
                }
            }

            // фильтруем по доступности на складе, если опция в настройках задана
            if (Configuration::get('SIMPLEFILTER_STOCK')) {
                // очистим, используемую ранее переменную
                $flt_attr_vals_tmp = array();

                // в зависимости от переданного get-запроса, у нас может быть массив
                // с отфильтрованными товарами или же нет
                $flt_attr_vals = $flt_attr_vals ? $flt_attr_vals : $attr_vals;

                // выбираем только с положительным остатком на складе
                foreach ($flt_attr_vals as $group => $comb) {
                    if ($flt_attr_vals[$group]['quantity'] >= 1) {
                        $flt_attr_vals_tmp[$group] = $flt_attr_vals[$group];
                    }
                }
                $flt_attr_vals = $flt_attr_vals_tmp;
            }

            // убираем товары, имеющих одинаковый вид - точнее оставляем по
            // одной комбинации на товар
            if ($flt_attr_vals) {
                $first       = 0;
                $filtered_id = null;
                foreach ($flt_attr_vals as $group => $comb) {
                    // первый пропустим, чтобы потом с ним сравнивать, пока не
                    // не перейдем к другому товару
                    if ($first++ == 0) {
                        $filtered_id = $flt_attr_vals[$group]['id_product'];
                        continue;
                    }

                    // если одинаковый с предыдущим, то удаляем его
                    if ($flt_attr_vals[$group]['id_product'] == $filtered_id) {
                        unset($flt_attr_vals[$group]);
                    }
                    else { // иначе - это другой товар
                        $filtered_id = $flt_attr_vals[$group]['id_product'];
                    }
                }
            }

            // фильтруем по количеству товаров на странице
            if ($flt_attr_vals) {
                $nbProduct      = Configuration::get('SIMPLEFILTER_NBR');
                $nbPages        = ceil(count($flt_attr_vals) / $nbProduct);
                $flt_pages      = array_chunk($flt_attr_vals, $nbProduct, true);
                $flt_attr_vals  = $flt_pages[$flt_page - 1];
            }
        }

        return array(
            'attr_vals'          => $flt_attr_vals,
            'attr_groups'        => $attr_groups,
            'attr_group_ids'     => $attr_group_ids,
            'attr_colors'        => $attr_colors,
            'attr_colors_groups' => $attr_colors_groups,
            'prices'             => $prices,
            'manufacturers'      => $manufacturers,
            'nbPages'            => $nbPages,
        );
    }

    function hookLeftColumn($params) {
        global $smarty;

        if (strpos($_SERVER['REQUEST_URI'], $this->getFilterResetUri()) === false) {
            return '';
        }

        $conf = Configuration::getMultiple(array_keys(self::$conf));

        $smarty->assign($this->filterByRequest());
        $smarty->assign(array(
            'module' => $this,
            'conf'   => $conf,
        ));

        return $this->display(__FILE__, 'simplefilter-toolbar.tpl');
    }

    function hookRightColumn($params) {
        return $this->hookLeftColumn($params);
    }

    function hookHeader() {
        Tools::addCSS($this->_path . 'simplefilter.css');
    }
}
