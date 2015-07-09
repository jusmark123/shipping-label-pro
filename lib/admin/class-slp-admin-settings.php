<?php 
/**
 * Class Name: 	SLP Admin Settings
 **/
  
 if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly
 
 if( ! class_exists( 'SLP_Admin_Settings' ) ) :
 
 class SLP_Admin_Settings {
	 
	 private static $settings = array();
	 private static $errors = array();
	 private static $messages = array();
	 
	 public static function get_settings_pages() {
		 if( empty( self::$settings ) ) {
			$settings = array(); 
			 
		 	include_once( 'class-slp-settings-page.php' );

		 	$settings[] = include_once( 'class-slp-general-settings.php' );	
			$settings[] = include_once( 'class-slp-shipping-method-settings.php' );
		 	//$settings[] = include_once( 'class-slp-reports.php' );
			
			self::$settings = apply_filters( 'slp_get_settings_pages', $settings );
		 }
		 
		 return self::$settings;
	 }
	 
	 public static function save() {
		global $current_tab, $current_section;
		
		if( empty( $_REQUEST['_wpnonce'] ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'slp_settings' ) ) {
			die( __( 'An error has occured. Please refresh the page and try again.', 'slp' ) );
		}
	 	
		do_action( 'slp_settings_save_' . $current_tab );
		do_action( 'slp_update_options_' . $current_tab );
		do_action( 'slp_update_options' );
	 
		self::add_message( __( 'Settings have been updated.', 'slp' ) );

		do_action( 'slp_settings_saved' );
		do_action( 'slp_settings_saved_' . $current_section );
	 }
	 
	 public static function add_message( $message ) {
		self::$messages[] = $message;  
	 }
	 
	 public static function add_error( $error ) {
		 self::$errors[] = $error;
	 }
	 
	 public static function show_messages() {
		if ( sizeof( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error )
				echo '<div id="message" class="error fade"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
		} elseif ( sizeof( self::$messages ) > 0 ) {
			foreach ( self::$messages as $message )
				echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
		}
	 }
	 
	 public static function output() {
		global $current_section, $current_tab;
		
		do_action( 'slp_settings_start' );
				
		wp_localize_script( 'slp_settings', 'slp_settings_params', array(
			'i18n_nav_warning' => __( 'The changes you made will be lost if you navigate away from this page.', 'slp' )
		) );
 
 	 	self::get_settings_pages();
		
		$current_tab 	 = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );
				
		if( ! empty( $_POST ) )
			self::save();
			
		if( ! empty( $_GET['slp_error'] ) )
			self::add_error( stripslashes( $_GET['slp_error'] ) );
		
		if( ! empty( $_GET['slp_message'] ) )
			self::add_message( stripslashes( $_GET['slp_message'] ) );
		
		self::show_messages();
		
		$tabs = apply_filters( 'slp_settings_tabs_array', array() );
			
		include 'pages/page-admin-settings.php';
	 }
	 
	public static function get_option( $option_name, $default = '' ) {
		
		var_dump( $option_name );
		
		// Array value
		if ( strstr( $option_name, '[' ) ) {

			parse_str( $option_name, $option_array );

			// Option name is first key
			$option_name = current( array_keys( $option_array ) );

			// Get value
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) ) {
				$option_value = $option_values[ $key ];
			} else {
				$option_value = null;
			}

		// Single value		
		} else {					
			$option_value = get_option( $option_name, null );
		}		

		if ( is_array( $option_value ) ) {
			$option_value = array_map( 'stripslashes', $option_value );
		} elseif ( ! is_null( $option_value ) ) {
			$option_value = stripslashes( $option_value );
		}

		return $option_value === null ? $default : $option_value;
	}
	 
	 public static function output_fields( $options ) {

		foreach( $options as $option ) {
			if( ! isset( $option['type'] ) ) continue;
			if( ! isset( $option['id'] ) ) $option['id'] = '';
			if( ! isset( $option['class'] ) ) $option['class'] = '';
			if( ! isset( $option['name'] ) ) $option['name'] = '';
			if( ! isset( $option['style'] ) ) $option['style'] = '';
			if( ! isset( $option['default'] ) ) $option['default'] = ''; 
			if( ! isset( $option['desc'] ) ) $option['desc'] = '';
			if( ! isset( $option['tooltip']) ) $option['tooltip'] = '';
			
			$custom_attributes = array();
			
			if( ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
				foreach( $option['custom_attributes'] as $attribute => $option ) {
					$custom_attributes[] = esc_attr( $attribute ) . '= "' . esc_attr( $option ) . '"';
				}
			}
			
			// Description handling
			if ( $option['tooltip'] === true ) {
				$description = '';
				$tip = $option['desc'];
			} elseif ( ! empty( $option['tooltip'] ) ) {
				$description = $option['desc'];
				$tip = $option['tooltip'];
			} elseif ( ! empty( $option['desc'] ) ) {
				$description = $option['desc'];
				$tip = '';
			} else {
				$description = $tip = '';
			}

			if ( $description && in_array( $option['type'], array( 'textarea', 'radio' ) ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			} elseif ( $description && in_array( $option['type'], array( 'checkbox' ) ) ) {
				$description =  wp_kses_post( $description );
			} elseif ( $description ) {
				$description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
			}

			if ( $tip && in_array( $option['type'], array( 'checkbox' ) ) ) {

				$tip = '<p class="description">' . $tip . '</p>';

			} elseif ( $tip ) {

				//$tip = '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

			}
			
			switch( $option['type'] ) {
				case 'title':
					if( ! empty( $option['title'] ) )
						echo '<h3>' . esc_html( $option['title'] ) . '</h3>';
					
					if( ! empty( $option['desc'] ) ) 
						echo wpautop( wptexturize( wp_kses_post( $option['desc'] ) ) );
						
					echo '<table class="form-table">'. "\n\n";
					
	            	if ( ! empty( $option['id'] ) ) {
	            		do_action( 'slp_settings_' . sanitize_title( $option['id'] ) );
	            	}
					break;
				case 'sectionend':
					if ( ! empty( $option['id'] ) ) {
	            		do_action( 'slp_settings_' . sanitize_title( $option['id'] ) . '_end' );
	            	}
	            	echo '</table>';
	            	if ( ! empty( $option['id'] ) ) {
	            		do_action( 'slp_settings_' . sanitize_title( $option['id'] ) . '_after' );
	            	}
	            break;
					
				case 'data_table':
					?><tr valign="top">
                    	<th scope="row" class="titledesc">
                      		<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
                        </th>
                        <td class="forminp">
                        	<table id="<?php echo esc_attr( $option['id'] ); ?>" class="<?php echo esc_attr( $option['id'] );?>">
                            	<tr>
								<?php 
								$count = 0;
								foreach( $option['data'] as $id => $value ) { ?>
                                	<td><?php echo esc_html( $id ); ?> : <?php echo $value; ?></td> 
                                <?php
									++$count;
									if( $count > 2 ) {
										echo '</td>
											<tr>';
										$count = 0;
									} else {
										echo '</td>	';
									}
								}?>
                                </tr>
                            </table>
                         </td>
                   <?php break; 
				case 'button':
				?>
                	<tr>
                		<td>
							<button type="button" class="<?php echo esc_attr( $option['id'] );?>"><?php echo esc_html( $option['title'] ); ?></button><?php
                            if( ! empty( $option['desc'] ) ) {?>
								<td><span><?php echo $option['desc']; ?></span></td>
					<?php	} ?>
                    	</td>
                    </td>
				<?php break;     			
				case 'text':
				case 'email';
				case 'number':
				case 'color':
				case 'password':
	            	$type 			= $option['type'];
	            	$class 			= '';
	            	$option_value 	= isset( $option['value'] ) ? $option['value'] : self::get_option( $option['id'], $option['default'] );
					
	            	if ( $option['type'] == 'color' ) {
	            		$type = 'text';
	            		$option['class'] .= 'colorpick';
		            	$description .= '<div id="colorPickerDiv_' . esc_attr( $option['id'] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>';
	            	}

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
	                    	<input
	                    		name="<?php echo esc_attr( $option['id'] ); ?>"
	                    		id="<?php echo esc_attr( $option['id'] ); ?>"
	                    		type="<?php echo esc_attr( $type ); ?>"
	                    		style="<?php echo esc_attr( $option['css'] ); ?>"
	                    		value="<?php echo esc_attr( $option_value ); ?>"
	                    		class="<?php echo esc_attr( $option['class'] ); ?>"
	                    		<?php echo implode( ' ', $custom_attributes ); ?>
	                    		/> <?php echo $description; ?>
	                    </td>
	                </tr><?php
	            	break;
				case 'radio':
					$option_value = self::get_option( $option['id'], $option['default'] );

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
	                    	<fieldset>
	                    		<?php echo $description; ?>
	                    		<ul>
	                    		<?php
	                    			foreach ( $option['settings'] as $key => $val ) {
			                        	?>
			                        	<li>
			                        		<label>
                                            <input
				                        		name="<?php echo esc_attr( $option['id'] ); ?>"
				                        		value="<?php echo $key; ?>"
				                        		type="radio"
					                    		style="<?php echo esc_attr( $option['css'] ); ?>"
					                    		class="<?php echo esc_attr( $option['class'] ); ?>"
					                    		<?php echo implode( ' ', $custom_attributes ); ?>
					                    		<?php checked( $key, $option_value ); ?>
				                        		/> <?php echo $val ?></label>
			                        	</li>
			                        	<?php
			                        }
	                    		?>
	                    		</ul>
	                    	</fieldset>
	                    </td>
	                </tr><?php
	            	break;
				case 'checkbox':
					$option_value    =  isset( $option['value'] ) ? $option['value'] : self::get_option( $option['id'], $option['default'] );
					$visbility_class = array();

	            	if ( ! isset( $option['hide_if_checked'] ) ) {
	            		$option['hide_if_checked'] = false;
	            	}
	            	if ( ! isset( $option['show_if_checked'] ) ) {
	            		$option['show_if_checked'] = false;
	            	}
	            	if ( $option['hide_if_checked'] == 'yes' || $option['show_if_checked'] == 'yes' ) {
	            		$visbility_class[] = 'hidden_option';
	            	}
	            	if ( $option['hide_if_checked'] == 'option' ) {
	            		$visbility_class[] = 'hide_settings_if_checked';
	            	}
	            	if ( $option['show_if_checked'] == 'option' ) {
	            		$visbility_class[] = 'show_settings_if_checked';
	            	}

	            	if ( ! isset( $option['checkboxgroup'] ) || 'start' == $option['checkboxgroup'] ) {
	            		?>
		            		<tr valign="top" class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
								<th scope="row" class="titledesc"><?php echo esc_html( $option['title'] ) ?></th>
								<td class="forminp forminp-checkbox">
									<fieldset>
						<?php
	            	} else {
	            		?>
		            		<fieldset class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
	            		<?php
	            	}

	            	if ( ! empty( $option['title'] ) ) {
	            		?>
	            			<legend class="screen-reader-text"><span><?php echo esc_html( $option['title'] ) ?></span></legend>
	            		<?php
	            	}

	            	?>
						<label for="<?php echo $option['id'] ?>">
							<input
								name="<?php echo esc_attr( $option['id'] ); ?>"
								id="<?php echo esc_attr( $option['id'] ); ?>"
								type="checkbox"
								value="1"
								<?php checked( $option_value, 'yes'); ?>
								<?php echo implode( ' ', $custom_attributes ); ?>
							/> <?php echo $description ?>
						</label> <?php echo $tip; ?>
					<?php

					if ( ! isset( $option['checkboxgroup'] ) || 'end' == $option['checkboxgroup'] ) {
									?>
									</fieldset>
								</td>
							</tr>
						<?php
					} else {
						?>
							</fieldset>
						<?php
					}
	            break;
				case 'select':				  
				case 'multiselect':
				  $option_value = isset( $option['value'] ) ? $option['value'] : self::get_option( $option['id'], $option['default'] );

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
	                    	<select
	                    		name="<?php echo esc_attr( $option['id'] ); ?><?php if ( $option['type'] == 'multiselect' ) echo '[]'; ?>"
	                    		id="<?php echo esc_attr( $option['id'] ); ?>"
	                    		style="<?php echo esc_attr( $option['css'] ); ?>"
	                    		class="<?php echo esc_attr( $option['class'] ); ?>"
	                    		<?php echo implode( ' ', $custom_attributes ); ?>
	                    		<?php if ( $option['type'] == 'multiselect' ) echo 'multiple="multiple"'; ?>
	                    		>
		                    	<?php
			                        foreach ( $option['settings'] as $key => $val ) {
			                        	?>
			                        	<option value="<?php echo esc_attr( $key ); ?>" <?php

				                        	if ( is_array( $option_value ) )
				                        		selected( in_array( $key, $option_value ), true );
				                        	else
				                        		selected( $option_value, $key );

			                        	?>><?php echo $val ?></option>
			                        	<?php
			                        }
			                    ?>
	                       </select> <?php echo $description; ?>
	                    </td>
	                </tr><?php
						if( $option['id'] === 'country' || $option['id'] === 'state' ) {
                  			$_GET[$option['id']] = $option_value;			  
				  		}	
	            break;
				case 'textarea';
					$option_value 	= self::get_option( $option['id'], $option['default'] );

	            	?><tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
							<?php echo $tip; ?>
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">
	                    	<?php echo $description; ?>

	                        <textarea
	                        	name="<?php echo esc_attr( $option['id'] ); ?>"
	                        	id="<?php echo esc_attr( $option['id'] ); ?>"
	                        	style="<?php echo esc_attr( $option['css'] ); ?>"
	                        	class="<?php echo esc_attr( $option['class'] ); ?>"
	                        	<?php echo implode( ' ', $custom_attributes ); ?>
	                        	><?php echo esc_textarea( $option_value );  ?></textarea>
	                    </td>
	                </tr><?php
					break;
	            default:
	            	do_action( 'slp_admin_field_' . $option['type'], $option );
	            	break;
			}
		}
	}
	
	public static function save_fields( $settings ) {
		global $current_section;
		
		if( empty( $_POST ) )
			return false;
		
		$update_settings = array();		
	
		foreach( $settings as $option ) {
			if( empty( $option['type'] ) )
				continue;	
			
			$option_value = null;	
			switch( $option['type'] ) {
				case 'checkbox':
					if ( isset( $_POST[ $option['id'] ] ) ) {
		    			$option_value = 'yes';
		            } else {
		            	$option_value = 'no';
		            }
					break;
				case "text" :
		    	case 'email':
	            case 'number':
		    	case "select" :
		    	case "color" :
	            case 'password' :
				case 'radio':
					if ( $option['id'] == 'slp_price_thousand_sep' || $option['id'] == 'slp_price_decimal_sep' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $option['id'] ] )  ) {
							$option_value = wp_kses_post( stripslashes( $_POST[ $option['id'] ] ) );
						} else {
			            	$option_value = '';
			            }

		    		} elseif ( $option['id'] == 'slp_price_num_decimals' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $option['id'] ] )  ) {
							$option_value = absint( $_POST[ $option['id'] ] );
						} else {
			               $option_value = 2;
			            }

					
					} else {
						if ( isset( $_POST[$option['id']] ) ) {
			        		$option_value = sanitize_text_field( stripslashes( $_POST[ $option['id'] ] ) );
			        	} else {
			        		$option_value = '';
			       		}
					}
					break;
				case 'text_area':
			    	if ( isset( $_POST[$option['id']] ) ) {
			    		$option_value = wp_kses_post( trim( stripslashes( $_POST[ $option['id'] ] ) ) );
		            } else {
		                $option_value = '';
					}
		    		break;
				default:
		    		do_action( 'slp_update_option_' . $option['type'], $option );
		    		break;
			}
			
			if( ! is_null( $option_value ) ) {
				if( strstr( $option['id'], '[' ) ) {
					parse_str( $option['id'], $option_array );
					
					$option_name = current( array_keys( $option_array ) );
					
					if( ! isset( $update_settings[$option_name] ) )
						$update_settings[$option_name] = get_option( $option_name, array() );
						
					if( ! is_array( $update_settings[$option_name] ) )
						$update_settings[$option_name] = array();
					
					$key = key( $option_array[$option_name] );
					
					$update_settings[ $option_name][$key] = $option_value;
					
				} else {
					
					$update_settings[$option['id']] = $option_value;
				}
			}
			
			do_action( 'slp_update_settings', $option );
		}	
		
		if( $current_section ) {
			update_option( 'slp_' . $current_section . '_settings', $update_settings );
		} else {
			update_option( 'slp_general_settings', $update_settings );
		}
		
		return true;
	}		 
 }
 endif;
 