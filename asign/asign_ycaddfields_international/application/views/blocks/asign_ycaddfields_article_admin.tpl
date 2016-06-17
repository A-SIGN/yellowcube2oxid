[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{ oxmultilang ident="ARTICLE_EXTEND_TARA" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="10" maxlength="[{$edit->oxarticles__asigntara->fldmax_length}]" name="editval[oxarticles__asigntara]" value="[{$edit->oxarticles__asigntara->value}]" [{ $readonly }]>[{oxmultilang ident="ARTICLE_EXTEND_WEIGHT_UNIT"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{ oxmultilang ident="ARTICLE_EXTEND_ORIGIN" }]
    </td>
    <td class="edittext">
        [{assign var='countrylist' value=$edit->getCountryOriginList($edit->getLanguage())}]
        <select class="editinput" name="editval[oxarticles__asignorigin]" [{ $readonly }]>
            <option value="" [{if $oCountry->oxcountry__oxid->value == ''}]selected[{/if}]>-</option>
            [{foreach from=$countrylist item=oCountry}]
            <option value="[{$oCountry->oxcountry__oxid->value}]" [{if $oCountry->oxcountry__oxid->value == $edit->oxarticles__asignorigin->value}]selected[{/if}]>[{$oCountry->oxcountry__oxtitle->value}]</option>
            [{/foreach}]
        </select>
    </td>
</tr>

<tr>
    <td class="edittext">
        [{ oxmultilang ident="ARTICLE_EXTEND_CUSTOMS" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="10" maxlength="[{$edit->oxarticles__asigncustomstariff->fldmax_length}]" name="editval[oxarticles__asigncustomstariff]" value="[{$edit->oxarticles__asigncustomstariff->value}]" [{ $readonly }]>
    </td>
</tr>