<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 9/1/17
 * Time: 10:45
 */ ?>
<script id="tmpl-coupon-edit" type="text/html">
    <td class="editeable coupon_code column-coupon_code has-row-actions column-primary" data-colname="Code">
        <input name="code" value="{{data.code}}">
        <div class="row-actions">
            <span class="cancel"><a href="#">Cancel</a> | </span><span class="save"><a href="#"><b>Save</b></a></span>
        </div>
    </td>
    <td class="editeable amount column-amount" data-colname="Amount">
        <input name="amount" value="{{data.amount}}" type="number">
        <select name='type'>
			<?php foreach ( \modules\coupons\Coupon::get_types() as $value => $type ) {
				echo "<option value='{$value}'<# if({$value} == data.type) {#> selected='selected'<#}#>>{$type}</option>";
			} ?>
        </select>
    </td>
    <td class="editeable used column-used" data-colname="Used">
        {{data.used}} / <input type="number" name="count" value="{{data.count}}">
    </td>
    <td class="editeable status column-status" data-colname="Status">
        <select name="status">
			<?php foreach ( \modules\coupons\Coupon::get_statuses() as $value => $type ) {
				echo "<option value='{$value}'<# if({$value} == data.status) {#> selected='selected'<#}#>>{$type}</option>";
			} ?>
        </select>
    </td>
    <td class="editeable created column-created" data-colname="Created">{{data.created}}</td>
</script>
