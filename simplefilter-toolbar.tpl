{**
* Products simple filter: module for PrestaShop 1.4
*
* @author     zapalm <zapalm@ya.ru>
* @copyright (c) 2012-2016, zapalm
* @link      http://prestashop.modulez.ru/en/frontend-features/44-products-simple-filter.html The module's homepage
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
*}

<!-- MODULE: simplefilter toolbar -->
<div class="simplefilter-toolbar" id="hfilter-toolbar">
    {* buttons for combinations *}
    {foreach from=$attr_groups key='group' item='attrs'}
        {assign var="attr_group_id" value="flt_`$attr_group_ids.$group`"}
        <div class="simplefilter-buttons-group">
        {foreach from=$attrs key='a' item='id'}
            {if $attr_colors_groups.$group} {* is a color atrribute group *}
                <a class="color_pick simplefilter-color{if $smarty.get.$attr_group_id == $id} simplefilter-selected{/if}"  href="{$module->getFilterOptionUrl($attr_group_id, $id)}" style="background: {$attr_colors.$group.$a};" title="{$a}"></a>
            {else}
                {* sex buttons *}
                <a class="{if $a=='man'}simplefilter-sex-man{else}{if $a=='woman'}simplefilter-sex-woman{else}simplefilter-other{/if}{/if}{if $smarty.get.$attr_group_id == $id} simplefilter-selected{/if}" href="{$module->getFilterOptionUrl($attr_group_id, $id)}" title="{$a}">{if $group!="sex"}{$a}{/if}</a>
            {/if}
        {/foreach}
        </div>
    {/foreach}

    {* manufacturers *}
    {if $conf.SIMPLEFILTER_MAN}
        {foreach from=$manufacturers key=id item=man}
            <div class="simplefilter-buttons-group">
                <a class="manufacturer{if $smarty.get.flt_man == $id} simplefilter-selected{/if}"  href="{$module->getFilterOptionUrl('flt_man', $id)}">{$man}</a>
            </div>
        {/foreach}
    {/if}

    {* prices *}
    <div class="simplefilter-buttons-group">
        {foreach from=$prices item=price}
            <a class="price simplefilter-price{if isset($smarty.get.flt_price) && $smarty.get.flt_price == $price} simplefilter-selected{/if}"  href="{$module->getFilterOptionUrl('flt_price', $price)}">{convertPrice price=$price}</a>
        {/foreach}
    </div>

    {* reset group *}
    <div class="simplefilter-buttons-group">
        <a class="simplefilter-reset-image" href="{$module->getFilterResetUri()}" title="{l s="Reset" mod="simplefilter"}"></a>
        <a class="simplefilter-reset" href="{$module->getFilterResetUri()}">{l s="Reset" mod="simplefilter"}</a>
    </div>

    {* pagination *}
    <div id="pagination" class="pagination simplefilter-buttons-group last-buttons-group">
        <ul class="pagination">
        {if isset($smarty.get.flt_page) && $smarty.get.flt_page}
            {assign var='p' value=$smarty.get.flt_page}
        {else}
            {assign var='p' value=1}
        {/if}

        {section name=pagination start=1 loop=$nbPages+1 step=1}
            {if $p == $smarty.section.pagination.index}
                <li class="current"><span>{$p|escape:'htmlall':'UTF-8'}</span></li>
            {else}
                <li><a href="{$module->getFilterOptionUrl('flt_page', $smarty.section.pagination.index)}">{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</a></li>
            {/if}
        {/section}
        </ul>
    </div>
</div>
<!-- /MODULE: simplefilter toolbar -->
