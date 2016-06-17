[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{assign var="stylepath" value=$oViewConf->getModuleUrl("asign/asign_yellowcube", "out/css/ycubestyles.css")}]
<link rel="stylesheet" type="text/css" href="[{$stylepath}]">
[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <input type="hidden" name="oxidCopy" value="[{$oxid}]" />
    <input type="hidden" name="cl" value="asign_yellowcube_main" />
    <input type="hidden" name="language" value="[{$actlang}]" />
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="asign_yellowcube_main" />
<input type="hidden" name="fnc" value="" id="fnc" />
<input type="hidden" name="oxid" value="[{$oxid}]" />
<input type="hidden" name="voxid" value="[{$oxid}]" />
<input type="hidden" name="oxparentid" value="[{$oxparentid}]" />
<input type="hidden" name="editval[asign_yellowcube__oxid]" value="[{$oxid}]" />
<input type="hidden" name="language" value="[{$actlang}]" />

    [{assign var="lastupdate" value=$oView->getLastUpdateDate()}]
    [{if $blStatus}]
        <div class="messagebox"><p class="success">[{oxmultilang ident="ASIGN_MESSAGE_LAST_UPDATE_ON"}] [{$lastupdate}]</p></div>
        <script type="text/javascript">top.basefrm.list.location.reload()</script>
    [{/if}]
<table cellspacing="0" cellpadding="0" border="0" width="90%">     
    [{block name="asign_yellowcube_overview_block"}]                    
        <tr>
            <td width="40%" valign="top">
                <div class="logBox capBox">
                    <table border = "0" cellpadding = "5" cellspacing = "0" width="98%">
                        <thead>
                            <td colspan=3>
                                <h3 class="noborder" style="margin-top:-2px;">[{oxmultilang ident="ASIGN_OVERVIEW_ADDITIONAL"}]</h3>
                            </td>
                        </thead>                                   
                        [{foreach from=$aInventory key="label" item="value"}]
                            [{if $value ne ""}]
                                <tr>
                                    <td><strong>[{$label}]: </strong></td>
                                    <td>
                                        [{if $label eq "StockType"}]
                                            [{assign var="value" value=$oView->getStockText($value)}]
                                        [{/if}]
                                        [{$value}]
                                    </td>
                                </tr>
                            [{/if}]
                        [{/foreach}]
                    </table>
                </div>
            </td>                        
            <td width="30%" valign="top">
                <div class="logBox capBox">
                    <table border = "0" cellpadding = "5" cellspacing = "0" width="100%">
                        <thead>
                            <td colspan=3>
                                <h3 class="noborder" style="margin-top:-2px;">[{oxmultilang ident="ASIGN_INVENTORY_UPDATE"}]</h3>
                            </td>
                        </thead>
                        <tr>
                            <td>
                                [{oxmultilang ident="ASIGN_OVERVIEW_INVENTORY_MESSAGE"}]<br /><br />
                                [{oxmultilang ident="ASIGN_OVERVIEW_INVENTORY_MESSAGE_1"}]
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>[{oxmultilang ident="ASIGN_INVENTORY_UPDATEDLAST"}]: </strong>[{$lastupdate}]
                            </td>
                        </tr>
                        <tr>
                            <td><br />
                                <button type="submit" name="refresh" id="irefresh" onclick="document.myedit.fnc.value='refresh'">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_REFRESH"}]</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    [{/block}]  
</table><br />
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
