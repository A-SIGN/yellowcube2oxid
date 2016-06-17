[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{ oxmultilang ident="SHOP_MODULE_EORI_FIELD" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="15" maxlength="[{$edit->oxorder__asigneori->fldmax_length}]" name="editval[oxorder__asigneori]" value="[{$edit->oxorder__asigneori->value }]" [{ $readonly }]>
    </td>
</tr>