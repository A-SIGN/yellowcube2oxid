[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]
<style type="text/css">.listheader{padding-left: 5px;}</style>
[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
    [{/if}]
}
//-->
</script>
<div class="export">[{oxmultilang ident="GENERAL_YELLOWCUBE_HEADING"}]</div>
<div id="liste">
    <form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
[{include file="_formparams.tpl" cl="asign_yellowcube_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_yellowcube_list_colgroup"}]
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="30%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
        [{block name="admin_yellowcube_list_filter"}]  
            <td valign="top" class="listfilter">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="20" maxlength="128" name="where[asign_ycinventory][oxshopid]" value="[{$where.asign_ycinventory.oxshopid}]" />
                </div></div>
            </td>
            <td valign="top" class="listfilter">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="20" maxlength="128" name="where[asign_ycinventory][oxycarticlenr]" value="[{$where.asign_ycinventory.oxycarticlenr}]" />
                </div></div>
            </td>                      
            <td valign="top" class="listfilter">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="20" maxlength="128" name="where[asign_ycinventory][oxarticlenr]" value="[{$where.asign_ycinventory.oxarticlenr}]" />
                </div></div>
            </td>
            <td valign="top" class="listfilter">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="50" maxlength="128" name="where[asign_ycinventory][oxartdesc]" value="[{$where.asign_ycinventory.oxartdesc}]" />
                </div></div>
            </td>            
            <td valign="top" class="listfilter" align="left" colspan="2">
                <div class="r1"><div class="b1">
                <input class="listedit" type="text" size="25" maxlength="128" name="where[asign_ycinventory][oxtimestamp]" value="[{$where.asign_ycinventory.oxtimestamp}]" />
                <button class="listedit" type="submit" name="submitit" onClick="Javascript:document.search.lstrt.value=0;">[{oxmultilang ident="GENERAL_SEARCH"}]</button>
                </div></div>
            </td>
        [{/block}]
    </tr>
    <tr>
        [{block name="admin_asign_ycinventory_listsorting"}]            
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'asign_ycinventory', 'oxshopid', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_SHOPID"}]</a></td>
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'asign_ycinventory', 'oxycarticlenr', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_YCARTICLENO"}]</a></td>            
            <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'asign_ycinventory', 'oxarticlenr', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_ARTICLENO"}]</a></td>
            <td class="listheader" height="15" ><a href="Javascript:top.oxid.admin.setSorting( document.search, 'asign_ycinventory', 'oxartdesc', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_ARTICLEDESC"}]</a></td>            
            <td class="listheader" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'asign_ycinventory', 'oxtimestamp', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_TIMESTAMP"}]</a></td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">
        [{block name="admin_conversation_list_item"}]
            [{if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3}]
            [{else}]
                [{assign var="listclass" value=listitem$blWhite}]
            [{/if}]
            [{if $listitem->getId() == $oxid}]
                [{assign var="listclass" value=listitem4}]
            [{/if}]            
            <td valign="top" class="[{$listclass}]" height="15" style="padding-left:10px;"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->asign_ycinventory__oxid->value}]');" class="[{$listclass}]">[{$oView->getShopName($listitem->asign_ycinventory__oxid->value)}]</a></div></td>
            <td valign="top" class="[{$listclass}]" height="15" style="padding-left:10px;"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->asign_ycinventory__oxid->value}]');" class="[{$listclass}]">[{$listitem->asign_ycinventory__oxycarticlenr->value}]</a></div></td>
            <td valign="top" class="[{$listclass}]" height="15" style="padding-left:10px;"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->asign_ycinventory__oxid->value}]');" class="[{$listclass}]">[{$listitem->asign_ycinventory__oxarticlenr->value}]</a></div></td>
            <td valign="top" class="[{$listclass}]" height="15" style="padding-left:10px;"> <div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->asign_ycinventory__oxid->value}]');" class="[{$listclass}]">[{$listitem->asign_ycinventory__oxartdesc->value}]</a></div></td>            
            <td valign="top" class="[{$listclass}]" height="15" style="padding-left:10px;"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->asign_ycinventory__oxid->value}]');" class="[{$listclass}]">[{$listitem->asign_ycinventory__oxtimestamp->value}]</a></div></td>
            <td align="right" class="[{$listclass}]">
            [{if !$readonly}]
            <a href="Javascript:top.oxid.admin.deleteThis('[{$listitem->asign_ycinventory__oxid->value}]');" class="delete" id="del.[{$_cnt}]" title="" [{include file="help.tpl" helpid=item_delete}]></a>
            [{/if}]
            </td>
        [{/block}]
    </tr>
[{if $blWhite == "2"}]
    [{assign var="blWhite" value=""}]
[{else}]
    [{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="10"}]
</table>
</form>
</div>
[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{oxmultilang ident="ASIGN_YELLOWCUBE"}]";
    parent.parent.sMenuSubItem = "Overview";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
