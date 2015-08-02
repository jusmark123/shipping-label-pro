<?
/*
 * Class Name: 		SLP USPS
 * Version: 		3.1.0
 */
if( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly
 
if( ! class_exists( 'SLP_USPS' ) ): 
 
class SLP_USPS extends SLP_Shipping_Method {
	
	private $integration_id = '61eb9585-aaf7-4b88-808a-f12a7510f026';
		
	private $authenticator;
	
	private $account_info;
		
	public $stamps;
	
	/*private $boxes = array( 
		'flat_rate' => array(
			'domestic' => array( 
				'priority_mail_express' => array(
					"d13"     => array(
						"name"        => "Priority Mail Express Flat Rate Envelope",
						"length"      => "12.5",
						"width"       => "9.5",
						"height"      => "0.25",
						"weight"      => "70",
						"type"        => "express"
					),
					"d30"     => array(
						"name"        => "Priority Mail Express Legal Flat Rate Envelope",
						"length"      => "9.5",
						"width"       => "15",
						"height"      => "0.25",
						"weight"      => "70",
						"type"        => "express"
					),
					"d55"     => array(
						"name"   => "Priority Mail Express Flat Rate Box",
						"length" => "11",
						"width"  => "8.5",
						"height" => "5.5",
						"weight" => "70",
						"type"   => "express"
					),
					"d63"     => array(
						"name"        => "Priority Mail Express Padded Flat Rate Envelope",
						"length"      => "12.5",
						"width"       => "9.5",
						"height"      => "1",
						"weight"      => "70",
						"type"        => "express"
					),
				),
				'priority_mail' => array(
					"d16"     => array(
						"name"        => "Priority Mail Flat Rate Envelope",
						"length"      => "12.5",
						"width"       => "9.5",
						"height"      => "0.25",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d17"     => array(
						"name"        => "Priority Mail Flat Rate Medium Box",
						"length"      => "11.875",
						"width"       => "13.625",
						"height"      => "3.375",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d17b"     => array(
						"name"        => "Priority Mail Flat Rate Medium Box",
						"length"      => "11",
						"width"       => "8.5",
						"height"      => "5.5",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d22"     => array(
						"name"        => "Priority Mail Flat Rate Large Box",
						"length"      => "12",
						"width"       => "12",
						"height"      => "5.5",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d28"     => array(
						"name"        => "Priority Mail Flat Rate Small Box",
						"length"      => "5.375",
						"width"       => "8.625",
						"height"      => "1.625",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d29"     => array(
						"name"        => "Priority Mail Padded Flat Rate Envelope",
						"length"      => "12.5",
						"width"       => "9.5",
						"height"      => "1",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d38"     => array(
						"name"        => "Priority Mail Gift Card Flat Rate Envelope",
						"length"      => "10",
						"width"       => "7",
						"height"      => "0.25",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d40"     => array(
						"name"        => "Priority Mail Window Flat Rate Envelope",
						"length"      => "5",
						"width"       => "10",
						"height"      => "0.25",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d42"     => array(
						"name"        => "Priority Mail Small Flat Rate Envelope",
						"length"      => "6",
						"width"       => "10",
						"height"      => "0.25",
						"weight"      => "70",
						"type"        => "priority"
					),
					"d44"     => array(
						"name"        => "Priority Mail Legal Flat Rate Envelope",
						"length"      => "9.5",
						"width"       => "15",
						"height"      => "0.5",
						"weight"      => "70",
						"type"        => "priority"
					),
				),
			),
			'international' => array(
				'priority_mail_express' => array(
					// International Priority Mail Express
					"i13"     => array(
						"name"    => "Priority Mail Express Flat Rate Envelope",
						"length"  => "12.5",
						"width"   => "9.5",
						"height"  => "0.25",
						"weight"  => "4",
						"type"    => "express"
					),
					"i30"     => array(
						"name"    => "Priority Mail Express Legal Flat Rate Envelope",
						"length"  => "9.5",
						"width"   => "15",
						"height"  => "0.25",
						"weight"  => "4",
						"type"    => "express"
					),
					"i55"     => array(
						"name"   => "Priority Mail Express Flat Rate Box",
						"length" => "11",
						"width"  => "8.5",
						"height" => "5.5",
						"weight" => "20",
						"type"   => "express"
					),
					"i63"     => array(
						"name"    => "Priority Mail Express Padded Flat Rate Envelope",
						"length"  => "12.5",
						"width"   => "9.5",
						"height"  => "1",
						"weight"  => "4",
						"type"    => "express"
					),
				),
				'priority_mail' => array(
					// International Priority Mail
					"i8"      => array(
						"name"   => "Priority Mail Flat Rate Envelope",
						"length" => "12.5",
						"width"  => "9.5",
						"height" => "0.25",
						"weight" => "4",
						"type"   => "priority"
					),
					"i16"     => array(
						"name"   => "Priority Mail Flat Rate Small Box",
						"length" => "5.375",
						"width"  => "8.625",
						"height" => "1.625",
						"weight" => "4",
						"type"   => "priority"
					),
					"i9"      => array(
						"name"   => "Priority Mail Flat Rate Medium Box",
						"length" => "11.875",
						"width"  => "13.625",
						"height" => "3.375",
						"weight" => "20",
						"type"   => "priority"
					),
					"i9b"      => array(
						"name"   => "Priority Mail Flat Rate Medium Box",
						"length" => "11",
						"width"  => "8.5",
						"height" => "5.5",
						"weight" => "70",
						"type"   => "priority"
					),
					"i11"     => array(
						"name"   => "Priority Mail Flat Rate Large Box",
						"length" => "12",
						"width"  => "12",
						"height" => "5.5",
						"weight" => "20",
						"type"   => "priority"
					)
				)
			)
		)
	);*/
	
	/*private $addons = array(
		'SC-A-HP' 	=> array(
			'Name'			=> 'Hidden Postage',
			'Description'	=> 'Specifying Hidden Postage will generate a shipping label indicium that does not show the actual postage amount. This option is useful for customers who wish to hide the actual postage amount from the recipients of packages.'
		),	
		'SC-A-INS' 	=> array(
			'Name'			=> 'Stamps.com Insurance',
			'Description'	=> 'Stamps.com insurance add-on. Similar to USPS only lower cost and less paperwork.',
			'AmtReq'		=> true,
		),
		'SC-A-INSRM' 	=> array(
			'Name'			=> 'Insurance for Registered Mail',
			'Description'	=> 'Stamps.com insurance add-on for use with USPS Registered Mail. Currently unsupported.',
			'AmtReq'		=> true
		),
		'US-A-CM' 	=> array(
			'Name'			=> 'Certified Mail',
			'Description'	=> 'USPS Certified Mail.'
		),
		'US-A-COD' 	=> array(
			'Name'			=> 'Collect on Delivery',
			'Description'	=> 'USPS Collect on Delivery.'
		),
		'US-A-COM' 	=> array(
			'Name'			=> 'Certificate of Mailing',
			'Description'	=> 'USPS Certificate of Mailing.'
		),
		'US-A-DC' 	=> array(
			'Name'			=> 'USPS Delivery Confirmation',
			'Description'	=> 'Include Delivery Confirmation service. If the service is free of charge, it will be automatically added even if the caller does not request it.'
		),
		'US-A-ESH' 	=> array(
			'Name'			=> 'USPS Express – Sunday / Holiday Guaranteed',
			'Description'	=> 'Guaranteed Priority Mail Express delivery on a Sunday or holiday, incurring an additional fee.'
		),
		'US-A-INS' 	=> array(
			'Name'			=> 'USPS Insurance',
			'Description'	=> 'USPS Insurance',
			'AmtReq'		=> true
		),
		'US-A-NDW' 	=> array(
			'Name'			=> 'USPS Express – No Delivery on Saturdays',
			'Description'	=> 'Instruct the USPS not to attempt Priority Mail Express delivery on a Saturday (formerly weekend) when a business may be closed.'
		),
		'US-A-RD' 	=> array(
			'Name'			=> 'Restricted Delivery',
			'Description'	=> 'USPS Restricted Delivery (delivery to specifically named person only).'
		),
		'US-A-REG' 	=> array(
			'Name'			=> 'Registered Mail',
			'Description'	=> 'USPS Registered Mail.'
		),
		'US-A-RR' 	=> array(
			'Name'			=> 'Return Receipt Requested',
			'Description'	=> 'USPS Return Receipt Requested.'
		),
		'US-A-RRM' 	=> array(
			'Name'			=> 'Return Receipt for Merchandise',
			'Description'	=> 'USPS Return Receipt for Merchandise.'
		),
		'US-A-SC' 	=> array(
			'Name'  		=> 'USPS Signature Confirmation',
			'Description'	=> 'Include Signature Confirmation service.'
		),
		'US-A-SH' 	=> array(
			'Name'			=> 'Special Handling',
			'Description'	=> 'USPS surcharge for shipping "special handling" items.'
		),
		'US-A-NDW' 	=> array(
			'Name'			=> 'Do not Deliver on Saturday',
			'Description'	=> 'Priority Mail Express item cannot be delivered on Saturdays.'
		),
		'US-A-ESH' 	=> array(
			'Name'			=> 'Sunday/Holiday Delivery Guaranteed',
			'Description'	=> 'Priority Mail Express item will be delivered on Sunday or Holiday.'
		),
		'US-A-NND' 	=> array(
			'Name'			=> 'Notice of non-delivery.',
			'Description'	=> 'USPS notice of non-delivery.'
		),
		'US-A-RRE' 	=> array(
			'Name'			=> 'Electronic Return Receipt',
			'Description'	=> 'Electronic Return Receipt (ERR) is a USPS Extra Service that includes Signature proof of delivery.'
		),
		'US-A-LANS' 	=> array(
			'Name'			=> 'Live Animal No Surcharge',
			'Description'	=> 'Mailable Live Animals without charge.'
		),
		'US-A-LAWS' 	=> array(
			'Name'			=> 'Live Animal with Surcharge',
			'Description'	=> 'Mailable Live Animals with charge.'
		),
		'US-A-HM' 	=> array(
			'Name'			=> 'Hazardous Materials',
			'Description'	=> 'Hazardous Materials.'
		),
		'US-A-CR' 	=> array(
			'Name'			=> 'Cremated Remains',
			'Description'	=> 'Cremated Remains.'
		),
		'US-A-1030' 	=> array(
			'Name'			=> 'Deliver Priority Mail Express by 10:30 am',
			'Description'	=> 'The delivery option allows customers to send domestic Priority Mail Express packages to most locations in the U.S. by 10:30 a.m.'
		),
	);*/
	
	/**
	 * Private var - array usps service codes conversion for stamps.com
	 */
	public $service_types = array (
		'First-Class Mail' 			   		  => 'US-FC',
		'Media Mail' 			 	  		  => 'US-MM',
		'Bound Printed Matter' 	 	  		  => 'US-BP',
		'Pacel Post'				 	    	  => 'US-PP',
		'Priority Mail'				  		  => 'US-PM',
		'Express Mail'				  		  => 'US-XM',
		'Priority Mail Express International' => 'US-EMI',
		'Priority Mail International' 	 	  => 'US-PMI',
		'First Class Mail International' 	  => 'US-FCI',
	);
	
	public $trackable = array(
		'Priority Mail&#0174;',				 
		'Priority Mail Express&#8482',	
		'Priority Mail International&#0174',
		'Priority Mail Express International&#8482;', 	 
		'First Class Package Service&#8482; International' 
	);
	
	/**
	 * Private var - array containing Domestic Shipment country codes
	 */
	private $domestic = array( "US", "PR", "VI" );		
	
	/**
	 * Private var - array containing endpoint urls for testing and production environments
	 */
	private $wsdl = array( 
		'test' => 'https://swsim.testing.stamps.com/swsim/swsimv45.asmx?wsdl',
		'production' => 'https://swsim.stamps.com/swsim/swsimv45.asmx?wsdl'
	);
	
	/**
	 * Private var - array containing endpoint urls for testing and production environments
	 */
	private $endpoint_url;
	
		
	/**
	 * Public Function __construct
	 *
	 * Parameters - $post_id of the current order
	 * returns new slp_ups object
	 */ 
	public function __construct() {		
		$this->method_id  = 'usps';
		$this->label 	  = 'USPS';
		$this->settings   =  $this->init_settings();
		
		add_action( 'update_option_slp_usps_settings', array( $this, 'init_settings' ) );
		add_action( 'update_option_slp_usps_settings', array( $this, 'get_auth' ) );
		add_action( 'wp_ajax_add_postage', array( $this, 'add_postage' ) );
	}
	/**
	 *
	 * Public Function _get_settings_fields
	 *
	 * Parameters - none
	 * Return string
	 */
	public function get_settings() {
		$settings = $this->settings;
				
		$setting_fields = array( 
			array( 
				'id' 	=> 'slp_usps_settings',
				'title' => __( 'USPS Account Settings', 'slp' ),
				'type'	=> 'title',
				'desc'  => __( 'A Stamps.com account is required. Enter your login creditials below or click <a href="#">here</a> to register for a Stamps.com Account. *<strong>Monthly charges may apply.</strong>', 'slp' )
			),
		 	array(
				'id'	=> 'username',
				'title'	=> __( 'Stamps.com Username', 'slp' ),
				'type'	=> 'text',
				'desc'	=> '',
				'value'	=> 	isset( $settings['username'] ) ? $settings['username'] : '' ,
			),
			array(
				'id'	=> 'password',
				'title'	=> __( 'Stamps.com Password'  , 'slp' ),
				'type'	=> 'password',
				'desc'	=> '',
				'value'	=>  isset( $settings['password'] ) ? $settings['password'] : '' ,
			),	
			array(
				'id'	=> 'scanform',
				'title'	=> __( 'Enable Stamps Scanforms?', 'slp' ),
				'type'	=> 'checkbox',
				'tooltip'	=> __( '<br>The USPS Shipment Confirmation Acceptance Notice, known as a SCAN Form, is a piece of paper with a master barcode that includes all the packages associated with a group shipment. At the time of pick-up, the USPS employee scans the barcode on the SCAN form to disseminate tracking info for all the packages rather than scanning each package individually. This single step saves a lot of time for USPS employees. <br/><br/>When the barcode is scanned, the package information is instantly transmitted into the USPS tracking database, notifying both shippers and recipients that their shipments have entered the USPS mailstream and that the shipping process has started. After being scanned, shippers and package recipients can track and check the status of their packages using the USPS Tracking number.', 'slp' ),
				'value' => isset( $settings['scanform'] ) ? $settings['scanform'] : 'no',
			),
			array(
				'id'	=> 'debug',
				'title'	=> __( 'Enable Debug Mode', 'slp' ),
				'type'	=> 'checkbox',
				'desc'	=> __( 'Test mode for Stamps API.<br />*IP address must be whitelisted with Stamps.com for access. Click <a href="http://developer.stamps.com/developer/downloads/files/Stamps.com_SWS_How_To_Get_Started.doc">here</a> for information on how to register for to the testing server.', 'slp' ),
				'value'	=> isset( $settings['debug'] ) ? $settings['debug'] : 'no',
			),
			array(
				'type'	=> 'sectionend',
				'id'	=> 'slp_usps_settings',
			),
		);		
		
		if( ! $this->account_info ) {
			$this->get_auth();
		}
			
		if( ! is_soap_fault( $this->account_info ) ) {
			$setting_fields = array_merge( $setting_fields, $this->account_details() );
		}
		
		return apply_filters( 'slp_usps_admin_settings', $setting_fields );	
	}
	
	public function account_details() {
	  $account = $this->account_info;
	
	  $postage = array(
		  'Postage Balance' 			=> wc_price( $account->AccountInfo->PostageBalance->AvailablePostage ),
		  'Maximum Postage Allowed'	=> wc_price( $account->AccountInfo->MaxPostageBalance ),
	  );
		  
	  $meter_address = array(
		  'First Name'	 	=> $account->AccountInfo->MeterPhysicalAddress->FirstName,
		  'Last Name'		=> $account->AccountInfo->MeterPhysicalAddress->LastName,
		  'Company'		=> $account->AccountInfo->MeterPhysicalAddress->Company,
		  'Address'		=> $account->AccountInfo->MeterPhysicalAddress->Address1,
		  'Address2'		=> isset( $account->AccountInfo->MeterPhysicalAddress->Address2 ) ? $account->AccountInfo->MeterPhysicalAddress->Address2 : '' ,
		  'City'			=> $account->AccountInfo->MeterPhysicalAddress->City,
		  'State'			=> $account->AccountInfo->MeterPhysicalAddress->State,
		  'Zipcode'		=> $account->AccountInfo->MeterPhysicalAddress->ZIPCode,
	  );
		  
		  
	  $account_details = apply_filters( 'stamps_account_details', array(
		  array(
			  'id' 	=> 'stamps_account',
			  'title'	=> __( 'Stamps.com Account Details', 'slp' ),
			  'type'	=> 'title',
			  'desc'	=> __( 'Stamps.com Account Information', 'slp' ),
		  ),
		  array(	
			  'id' 	=> 'postage_details',
			  'title'	=> __( 'Available Postage', 'slp' ),
			  'data'	=> $postage,
			  'type'	=> 'data_table'
		  ),
		  array(	
			  'id' 	=> 'address_details',
			  'title'	=> __( 'Address Details', 'slp' ),
			  'data'	=> $meter_address,
			  'type'	=> 'data_table'
		  ),
		  array( 
			  'id'	=> 'stamps_account',
			  'type'	=> 'sectionend',
		  ),
	  ) );	
		  
	  return $account_details;
	}
	
	public function get_auth() {
		$this->settings = $this->init_settings();
		
		$params = array(
			'Credentials' => array(
				'IntegrationID' 	=> $this->integration_id,
				'Username'		=> $this->settings['username'],
				'Password'		=> $this->settings['password']
			)
		);	
	
		//Make connection to Stamps.com server. Catch exception if error occurs
		try{
			
			//check if debug option is enabled
			$this->endpoint_url = $this->settings['debug'] == 'yes' ? $this->wsdl['test'] : $this->wsdl['production'];	
			
			//Create new SoapClient connection for Stamps API access
			$this->stamps = new SoapClient( $this->endpoint_url, array( 'exceptions' => false ) );
			
			//Validate login credentials
			//Stamps.com login failed.
			if( is_soap_fault( $auth = $this->stamps->AuthenticateUser( $params ) ) ) {
				
				//Ensure we are on the USPS setting page
				if( isset( $_GET['section'] ) && $_GET['section'] === 'usps' ) {
					
					//display login failure
					SLP_Admin_Settings::add_error( 'Login Failed. Please enter valid credentials below.' );
				} 
				
				return false;
				
			//Stamps.com login successful
			} else {
				
				//get account info and store in global variable
				$this->get_account( $auth );
				
				//update we are on the USPS settings page. 
				if( isset( $_GET['section'] ) && $_GET['section'] === 'usps' ) {
					
					//get and display user welcome text and login success
					$firstname = $this->account_info->AccountInfo->MeterPhysicalAddress->FirstName;
			   		SLP_Admin_Settings::add_message( 'Login Successful. Welcome back ' . $firstname  . ' and thank you for using Shipping Label Pro.' );
				}
				
				return true;
			}
		
		//catch all other errors and display to user.
		} catch ( Exception $e )  {
			slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, $e );
		}	
	}
	
	public function get_account( $auth ) {
		$account = $this->stamps->GetAccountInfo( $auth );
  
		if( is_soap_fault( $account ) ) {
			slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, 'Stamps.com Error: ' . $account->faultstring );
		} else {
			$this->account_info = $account;
			$this->authenticator = $account->Authenticator;			
		}
	}
	
	/**
	 * Public Function un
	 *
	 * Parameters - $amount(postage), $control( control total setting )
	 *
	 * Return string - xml_response
	 */
	
	public function add_postage( $amount ) {	
	
		$integratorTxID = substr( md5( rand( 0, 1000000 ) ), 0, 64 );
		
		$this->get_auth();
		
		$params = array(
			'Authenticator'  => $this->account_info->Authenticator,
			'PurchaseAmount' => (float)$amount,
			'ControlTotal'   => (float)$this->account_info->AccountInfo->PostageBalance->ControlTotal,
			'IntegratorTxID' => $integratorTxID
		);
			
		$response = $this->stamps->PurchasePostage( $params );	
		 
		if( is_soap_fault( $response ) ) {	
			slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, $response->faultstring );
		}
		
		$trans_id = $response->TransactionID;
		$count = 0;
		$wait = 8;
		
		$status = $response->PurchaseStatus;
		
		while( ($status === 'Pending' || $status === 'Processing') && $count < 3 ){			
			sleep( $wait );
			
			$params = array( 
				'Authenticator' => $response->Authenticator,
				'TransactionID' => $trans_id,
				'IntegratorTxID' => $integratorTxID
			);
			
			$response = $this->stamps->GetPurchaseStatus( $params );
			$status = $response->PurchaseStatus;
			$wait *= $wait;
		}
		
		if( $status === 'Success' ) {
			$title = 'Payment Successful.';	
			unset( $_POST['amount'] );
			ob_start();?>
            <p>Your Postage Balance is <?php echo wc_price( $response->PostageBalance->AvailablePostage );?>. Transaction ID# <?php echo $trans_id; ?>.</p><p>Click continue to complete this shipment</p>
			<script type="text/javascript">
				(function($){
					var button = $('.ui-dialog-buttonset').children().get(0);
					$(button).show();	
					$('#back').hide();
				})(jQuery);
			</script><?php
			$message = ob_get_clean();
            
		} else if( $status === 'Rejected' ) { 
			$reason  = $response->RejectionReason;
			$title  = 'Transaction Failed';
			$message = '<p>System Response: Purchase Status - ' . $status . '</p><p>Rejection Reason - ' . $reason . '</p><p>Transaction ID: '. $trans_id . '</p>Please try again. If the problems persists please contact Stamps.com Customer Service at 1(888)434-0055. Representatives are available Monday - Friday 6am - 6pm Pacific Time.</p>';
				$xml['ErrorDescription'] = $reason;
		} 

		$xml = array(	
			'Success'		 => true,
			'Title'  		 => $title,
			'StatusMessage'  => $message,
			'Post'			 => $_POST['post']	
		);
		
		echo json_encode( $xml );
		die();
	}
	
	/**
	 * Public Function schedule_pickup
	 *
	 * Parameters - $prn ( Pickup Request Number )
	 * Cancels specified Pickup Request
	 */ 
	public function schedule_pickup( $packages ) {
		$this->xml_response['Call'] = 'PickupConfirm';
		
		$this->get_account();
		
		$address = $this->account_info->Address;
		
		$pieces = count( $packages );
	
		foreach( $packages as $package ) {
			$weight += $package->weight;
		}
		
		$service = $this->get_service();
		
		$services = array( 
			'Priority Mail' => 'NumberOfPriorityMailPieces',
			'Express Mail'  => 'NumberOfExpressMailPieces',
			'Standard Post' => 'NumberOfOtherPieces',
			'Media Mail' 	=> 'NumberOfOtherPieces',
		);
		
		$service_key = array_key_exists( $service, $services ) ? $services[$service] : 'NumberOfInternationalPieces'; 
		
		$params = array(
			'Authenticator' => $this->account_info->Authenticator,
			'FirstName' 	=> $address->FirstName,
			'LastName'		=> $address->LastName,
			'Company'		=> $address->Company,
			'Address'		=> $address->Address1,
			'City'			=> $address->City,
			'State'			=> $address->State,
			'ZIP'			=> $address->ZIPCode,
			'PhoneNumber'	=> $address->PhoneNumber,
			$service_key	=> $pieces,
			'TotalWeightOfPackagesLbs' => $weight,
			'PackageLocation' => 'Office'				
		);
	
		$shipment = $this->stamps->CarrierPickup( $params );
	
		if( ! is_soap_fault( $shipment ) ) {		
			//set pickup info
			$pickup_date = $shipment->PickupDate;
			$pickup_day = $shipment->PickUpDayOfWeek;
			$pickup_ref = $shipment->ConfirmationNumber;
			
			//create xml_response for ajax processing
			$this->xml_response['success'] = 1;
			$this->xml_response['PickupRef'] = $pickup_ref;
			$this->xml_response['TrackingStatus'] = 'Pickup Scheduled: ' . date( 'm-d-Y', strtotime( $pickup_date ) );
			$this->xml_response['StatusMessage'] = 'Your USPS Pickup Request has been scheduled on ' . $pickup_day . ' - ' . date( 'm-d-Y', strtotime( $pickup_date ) ) . '.';																		
		} else {
			//Error Handling
			$this->xml_response = $this->handle_errors( $shipment );
		}
		
		return $this->xml_response;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function check_pickup( $pickup_date, $pickup_ref ) {
		global $wpdb;
				
		$pickup_date = strtotime( $pickup_date );
				
		//format pickup date for title
		$post_title = date( 'Ymd', $pickup_date );
		
		//Query database for existing scheduled pickup	
		$query = "SELECT ID FROM $wpdb->posts WHERE post_title = '$post_title' AND post_type = 'slp_pickup'";
		
		//return pickup post ID if one exists on the specified date
		$pickup_post_id = $wpdb->get_var( $query );
		
		/*if pickup post exists add this order to pickups array for record keping 
		else create new pickup post and API request*/
		if( $pickup_post_id ) {	
			
			$this->shipment['_pickup_post_id'] = $pickup_post_id;
						
			$pickup_data = get_post_meta( $pickup_post_id, '_pickup_data', true );
	
			array_push( $pickup_data['_pickups'], $this->order->id );
			
		} else {
		
			//Create new pickup post and associated post neta.	
			$pickup_post_id = $this->create_pickup_post( $post_title );	
				
			$this->shipment['_pickup_ref'] = $pickup_ref;
			$this->shipment['_pickup_date'] = $post_title;
			$this->shipment['_pickup_post_id'] = $pickup_post_id;
			$this->shipment['_pickup_info' ]  = date( 'm-d-y', strtotime( $post_title ) ) . ' - ' . $pickup_ref;
		
			$pickup_data['_pickup_ref'] = $pickup_ref;
			$pickup_data['_pickups'][] = $this->order->id;
			
			//Update pickup post meta
			update_post_meta( $pickup_post_id, $this->method_name . '_pickup_data', $pickup_data );	
						
		}
		return;
	}
	
	public function void_shipment() {
		$stampsTxIDs = $this->shipment['_stampsTxIDs'];
		$count = 0;
		
		$auth = $this->get_auth();
		
		$authenticator = $auth->Authenticator;
		
		foreach( $stampsTxIDs as $key => $stampsTxID ) {
			$params = array( 
				'Authenticator' => $authenticator,
				'StampsTxID'	=> $stampsTxID,
			);
			
			$void = $this->stamps->CancelIndicium( $params );
			
			$authenticator = $void->Authenticator;
			
			if( !is_soap_fault( $void ) ) {
				$message .= '<p>' . $count . ' packages have be canceled. Please discard all print postage associated with these packages. Your account will be credit upon USPS approval.</p>';
			} else {
				$message .= '<p>' . 'The package associated with tracking number ' . $this->shipment['_tracking_numberss'][$key] . ' has not been cancelled due to an error. See error details below for more information.';
				$message .= '<p>' . 'Error Detail: Error code - ' . $void->faultcode . '. Error description - ' . $void->faultstring;
			}
			
			++$count;
		}
		
		$this->xml_response['Call'] = 'VoidShipment';
		$this->xml_response['ResponseStatusCode'] = 1;
		$this->xml_response['StatusMessage'] = $message;
		$this->xml_response['Title'] = 'Void Shipment Request';
		
		return $this->xml_response;	
	}
	
	public function get_content_types() {
		$content_types = array(
			'Gift' => 'Gift',
			'Document' => 'Documents',
			'Commerical Sample' => 'Commercial Sample',
			'Other' => 'Other'
		);
		
		return $content_types;
	}
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function get_boxes() {
		$settings = get_option( 'woocommerce_usps_settings' );
		return $settings['boxes'];
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function get_method() {
		return 'USPS';
	}
	 
	public function check_postage_balance( $order, $shipment ) {
		
		$this->get_auth();
		
		$account = $this->account_info;
		
		 //Get shipping charged to customer
		$postage_charged = $shipment['_shipping_cost'];
		
		//Get user's available postage balance
		$postage_balance = (float)$account->AccountInfo->PostageBalance->AvailablePostage;
		
		if( $postage_balance < $postage_charged ) {
			$max_postage 	 = $account->AccountInfo->MaxPostageBalance;
			$control_total 	 = $account->AccountInfo->PostageBalance->ControlTotal;
			$allowed_postage = $max_postage - $postage_balance;
			
			ob_start();?>
			<div id="content">
				<p>You currently do not have enough prepaid postage available to complete this transaction.</p>
                <p>Postage required: <?php echo wc_price( $postage_charged ); ?>
				<p>Your account balance is <strong><?php echo wc_price( $postage_balance ); ?></strong>.</p>
				<p>Purchase Amount: $ <input type="text" id="postage_amount" placeholder="10.00" value="10.00" /><span><strong> Min = $10/Max = <?php echo wc_price( $allowed_postage ); ?></strong></span></p>
			</div>
			<p><button id="add_postage" class="dialog_nav no_continue">Next</button></p>
			
			<script type="text/javascript">
				(function($) {
					$('#add_postage').on('click', function() {
						var amount = $('#postage_amount').val();
						var title = 'Confirm Postage Purchase';
						var history = {
							Title: $('.dialog').dialog( 'option', 'title' ),
							StatusMessage: $('.dialog_content').html()
						}
							  						
						$('.dialog').dialog( 'option', 'title', title );
						$('#content').html('<p>Confirm postage purchase of $' + amount + '.</p>');
						$('#back').text('Edit Purchase').show();
						$('#add_postage').text('Confirm Purchase').off('click').on('click', function() {
							$('.dialog').dialog( 'option', 'title', 'Processing...Action may take upto 30 seconds.');
							var data = {
								action: 'process_shipment',
								amount: amount,
								post: <?php echo $_POST['post']; ?>
							}
							
							ajax_request( data );
						});				
					});
				})(jQuery);
			</script><?php
			
			$xml = array(
				'Success' 			=> true,
				'Title'				=> 'Action Required',
				'PostageBalance'  	=> $postage_balance,
				'MaxPostageBalance' => $max_postage,
				'AllowedPostage'	=> $allowed_postage,
				'StatusMessage'		=> ob_get_clean(),
			);
			
			echo json_encode( $xml );
			die();
		} 
		
		return;
	}

	
	public function verify_address( $order, $shipment ) {
		
		$this->get_auth();	
	
		if( isset( $_POST['address'] ) ) {
			$order->set_address( $_POST['address'], 'shipping' );
		}
		
		$params = array(
			'Authenticator' => $this->authenticator,
			'Address' => $this->get_shipping_address( $order ),
		);
		
		$check = $this->stamps->CleanseAddress( $params );
			
		if( is_soap_fault( $check ) ) {
			slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, $check->faultstring );
		} else {
			if( ! $check->AddressMatch ) {
				if( $check->CityStateZipOK ) {
					$valid = false;
					$message .= 'The City, State, & ZipCode entered are valid; however, no match was found for the street address. Please check the street address for errors.';
				} else {
					$valid = false;
					$message .= 'The address provided is not valid. Please confirm address information below and resubmit.';
				}
			} else {	
				$valid = true;
				$message = 'Please review updated address below and click continue to proceed.';
			}
		
			$temp = array(
				'address_1' => $check->Address->Address1,
				'address_2' => $check->Address->Address2,
				'city' 		=> $check->Address->City,
				'state' 	 	=> $check->Address->State,
				'country'	=> isset( $check->Address->Country ) ? $check->Address->Country : $order->shipping_country,
			);
			
			if( isset( $check->Address->ZIPCode ) ) {
				$temp['postcode'] = $check->Address->ZIPCode . ' - ' . $check->Address->ZIPCodeAddOn ;
			} else if( isset( $check->Address->PostalCode ) ) {
				$temp['postcode'] = $check->Address->PostalCode;
			} else {
				$temp['postcode'] = '';
			}
				
			$address[] = $temp;
				
			$xml = array(
				'address' => $address,
				'valid'	  => $valid,
				'message' => $message
			);
				
			slp_ajax_functions::verify_address( $xml, $order, $shipment );
		}
	}	
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function get_rates( $order, $shipment, $package = '' ) {
		
		$this->get_auth();
		
		$address = $this->account_info->Address;
		
		$authenticator = $this->authenticator;
		
		$service = $this->get_service( $order );	
		
		$service_code = $this->service_types[$service];
		
		$total = 0.00;
				
		$packages = empty( $package ) ? $shipment['_packages'] : (array)$shipment['_packages'][$package];
	
		foreach( $packages as $package ) {
			$type = $this->get_package_type( $package );
			$params = array(
				'Authenticator' => $authenticator,
				'Rate' => array (
					'FromZIPCode' 	=> $address->ZIPCode,
					'ServiceType'	=> $service_code,
					'WeightLb' 		=> $package->weight,
					'PackageType'  	=> $type,
					'Length'		=> $package->length,
					'Width'			=> $package->width,
					'Height'		=> $package->height,
					'ShipDate'		=> date( 'Y-m-d' ),
				)
			);
			
			if( in_array( $order->shipping_country, $this->domestic ) ) {
				$params['Rate']['ToZIPCode'] = strlen( $order->shipping_postcode > 5 ) ? substr( $order->shipping_postcode, 0, 5 ) : $order->shipping_postcode;
			} else {
				$params['Rate']['ToCountry']	 = $order->shipping_country;
				$params['Rate']['DeclaredValue'] = $order->get_subtotal();
			}

			$rate = $this->stamps->getRates( $params );
						
			if( is_soap_fault( $rate ) ) {
				slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, $rate->faultstring );
			}	else {
				$authenticator = $rate->Authenticator;
				unset( $rate->Rates->Rate->AddOns );
				$total += $rate->Rates->Rate->Amount;
				$package->Rate = $rate->Rates->Rate;
				$package->PackageType = $type;
			}
		}
		
		$shipment['_packages'] = $packages;
		$shipment['_shipping_cost'] = $total;
		
		$xml = array(			
			'ShippingTotal' => $total
		);	
		
		slp_ajax_functions::confirm_rates( $order, $shipment );
	}
	
	/**
	 * Get and update tracking information if available
	 *
	 * @param  array $Shipment for order we are checking
	 * @param  mixed $item_type type of the item we're checking, if not a line_item
	 * @return integer
	 */
	public function track_shipment( $shipment, $order ) {
		//get new authentication key
		$this->get_auth();
		
		//load packages for processing
		$packages = $shipment['_packages'];
				
		//parse through packages
		foreach( $packages as $key => $package ) {	
						
			//check if tracking is necessary
			if( ! isset( $_POST['tracking_number'] ) && ( $package->id === 'No Tracking# Assigned' ) ) {
				return $shipment;
			} 
			
			//assign function parameters
			$params = array( 
				'Authenticator'  => $this->authenticator,
				'TrackingNumber' => $package->id
			);
			
			//track package
			$tracking = $this->stamps->TrackShipment( $params );
						
			//handle errors
			if( is_soap_fault( $tracking ) ) {
				
				$shipment['_shipment_status'] = 'Tracking Error';
				
				$package->Error = array(
					'code'  => $tracking->faultcode,
					'error' => $tracking->faultstring
				);
				
				if( end( $packages ) )
					return $shipment;
				
				continue;	
				 
			} 
			
			//set success to true for updating tracking number
			$sucess = true;
			
			//reset global autheniticator for next package
			$this->authenticator = $tracking->Authenticator;
							
			//if tracking information is returned	
			if( isset( $tracking->TrackingEvents ) ) {
				
				$events = $tracking->TrackingEvents->TrackingEvent;
			
				$package->TrackingStatus = array();
				
				foreach( $events as $event ) {
					
					//format tracking information for storage
					$timestamp =  $event->Timestamp;
			
					$event_type = strtoupper( $event->TrackingEventType );
					
					$tracking_info = array( 
						'timestamp' => date( 'm/d/Y h:i a', strtotime( $timestamp, current_time( 'timestamp' ) ) ),
						'status'	=> $event_type,
						'desc'		=> $event->Event,
						'location'	=> array(
							'city' 		=> $event->City ,
							'state'		=> $event->State ,
							'zip'		=> $event->Zip,
							'country'	=> $event->Country,
						),
						'signer' 	=> $event->SignedBy,
					);
					
					$package->TrackingStatus[] = $tracking_info; 
				}
			
			//add updated tracking info
			} else if( count( $events ) > count( $package->TrackingStatus ) ) {
				//update tracking results
				$package->TrackingStatus[] = $tracking_info;
				
				$order->add_order_note( 'USPS Shipment Status Updated: ' . $status, 'shipping_label_pro' );		
			} 				
		}

		//update shipment data
		$shipment['_shipment_status'] = $event;
		
		//add changes to shipment
		$shipment['_packages'] = $packages;
		
		//return shipment for further processing
		return $shipment;
	}
	
	public function update_tracking( $order, $shipment ) {
		
		//if updating tracking number update shipment and return xml array for js processing
		if( isset( $_POST['tracking_number'] ) ) {
			
			//update tracking number
			$packages[$_POST['box']]->id = $_POST['tracking_number'];
			
			//update changes to shipment
			$shipment['_packages'][$_POST['box']] = $packages[0];
			
			//update shipment status
			$shipment['_shipment_status'] = $package->TrackingStatus['status'];
			
			//save changes to DB
			update_post_meta( $order->id, '_shipment', $shipment );
			
			//Prepare JSON array for JS processing
			$xml = array( 
				'Success' => true,
				'TrackingNumber' => $package->id,
				'ShipmentStatus' => $package->TrackingStatus['status']
			);
			
			//send JSON array to JS for processing
			echo json_encode( $xml );
			die();
		}
	}	

	public function create_shipment( $order, $shipment ) {	
		
		//get authoriztion key 
		$this->get_auth();	
	
		//get user address info		
		$address = $this->account_info->Address;
		
		//check if shipment is domestic or international
		$domestic = in_array( $order->shipping_country, $this->domestic );
		
		//load packages 
		$packages = $shipment['_packages'];
		
		//set costs to zero
		$shipment['_shipping_costs'] = 0;
		
		//load senders as
		//$address = $this->get_sender_address();
		
		//loop through packages		
		foreach( $packages as $key => $package ) {
			
			//verify a label has been generated
			if( ! isset( $package->ShippingLabel ) ) {
			
				//create unique transaction ID		
				$integratorTxID = substr( md5( rand( 0, 1000000 ) ), 0, 64 );
				
				//set params for API call
				$params = array(  
					'Authenticator'  => $this->authenticator,
					'IntegratorTxID' => $integratorTxID,
					'Rate' 			 => $package->Rate,
					'From' 			 => $address,
					'To' 			 => $this->get_shipping_address( $order ),
				);		
				
				//check for memo and set params if needed
				if( isset( $POST['memo'] ) ) {
				  	$params['memo'] = sanitize_text_field( $_POST['memo'] );
					$params['printMemo'] = true;
				}
				
				//international shipment
				if( ! $domestic ) {
					
					//load and set customs form params	
					if( isset( $shipment['_customs'] ) ) {
						$params['Customs'] = $shipment['_customs'];
					} else {
						slp_ajax_functions::get_customs_form( $order, $shipment );
					}
				}
				
				//receive create indicium response				
				$response = $this->stamps->CreateIndicium( $params );
		
				//handle errors	
				if( is_soap_fault( $response ) ) {			
					
					//save transaction id for later use
					$package->IntegratorTxID = $integratorTxID;
					
					//document errors
					$package->Errors = array(
						'type'  => 'processing',
						'code'  => $response->faultcode,
						'error' => $response->faultstring
					);
					
					//send to error handler
					$this->error_handler( $order, $shipment, __FUNCTION__, __LINE__, $response );
					
					//get new authenticator
					$this->get_auth();
					$authenticator = $this->authenticator;				
					
					//process next package if any				
					continue;
				}
				//update shipping costs
				$shipment['_shipping_costs']  += (float)$response->Rate->Amount;
				
				//check if package is trackable
				if( $this->is_trackable( $order ) ) {
					
					//set package tracking number
					$package->id = isset( $response->TrackingNumber ) ? $response->TrackingNumber : 'Not Trackable';
					//set initial tracking info
					$shipment['_tracking_info'] = array( 
						'status' => 'CONFIRMED',
						'timestamp' => current_time( 'm/d/Y h:m a', 'timestamp' )
					);
				}
				
				$package->ShippingCost     = (float)$response->Rate->Amount;
				
				$package->ShippingLabel    = $response->URL; 
				
				$package->StampsTxID       = $response->StampsTxID;
				
				$txIDs[] = $response->StampsTxID;
				
				$this->authenticator = $response->Authenticator;	
			}
		}
	
		$shipment['_shipment_status'] = 'CONFIRMED';
		$shipment['_shipping_date']   = $response->Rate->ShipDate;
		
		if( $this->settings['scanform'] ) {		
			$shipment = $this->get_scanform( $shipment );
		}
		
		slp_ajax_functions::confirm_shipment( $order, $shipment, true );
	}

	public function get_package_type( $package ) {
		$length = $package->length;
		$width  = $package->width;
		$height = $package->height;
		
		$dimensions = array( woocommerce_get_dimension( $length, 'in' ), woocommerce_get_dimension( $height, 'in' ), woocommerce_get_dimension( $width, 'in' ) );
		
		$girth = 2*( $width + $height );	 
				
		if( ( ( $length >= 11.5 && $length <= 15 ) || ( $width >= 6.125 && $width <= 12 ) ) && ( $height >= 0.25 && $height <= 0.75 ) ) {
			$package_type = 'Large Envelope or Flat'; 
		} else if(  $girth + $length <= 84 ) {
			$package_type = 'Package';
		} else if( ( $girth + $length > 84  ) && ( $length + $girth <= 108 ) ) {
			$package_type = 'Large Package';	
		} else if( ( max( $dimensions ) > 12 ) && ( $length + $girth > 108 && $length + $girth >= 130 ) ) {
			$package_type = 'Oversized Package';
		}
		
		return $package_type;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	private function get_service( $order ) {
		$service = $order->get_shipping_method();
		$service = substr( $service, 0, strpos( $service, '®') ) == '' ? substr( $service, 0, strpos( $service, '&') ) : substr( $service, 0, strpos( $service, '®') );
				
		return $service;
	}

	private function get_scanform( $shipment ) {
		
		$params = array( 
			'Authenticator' 		=> $this->authenticator,
			'StampsTxIDs' 	    => $txIDs,
			'FromAddress'    	=> $this->get_sender_address(),
			'ImageType'		    => 'Pdf',
			'PrintInstructions' => false,
		);
		
		$scanform = $this->stamps->CreateScanForm( $params );
		
		if( is_soap_fault( $scanform ) ) {
			
			$this->get_auth();
			
			$params['Authenitcator'] = $this->authenticator;
			
			$scanform = $this->stamps->CreateScanForm;
			
			if( is_soap_fault( $scanform ) ) {
			
				$shipment['_errors'][] = array( 
					'type'  	  => 'scanform',
					'code'		  => $scanform->faultcode,
					'error' 	  	  => $scanform->faultstring,
					'StampsTxIDs' => $txIDs,
				);
				
				$this->error_handler( $order, $shipment, __FUNCTION__, __LINE__, $e );
			}
		
		} else{
			
			$shipment['_message'] = '<strong>USPS Scanform: This form will be scanned at pickup. Click the link to view and print.</strong> <a href="' . $scanform->Url . '" target="_blank">Open ScanForm</a>';
			
			$shipment['_scanform_id'] = $scanform->ScanFormId;
			
			$shipment['_scanform_url'] = $scanform->Url;
		}	
		
		return $shipment;	
	}
	
	public function get_sender_address() {
		$address = $this->account_info->Address;

		return apply_filters( 'slp_usps_shipping_address', array(
			'Company' 		=> $address->Company,
			'Address1'		=> $address->Address1,
			'City'			=> $address->City,
			'State'			=> $address->State,
			'ZIPCode'		=> $address->ZIPCode,
			'ZIPCodeAddOn'  => $address->ZIPCodeAddOn,
			'PhoneNumber' 	=> $address->PhoneNumber
		) );	
	}
	
	public function get_shipping_address( $order ) {
		
		$address = array(
			'FirstName'   => $order->shipping_first_name,
			'LastName'    => $order->shipping_last_name,
			'Company' 	  => $order->shipping_company,
			'Address1'	  => $order->shipping_address_1, 
			'Address2' 	  => $order->shipping_address_2, 
			'City'		  => $order->shipping_city,
			'State'		  => $order->shipping_state,
			'Country'	  => $order->shipping_country,
			'PhoneNumber' => $order->billing_phone
		);	
		
		if( ! empty( $order->shipping_postcode ) ) {
			if( in_array( $order->shipping_country, $this->domestic ) ) {
				$address['ZIPCode'] = substr( $order->shipping_postcode, 0, 5 );
				if(  strlen( $order->shipping_postcode ) > 5 )
					$address['ZIPCodeAddOn'] = substr( $order->shipping_postcode, 8 );
			} else {
				$address['PostalCode'] = substr( $order->shipping_postcode, 0, strpos( $order->shipping_postcode, ' ') );
			}
		}
			
		return $address;
	}
	
	public function error_handler( $order, $shipment, $function, $line, $error ) {
		
		switch( $error->faultstring ) {
			case 'Expired Authenticator':
			case 'Invalid Conversation Token':
			case 'Conversation Out-of-Sync':
				$this->get_auth();
				break;
			case 'Insufficient Postage':
				$this->check_postage_balance( $order, $shipment );
			default:
				slp_ajax_functions::error_handler( $function, __CLASS__, $line, $error->faultstring, $error );
		}
		
		return;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	private function xml_to_array( $array ) {
		$parsed = array();

		foreach( $array as $element ) {	
			$parsed[] = (string)$element;	
		}	

		return $parsed;
	
	}
	
	/**
	 *
	 *
	 *
	 *
	 */
	public function is_trackable( $order ) {
		$method = substr( $order->get_shipping_method(), 0, strpos( $order->get_shipping_method(), '(' ) - 1 );		

		return in_array( trim( $method ), $this->trackable ) ? true : false; 
			
	}
}

endif;
?>