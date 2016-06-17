[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{ oxmultilang ident="SHOP_MODULE_EORI_FIELD" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="15" maxlength="[{$edit->oxuser__asigneori->fldmax_length}]" name="editval[oxuser__asigneori]" value="[{$edit->oxuser__asigneori->value }]" [{ $readonly }]>
    </td>
</tr>