[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{assign var="stylepath" value=$oViewConf->getModuleUrl("asign/asign_yellowcube", "out/css/ycubestyles.css")}]
<link rel="stylesheet" type="text/css" href="[{$stylepath}]">
<script type="text/javascript">
    window.onload = function ()
    {
        [{if $updatelist == 1}]
            top.oxid.admin.updateList('[{$oxid}]');
        [{/if}]
        var oField = top.oxid.admin.getLockTarget();
        oField.onchange = oField.onkeyup = top.oxid.admin.unlockSave;
    }
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{assign var="isManual" value=$oViewConf->isFunctionalityEnabled('blYellowCubeOrderManualSend')}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <input type="hidden" name="oxidCopy" value="[{$oxid}]" />
    <input type="hidden" name="cl" value="asign_yellowcube_orders" />
    <input type="hidden" name="language" value="[{$actlang}]" />
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post" enctype="multipart/form-data">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="asign_yellowcube_orders" />
    <input type="hidden" name="fnc" value="" id="fnc" />
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <input type="hidden" name="voxid" value="[{$oxid}]" />
    <input type="hidden" name="language" value="[{$actlang}]" />

    [{if $mcreate eq "true"}]
        <div class="messagebox"><p class="success">[{oxmultilang ident="ASIGN_MESSAGE_YCORDER_CREATED"}]</p></div>
    [{/if}]
    [{if $mstatus eq "true"}]
        <div class="messagebox"><p class="success">[{oxmultilang ident="ASIGN_MESSAGE_YCORDER_UPDATED"}]</p></div><br />
    [{elseif $mstatus eq "false"}]
        <div class="messagebox"><p class="failerror">[{oxmultilang ident="ASIGN_MESSAGE_YCORDER_UPDATED_FAILED"}]</p></div>
    [{/if}]
    <div class="messagebox hideit" id="vmessage"><p class="failerror">[{oxmultilang ident="ASIGN_MESSAGE_WRONG_FILE_FORMAT"}]</p></div>
    [{if !$isManual}]
        <p>[{oxmultilang ident="ASIGN_MESSAGE_MANUAL_MODE_OFF"}]</p><hr>
    [{/if}]
    <table cellspacing="0" cellpadding="0" border="0">        
        <tr>
            [{if $isManual}]
                <td width="50%">
                    <fieldset>
                        <legend><strong>[{oxmultilang ident="ASIGN_LABEL_CREATEORDER"}]</strong></legend><br />
                        <table>
                            <tr>
                                <td class="listitem">[{oxmultilang ident="ASIGN_YCORDERS_ORDER_PDF"}]</td>
                                <td class="listitem"><input type="file" name="orderpdf" id="orderpdf" /></td>
                            </tr>
                            <tr>
                                <td class="listitem">[{oxmultilang ident="SHOP_MODULE_sYellowCubeYCLot"}]</td></td>
                                <td class="listitem"><input type="text" size="40" maxlength="255" name="sYCLot" id="sYCLot" value="" /></td>
                            </tr>
                            <tr>
                                <td class="listitem">[{oxmultilang ident="SHOP_MODULE_sYellowCubeLot"}]</td></td>
                                <td class="listitem"><input type="text" size="40" maxlength="255" name="sLot" id="sLot" value="" /></td>
                            </tr>
                            <tr>
                                <td class="listitem">[{oxmultilang ident="SHOP_MODULE_sYellowCubePMessage"}]</td></td>
                                <td class="listitem"><input type="text" size="40" maxlength="255" name="sMessage" id="sMessage" value="" /> </td>
                            </tr>
                            <tr>
                                <td class="listitem">[{oxmultilang ident="SHOP_MODULE_sYellowCubeRReason"}]</td></td>
                                <td class="listitem">
                                    <textarea name="sReason" id="sReason" cols="40" rows="5"></textarea>
                                </td>
                            </tr>
                        </table><br />
                        <button type="submit" name="create" id="icreate" onclick="return validateFileExt(document.myedit.orderpdf.value)">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_CREATE"}]</button>
                    </fieldset>
                </td>               
            [{/if}]
            
            [{if $ORDResponse ne ""}]
                <td [{if $isManual}]width="500"[{else}]width="40%"[{/if}] valign="top" style="padding-left:20px;">
                    <fieldset>
                        <legend><strong>[{oxmultilang ident="ASIGN_LABEL_CREATEORDER_RESPONSE"}]</strong></legend>
                        <table width="100%">
                            [{foreach from=$ORDResponse key="label" item="value"}]
                                <tr>
                                    <td class="listitem [{if $value eq 'E'}]failerror[{/if}]"><strong>[{$label}]:</strong></td>
                                    <td class="listitem [{if $value eq 'E'}]failerror[{/if}]">[{$value}]</td>
                                </tr>
                            [{/foreach}]
                        </table>
                    </fieldset>
                    <br />                    
                    [{if $ORDResponse.StatusCode eq 10 && $WABResponse.StatusCode ne 100}] 
                        <input type="hidden" name="referencenr" value="[{$ORDResponse.Reference}]" />
                        <button type="submit" name="status" id="istatus" onclick="document.myedit.fnc.value='prestatus'">[{oxmultilang ident="ASIGN_YCORDERS_BUTTON_STATUS"}]</button>
                    [{/if}]
                </td>
            [{/if}]
            
            [{if $WABResponse ne ""}]
                <td valign="top" width="40%" style="padding-left:20px;">
                    <fieldset>
                        <legend><strong>[{oxmultilang ident="ASIGN_LABEL_CREATEORDER_STATUS"}] (WAB)</strong></legend>
                        <table width="100%">
                            [{foreach from=$WABResponse key="label" item="value"}]
                                <tr>
                                    <td class="listitem [{if $value eq 'E'}]failerror[{/if}]"><strong>[{$label}]:</strong></td>
                                    <td class="listitem [{if $value eq 'E'}]failerror[{/if}]">[{$value}]</td>
                                </tr>
                            [{/foreach}]
                        </table>
                    </fieldset>
                    <br />
                    [{if $WABResponse.StatusCode == 100 && $GoodResponse eq ""}] 
                        <input type="hidden" name="referencenr" value="[{$WABResponse.Reference}]" />
                        <button type="submit" name="reply" id="ireply" onclick="document.myedit.fnc.value='status'">[{oxmultilang ident="ASIGN_YCORDERS_BUTTON_REPLY"}]</button>
                    [{/if}]
                </td>
            [{/if}]            
        </tr>
        <tr>
            <td>
                [{if $GoodResponse ne ""}]
                    <h3>[{oxmultilang ident="ASIGN_LABEL_CREATEORDER_REPLY"}]</h3>
                    <fieldset>
                        <legend><strong>[{$GoodResponse.title}]</strong></legend>
                        <table width="100%">
                            [{foreach from=$GoodResponse key="label" item="value"}]
                            [{if $label ne "title"}]
                            <tr>
                                <td class="listitem" width="30%"><strong>[{$label}]:</strong></td>
                                <td class="listitem">[{$value}]</td>
                            </tr>
                            [{/if}]
                            [{/foreach}]
                        </table>
                    </fieldset><br />
                [{/if}]

                [{if $CustResponse ne ""}]
                    <fieldset>
                        <legend><strong>[{$CustResponse.title}]</strong></legend>
                        <table width="100%">
                            [{foreach from=$CustResponse key="label" item="value"}]
                            [{if $label ne "title"}]
                            <tr>
                                <td class="listitem" width="30%"><strong>[{$label}]:</strong></td>
                                <td class="listitem">[{$value}]</td>
                            </tr>
                            [{/if}]
                            [{/foreach}]
                        </table>
                    </fieldset><br />
                [{/if}]

                [{if $ListResponse ne "" || $ListResponseSingle ne ""}]                    
                    <fieldset>
                        [{if $ListResponse ne ""}]
                            <legend><strong>[{oxmultilang ident="ASIGN_OVERVIEW"}]</strong></legend>
                            <table width="100%">
                                [{foreach from=$ListResponse key="label" item="response"}]
                                    [{foreach from=$response key="col" item="value" name="inloop"}]
                                        [{if $col eq "QuantityUOM"}]
                                            <tr>
                                                <td class="listitem" width="30%"><strong>[{$col}][[{$value->QuantityISO}]]:</strong></td>
                                                <td class="listitem">[{$value->_}]</td>
                                            </tr>
                                        [{else}]
                                            <tr>
                                                <td class="listitem" width="30%"><strong>[{$col}]:</strong></td>
                                                <td class="listitem">[{$value}]</td>
                                            </tr>
                                        [{/if}]
                                        [{if $smarty.foreach.inloop.last}]
                                            <tr><td><br /></td></tr>
                                        [{/if}]
                                    [{/foreach}]
                                [{/foreach}]
                            </table>
                        [{/if}]
                        
                        [{if $ListResponseSingle}]
                            <legend><strong>[{oxmultilang ident="ASIGN_OVERVIEW"}]</strong></legend>
                            <table width="100%">
                                [{foreach from=$ListResponseSingle key="col" item="value"  }]
                                    [{if $col eq "QuantityUOM"}]
                                        <tr>
                                            <td class="listitem" width="30%"><strong>[{$col}][[{$value->QuantityISO}]]:</strong></td>
                                            <td class="listitem">[{$value->_}]</td>
                                        </tr>
                                    [{else}]
                                        <tr>
                                            <td class="listitem" width="30%"><strong>[{$col}]:</strong></td>
                                            <td class="listitem">[{$value}]</td>
                                        </tr>
                                    [{/if}]
                                [{/foreach}]
                            </table>
                        [{/if}]
                    </fieldset><br />
                [{/if}]                 
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
    function validateFileExt(filename)
    {
        var ext = filename.split('.').pop();
        if (filename != '' && ext != 'pdf') {
            document.getElementById('vmessage').style.display = 'block';
            return false;
        } else {
            document.myedit.fnc.value='create';
        }
    }
</script>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
