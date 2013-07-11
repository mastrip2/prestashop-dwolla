<?php

if(!defined('_PS_VERSION_'))
	exit;

class DwollaPaymentModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		include_once(_PS_MODULE_DIR_.'dwolla/lib/dwolla.php');
		include_once(_PS_MODULE_DIR_.'dwolla/dwolla.php');
		
		parent::initContent();
		
		$shop_url = Tools::getShopDomainSsl(true)._MODULE_DIR_;
		$dwolla = new DwollaRestClient(Configuration::get('apiKey'), Configuration::get('apiSecret'),$shop_url.'dwolla/complete.php');
		
		//setting test mode on
		if(Configuration::get('mode'))
			$dwolla->setMode('TEST');
			
		$dwolla->startGatewaySession();

		//inserting products
		foreach ($this->context->cart->getProducts() as $product) {
			$dwolla->addGatewayProduct($product["name"], $product["price"],$product["cart_quantity"]);
		}
		
		//tax computing
		$tax = $this->context->cart->getOrderTotal() - $this->context->cart->getOrderTotal(false);
		
		if ($tax < 0)
			$tax = 0;
		
		$billing_address = new Address($this->context->cart->id_address_invoice);
		$billing_address->state	= new State($billing_address->id_state);
		$discount =$this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
		$shipping= $this->context->cart->getTotalShippingCost();
		$callback= $shop_url.'dwolla/notifier.php';
		$id=$this->context->cart->id;
		$custInfo= array(
				'firstName'=> $this->billing_address->firstname,
				'lastName'=> $this->billing_address->lastname,
				'email'=> $this->context->customer->email,
				'city'=> $this->billing_address->city,
				'state'=> $this->billing_address->state->name,
				'zip'=> $this->billing_address->postcode
		);
		
		$url=$dwolla->getGatewayURL(Configuration::get('destinationId'), $id, $discount, $shipping, $tax, '', $callback, $custInfo);
		 
		 if(!$url){
		 	$this->context->smarty->assign(array(
				'error'=> $dwolla->getError()
			));
			$this->setTemplate('payment.tpl');
		}
		//Sending customer to Dwolla website to complete their order
		Tools::redirect($url);
	}
}

?>