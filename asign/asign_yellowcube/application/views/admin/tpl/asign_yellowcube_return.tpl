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
    <input type="hidden" name="cl" value="asign_yellowcube_return" />
    <input type="hidden" name="language" value="[{$actlang}]" />
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post" enctype="multipart/form-data">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="asign_yellowcube_return" />
    <input type="hidden" name="fnc" value="" id="fnc" />
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <input type="hidden" name="voxid" value="[{$oxid}]" />
    <input type="hidden" name="language" value="[{$actlang}]" />

    [{if $mreturn eq "true"}]
        <div class="messagebox"><p class="success">[{oxmultilang ident="ASIGN_MESSAGE_YCRETURN_CREATED"}]</p></div>
    [{/if}]
    
    <table cellspacing="0" cellpadding="0" border="0">        
        <tr>            
            [{if $retResponse eq ""}]
                <td style="width:300px;">
                    <h3>[{oxmultilang ident="SHOP_MODULE_sYellowCubeRReason"}]</h3>
                    <table>
                        <tr>                            
                            <td>
                                <textarea name="sReason" id="sReason" cols="50" rows="5"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><button type="submit" name="return" id="ireturn" onclick="document.myedit.fnc.value='returnme'">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_RETURN"}]</button></td>
                        </tr>
                    </table>
                </td>
            [{else}]
                <td width="300" valign="top" style="padding-left:20px;">
                    <fieldset>
                        <legend><strong>[{oxmultilang ident="ASIGN_LABEL_RETURN_RESPONSE"}]</strong></legend>
                        <table width="100%">
                            [{foreach from=$retResponse key="label" item="value"}]
                                <tr>
                                    <td class="listitem [{if $value eq 'E'}]failerror[{/if}]"><strong>[{$label}]:</strong></td>
                                    <td class="listitem [{if $value eq 'E'}]failerror[{/if}]">[{$value}]</td>
                                </tr>
                            [{/foreach}]
                        </table>
                    </fieldset>                    
                </td>
            [{/if}] 
        </tr>        
    </table>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
