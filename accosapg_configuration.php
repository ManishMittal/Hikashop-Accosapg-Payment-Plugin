<?php
/**
 * @package  Accosapg Payment Plugin for Hikashop and for Joomla! 2.5, Joomla! 3.x
 * @name    Accosapg Payment Plugin for Hikashop
 * @date: 2015-11-03
 * @version	1.1
 * @author	OpenSource Technologies
 * @copyright	(C) 2009-2015 OPENSOURCETECHNOLOGIES. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
?><tr>
	<td class="key">
		<label for="data[payment][payment_params][pgdomain]"><?php
			echo JText::_( 'URL' );
		?></label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][pgdomain]" value="<?php echo $this->escape(@$this->element->payment_params->pgdomain); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][pgInstanceId]"><?php
			echo JText::_( 'Pg Instance Id' );
		?></label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][pgInstanceId]" value="<?php echo $this->escape(@$this->element->payment_params->pgInstanceId); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][merchantId]"><?php
			echo JText::_( 'Merchant Id' );
		?></label>
	</td>
	<td>	<input type="text" name="data[payment][payment_params][merchantId]" value="<?php echo $this->escape(@$this->element->payment_params->merchantId); ?>" /></td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][hashKey]"><?php
			echo JText::_( 'Hash Key' );
		?></label>
	</td>
	<td>	<input type="text" name="data[payment][payment_params][hashKey]" value="<?php echo $this->escape(@$this->element->payment_params->hashKey); ?>" /></td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][currency_code]"><?php
			echo JText::_( 'Currency Code' );
		?></label>
	</td>
	<td>	<input type="text" name="data[payment][payment_params][currency_code]" value="<?php echo $this->escape(@$this->element->payment_params->currency_code); ?>" /></td>
</tr><tr>
	<td class="key">
		<label for="data[payment][payment_params][perform]"><?php
			echo JText::_( 'Perform' );
		?></label>
	</td>
	<td><?php
		$arr = array(
			JHTML::_('select.option', 'initiatePaymentCapture#preauth', JText::_('Preauth') ),
			JHTML::_('select.option', 'initiatePaymentCapture#sale', JText::_('Sale') ),
		);
		echo JHTML::_('hikaselect.radiolist',  $arr, "data[payment][payment_params][perform]", '', 'value', 'text', @$this->element->payment_params->perform);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][invalid_status]"><?php
			echo JText::_('INVALID_STATUS');
		?></label>
	</td>
	<td><?php
		echo $this->data['order_statuses']->display("data[payment][payment_params][invalid_status]", @$this->element->payment_params->invalid_status);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][pending_status]"><?php
			echo JText::_('PENDING_STATUS');
		?></label>
	</td>
	<td><?php
		echo $this->data['order_statuses']->display("data[payment][payment_params][pending_status]", @$this->element->payment_params->pending_status);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][verified_status]"><?php
			echo JText::_('VERIFIED_STATUS');
		?></label>
	</td>
	<td><?php
		echo $this->data['order_statuses']->display("data[payment][payment_params][verified_status]", @$this->element->payment_params->verified_status);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][rm]"><?php
			echo 'Return method';
		?></label>
	</td>
	<td><?php
		if(!isset($this->element->payment_params->rm))
			$this->element->payment_params->rm = 1;
		echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][rm]" , '', $this->element->payment_params->rm);
	?></td>
</tr>
