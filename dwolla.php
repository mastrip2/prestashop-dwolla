<?php

if (!defined('_PS_VERSION_'))
	exit;


class Dwolla extends PaymentModule
{
	protected $_html = '';	
	public $_errors	= array();
	
	public function __construct()
	{
		$this->name = 'dwolla';
		$this->tab = 'payments_gateways';
		$this->version = '0.1';
		$this->author = 'Michael Stripling';	
		$this->ps_versions_compliancy = array('min' => '1.5');	
		$this->currencies = true;
		$this->currencies_mode = 'radio';
		
		parent::__construct();
		
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Dwolla');
		$this->description = $this->l('Start accepting dwolla.');
		
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
		if (!Configuration::get('Dwolla'))
			$this->warning = $this->l('No name provided');
	}
	
	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn') || !Configuration::updateValue('mode', 0))
			return false;
		$this->createOrderState();
		return TRUE;
	}
	
	public function createOrderState()
	{
		if (!Configuration::get('dwolla_OS_AUTHORIZATION'))
		{
			$orderState = new OrderState();
			$orderState->name[1] = 'Awaiting Dwolla Payment';
			$orderState->send_email = false;
			$orderState->color = 'RoyalBlue';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = true;
			$orderState->invoice = true;
			$orderState->add();
			
			Configuration::updateValue('dwolla_OS_AUTHORIZATION', (int)$orderState->id);
		}
	}
	
	public function uninstall()
	{
		if (!parent::uninstall() ||
				!Configuration::deleteByName('Dwolla') ||
				!Configuration::deleteByName('apiKey') ||
				!Configuration::deleteByName('apiSecret') ||
				!Configuration::deleteByName('destinationId') ||
				!Configuration::deleteByName('mode')
		)
			return false;
		return true;
	}
	
	public function _validate($key)
	{
		$output=null;
		$text = Tools::getValue($key);
		
        if (!$text  || empty($text) || !Validate::isGenericName($text))
            $output = $this->displayError( $this->l('Invalid '.substr($key,3)) );
        else
        {
            if(configuration::get($key) != $text)
            {
            	Configuration::updateValue($key, $text); 
            	$output = $this->displayConfirmation($this->l(substr($key,3).' updated'));
            }
        }
		return $output;
	}
	
	public function getContent()
	{
	    $output = null;
	    
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	    	include_once(_PS_MODULE_DIR_.'/dwolla/lib/dwolla.php');
	    	$dwolla = new DwollaRestClient();
	        
	        $output= $this->_validate('apiKey');
	        $output= $this->_validate('apiSecret');
	        
	        //check dwolla id
	        $destinationId = Tools::getValue('destinationId');
	        if (!$destinationId  || empty($destinationId) || !Validate::isGenericName($destinationId) || strlen($destinationId)!=10 && strlen($destinationId)!=12)
	            $output .= $this->displayError( $this->l('Invalid Dwolla ID') );
	        else
	        {
	            if(Configuration::get('destinationId') != $destinationId)
	            {
	            	Configuration::updateValue('destinationId', $dwolla->parseDwollaID($destinationId)); 
	            	$output .= $this->displayConfirmation($this->l('Dwolla ID updated'));
	            }
	        }
	        
	        //mode selection
	        $mode = Tools::getValue('mode');
	        if(Configuration::get('mode') != $mode)
	        {
	            	Configuration::updateValue('mode', $mode); 
	            	$output .= $this->displayConfirmation($this->l('Mode updated'));
	        }
	    }
	    return $output.$this->displayForm();
	}

	public function displayForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Key'),
	                'name' => 'apiKey',
	                'size' => 63,
	                'required' => true,
	                'hint' => $this->l('Consumer key for the application.')
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Secret'),
	                'name' => 'apiSecret',
	                'size' => 63,
	                'required' => true,
	                'hint' => $this->l('Consumer secret for the application.')
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Dwolla ID'),
	                'name' => 'destinationId',
	                'size' => 15,
	                'required' => true,
	                'empty_message' => '812-xxx-xxxx',
	                'hint' => $this->l('Dwolla ID of the Dwolla account receiving the funds. Format will always match "812-xxx-xxxx".')
	            ),
	            array(
	                'type' => 'radio',
	                'label' => $this->l('Mode'),
	                'name' => 'mode',
	                'values' => array(
					array(
						'id' => 'prod',
						'value' => 0,
						'label' => $this->l('Production')
					),
					array(
						'id' => 'prod',
						'value' => 1,
						'label' => $this->l('Test')
					)
			),
			'required' => true,
	            ),
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	    
	    $fields_form[1]['form'] = array(    	
	        'legend' => array(
	            'title' => $this->l('Donation'),
	        ),
	    	'input' => array(
	    array(
	        'type' => 'free',
	        'label' => 'If you like my work. Consider making a small donation.
	        <script
	  src="https://www.dwolla.com/scripts/button.min.js" class="dwolla_button" type="text/javascript"
	  data-key="rAhKhh7NSbP55gw1s0woDI2NIDF4TLbwk8R8i282MOgTjJIyfW"
	  data-redirect="http://ccrazy.exofire.net/thankyou.php"
	  data-label="Donate now"
	  data-name="Dwolla payment gateway donation"
	  data-description="undefined"
	  data-amount="5"
	  data-shipping="0"
	  data-tax="0"
	  data-guest-checkout="true"
	  data-type="freetype"
	>
	</script>',
	        'name' => 'donation',
	    ))
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['apiKey'] = Configuration::get('apiKey');
	    $helper->fields_value['apiSecret'] = Configuration::get('apiSecret');
	    $helper->fields_value['destinationId'] = Configuration::get('destinationId');
	    $helper->fields_value['mode'] = Configuration::get('mode');
	     
	    return $helper->generateForm($fields_form);
	}
	
	public function hookPayment($params)
	{
		$this->smarty->assign(array(
			'this_path' => $this->_path
		));
		
		return $this->display(__FILE__, 'payment.tpl');
	}
	
	public function hookPaymentReturn($params)
	{	
		$this->context->smarty->assign(array(				
				'dwolla_transaction' => $this->context->cookie->dwolla_transaction,
				'id_order' => $params['objOrder']->reference,
				'total_to_pay' => $params['total_to_pay'],
				'clearingDate' => $this->context->cookie->dwolla_clearingDate,
				'order' => $order
			));
			
		return $this->display(__FILE__, 'confirmation.tpl');
	}
	
	public function validate($data)
	{
		include_once(_PS_MODULE_DIR_.'/dwolla/lib/dwolla.php');
		
		$data = json_decode($data);
		
		$context->cart = new Cart($data->OrderId);
		
		$message = null;
		$dwolla = new DwollaRestClient(Configuration::get('apiKey'), Configuration::get('apiSecret'));
		
		if($dwolla->verifyGatewaySignature($data->Signature, $data->CheckoutId, sprintf("%01.2f",$data->Amount)))
		{
			if(date("d-M-Y",strtotime($data->ClearingDate)) != date("d-M-Y"))
			{
				$payment = Configuration::get('dwolla_OS_AUTHORIZATION');
				$message .= $this->l('Payment pending.').'<br />'.$this->l('Estimated Clearance date: ').$data->ClearingDate;
			}
			else
			{
				$payment = Configuration::get('PS_OS_PAYMENT');
				$message .= $this->l('Payment accepted.').'<br />';
			}
		}
		else 
		{
			$payment = Configuration::get('PS_OS_ERROR');
			$message .= $this->l('Transaction could not be verified. Invalid signature.').'<br />';
		}

		$this->validateOrder($context->cart->id, (int)$payment, $data->Amount, 'Dwolla', $message, array("transaction_id" => $data->TransactionId), NULL, false, $context->cart->secure_key);
	}
}

?>