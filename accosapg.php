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
?><?php
class plgHikashoppaymentAccosapg extends hikashopPaymentPlugin
{
	var $accepted_currencies = array(
		'EUR','CHF','USD','GBP','JPY','CAD','AUD'
	);

	var $multiple = true;
	var $name = 'accosapg';
	var $doc_form = 'accosapg';

	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	function onBeforeOrderCreate(&$order,&$do){
		if(parent::onBeforeOrderCreate($order, $do) === true)
			return true;

		if(empty($this->payment_params->pgdomain)|| empty($this->payment_params->pgInstanceId) || empty($this->payment_params->merchantId) || empty($this->payment_params->pgdomain) || empty($this->payment_params->hashKey)) {
			$this->app->enqueueMessage('Please check your &quot;Accosapg&quot; plugin configuration');
			$do = false;
		}
	}

	function onAfterOrderConfirm(&$order, &$methods, $method_id) {
		parent::onAfterOrderConfirm($order, $methods, $method_id);
		$amout = round($order->cart->full_total->prices[0]->price_value_with_tax,2)*100;
		$perform=$this->payment_params->perform;
		$currencyCode=$this->payment_params->currency_code;
		$amount=$amout;
		$merchantReferenceNo=$order->order_id;
		$orderDesc= "order number : ".$order->order_number;

		$messageHash = $this->payment_params->pgInstanceId."|".$this->payment_params->merchantId."|".$perform."|".$currencyCode."|".$amount."|".$merchantReferenceNo."|".$this->payment_params->hashKey."|";

		$message_hash = "CURRENCY:7:".base64_encode(sha1($messageHash, true));

			$vars = array(
				'pg_instance_id'=>$this->payment_params->pgInstanceId,
				'merchant_id' =>$this->payment_params->merchantId,
				'hashKey' => $this->payment_params->hashKey,
				'perform' => $perform, //User's identifier on the payment platform
				'currency_code' => $currencyCode, //Order's user id
				'order_desc' => $orderDesc, //Order's description
				'amount' => $amout ,//The amount of the order
				'merchant_reference_no'=>$order->order_id,
				'message_hash'=>$message_hash
			);
			$vars["order_id"]=$order->order_id;


		$this->vars = $vars;
		return $this->showPage('end'); 

	
	}

	function onPaymentNotification(&$statuses) {
//We first create a filtered array from the parameters received
    $pluginsClass = hikashop_get('class.plugins');
        $elements = $pluginsClass->getMethods('payment', 'accosapg');
        if(empty($elements))
            return false;
        $element = reset($elements);
		$vars = array();
		$filter = JFilterInput::getInstance();
		$app =& JFactory::getApplication();
		$httpsHikashop = HIKASHOP_LIVE;
		global $Itemid;
		$url_itemid = '';
		if (!empty($Itemid)) {
		$url_itemid = '&Itemid=' . $Itemid;
		}
		foreach($_REQUEST as $key => $value)
		{
			$key = $filter->clean($key);
			$value = JRequest::getString($key);
			$vars[$key]=$value;
		}
		$transactionTypeCode=$vars["transaction_type_code"];
		$installments=$vars["installments"];
		$transactionId=$vars["transaction_id"];

		$amount=$vars["amount"];
		$exponent=$vars["exponent"];
		$currencyCode=$vars["currency_code"];
		$merchantReferenceNo=$vars["merchant_reference_no"];

		$status=$vars["status"];
		$eci=$vars["3ds_eci"];
		$pgErrorCode=$vars["pg_error_code"];

		$pgErrorDetail=$vars["pg_error_detail"];
		$pgErrorMsg=$vars["pg_error_msg"];

		$messageHash=$vars["message_hash"];


		$messageHashBuf=$pgInstanceId."|".$merchantId."|".$transactionTypeCode."|".$installments."|".$transactionId."|".$amount."|".$exponent."|".$currencyCode."|".$merchantReferenceNo."|".$status."|".$eci."|".$pgErrorCode."|".$hashKey."|";

		$messageHashClient = "13:".base64_encode(sha1($messageHashBuf, true));

		$hashMatch=false;

		if ($messageHash==$messageHashClient){
		$hashMatch=true;
		} else {
		$hashMatch=false;
		} 

		//TWe load the parameters of the plugin in $this->payment_params and the order data based on the order_id coming from the payment platform

		$order_id = (int)@$vars['merchant_reference_no'];
		$dbOrder = $this->getOrder($order_id);
		$this->loadPaymentParams($dbOrder);
	
		$this->loadOrderData($dbOrder);

		//Configure the "succes URL" and the "fail URL" to redirect the user if necessary (not necessary for our example platform
		//$return_url = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=after_end&order_id='.$order_id.$this->url_itemid;
		//$cancel_url = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=order&task=cancel_order&order_id='.$order_id.$this->url_itemid;

		//Recalculate the hash to check if the information received are identical to those sent by the payment platform
	

		//Confirm or not the Order, depending of the information received
			if("50020"==$status || "50097"==$status) {
				echo '<font color="#339900"><b>Transaction Passed</b></font>';
				$history = new stdClass();
				$email = new stdClass();
				$TransactionStatus='Complete';
				$history->notified = 1;
				$history->amount = $result['amount'];
				$history->data = ob_get_clean().'/r/n'.$vars['transaction_id'];

				$order_status = $this->payment_params->verified_status;
				if ($dbOrder -> order_status == $order_status)
					return true;

				$email->subject = JText::sprintf('PAYMENT_NOTIFICATION_FOR_ORDER', 'Accosapg', $TransactionStatus, $dbOrder -> order_number);
				$body = str_replace('<br/>', "\r\n", JText::sprintf('PAYMENT_NOTIFICATION_STATUS', 'Accosapg', $TransactionStatus)) . ' ' . JText::sprintf('ORDER_STATUS_CHANGED', $order_status) . "\r\n\r\n" . $order_text;
				$email->body = $body;
				$this->modifyOrder($order_id, $this->payment_params->verified_status,$history, $email);
			$return_url = $httpsHikashop.'index.php?option=com_hikashop&ctrl=checkout&task=after_end&order_id='.$order_id.$url_itemid;
			$app->enqueueMessage('Payment completed');
			$app->redirect($return_url);
			return true;
				} else {	
					$email = new stdClass();
				$email->subject = JText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', $this->name).'invalid response';
				$email->body = JText::sprintf("Hello,\r\n A Postfinance notification was refused because the response from the Post finance server was invalid")."\r\n\r\n".$order_text;
				$history = new stdClass();
				$this->modifyOrder($vars['callerReference'], $this->payment_params->invalid_status, $history, $email);
					$html  ='<table><tr> <td valign="top" align="center">';
					$html .='<font color="#FF0000"><b>Transaction Failed</b></font></td></tr>';
					$html .='<tr> <td valign="top" class="mainText">';
					$html .='<table border="1" width="400" align="center"><tr><td align="right">HashMatch</td><td align="left">: '. $hashMatch.'</td></tr>';
					$html .='<tr><td align="right">TransactionTypeCode</td><td align="left">: '. $transactionTypeCode.'</td></tr>';
					$html .='<tr><td align="right">TransactionId</td><td align="left">: '. $transactionId.'</td></tr>';
					$html .='<tr><td align="right">Amount</td><td align="left">: '. $amount.'</td></tr>';
					$html .='<tr><td align="right">Exponent</td><td align="left">: '. $exponent.'</td>/tr>';
					$html .='<tr><td align="right">CurrencyCode</td><td align="left">: '.$currencyCode.'</td></tr>';
					$html .='<tr><td align="right">MerchantReferenceNo</td><td align="left">: '. $merchantReferenceNo.'</td></tr>';
					$html .='<tr><td align="right">Status</td><td align="left">: '. $status.'</td></tr>';
					$html .='<tr><td align="right">3dsEci</td><td align="left">: '. $eci.'</td></tr>';
					$html .='<tr><td align="right">PG ErrorCode</td><td align="left">: '. $pgErrorCode.'</td></tr>';
					$html .='<tr><td align="right">PG ErrorDetail</td><td align="left">: '. $pgErrorDetail.'</td></tr>';
					$html .='<tr><td align="right">PG ErrorMsg</td><td align="left">: '. $pgErrorMsg.'</td></tr>';
					$html .='</table></td></tr><tr> </tr></table>';
					echo $html;
					$cancel_url = $httpsHikashop.'index.php?option=com_hikashop&ctrl=order&task=cancel_order&order_id='.$YOUR_ORDER_ID.$url_itemid;
					$app->enqueueMessage('Transaction Failed');
					$app->redirect($cancel_url);					
					return false;
				}
	
	}

	function onPaymentConfiguration(&$element) {
		$subtask = JRequest::getCmd('subtask', '');
		if($subtask == 'ips') {
			$ips = null;
			echo implode(',', $this->_getIPList($ips));
			exit;
		}

		parent::onPaymentConfiguration($element);
		$this->address = hikashop_get('type.address');

		
	}

	function onPaymentConfigurationSave(&$element) {
		if(!empty($element->payment_params->ips))
			$element->payment_params->ips = explode(',', $element->payment_params->ips);
		return true;
	}

	function getPaymentDefaultValues(&$element) {
		$element->payment_name = 'ACCOSAPG';
		$element->payment_description='You can pay by credit card or ACCOSAPG using this payment method';
		$element->payment_images = 'MasterCard,VISA';
		$element->payment_params->currency_code='144';
		$element->payment_params->perform='initiatePaymentCapture#preauth';
		$element->payment_params->invalid_status = 'cancelled';
		$element->payment_params->pending_status = 'created';
		$element->payment_params->verified_status = 'confirmed';
	}

	
}
