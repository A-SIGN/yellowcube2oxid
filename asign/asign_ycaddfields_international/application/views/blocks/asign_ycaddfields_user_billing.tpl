<li [{if $aErrors.oxuser__asigneori}]class="oxInValid"[{/if}]>
    <label [{if $oView->isFieldRequired(oxuser__asigneori) }]class="req"[{else}]class="form_userbill"[{/if}]>[{ oxmultilang ident="EORI_FIELD" suffix="COLON" }]</label>

    <input class="textbox" [{if $oView->isFieldRequired(oxuser__asigneori) }]class="textbox js-oxValidate js-oxValidate_notEmpty" [{/if}]type="text"  maxlength="255" name="invadr[oxuser__asigneori]" value="[{if isset( $invadr.oxuser__asigneori ) }][{ $invadr.oxuser__asigneori }][{else }][{ $oxcmp_user->oxuser__asigneori->value }][{/if}]">
    [{if $oView->isFieldRequired(oxuser__asigneori)}]
    <p class="oxValidateError message_invalid [{if $oView->getClassName() == 'oxwservicemenu'}]od_popupreg[{/if}]">
        <span class="js-oxError_notEmpty validate_error_bill">[{ oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS" }]</span>
        [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__asigneori}]
    </p>
    [{/if}]
    <a class="eori_info" title="[{oxifcontent ident='eori_info' object='oCont'}]
                         [{$oCont->oxcontents__oxcontent->value}]
                     [{/oxifcontent}]"><img src="[{$oViewConf->getImageUrl('info_icon.png')}]"></a>
</li>
[{oxscript add="
$('.eori_info').tooltip();
"}]
[{$smarty.block.parent}]