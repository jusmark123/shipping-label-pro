<?
/*
 * Class Name: 		SLP UPS
 * Version: 		3.0.0
 */
 if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly
	
 if( ! class_exists( 'SLP_UPS' ) ) :
 
class SLP_UPS extends SLP_Shipping_Method {

	private $domestic = array( "US", "PR", "VI" );
	
	/*private $boxes = array (
		'UPS' => array(
			"01" => array(
				"name" 	 => "UPS Letter",
				"length" => "12.5",
				"width"  => "9.5",
				"height" => "0.25",
				"weight" => "0.5"
			),
			"03" => array(
				"name" 	 => "Tube",
				"length" => "38",
				"width"  => "6",
				"height" => "6",
				"weight" => "100"
			),
			"24" => array(
				"name" 	 => "25KG Box",
				"length" => "19.375",
				"width"  => "17.375",
				"height" => "14",
				"weight" => "25"
			),
			"25" => array(
				"name" 	 => "10KG Box",
				"length" => "16.5",
				"width"  => "13.25",
				"height" => "10.75",
				"weight" => "10"
			),
			"2a" => array(
				"name" 	 => "Small Express Box",
				"length" => "13",
				"width"  => "11",
				"height" => "2",
				"weight" => "100"
			),
			"2b" => array(
				"name" 	 => "Medium Express Box",
				"length" => "15",
				"width"  => "11",
				"height" => "3",
				"weight" => "100"
			),
			"2c" => array(
				"name" 	 => "Large Express Box",
				"length" => "18",
				"width"  => "13",
				"height" => "3",
				"weight" => "30"
			)
		)
	);*/
	
	public $serivce_codes = array( 
	'UPS Express®' 	 		=> '07',
	'UPS Expedited®' 		=> '08',
	'UPS StandardSM'  		=> '11',
	'UPS Express Plus®'		=> '54',
	'UPS Express Saver®'	=> '65',
	'UPS Today StandardSM'	=> '82',
	'UPS Today Dedicated Courier SM' => '83',
	'UPS Today Express'		=> '85',
	'UPS Today Express Saver' => '86',
	'UPS Express Early A.M®' => '14',
	'UPS Worldwide Express Plus®' => '54',
	'UPS Express®' => '01',
	'UPS Worldwide Express®' => '01',
	'UPS Express Saver ®' => '65',
	'UPS Worldwide Express Saver®' => '65',
	'UPS Expedited®' => '08',
	'UPS Worldwide Expedited®' => '08',
	'UPS 3Day Select®' => '12',
	'UPS StandardSM' => '11',
	'UPS Express®' => '07',
	'UPS Expedited®' => '08',
	'UPS Standard' => '11',
	'UPS Express Plus®' => '54',
	'UPS Saver' => '65', 
	'UPS Express®' => '07',
	'UPS Worldwide Express Plus®' => '54',
	'UPS Saver' => '65',
	'UPS Next Day Air®' => '01',
	'UPS 2nd Day Air®' => '01',
	'UPS Ground' => '01',
);
/*'07' => 'UPS Worldwide Express®'
'08' => 'UPS Next Day Air® Early A.M.®',
'14' => 'UPS Worldwide Express Plus®'
'54' => 'UPS Saver',
'65' => 'UPS Express®',
'07' => 'UPS Expedited®',
'08'
"UPS StandardSM
Shipments Originating in the European Union
11
11
UPS Worldwide Express Plus®
Shipments Originating in the European Union
54
54
UPS Saver
Shipments Originating in the European Union
65
65
UPS Next Day Air®
Shipments Originating in United States
01
01
UPS 2nd Day Air®
Shipments Originating in United States
02
02
UPS Ground
Shipments Originating in United States
03
03
UPS Worldwide Express®
Shipments Originating in United States
07
07
UPS Worldwide Expedited®
Shipments Originating in United States
08
08
UPS StandardSM
Shipments Originating in United States
11
11
UPS 3 Day Select®
Shipments Originating in United States
12
12
UPS Next Day Air® Early A.M.®
Shipments Originating in United States
14
14
UPS Worldwide Express Plus®
Shipments Originating in United States
54
54
UPS 2nd Day Air A.M. ®
Shipments Originating in United States
59
59
UPS Worldwide Saver®
Shipments Originating in United States
65
65
UPS Next Day Air®
United States Domestic Shipments
01
01
UPS 2nd Day Air®
United States Domestic Shipments
02
02
UPS Ground
United States Domestic Shipments
03
03
UPS 3Day Select®
United States Domestic Shipments
12
12
UPS Next Day Air Saver®
United States Domestic Shipments
13
13
UPS Next Day Air® Early A.M.®
United States Domestic Shipments
14
14
UPS 2nd Day Air A.M. ®
United States Domestic Shipments
59
59
UPS Worldwide Express Freight SM
Shipments Originating in any country
96
96
UPS First-Class Mail
United States Domestic Shipments
M2
M2
UPS Priority Mail
United States Domestic Shipments
M3
M3
UPS Expedited Mail Innovations
United States Domestic Shipments
M4
M4
UPS Priority Mail Innovations
Shipments Originating in any country
M5
M5
UPS Economy Mail Innovations
Shipments Originating in any country
M6
M6"*/
		
	private $endpoints = array(
		'testing'	 => 'https://wwwcie.ups.com/ups.app/xml/',
		'production' => 'https://onlinetools.ups.com/ups.app/xml/'
	);

	private $test_tracking_numbers = array(
		'void' => '1ZISDE016691676846',
		'track' => array(
			'delivered' 	=> '1Z12345E0291980793',
			'in-transit'	=> '1Z12345E029198079',
			'orgin scan' 	=> '1Z12345E1392654435',
		),
	);
	
	/**
	 * Public Function __construct
	 *
	 * Parameters - $post_id of the current order
	 * returns new slp_ups object
	 */ 
	public function __construct() {		
		$this->method_id = 'ups';
		$this->label = 'UPS'; 
		$this->settings = $this->init_settings();
	}
	
	public function get_settings() {
		$settings = $this->settings;
		//var_dump( $settings['debug'] );
			
		return apply_filters( 'slp_ups_admin_settings', array(
			array( 
				'id' 	=> 'slp_ups_settings',
				'title' => __( 'UPS Account Settings', 'slp' ),
				'type'	=> 'title',
				'desc'  => __( 'Fields will auto populate If set in UPS Shipping Method Settings. Click <a href="https://www.ups.com/one-to-one/register?sysid=myups&loc=en_US">here</a> to register for a UPS Shipper Account.', 'slp' )
			),
			array(
				'id'		=> 'user_id',
				'title'		=> 'Username',
				'type'		=> 'text',
				'css'		=> '',
				'desc'		=> 'UPS Username',
				'value'		=> $settings['user_id'],
			),
			array(
				'id'		=> 'password',
				'title'		=> 'UPS Password',
				'type'		=> 'password',
				'css'		=> '',
				'desc'		=> 'UPS Password',
				'value'		=> $settings['password'],
			),	
			array(
				'id'		=> 'access_key',
				'title'		=> 'UPS API Access Key',
				'type'		=> 'text',
				'css'		=> '',
				'desc'		=> 'Required to access UPS servers. Click <a href="https://www.ups.com/one-to-one/register?sysid=myups&lang=en&langc=US&loc=en_US&returnto=https%3A%2F%2Fwww.ups.com%2Fupsdeveloperkit%3Floc%3Den_US%26rt1">here</a> to obtain an API Key.',
				'value'		=> $settings['access_key'],
			),
			array(
				'id'		=> 'shipper_number',
				'title'		=> 'UPS Shipper Number',
				'type'		=> 'text',
				'css'		=> '',
				'desc'		=> 'Account number to be charged for shipments. This field will auto populate If set in UPS Shipping Method Settings.',
				'value'		=> $settings['shipper_number'],
			),
			array(
			  	'id'		=> 'readytime',
			  	'title'		=> __( 'Select Earliest Pickup Time', 'shipping_label_pro' ),
			  	'type'		=> 'text',
				'css'		=> '',
				'class'		=> 'timepicker',
			  	'desc'		=> __( 'Select the earliest time that your shipment will be ready for pickup', 'shipping_label_pro' ),
			  	'value'		=> isset( $settings['readytime'] ) ? $settings['readytime'] : '9:00pm',
		  	),
		  	array(
				'id'		=> 'closetime',
				'title'		=> __( 'Select Latest Pickup Time', 'shipping_label_pro' ),
				'type'		=> 'text',
				'css'		=> '',
				'class'		=> 'timepicker',
			 	'desc'		=> __( 'Select the latest time that your shipment will be ready for pickup', 'shipping_label_pro' ),
				'value'		=> isset( $settings['closetime'] ) ? $settings['closetime'] : '5:00pm',
		  	),
		  	array( 
				'id'		=> 'saturday_pickup',
				'title'		=> 'Enable Saturday Pickup',
				'type'		=> 'checkbox',
				'css'		=> '',
				'desc'		=> 'Extra charges may apply for this service.',
				'value'		=> isset( $settings['saturday_pickup'] ) ? $settings['saturday_pickup'] : 'no',				
			),
			array(
				'id'		=> 'debug',
				'title'		=> 'Enable Debug Mode',
				'type'		=> 'checkbox',
				'css'		=> '',
				'desc'		=> 'Check to enter debug mode for test API functions',
				'value'		=> isset( $settings['debug'] ) ? $settings['debug'] : 'no',
			),
			array(
				'type'		=> 'sectionend',
				'id'		=> 'slp_ups_settings',
			)
		) );	
	}
	
	public function generate_account_html( $settings ) {
		
		$accounts= (array)$settings['shipper_number'];
		
		ob_start(); ?>
		<tr align="top" id="accounts_settings">
        	<th scope="row" class="titledesc"><?php _e( 'Accounts', 'slp' ); ?></th>
            <td class="forminp">
            	<table class="ups_accounts widefat">
                	<thead>
                    	<tr>
	                    	<th class="default"><?php _e( 'Default', 'slp' ); ?></th>
     	                   	<th class="account"><?php _e( 'Account', 'slp' ); ?></th>
							<th class="description"><?php _e( 'Description', 'slp' ); ?></th>
            	            <th class="status"><?php _e( 'Default', 'slp' ); ?></th>
                        </tr>
                   	</thead>
                    <tfoot>
                    	<tr>
                        	<th colspan="3">
                            	<a href="#" class="button plus insert"><?php _e( 'Add Account', 'slp' ); ?></a>
                                <a href="#" class="button minus remove"><?php _e( 'Remove Select Account(s)', 'slp' ); ?></a> 
                            </th>
                        </tr>
                    </tfoot>
                    <tbody>
                    	<?php
						foreach( $accounts as $key => $account ) {
							echo '<tr>
									<td width:"1%" class="default">
										<input type="radio" name="default_shipper_account" value"' . esc_attr( $account['shipper'] ) . checked( $default_account_number, $account['number'], false ) . ' />
									</td>
									<td class="account"
										' . $account['number'] . '
									</td>
									<td class="description">
										' . $account['description'] . '
									</td>
									<td class="status">';
									
							if( $account['enabled'] == 'yes' ) {
								echo '<span class="status-enabled tips" data-tip"' . __('Enabled', 'slp' ) . '">' . __( 'Enabled', 'slp' ) . '</span>';
							} else {
								echo '-';
							}
							
							echo '</tr>';
						} ?>
					</tbody>
				</table>
			</td>
		</tr>
			
       	<?php //return ob_get_clean();	
	}
	
	/**
	 * Public Function cancel_pickup
	 *
	 * Parameters - $prn ( Pickup Request Number )
	 * Cancels specified Pickup Request
	 */ 
	public function cancel_pickup() {
		
		$pickup_post_id = 
		
		$request  = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:wsf="http://www.ups.com/schema/wsf" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">' . "\n";
		$request .= "	<envr:Header>" . "\n";
		$request .= "		<upss:UPSSecurity>" . "\n";
		$request .= "			<upss:UsernameToken>" . "\n";
		$request .= "				<upss:Username>" . $this->ups_settings['user_id'] . "</upss:Username>" . "\n";
		$request .= "				<upss:Password>" . $this->ups_settings['password'] . "</upss:Password>" . "\n";
		$request .= "			</upss:UsernameToken>" . "\n";
		$request .= "			<upss:ServiceAccessToken>" . "\n";
		$request .= "				<upss:AccessLicenseNumber>" . $this->ups_settings['access_key'] . "</upss:AccessLicenseNumber>" . "\n";
		$request .= "			</upss:ServiceAccessToken>" . "\n";
		$request .= "		</upss:UPSSecurity>" . "\n";
		$request .= "	</envr:Header>" . "\n";
		$request .= "	<envr:Body>" . "\n";
		$request .= '		<PickupCancelRequest xmlns="http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n";
		$request .= "			<common:Request>" . "\n";
		$request .= "				<common:RequestOption/>" . "\n";
		$request .= "				<common:TransactionReference>" . "\n";
		$request .= "					<common:CustomerContext>cancel_pickup</common:CustomerContext>" . "\n";
		$request .= "				</common:TransactionReference>" . "\n";
		$request .= "			</common:Request>" . "\n";
		$request .= "			<CancelBy>02</CancelBy>" . "\n";
		$request .= "			<PRN>" . $prn . "</PRN>" . "\n";
		$request .= "		</PickupCancelRequest>" . "\n";
		$request .= "	</envr:Body>" . "\n";
		$request .= "</envr:Envelope>" . "\n";	
		
		$request = str_replace( array( "\n", "\r" ), '', $request );
		
		$xml = slp_ajax_functions::xml_request( $request, $this->endpoint_url[1] . 'Pickup', true );
			
		$xml->registerXPathNamespace( 'common', true );
				
		$xml_pickup_status = $xml->xpath( '//Response/ResponseStatus/Code' );
				
		$this->xml_response['call'] = 'Pickup Cancel Response';
		
		if( $xml_pickup_status[0] ) {
			$this->xml_response['ResponseStatusCode'] = $xml_pickup_status[0];
		} else {
			$xml->registerXPathNamespace( 'err', true );
			$xml_error = $xml->xpath( '//Errors/ErrorDetail/PrimaryErrorCode' );
				
			$this->xml_response['ErrorCode'] = (string)$xml_error[0]->Code;
			$this->xml_response['ErrorDescription'] = (string)$xml_error[0]->Description;
		
		}
		
		return $this->xml_response;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function check_pickup( $pickup_date ) {
		global $wpdb;
				
		$pickup_date =  $pickup_date == "" ? time() : strtotime( $pickup_date );
				
		//Set current time with UTC offset
		$currtime = strtotime( $this->utc_offset . ' hours');
	
		//Set shipment readytime on selected pickup date	
		$readytime = strtotime( $this->slp_settings['readytime'], $pickup_date );
		
		//Temp time used for timestamp comparison 	
		$temp_time =  strtotime( '-1 minute', $readytime );
	
		/* if post date and time greater than current time or is a Sunday add schedule pickup for next day
		   else if post date is a Saturday and Saturday pickup not enabled schedule pickup for next business day
		   else schdule pickup for today.*/
		if( $currtime > $temp_time || date( 'l', $temp_time ) == 'Sunday' ) {
			$post_title = strtotime( '+1 day' , $readytime );
		} else if( ( date( 'l', $temp_time ) == 'Saturday' || date( 'l', $post_title ) == 'Saturday' ) && !isset( $this->slp_settings['saturday_pickup'] ) ) {
			$post_title = strtotime( '+2 days' , $readytime );
		} else { 
			$post_title = $readytime;
		}
		
		//format pickup date for title
		$post_title = date( 'Ymd', $post_title );
		
		//Query database for existing scheduled pickup	
		$query = "SELECT ID FROM $wpdb->posts WHERE post_title = '$post_title' AND post_type = 'slp_pickup'";
		
		//return pickup post ID if one exists on the specified date
		$pickup_post_id = $wpdb->get_var( $query );
		
		//update shipment with pickup date
		$this->shipment['pickup_post_title'] = $post_title;
		
		/*if pickup post exists add this order to pickups array for record keping 
		else create new pickup post and API request*/
		if( $pickup_post_id ) {	
			
			$shipment['_pickup_post_id'] = $pickup_post_id;
						
			$pickup_data = get_post_meta( $pickup_post_id, $this->method_name . '_pickup_data', true );
			
			if( !in_array( $this->order->id, $pickup_data['_pickups']) ) {
				array_push( $pickup_data['_pickups'], $this->order->id );
			} else {
				$this->xml_response['PickupInfo'] = 'Pickup already scheduled for this order';	
			}
			
			$this->xml_response['PickupRef'] = $pickup_data['_pickup_ref'];
			$this->xml_response['PickupDate'] = date( 'm-d-Y', strtotime( $post_title ) ) . ' - ' . $pickup_data['_pickup_ref'];
			
			$shipment['_pickup_ref'] = $pickup_data['_pickup_ref'];
			$shipment['_pickup_date'] = $post_title;
			$this->shipment['_pickup_info' ]  = date( 'm-d-Y', strtotime( $post_title ) ) . ' - ' . $pickup_data['_pickup_ref'];
				
		} else {
			
			$xml = $this->schedule_pickup( $post_title );
			
			$xml = new SimpleXMLElement( $xml );
																	
			$xml->registerXPathNamespace( 'common', true );
				
			$xml_pickup_status = $xml->xpath( '//Response/ResponseStatus/Code' );
							
			$this->xml_response['call'] = 'Pickup Creation Response';
						
			/* If pickup request successful parse XML, update shipment and create new pickup post
			else parse error into xmlresponse array and return*/						
			if( (string)$xml_pickup_status[0] ) {
				
				$xml->registerXPathNamespace( 'pkup', true );
				$xml_success = $xml->xpath( '//PickupCreationResponse' );
				$prn = (string)$xml_success[0]->PRN;
				
				$this->shipment['_pickup_ref'] = $prn;
				$this->shipment['_pickup_date'] = $post_title;
				$this->shipment['_pickup_info' ]  = date( 'm-d-y', strtotime( $post_title ) ) . ' - ' . $prn;
				
				$this->xml_response['ResponseStatusCode'] = (string)$xml_pickup_status[0];
				$this->xml_response['PickupDate'] = date( 'm-d-Y', strtotime( $this->shipment['_pickup_date'] ) ) . ' - ' . $prn;
			
		    	//Create new pickup post and associated post neta.	
				$pickup_post_id = $this->create_pickup_post( $post_title );
			
				$pickup_data['_pickup_ref'] = $prn;
				$pickup_data['_pickup_cost'] = (float)$xml_success[0]->RateResult->GrandTotalOfAllCharge;
				$pickup_data['_pickups'][] = $this->order->id;
			
				//Update pickup post meta
				update_post_meta( $pickup_post_id, $this->method_name . '_pickup_data', $pickup_data );	
							
			} else {
				$this->xml_response['ResponseStatusCode'] = (string)$xml_pickup_status[0];
				
				$xml->registerXPathNamespace( 'err', true );
				$xml_error = $xml->xpath( '//Errors/ErrorDetail/PrimaryErrorCode' );
				
				$this->xml_response['ErrorCode'] = (string)$xml_error[0]->Code;
				$this->xml_response['ErrorDescription'] = (string)$xml_error[0]->Description;
				
				$this->shipment['_shipment_status'] = 'Pickup Not Scheduled on Error';
			}
		}
		
		return;
	}
	
	
	public function get_content_types() {
		$content_types = array(
			1 => 'Gift',
			2 => 'Documents',
			3 => 'Commercial Sample',
			4 => 'Other'
		);
		
		return $content_types;
	}
	
	public function check_rates() {
		$data = $_POST['data'];
		$packages = '';
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function get_boxes() {
		$settings = get_option( 'woocommerce_ups_settings' );
		return $settings['boxes'];
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function get_method() {
		return $this->method_name;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
 	public function get_shipment() {
		return $this->shipment; 
	}
	
	public function verify_address( $order, $shipment ) {
		$countries = new WC_Countries();
		
		if( ! in_array( $order->shipping_country, $this->domestic ) ) {
			$xml = array(
			 	'message' => 'UPS does not currently support validation for international addresses. Please ensure the address below is correct and click continue to proceed to complete customs form.',
			);	
			
		} else {
		
			$state = strlen( $order->shipping_state ) > 2 ? array_search( ucwords( strtolower( $order->shipping_state ) ), $countries->get_states( $order->shipping_country ) ) : $order->shipping_state;
			
			$name =  $order->shipping_first_name . ' ' . $order->shipping_last_name ;
			
			$request  = $this->xml_header();
			$request .= 
			"<AddressValidationRequest xml:lang='en-US'>
				<Request>
					<TransactionReference>
						<CustomerContext>Customer Context</CustomerContext>
						<XpciVersion>1.0</XpciVersion>
					</TransactionReference>
					<RequestAction>XAV</RequestAction>
					<RequestOption>1</RequestOption>
				</Request>
				<AddressKeyFormat>
					<ConsigneeName>$name</ConsigneeName>
					<AddressLine>$order->shipping_address_1</AddressLine>";
			if( ! empty( $order->shipping_address_2 ) )
			$request .=	"
					<AddressLine>$order->shipping_address_2</AddressLine>";
			$request .= "
					<PoliticalDivision2>$order->shipping_city</PoliticalDivision2>
					<PoliticalDivision1>$state</PoliticalDivision1>
					<PostcodePrimaryLow>$order->shipping_postcode</PostcodePrimaryLow>
					<CountryCode>$order->shipping_country</CountryCode>
				</AddressKeyFormat>			
			</AddressValidationRequest>";
		
			$request = str_replace( array( "\n", "\r" ), '', $request );	
	
			//send, receive and parse xml response	
			$response = $this->xml_request( $request, $this->endpoints['production'] . 'XAV' );
		
			$count = 1;
	
			if( isset( $response->AmbiguousAddressIndicator ) ) {
				$message = 'An exact match could not be found. Please select an address option below and click continue to proceed.';
				foreach( $response->AddressKeyFormat as $candidate ) {
					$address = array(
					  'city' 	 => (string)$candidate->PoliticalDivision2,
					  'state'	 => (string)$candidate->PoliticalDivision1,
					  'country'	 => (string)$candidate->CountryCode, 
					  'postcode' => (string)$candidate->PostcodePrimaryLow . ' - ' . (string)$candidate->PostcodeExtendedLow
					);
					
					foreach( $candidate->AddressLine as $line ) {
						$address["address_$count"] = (string)$line;
						++$count;
					}
					
					$addresses[] = $address;
				}
				$address = $addresses;
			} else if( isset( $response->ValidAddressIndicator ) ) {
				$message = 'The address was matched. Please review updated address below and click continue to proceed.';
	  
				$block = array(
					  'city'	 => (string)$response->AddressKeyFormat->PoliticalDivision2,
					  'state'	 => (string)$response->AddressKeyFormat->PoliticalDivision1,
					  'country'	 => (string)$response->AddressKeyFormat->CountryCode, 
					  'postcode' => (string)$response->AddressKeyFormat->PostcodePrimaryLow . ' - ' . $response->AddressKeyFormat->PostcodeExtendedLow
				);
				
				foreach( $response->AddressKeyFormat->AddressLine as $line ) {
					$block["address_$count"] = (string)$line;
					++$count;
				}
				
				$address[] = $block;
			} else if( isset( $response->NoCandidatesIndicator ) ) {
				$valid = false;
				$message .= 'The address provided is not valid. Please confirm address information below and resubmit.';
			}
				
			$xml = array(
				'address' => $address,
				//'valid'	  => $valid,
				'message' => $message
			);
		}
		slp_ajax_functions::verify_address( $xml, $order, $shipment );;	
	}
	
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function get_rates( $order, $shipment ) {
		
		$package_requests = $this->get_package_request( $shipment['_packages'] );
		
		//Create XML for Shipment Confirm Request
		$request  = $this->xml_header();
		$request .= 
		"<ShipmentConfirmRequest xml:lang='en-US'>
			<Request>
				<RequestAction>ShipConfirm</RequestAction>
				<RequestOption>nonvalidate</RequestOption>
		 	</Request>
		  	<TransactionReference>
			 	 <CustomerContext>Shipping Label Pro</CustomerContext>
		  	</TransactionReference>
			<Shipment>";
		$request .=	isset( $shipment['_customs'] ) ? $this->get_customs( $order, $shipment ) : '';
		$request .=	$this->get_shipper();
		$request .=	$this->get_shipping_address( $order );
		$request .= "		
				<PaymentInformation>
					<Type>01</Type>
					<Prepaid>
						<BillShipper>
							<AccountNumber>" . $this->settings['shipper_number'] . "</AccountNumber>
						</BillShipper>
					</Prepaid>
				</PaymentInformation>";
		$request .=	$this->get_service_code( $order );
		foreach ( $package_requests as $package_request ) {
			$request .= $package_request;
		}
		$request .= "
			</Shipment>
			<LabelSpecification>
				<LabelPrintMethod>
					<Code>GIF</Code>
					<Description>gif file</Description>
				</LabelPrintMethod>
				<HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
				<LabelImageFormat>
					<Code>GIF</Code>
				</LabelImageFormat>
			</LabelSpecification>
		</ShipmentConfirmRequest>";

		$request = str_replace( array( "\n", "\r" ), '', $request );	
		
		//var_dump( htmlentities( $request ) );
					
		//send, receive and parse xml response	
		$response = $this->xml_request( $request, $this->endpoints['production'] . 'ShipConfirm' );
		
		//var_dump( $response );
			
		$shipment['_shipment_digest'] = (string)$response->ShipmentDigest;
		$shipment['_shipping_cost']  = (float)$response->ShipmentCharges->TotalCharges->MonetaryValue;
	
		slp_ajax_functions::confirm_rates( $order, $shipment );
	}
	
	public function get_customs( $order, $shipment ) {
		$customs = (object)$shipment['_customs'];
	
		$request = "	
		<Description>$customs->Comments</Description>
		<ShipmentServiceOptions>
			<InternationalForms>
				<FormType>09</FormType>
				<CN22Form>
					<LabelSize>6</LabelSize>
					<PrintsPerPage>1</PrintsPerPage>
					<LabelPrintType>pdf</LabelPrintType>
					<CN22Type>$customs->ContentType</CN22Type>
					<CN22OtherDescription>$customs->Comments</CN22OtherDescription>";
		foreach( $customs->CustomsLines as $line ) { 
			$line = (object)$line;
			$weight = $line->WeightLb + ( $line->WeightOz * 0.0625 );
			$request .=	   
					"<CN22Content>
						<CN22ContentQuantity>$line->Quantity</CN22ContentQuantity>
						<CN22ContentDescription>$line->Description</CN22ContentDescription>
						<CN22ContentWeight>
							<UnitOfMeasurement>
								<Code>lbs</Code>
							</UnitOfMeasurement>
							<Weight>$weight</Weight> 
						</CN22ContentWeight>
						<CN22ContentTotalValue>$line->Value</CN22ContentTotalValue>
						<CN22ContentCurrencyCode>USD</CN22ContentCurrencyCode>
						<CN22ContentCountryOfOrigin>$line->CountryOfOrigin</CN22ContentCountryOfOrigin>
						<CN22ContentTariffNumber>$line->HSTariffNumber</CN22ContentTariffNumber>
					</CN22Content>";
		}
		$request .=	
				"</CN22Form>";
		$items = $order->get_items();
		foreach( $items as $item ) {
			$item = (object)$item;
			$request .=
			  	"<Product>
					<Description>$item->name</Description>
					<Unit>
						<Number>$item->qty</Number>
						<Value>$item->line_total</Value>
						<UnitOfMeasurement>
							<Code>EA</Code>
						</UnitOfMeasurement>
					</Unit>
				</Product>";
		}
		$request .= 
			"</InternationalForms>
		</ShipmentServiceOptions>";
			
		return $request;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function create_shipment( $order, $shipment ) {
		
		//Create XML for Shipment Accept Request	
		$request  = $this->xml_header();				  					  
		$request .= 
		"<ShipmentAcceptRequest>
			<Request>
				<TransactionReference>
					<CustomerContext>ship_accept</CustomerContext>
					<XpciVersion>1.0</XpciVersion>
				</TransactionReference>
				<RequestAction>ShipAccept</RequestAction>
				<RequestOption>1</RequestOption>
			</Request>
			<ShipmentDigest>" . $shipment['_shipment_digest'] . "</ShipmentDigest>
		 </ShipmentAcceptRequest>";
		
		$request = str_replace( array( "\n", "\r" ), '', $request );

		//Send XML to UPS for processing and recieve XML Response				  
		$response = $this->xml_request( $request, $this->endpoints['production'] . 'ShipAccept' );
		
		unset( $shipment['_shipment_digest'] );
		
		$shipment['_shipping_cost']   = (string)$response->ShipmentResults->ShipmentCharges->TotalCharges->MonetaryValue;
		
		$shipment['_shipping_date']   = time() < strtotime( $this->settings['readytime'] ) ? date( 'm/d/Y' ) : date( 'm/d/Y', strtotime( '+1 day' ) );
		
		$shipment['_shipment_status'] = 'CONFIRMED';
		
		$rates = $response->ShipmentResults->PackageResults;
			
		$packages = $shipment['_packages'];
	
		$count = 0;
	
		foreach( $rates as $rate ) {
	
			$package = $packages[$count];
	
			$package->id 		    = (string)$rate->TrackingNumber;
	
			$package->ShippingLabel	= 'data:image/gif;base64,' . (string)$rate->LabelImage->GraphicImage;
	
			++$count;
		}
		
		slp_ajax_functions::confirm_shipment( $order, $shipment, true );	
	}
	
	private function get_package_type( $package ) {
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
	public function track_shipment( $shipment, $order ) {
		
		//check if tracking is necessary
		if( ( ! isset( $_POST['tracking_number'] ) && $shipment['_packages'][0]->id === 'Not Trackable' ) || $shipment['_packages'][0]->id == "No Tracking# Assigned" ) {
				
			$shipment['_shipment_status'] = array( 
				'status' => 'Not Trackable',
				'timestamp' => $shipment['_shipping_date'],
			);
			return $shipment;
		}
		
		//if updating tracking assign desired tracking number
		if( isset( $_POST['tracking_number'] ) ) {
			$key = $_POST['box'];
			$tracking_number = $_POST['tracking_number'];
		} else {
			$tracking_number = $shipment['_packages'][0]->id;
		} 
					
		//Create XML Track Shipment Request
		$request  = 
		"<TrackRequest>
			<Request>
				<TransactionReference>
					<CustomerContext>track_shipment</CustomerContext>
					<XpciVersion>1.0</XpciVersion>
				</TransactionReference>
				<RequestAction>Track</RequestAction>
				<RequestOption>1</RequestOption>
			</Request>
			<TrackingNumber>" . $tracking_number . "</TrackingNumber>
		</TrackRequest>";
		

		$xml_header = $this->xml_header();
		$request = $xml_header . $request;	
		
		$request = str_replace( array( "\n", "\r" ), '', $request );
	
		//Send XML request and receive XML Response			
		$tracking = $this->xml_request( $request, $order, $this->endpoints['production']  . 'Track' );
				
		foreach( $shipment['_packages'] as $key => $package ) {
			if( (int)$tracking->Response->ResponseStatusCode ) {
				$activity = $tracking->Shipment->Package->Activity;
				$timestamp = date( 'm/d/Y h:i a', strtotime( (string)$activity->Date . ' ' . (string)$activity->Time, current_time( 'timestamp' ) ) );
				$city 	   = (string)$activity->ActivityLocation->Address->City;
				$state     = (string)$activity->ActivityLocation->Address->StateProvinceCode;
				$country   = (string)$activity->ActivityLocation->Address->CountryCode;	
				$status 	   = (string)$activity->Status->StatusType->Description;
				$desc	   = (string)$activity->ActivityLocation->Description;
				$signer	   = (string)$activity->ActivityLocation->SignedForByName;
			} else if( $tracking->Response->Error->ErrorCode == '151044' ) {
				$city = $state = $country = '';
				$timestamp = date( 'm/d/Y h:i a' );
				$status    = 'Electronic Notification';
				$desc 	   = 'Electronic Notification';
			} else {
				$package->Error = array(
					'code'  => (string)$tracking->Response->Error->ErrorCode,
					'error' => (string)$tracking->Response->Error->ErrorDescription
				);
				
				$xml = array( 
					'Success' => false
				);
				
				if( isset( $_POST['tracking_number'] ) ){
					echo json_encode( $xml );
					die();
				}
			}
			
			$package->TrackingStatus = array( 
				'timestamp' => $timestamp,
				'status'		=> $status,
				'desc'		=> $desc,
				'location'	=> $city . ', ' . $state . ', ' . $country,
			);

			var_dump( $package->TrackingStatus );
			
			isset( $signer ) ? $package->TrackingStatus['signer'] = $signer : '';			

			$shipment['_shipment_status'] = array(
				'status' => $status,
				'timestamp' => $timestamp
			);
		}
		
		if( isset( $_POST['tracking_number'] ) ) {
			$shipment['_shipment_status'] = array(
			 	'status' => $package->TrackingStatus['status'],
				'timestamp' => $package->TrackingStatus['timestamp']
			);
				
			$shipment['_packages'][$key]->id = $tracking_number;
			
			update_post_meta( $order->id, '_shipment', $shipment );
			
			$xml = array( 
				'Success' => true,
				'TrackingNumber' => $tracking_number,
				'ShipmentStatus' => $package->TrackingStatus['status']
			);
			
			echo json_encode( $xml );
			die();	
		}
	
		return $shipment;
	}
	
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	public function void_shipment() {
		global $current_user;
		
		$shipment_id = $this->debug == 1 ? $this->test_tracking_numbers['void'] : $this->shipment['_shipment_id'];
	
		//Create XML Void Shipment Request
		$request  = 
		"<VoidShipmentRequest>
			<Request>
				<RequestAction>Void</RequestAction>
				<TransactionReference>
					<CustomerContext>void_shipment</CustomerContext>
					<XpciVersion>1.0</XpciVersion>
				</TransactionReference>
			</Request>
			<ShipmentIdentificationNumber>$shipment_id</ShipmentIdentificationNumber>
		</VoidShipmentRequest>";

		$xml_header = $this->xml_header();
		$request = $xml_header . $request;	
		//Send XML to UPS for processing and receive XML Response
		
		$request = str_replace( array( "\n", "\r" ), '', $request );
		
		$xml = slp_ajax_functions::xml_request( $request, $this->endpoint_url[0]. 'Void' );
		
		$status_code = (string)$xml->Response->ResponseStatusCode;	
			
		if( $status_code == 1 ) {
			$status = 'Voided ' . date( 'm-d-y' ) . ' by ' . $current_user->display_name;
			$message = 'Shipment ID# ' . $this->shipment['_shipment_id'] . ' has been voided by ' . $current_user->display_name . '.';	
			unset( $this->shipment );
			$this->shipment['_shipment_status'] = $status;
		} else {
			$error_code = (string)$xml->Response->Error->ErrorCode;
			$error_desc = (string)$xml->Response->Error->ErrorDescription;	
			$message = 'UPS Error: ' . $error_code . ' - ' . $error_desc;
				
		}
			$this->xml_response['Call'] = 'VoidShipment';
			$this->xml_response['ResponseStatusCode'] = $status_code;
			$this->xml_response['StatusMessage'] = $message;
			isset( $status ) ? $this->xml_response['ShipmentStatus'] = $status : '' ;
		
		return $this->xml_response;
	}
	
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	private function create_pickup_post( $post_title ) {
		global $user_ID;
	
		$args = array(
			'post_title' 		=> $post_title,
			'post_status'		=> 'private',
			'post_date'			=> date( 'Y-m-d H:i:s' ),
			'post_date_gmt' 	=> gmdate( 'Y-m-d H:i:s' ),
			'post_author'		=> $user_ID,
			'post_type'			=> 'slp_pickup',
			'ping_status'		=> 'closed',
			'comment_status'	=> 'open',
		); 	
	
		return wp_insert_post( $args );
	}
	
	public function get_service_code( $order )  {
		$shipping = $order->get_shipping_methods();
		$id = array_keys( $shipping );
		$service_code = $shipping[$id[0]]['item_meta']['method_id'][0];
		$service_code = substr( $service_code, 4);
		
		$request  = "		
			<Service>
				<Code>$service_code</Code>
			</Service>";
		
		return $request;
	}
	
	public function get_shipper() {
		
		$settings = get_option( 'slp_general_settings' );

		$request = "		
			<Shipper>
				<AttentionName>" . $settings['company_name'] . "</AttentionName>
				<ShipperNumber>" . $this->settings['shipper_number'] . "</ShipperNumber>
				<Name>" . $settings['company_name'] . "</Name>
				<Address>
					<AddressLine1>" . $settings['address_1'] . "</AddressLine1>
					<City>" . $settings['city'] . "</City>
					<StateProvinceCode>" . $settings['state'] . "</StateProvinceCode>
					<PostalCode>" . $settings['zipcode'] . "</PostalCode>
					<CountryCode>" . $settings['country'] . "</CountryCode>
				</Address>
				<PhoneNumber>" . $settings['phone'] . "</PhoneNumber>
			</Shipper>
			<ShipFrom>
				<CompanyName>" . $settings['company_name'] . "</CompanyName>
				<Address>
					<AddressLine1>" . $settings['address_1'] . "</AddressLine1>
					<City>" . $settings['city'] . "</City>
					<StateProvinceCode>" . $settings['state'] . "</StateProvinceCode>
					<PostalCode>" . $settings['zipcode'] . "</PostalCode>
					<CountryCode>" . $settings['country'] . "</CountryCode>
				</Address>
				<PhoneNumber>" . $settings['phone'] . "</PhoneNumber>
			</ShipFrom>";
		
		return $request;	
	}
	
	public function get_shipping_address( $order ) {
		$countries = new WC_Countries();

		$name =  $order->shipping_first_name . ' ' . $order->shipping_last_name ;
		$domestic = in_array( $order->shipping_country, $this->domestic );
		$postcode = $domestic ? trim( str_replace( ' - ' , '' , $order->shipping_postcode ) ) : '';
		
		$request  = 
			"<ShipTo>
				<CompanyName>$name</CompanyName>
				<Address>
					 <AddressLine1>$order->shipping_address_1</AddressLine1>";
		if( !empty( $order->shipping_address_2 ) ) 
		$request .=	"<AddressLine2>$order->shipping_address_2</AddressLine2>";
		$request .=	"<City>$order->shipping_city</City>";	
						
		
		$state = ! $domestic && $order->shipping_country == 'CA' ? substr( $order->shipping_state, 5 ) : strlen( $order->shipping_state ) > 2 ? array_search( ucwords( strtolower( $order->shipping_state ) ), $countries->get_states( $order->shipping_country ) ) : $order->shipping_state;
				
		if( isset( $state ) ) 
			$request .= "<StateProvinceCode>$state</StateProvinceCode>";
		if( $domestic && isset( $postcode ) ) 
			$request .="<PostalCode>$postcode</PostalCode>";
			$request .="<CountryCode>$order->shipping_country</CountryCode>			
				</Address>
				<PhoneNumber>$order->billing_phone</PhoneNumber>
				<AttentionName>$name</AttentionName>
			</ShipTo>";
			
		return $request;
	}
	/**
	 *
	 *
	 *
	 *
	 */ 
	private function get_package_request( $packages ) {
		//set accumulator variables
		$counter = 0;
		$total_weight = 0;

		//parse order items and 
		foreach( $packages as $package ) {
	
			//check that we have the right data
			if( is_object( $package ) ) {
				$counter++;		  
	
				//get package weight 
				$weight = $package->weight;
		
				//adjust for weights below 1 lbs
				$weight = ( floor( $weight ) < 1 ) ? 1 : $weight;
		
				//format XML for package request 
				$request  = "		
				<Package>
					<PackagingType>
						<Code>02</Code>
						<Description>Customer Supplied</Description>
					</PackagingType>
					<Dimensions>
						<UnitOfMeasurement>
							<Code>IN</Code>
						</UnitOfMeasurement>
						<Length>$package->length</Length>
						<Width>$package->width</Width>
						<Height>$package->height</Height>
					</Dimensions>
					<PackageWeight>
						<UnitOfMeasurement>
							<Code>LBS</Code>
						</UnitOfMeasurement>
						<Weight>$weight</Weight>
					</PackageWeight>
					<PackageServiceOptions>
						<DeclaredValue>
							<Type>01</Type>
							<CurrencyCode>USD</CurrencyCode>
							<MonetaryValue>$package->value</MonetaryValue>
						</DeclaredValue>
					</PackageServiceOptions>
				</Package>";
	  
				//Calculate total weight for use in Ship Accept Request
				$total_weight += $weight;
				$package->PackageType   = (string)$this->get_package_type( $package );

				//Push XML to Array
				$requests[] = $request;	
			}
		}

		//Update shipment array
		$shipment['_total_weight'] = $total_weight;
		$shipment['_package_count'] = count( $requests );

		//Return compiled XML for use in Ship Confirm Request
		return $requests;	
		
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	 private function load_shipment() {
		 $this->shipment = get_post_meta( $this->order->id, '_shipment', true );
	 }
	 
	 /**
	 *
	 *
	 *
	 *
	 */ 
	private function schedule_pickup( $pickup_date ) {
		
		
		//parse email addresses into array for processing		
		$emails = explode( ',', trim( $this->slp_settings['email'] ) );

		//Create XML Pickup Creation Request
		$request = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:wsf="http://www.ups.com/schema/wsf" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">' . "\n";
		$request .= "	<envr:Header>" . "\n";
		$request .= "		<upss:UPSSecurity>" . "\n";	
		$request .= "			<upss:UsernameToken>" . "\n";	
		$request .= "				<upss:Username>" . $this->user_id. "</upss:Username>" . "\n";	
		$request .= "				<upss:Password>" . $this->password . "</upss:Password>" . "\n";	
		$request .= "			</upss:UsernameToken>" . "\n";	
		$request .= "			<upss:ServiceAccessToken>" . "\n";	
		$request .= "				<upss:AccessLicenseNumber>" . $this->access_key . "</upss:AccessLicenseNumber>" . "\n";	
		$request .= "			</upss:ServiceAccessToken>" . "\n";	
		$request .= "		</upss:UPSSecurity>" . "\n";	
		$request .= "	</envr:Header>" . "\n";	
		$request .= "	<envr:Body>" . "\n";	
		$request .= '		<PickupCreationRequest xmlns="http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n";
		$request .= "			<common:Request>" . "\n";	
		$request .= "				<common:RequestOption/>" . "\n";	
		$request .= "				<common:TransactionReference>" . "\n";	
		$request .= "					<common:CustomerContext>pickup_request</common:CustomerContext>" . "\n";	
		$request .= "				</common:TransactionReference>" . "\n";	
		$request .= "			</common:Request>" . "\n";	
		$request .= "			<RatePickupIndicator>Y</RatePickupIndicator>" . "\n";	
		$request .= "			<Shipper>" . "\n";	
		$request .= "				<Account>" . "\n";	
		$request .= "					<AccountNumber>" . $this->shipper_number . "</AccountNumber>" . "\n";	
		$request .= "					<AccountCountryCode>US</AccountCountryCode>" . "\n";	
		$request .= "				</Account>" . "\n";	
		$request .= "			</Shipper>" . "\n";	
		$request .= "			<PickupDateInfo>" . "\n";	
		$request .= "				<CloseTime>" . date( 'Hs', strtotime( $this->slp_settings['closetime'] ) ) . "</CloseTime>" . "\n";	
		$request .= "				<ReadyTime>" . date( 'Hs', strtotime( $this->slp_settings['readytime'] ) ) . "</ReadyTime>" . "\n";	
		$request .= "				<PickupDate>" . $pickup_date . "</PickupDate>" . "\n";	
		$request .= "			</PickupDateInfo>" . "\n";	
		$request .= "			<PickupAddress>" . "\n";	
		$request .= "				<CompanyName>" . $this->company_name . "</CompanyName>" . "\n";	
		$request .= "				<ContactName>" . $this->shipper_contact . "</ContactName>" . "\n";	
		$request .= "				<AddressLine>" . $this->shipper_address . "</AddressLine>" . "\n";	
		$request .=	"				<City>" . $this->shipper_city . "</City>" . "\n";	
		$request .= "				<StateProvince>" . $this->shipper_state . "</StateProvince>" . "\n";	
		$request .= "				<PostalCode>" . $this->shipper_postcode . "</PostalCode>" . "\n";	
		$request .= "				<CountryCode>" . $this->shipper_country . "</CountryCode>" . "\n";	
		$request .= "				<ResidentialIndicator>Y</ResidentialIndicator>" . "\n";	
		$request .= "				<Phone>" . "\n";	
		$request .= "					<Number>" . $this->shipper_phone . "</Number>" . "\n";	
		$request .= "				</Phone>" . "\n";	
		$request .= "			</PickupAddress>" . "\n";	
		$request .= "			<AlternateAddressIndicator/>" . "\n";	
		$request .= "			<PickupPiece>" . "\n";	
		$request .= "				<ServiceCode>0" . $this->shipment['_service_code'] . "</ServiceCode>" . "\n";	
		$request .= "				<Quantity>" . $this->shipment['_package_count'] . "</Quantity>" . "\n";	
		$request .= "				<DestinationCountryCode>" . $this->shipment['_country_code'] . "</DestinationCountryCode>" . "\n";	
		$request .= "				<ContainerCode>01</ContainerCode>" . "\n";	
		$request .= "			</PickupPiece>" . "\n";	
		$request .= "			<TotalWeight>" . "\n";	
		$request .= "				<Weight>" . $this->shipment['_total_weight'] . "</Weight>" . "\n";	
		$request .= "				<UnitOfMeasurement>LBS</UnitOfMeasurement>" . "\n";	
		$request .= "			</TotalWeight>" . "\n";		
		$request .= "			<PaymentMethod>01</PaymentMethod>" . "\n";	
		$request .= "			<Notification>" . "\n";	
		foreach( $emails as $email )
		$request .= "				<ConfirmationEmailAddress>" . $email . "</ConfirmationEmailAddress>" . "\n";		
		$request .= "				<UndeliverableEmailAddress>" . $emails[0] . "</UndeliverableEmailAddress>" . "\n";	
		$request .= "			</Notification>" . "\n";	
		$request .= "		</PickupCreationRequest>" . "\n";	
		$request .= "	</envr:Body>" . "\n";	
		$request .= "</envr:Envelope>" . "\n";	
		
		$request = str_replace( array( "\n", "\r" ), '', $request );
	
		$xml = slp_ajax_functions::xml_request( $request, $this->endpoint_url[1] . 'Pickup' , true );
		
		return $xml;
		
	}
	
	/**
	 *
	 *
	 *
	 *
	 */ 
	private function xml_header() {
		//Sanitize password for speical character
		$valid_pass = str_replace( '&', '&amp;', $this->settings['password'] );

		//Create XML Header 
		$request = 
			"<?xml version=\"1.0\" ?>
				<AccessRequest xml:lang='en-US'>
					<AccessLicenseNumber>" . $this->settings['access_key'] . "</AccessLicenseNumber>
					<UserId>" . $this->settings['user_id'] . "</UserId>
					<Password>$valid_pass</Password>
				</AccessRequest>
				<?xml version=\"1.0\" ?>";
		
		return $request;
	}
	
	/**
	 * Ajax function xml_request
	 *
	 * Sends XML data to UPS API for processing
	 * @return mixed array
	 */
	public function xml_request( $request, $order, $endpoint, $soap = false ) {	
		libxml_use_internal_errors( true );
					
		//send shipment request
		$response = wp_remote_post( $endpoint, 
			array(
				'timeout' 		=> 70,
				'sslverify'		=> 0,
				'body'			=> $request,
			)
		);

		//If XML -> recieve, sanitize and format XML Response		  	
		if( is_wp_error( $response ) ) {
			slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, 'Cannot connect to remote server. Please check internet connection' );
		}
		
		if( $soap ) {
			$xml = str_replace( 'xmlns', 'ns', $response['body'] );
		} else {
			$xml = simplexml_load_string( preg_replace('/<\?xml.*\?>/','', $response['body'] ) );
		}
		
		$status = (int)$xml->Response->ResponseStatusCode;
				
		if( $status ) {
			return $xml;
		} else {
			return $this->error_handler( $xml, $order );
		}
	}	
	
	public function error_handler( $error, $order ) {	
		
		switch( $error->Response->Error->ErrorCode ) {
			case '120802':
				//$this->verify_address( $order, false );
				break;
			//case '151018':
			case '151062':
			case '154030':
			case '151019':
			case '150023':
			case '150022':
			case '151044':
				return $error;
				break;
			default:
				$error_msg  = (string)$error->Response->Error->ErrorDescription;
				$error_code = (string)$error->Response->Error->ErrorCode; 
				slp_ajax_functions::error_handler( __FUNCTION__, __CLASS__, __LINE__, $order, 'UPS Error: ' . $error_code, $error_msg  );
		}
	
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
}
endif;
?>