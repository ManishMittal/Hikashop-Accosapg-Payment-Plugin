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

?>

<div class="hikashop_accpsapg_end" id="hikashop_accpsapg_end">
	<span id="hikashop_accpsapg_end_message" class="hikashop_accpsapg_end_message">
		<?php echo JText::sprintf('PLEASE_WAIT_BEFORE_REDIRECTION_TO_X',$this->payment_name).'<br/>'. JText::_('CLICK_ON_BUTTON_IF_NOT_REDIRECTED');?> <!-- Waiting message -->
	</span>
	<span id="hikashop_accpsapg_end_spinner" class="hikashop_accpsapg_end_spinner">
		<img src="<?php echo HIKASHOP_IMAGES.'spinner.gif';?>" />
	</span>
	<br/>
	<!-- To send all requiered information, a form is used. Hidden input are setted with all variables, and the form is auto submit with a POST method to the payment plateform URL -->
	<form id="hikashop_accpsapg_form" name="hikashop_accpsapg_form"  action="https://<?php echo $this->payment_params->pgdomain;?>/AccosaPG/verify.jsp" method="post"> 
		<div id="hikashop_accpsapg_end_image" class="hikashop_accpsapg_end_image">
			<input id="hikashop_accpsapg_button" class="btn btn-primary" type="submit" value="<?php echo JText::_('PAY_NOW');?>" name="" alt="<?php echo JText::_('PAY_NOW');?>" />
		</div>
		<?php
			foreach( $this->vars as $name => $value ) {
				echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars((string)$value).'" />';
			}
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration("window.addEvent('domready', function() {document.getElementById('hikashop_accpsapg_form').submit();});");
			JRequest::setVar('noform',1);
		?>
	</form>
</div>
