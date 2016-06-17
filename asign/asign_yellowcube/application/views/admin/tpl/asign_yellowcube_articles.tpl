
[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{* template params *}]
[{assign var="OptYesNo" value=$oView->getBooleanOptions()}]
[{assign var="OptPeriodExpDateType" value=$oView->getPeriodExpDateTypeOptions()}]
[{assign var="OptAlternateUnitISO" value=$oView->getAlternateUnitISOOptions()}]
[{assign var="OptEANTypes" value=$oView->getEANTypesOptions()}]
[{assign var="OptWeightUnits" value=$oView->getWeightUnitOptions()}]
[{assign var="OptLengthUnits" value=$oView->getLengthUnitOptions()}]
[{assign var="OptVolumeUnits" value=$oView->getVolumeUnitOptions()}]

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

[{if $storeok == 'true'}]
    <div class="messagebox">
        <p class="success">[{oxmultilang ident="ASIGN_YCSPDETAILS_SAVED_OK"}]</p>
    </div>
[{/if}]

[{if $blDownloadable}]
    <div class="messagebox">
        <p>[{oxmultilang ident="ASIGN_DOWNLOADABLE_PRODUCT_DETECTED"}]</p>
    </div>
[{/if}]
[{assign var="hideLots" value=$oViewConf->isFunctionalityEnabled('blYellowCubeIgnoreLotInfo')}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <input type="hidden" name="oxidCopy" value="[{$oxid}]" />
    <input type="hidden" name="cl" value="asign_yellowcube_articles" />
    <input type="hidden" name="language" value="[{$actlang}]" />
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="asign_yellowcube_articles" />
    <input type="hidden" name="fnc" value="" id="fnc" />
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <input type="hidden" name="voxid" value="[{$oxid}]" />
    <input type="hidden" name="oxparentid" value="[{$oxparentid}]" />
    <input type="hidden" name="language" value="[{$actlang}]" />
    <input type="hidden" name="mpkeyorderamt" value="[{$edit.ORDCOST}]" />

    [{if !$setError}]
        [{if $mcreate eq "true"}]
            <div class="messagebox"><p class="success">[{oxmultilang ident="ASIGN_MESSAGE_YCITEM_INSERTED"}]</p></div><br />
        [{/if}]
        [{if $mstatus eq "true"}]
            <div class="messagebox"><p class="success">[{oxmultilang ident="ASIGN_MESSAGE_YCITEM_UPDATED"}]</p></div><br />
        [{/if}]
    [{/if}]
    [{if $setError}]
        <div class="messagebox"><p class="failerror">[Error][Code:[{$ARTResponse.StatusCode}]] [{$ARTResponse.StatusText}]</p></div><br />
    [{/if}]
    <table cellspacing="0" cellpadding="0" border="0" width="80%">
        [{*if $ARTResponse eq ""*}]
            <tr>
                <td width="50%" valign="top">
                    <table width="90%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td colspan="2"><h3>[{oxmultilang ident="SHOP_MODULE_GROUP_article"}]</h3></td>
                        </tr>

                        [{if !$hideLots}]
                            <tr>
                                <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeYCLot" suffix=":"}]</strong></td>
                                <td class="listitem2">
                                    <input type="text" name="spsval[sYellowCubeYCLot]" value="[{$SpsDetails.sYellowCubeYCLot}]" id="iyclot" maxlength="10" />
                                </td>
                            </tr>
                            <tr>
                                <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeLot" suffix=":"}]</strong></td>
                                <td class="listitem2">
                                    <input type="text" name="spsval[sYellowCubeLot]" value="[{$SpsDetails.sYellowCubeLot}]" id="iyclot" maxlength="15" />
                                </td>
                            </tr>
                        [{/if}]

                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeBatchMngtReq" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeBatchMngtReq]">
                                    [{foreach from=$OptYesNo key="opt" item="value"}]
                                        <option value="[{$opt}]" [{if $SpsDetails.sYellowCubeBatchMngtReq eq $opt}]selected[{/if}]>
                                            [{oxmultilang ident="GENERAL_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeSerialNoFlag" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeSerialNoFlag]">
                                    [{foreach from=$OptYesNo key="opt" item="value"}]
                                        <option value="[{$opt}]" [{if $SpsDetails.sYellowCubeSerialNoFlag eq $opt}]selected[{/if}]>
                                            [{oxmultilang ident="GENERAL_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubePeriodExpDateType" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubePeriodExpDateType]">
                                    [{foreach from=$OptPeriodExpDateType key="opt" item="value"}]
                                        <option value="[{$opt}]" [{if $SpsDetails.sYellowCubePeriodExpDateType eq $opt}]selected[{/if}]>
                                            [{oxmultilang ident="SHOP_MODULE_sYellowCubePeriodExpDateType_"|cat:$opt}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="ASIGN_LABEL_BASEOUM" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeAlternateUnitISO]">
                                    [{foreach from=$OptAlternateUnitISO key="opt" item="value"}]
                                        <option value="[{$value}]" [{if $SpsDetails.sYellowCubeAlternateUnitISO eq $value}]selected[{/if}]>
                                            [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeEANType" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeEANType]">
                                    [{foreach from=$OptEANTypes key="opt" item="value"}]
                                        <option value="[{$value}]" [{if $SpsDetails.sYellowCubeEANType eq $value}]selected[{/if}]>
                                            [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                    </table><br />
                    <table width="90%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeNetWeightISO" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeNetWeightISO]">
                                    [{foreach from=$OptWeightUnits key="opt" item="value"}]
                                <option value="[{$value}]" [{if $SpsDetails.sYellowCubeNetWeightISO eq $value}]selected[{/if}]>
                                    [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                    </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeGrossWeightISO" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeGrossWeightISO]">
                                    [{foreach from=$OptWeightUnits key="opt" item="value"}]
                                <option value="[{$value}]" [{if $SpsDetails.sYellowCubeGrossWeightISO eq $value}]selected[{/if}]>
                                    [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                    </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeLengthISO" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeLengthISO]">
                                    [{foreach from=$OptLengthUnits key="opt" item="value"}]
                                        <option value="[{$value}]" [{if $SpsDetails.sYellowCubeLengthISO eq $value}]selected[{/if}]>
                                            [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeWidthISO" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeWidthISO]">
                                    [{foreach from=$OptLengthUnits key="opt" item="value"}]
                                        <option value="[{$value}]" [{if $SpsDetails.sYellowCubeWidthISO eq $value}]selected[{/if}]>
                                            [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeHeightISO" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeHeightISO]">
                                    [{foreach from=$OptLengthUnits key="opt" item="value"}]
                                        <option value="[{$value}]" [{if $SpsDetails.sYellowCubeHeightISO eq $value}]selected[{/if}]>
                                            [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeVolumeISO" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <select name="spsval[sYellowCubeVolumeISO]">
                                    [{foreach from=$OptVolumeUnits key="opt" item="value"}]
                                        <option value="[{$value}]" [{if $SpsDetails.sYellowCubeVolumeISO eq $value}]selected[{/if}]>
                                            [{oxmultilang ident="ASIGN_OPTION_"|cat:$value}]
                                        </option>
                                    [{/foreach}]
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeAltNumeratorUOM" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <input type="text" name="spsval[sYellowCubeAltNumeratorUOM]" value="[{if $SpsDetails.sYellowCubeAltNumeratorUOM}][{$SpsDetails.sYellowCubeAltNumeratorUOM}][{else}]1[{/if}]" id="numuom" maxlength="3" placeholder="1,12,20, etc." />
                            </td>
                        </tr>
                        <tr>
                            <td class="listitem2"><strong>[{oxmultilang ident="SHOP_MODULE_sYellowCubeAltDenominatorUOM" suffix=":"}]</strong></td>
                            <td class="listitem2">
                                <input type="text" name="spsval[sYellowCubeAltDenominatorUOM]" value="[{if $SpsDetails.sYellowCubeAltDenominatorUOM}][{$SpsDetails.sYellowCubeAltDenominatorUOM}][{else}]1[{/if}]" id="denouom" maxlength="3" placeholder="1,12,20, etc." />
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr /></td></tr>
                        <tr>
                            <td colspan="2">
                                <button type="submit" name="save" id="isave" onclick="document.myedit.fnc.value='save'">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_SAVE"}]</button>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%" valign="top">
                    [{if !$blDownloadable}]
                        <h3>[{oxmultilang ident="ASIGN_LABEL_INSERTARTICLE"}]</h3>
                        <select name="sflag">
                            [{if $ARTResponse.StatusCode ne 100}]
                                <option value="I">[{oxmultilang ident="ASIGN_YCARTICLES_OPTION_I"}]</option>
                            [{/if}]
                            <option value="U">[{oxmultilang ident="ASIGN_YCARTICLES_OPTION_U"}]</option>
                            <option value="D">[{oxmultilang ident="ASIGN_YCARTICLES_OPTION_D"}]</option>
                        </select>
                        <input type="hidden" name="referencenr" value="[{$ARTResponse.Reference}]" />
                        <button type="submit" name="create" id="icreate" onclick="document.myedit.fnc.value='create'">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_INSERT"}]</button>
                        <br />
                        <br />
                    [{/if}]
                    [{if $ARTResponse}]
                        <h3>[{oxmultilang ident="ASIGN_LABEL_INSERTARTICLE_STATUS"}]</h3>
                        <table width="100%">
                            [{foreach from=$ARTResponse key="label" item="value"}]
                            <tr>
                                <td class="listitem2 [{if $setError}][{if $label eq "StatusText" || $label eq "StatusCode" || $label eq "StatusType"}]failerror[{/if}][{/if}]"><strong>[{$label}]:</strong></td>
                                <td class="listitem2 [{if $setError}][{if $label eq "StatusText" || $label eq "StatusCode" || $label eq "StatusType"}]failerror[{/if}][{/if}]">[{$value}]</td>
                            </tr>
                            [{/foreach}]
                        </table><br />
                        [{if $ARTResponse.StatusCode ne 100}]
                            <input type="hidden" name="referencenr" value="[{$ARTResponse.Reference}]" />
                            <button type="submit" name="status" id="istatus" onclick="document.myedit.fnc.value='status'">[{oxmultilang ident="ASIGN_YCARTICLES_BUTTON_STATUS"}]</button>
                        [{/if}]
                    [{/if}]
                </td>
            </tr>
        [{*/if*}]
    </table>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
