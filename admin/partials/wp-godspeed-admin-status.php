<?php

/**
 * Provide a plugin status page.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/admin/partials
 */

$url          = get_site_url();
$admin_email  = get_option( 'admin_email' );
$current_user = wp_get_current_user();
$user_email   = $current_user->user_email;
$email        = $admin_email;
$status       = get_option( $this->option_name . '_status' );
$auth_token   = get_option( $this->option_name . '_auth_token' );
if ($auth_token)
{
	echo '<div id="token" value="' . base64_encode($auth_token) . '"></div>';
}
else
{
	echo '<div id="token" value=""></div>';
}
$plan      = get_option( $this->option_name . '_plan' );
$dist_id   = get_option( $this->option_name . '_distribution_id' );
$uid       = get_option( $this->option_name . '_uid' );
$subdomain = get_option( $this->option_name . '_subdomain' );

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

		<div class="wrap wpgodspeed">
			<span class="otitle"><h2><?php echo esc_html( get_admin_page_title() ); ?></h2></span>

			<?php
				settings_fields( $this->plugin_name . '-status' );
				do_settings_sections( $this->plugin_name . '-status' );
			?>

			<?php
				//plan definitions
				if ($plan == 'free')
				{
					$upgrade_to = 'starter';
					$price      = '12';
					$data       = '25';
				}
				if ($plan == 'starter')
				{
					$upgrade_to = 'business';
					$price      = '22';
					$data       = '50';
				}
				if ($plan == 'business')
				{
					$upgrade_to = 'pro1';
					$price      = '40';
					$data       = '100';
				}
				if ($plan == 'pro1')
				{
					$upgrade_to = 'pro2';
					$price      = '90';
					$data       = '250';
				}
				if ($plan == 'pro2')
				{
					$upgrade_to = 'pro3';
					$price      = '160';
					$data       = '500';
				}
				if ($plan == 'pro3')
				{
					$upgrade_to = 'pro4';
					$price      = '280';
					$data       = '1000';
				}
				if ($plan == 'pro4')
				{
					$upgrade_to = 'pro5';
					$price      = '1200';
					$data       = '5000';
				}

				if ( get_option( $this->option_name . '_needs_plan_upgrade' ) == 1 )
				{
					if ( is_ssl() )
					{
						echo '<p>Upgrade to the ' . ucfirst($upgrade_to) . ' plan for $' . $price . ' and get ' . $data . 'GB of transfer each month.</p>';
						echo '<iframe style="position:relative;left:-8px;" src="https://api.godspeedcdn.com/billing/payment/' . $upgrade_to . '/' . $email . '" style="overflow: hidden;" height=560px width=400px frameBorder=0 scrolling="no"></iframe>';
					}
					else
					{
						echo '<p>Upgrade to the ' . ucfirst($upgrade_to) . ' plan for $' . $price . ' and get ' . $data . 'GB of transfer each month.</p>';
						echo "<a href='https://api.godspeedcdn.com/billing/payment/$upgrade_to/$email'></a>";
					}
				}
			?>
		</div>
