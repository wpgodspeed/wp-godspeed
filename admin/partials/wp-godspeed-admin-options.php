<?php

/**
 * Provide an admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/admin/partials
 */

$status          = get_option( $this->option_name . '_status' );
$debug_http_code = get_option( $this->option_name . '_cdn_debug_response_code' );

if ( $status == 'not registered' )
{
	if ( ! function_exists( $this->option_name . '_notice_registration_dismissed' ) && empty( get_option( $this->option_name . '_notice_registration_dismissed' ) ) )
	{
		add_action( 'admin_notices', array( $this, 'notice_registration_incomplete' ) );
		do_action( 'admin_notices' );
	}
}

if ( $status == 'registered' )
{
	if ( ! function_exists( $this->option_name . '_notice_registration_dismissed' ) && empty( get_option( $this->option_name . '_notice_registration_dismissed' ) ) )
	{
		add_action( 'admin_notices', array( $this, 'notice_registration_complete' ) );
		do_action( 'admin_notices' );
	}
}

?>
	<style>
	.wpgodspeed label {
		font-size: 20px;
		margin-bottom: -15px;
	}
	</style>
	<div class="wrap wpgodspeed">
		<span class="otitle"><h2><?php echo esc_html( get_admin_page_title() ); ?></h2></span>
		<div id="options-table">
			<?php
				settings_fields( $this->plugin_name . '-options' );
				do_settings_sections( $this->plugin_name . '-options' );
				//$other_attributes = array(
					//'data-micron' => 'flicker',
				//	'class'       => 'button button-primary',
				//	'value'       => 'Save'
				//);

			?>
		</div>
	</div>

<?php

	if ( ( $status == FALSE || $status == 'not registered' ) )
	{
		$js = <<<EOD
<script>
var opt = document.getElementById("options-table");
opt.style.display = "none";
</script>
EOD;
		echo $js;
	}
