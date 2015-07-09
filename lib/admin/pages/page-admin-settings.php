<div class=" wrap slp">
<h2>Shipping Label Pro Settings</h2>
<form method="post" id="mainform" action="" enctype="multipart/form-data">
  <div class="icon32 icon32-slp-settings" id="icon-slp"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    <?php
	 
		foreach( $tabs as $tab => $label )
			echo '<a href="' . admin_url( 'admin.php?page=slp-settings&tab=' . $tab ) . '" class="nav-tab ' . ( $current_tab == $tab ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
					
		do_action( 'slp_settings_tabs' );		
		?>
  </h2>
  <?php
		do_action( 'slp_sections_' . $current_tab );
		do_action( 'slp_settings_' . $current_tab );
  ?>
  <p class="submit">
    <?php if( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
    <input name="save" class="button-primary" type="submit" value="<?php _e( 'Save Changes', 'slp' ); ?>" />
    <?php endif; ?>
    <input type="hidden" name="subtab" id="last_tab" />
    <?php wp_nonce_field( 'slp-settings' ); ?>
  </p>
</form>
</div>
