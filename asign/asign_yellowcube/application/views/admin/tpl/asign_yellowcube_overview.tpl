[{include file="headitem.tpl" title="GENERAL_YELLOWCUBE_HEADING"}]
<style type="text/css">td.listheader{padding:5px !important;}td.listitem{padding:3px 5px;}</style>
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="asign_yellowcube_overview">
    <input type="hidden" name="updatelist" value="1">
</form>
<div class="export">[{oxmultilang ident="GENERAL_YELLOWCUBE_HEADING"}]</div><br />
    <form name="showlist" id="showlist" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="asign_yellowcube_overview" />
        <input type="hidden" name="fnc" value="refresh" />
        <button type="submit" name="refresh" id="irefresh" style="float:right;margin-bottom: 10px;">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_REFRESH"}]</button>
        <table cellspacing="0" cellpadding="0" border="0" width="100%">            
            <tr>
                [{block name="admin_list_order_sorting"}]
                    <td class="listheader first">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_YCARTICLENO"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_ARTICLENO"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_ARTICLEDESC"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_PLANT"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_STORAGELOC"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_STOCKTYPE"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_QUANTITYISO"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_QUANTITYUOM"}]</a>
                    </td>
                    <td class="listheader">
                        <a href="Javascript:;" class="listheader">[{oxmultilang ident="ASIGN_COLUMN_TIMESTAMP"}]</a>
                    </td>
                [{/block}]
            </tr>
        
            [{assign var="blWhite" value=""}]
            [{assign var="_cnt" value=0}]
            [{foreach from=$aInventory item="item"}]
                [{assign var="_cnt" value=$_cnt+1}]
                <tr id="row.[{$_cnt}]">
                    [{block name="admin_list_order_item"}]
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxycarticlenr}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxarticlenr}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxartdesc}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxplant}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxstorageloc}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.stocktype}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxquantityiso}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxquantityuom}]</a>
                        </td>
                        <td class="listitem[{$blWhite}]">
                            <a href="Javascript:;" class="listitem[{$blWhite}]">[{$item.oxtimestamp}]</a>
                        </td>
                    [{/block}]
                </tr>
                [{if $blWhite == "2"}]
                    [{assign var="blWhite" value=""}]
                [{else}]
                    [{assign var="blWhite" value="2"}]
                [{/if}]
            [{/foreach}]
            
            [{include file="pagenavisnippet.tpl" colspan="8"}]    
        </table>
    </form>
</body>
</html>
