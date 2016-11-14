{**
* Products simple filter: module for PrestaShop 1.4
*
* @author     zapalm <zapalm@ya.ru>
* @copyright (c) 2012-2016, zapalm
* @link      http://prestashop.modulez.ru/en/frontend-features/44-products-simple-filter.html The module's homepage
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
*}

<!-- MODULE: simplefilter block -->
{if !(isset($attr_vals) AND $attr_vals)}
        {* no products *}
        <div class="simplefilter-title" style="min-height:510px;">
            {l s="Filter:" mod="simplefilter"}
            {l s='No products to display.' mod='simplefilter'}
        </div>
{else}
    {* filtered products block *}
    <div id="featured-products_block_center" class="block products_block">
        <div class="block_content">
            <ul style="height:{$conf.SIMPLEFILTER_HEIGHT_ADJUST|intval}px;">
            {foreach from=$attr_vals item='attr_val' key='attr_key' name='homefeaturezProducts'}
                <li style="width: {$blockLiWidth|intval}px" class="ajax_block_product {if $smarty.foreach.homefeaturezProducts.first}first_item{elseif $smarty.foreach.homefeaturezProducts.last}last_item{else}item{/if} {if $smarty.foreach.homefeaturezProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.homefeaturezProducts.iteration%$nbItemsPerLine == 1}clear{/if} {if $smarty.foreach.homefeaturezProducts.iteration > ($smarty.foreach.homefeaturezProducts.total - ($smarty.foreach.homefeaturezProducts.total % $nbItemsPerLine))}last_line{/if}">
                    {if $conf.SIMPLEFILTER_TITLE}
                        <h5><a href="{$attr_val.link}&id_product_attribute={$attr_key}" title="{$attr_val.name}">{$attr_val.name|truncate:29:'...'|escape:'htmlall':'UTF-8'}</a></h5>
                    {/if}
                    {if $conf.SIMPLEFILTER_DESCR}
                        <div class="product_desc"><a href="{$attr_val.link}&id_product_attribute={$attr_key}" title="{l s='More' mod='simplefilter'}">{$attr_val.description_short|strip_tags|truncate:130:'...'}</a></div>
                    {/if}
                    {if $attr_val.attribute_image}
                        {assign var='image' value="`$attr_val.id_product`-`$attr_val.attribute_image`"}
                    {else}
                        {assign var='image' value="`$attr_val.id_product`-`$attr_val.id_image`"}
                    {/if}
                    <a href="{$attr_val.link}&id_product_attribute={$attr_key}" title="{$attr_val.name|escape:html:'UTF-8'}" class="product_image" style="background-image: url({$link->getImageLink($attr_val.link_rewrite, $image, 'home')}); background-position: center center; background-repeat: no-repeat; background-size: contain; width: {$blockLiWidth-4}px;"></a>

                    {if $conf.SIMPLEFILTER_PRICE}
                        <p class="price_container">
                            <span class="price">{convertPrice price=$attr_val.price}</span>
                        </p>
                    {/if}
                    {if $conf.SIMPLEFILTER_VIEW}
                        <a class="button" href="{$attr_val.link}&id_product_attribute={$attr_key}" title="{l s='View' mod='simplefilter'}">{l s='View' mod='simplefilter'}</a>
                    {/if}

                    {if $conf.SIMPLEFILTER_CART}
                        {if ($attr_val.quantity > 0 OR $attr_val.allow_oosp) AND ($attr_val.customizable != 2)}
                            <a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$attr_val.id_product}_{$attr_key}" href="{$base_dir}cart.php?qty=1&amp;id_product={$attr_val.id_product}&amp;id_product_attribute={$attr_key}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='simplefilter'}">{l s='Add to cart' mod='simplefilter'}</a>
                        {else}
                            <span class="exclusive">{l s='Add to cart' mod='simplefilter'}</span>
                        {/if}
                    {/if}
                </li>
            {/foreach}
            </ul>
        </div>
    </div>
{/if}
<!-- /MODULE: simplefilter block -->
