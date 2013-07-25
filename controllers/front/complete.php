<?php

if(!defined('_PS_VERSION_'))
	exit;

class DwollaCompleteModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		include_once(_PS_MODULE_DIR_.'dwolla/lib/dwolla.php');
		include_once(_PS_MODULE_DIR_.'dwolla/dwolla.php');
		include_once( '../../../../classes/order/Order.php');
		$dwolla= new Dwolla();
		$this->display_column_left = false;
		
		parent::initContent();
		
		if(Tools::getValue('error')=="failure" & Tools::getValue('error_description')=="User Cancelled")
			tools::redirect('order.php');
		
		$checkoutId = Tools::getValue('checkoutId');
		$orderId = (int)Tools::getValue('orderId');
		$transaction = Tools::getValue('transaction');
		$postback = Tools::getValue('postback');
		$amount = (float)Tools::getValue('amount');
		$signature = Tools::getValue('signature');
		$clearingDate = Tools::getValue('clearingDate');
		$status = Tools::getValue('status');
		
		$this->context = Context::getContext();
		
		if(date("d-M-Y",strtotime($clearingDate)) == date("d-M-Y")) $clearingDate = false;
		
		$order = new Order(Order::getOrderByCartId((int)($orderId)));
		
		$this->context->cookie->dwolla_transaction=$transaction;
		$this->context->cookie->dwolla_clearingDate=$clearingDate;
		
		$url = 'index.php?controller=order-confirmation&';
	
		Tools::redirect($url.'id_module='.(int)$dwolla->id.'&id_cart='.(int)$orderId.'&key='.$order->secure_key);
	}
}

?>