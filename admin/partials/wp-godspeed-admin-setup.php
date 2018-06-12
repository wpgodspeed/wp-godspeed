<?php

/**
 * Provide a plugin setup page.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/admin/partials
 */

$url         = get_site_url();
$admin_email = get_option( 'admin_email' );
$timezone    = get_option( 'timezone_string' );
if ( empty( $timezone ) )
{
	$offset   = get_option( 'gmt_offset' );
	$timezone = "GMT$offset";
}

if ( empty( $timezone ) && empty( $offset ) )
{
	if (function_exists('system'))
	{
		ob_start();
		$timezone = 'GMT' . system('o1=`date +%z`; o2=${o1%??}; echo $o2');
		ob_clean();
	}
	elseif (function_exists('exec'))
	{
		$timezone = 'GMT' . exec('o1=`date +%z`; o2=${o1%??}; echo $o2');
	}
	elseif (function_exists('shell_exec'))
	{
		$timezone = trim('GMT' . shell_exec('o1=`date +%z`; o2=${o1%??}; echo $o2'));
	}
	else
	{
		$timezone = ini_get('date.timezone');
	}
}


if ( empty( $timezone ) && empty( $offset ) )
{
	$this_tz_str = date_default_timezone_get();
	if ($this_tz_str !== 'UTC')
	{
		$this_tz = new DateTimeZone($this_tz_str);
		$now     = new DateTime("now", $this_tz);
		$offset = $this_tz->getOffset($now) / 3600;
		if ($offset > 0)
		{
			$timezone = 'GMT+' . $offset;
		}
		else
		{
			$timezone = 'GMT' . $offset;
		}
	}
}

if ( empty( $timezone ) && empty( $offset ) )
{
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		if ("<?php echo $timezone; ?>".length == 0){
			var visitortime = new Date();
			var visitortimezone = "GMT" + -visitortime.getTimezoneOffset()/60;
			jQuery('#tz').html(visitortimezone);
			jQuery('input[type=hidden]#timezone').val(visitortimezone);
		}
	});
</script>
<?php
}

$body = wp_remote_get('http://checkip.dyndns.com/')['body'];
preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $body, $m);
$ip = $m[1];

$status      = get_option( $this->option_name . '_status' );
$auth_token  = get_option( $this->option_name . '_auth_token' );
delete_option( $this->option_name . '_cdn_status_waiting2' );
delete_option( $this->option_name . '_cdn_status_debug' );

if ( $_GET['reset'] == 'true' )
{
	foreach ( $this->option_list as $option )
	{
		delete_option( $this->option_name . $option );
	}
	//remove all wpgods_useage_history_* options
	foreach ( wp_load_alloptions() as $option => $value )
	{
		if ( strpos( $option, $this->option_name . '_usage_history' ) === 0 )
		{
			delete_option( $option );
		}
	}
	echo "<script>window.location.replace('admin.php?page=wp-godspeed-setup');</script>";
}

if ($auth_token)
{
	echo '<div id="token" value="' . base64_encode($auth_token) . '" style="display:none"></div>';
}
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
		<div id="registration" style="display:none">
			<div class="reginfo">
				<p>Before you can enable the Godspeed CDN for your site, you will need an Authorization Token. Getting one is as simple as clicking the button, though be sure to hover over the lock icon before doing so.</p>
			</div>
			<table>
				<tbody>
					<tr><td align="right">WordPress URL</td><td><span style="color:green"><?php echo $url; ?></span></td></tr>
					<tr><td align="right">Timezone</td><td><span id="tz" style="color:green"><?php echo $timezone; ?></span></td></tr>
					<tr><td align="right">IP Address</td><td><span id="tz" style="color:green"><?php echo $ip; ?></span></td></tr>
					<tr><td align="right">Your work email</td><td><a href="#" id="email_editable" data-type="text" data-title="Your work email"><?php $current_user = wp_get_current_user(); echo $current_user->user_email; ?></a></td></tr>
				</tbody>
			</table>
			<p>&nbsp;</p>
			<?php
				if ( ! is_multisite() )
				{
				?>
					<form id="form" action="" method="POST">
						<input type="hidden" name="email" id="email" value="<?php $current_user = wp_get_current_user(); echo $current_user->user_email; ?>">
						<input type="hidden" name="url" value="<?php echo $url; ?>">
						<input type="hidden" name="timezone" id="timezone" value="<?php echo $timezone; ?>">
						<input type="hidden" name="ip_address" id="ip_address" value="<?php echo $ip; ?>">
						<button type="submit" id="submit" class="btn btn-primary btn-lg">Generate Authorization Token</button>
							<i class="fa fa-lg fa-lock"
								aria-hidden="true"
								data-trigger="hover"
								data-toggle="popover"
								data-html="true"
								data-delay='{"hide": 2500}'
								title="Privacy Notice"
								data-content="When you click the button, these three pieces of information will be submitted to the WP Godspeed service, but your information will be safeguarded and will never be shared with any 3rd parties, ever. We hate SPAM and take this very seriously. You may review our <a href='https://wpgodspeed.io/privacy'>Privacy Policy</a> for GDPR specifics or more information in general.">
							</i>
					</form>
					<div id="error_reg" class="error" style="display:none"></div>
				<?php
				}
				else
				{
					echo "<p>Sorry, multisite is not currently supported with this plugin.</p>";
				}
			?>
		</div>
		<div id="distribution" style="display:none">
			<table>
				<!-- <tbody>
					<tr><td>AUTH TOKEN</td><td><?php echo get_option( $this->option_name . '_auth_token' ); ?></td></tr>
				</tbody> -->
			</table>
			<p>Before you can enable the Godspeed CDN for your site, we'll need to setup the CDN.</p>
			<form id="create_distribution" action="" method="POST">
				<button type="button" id="submit_create_dist" class="btn btn-primary btn-lg">Setup the CDN</button>
					<i class="fa fa-lg fa-info-circle"
					aria-hidden="true"
					data-trigger="hover"
					data-toggle="popover"
					title="FYI"
					data-content="The setup process usually takes about 30 minutes to complete. You'll get an admin notification just as soon as it's ready."></i>
			</form>
			<div id="dist_result" style="display:none;">
				<div class="progress dist-create-status">
				  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
				</div>
			</div>
			<div id="error_dist" class="error" style="display:none"></div>
		</div>
		<div id="setupcomplete" style="display:none">
			<table>
				<tbody>
					<tr><td align="right">AUTH TOKEN</td><td class="dataitem auth_token"><?php echo get_option( $this->option_name . '_auth_token' ); ?></td></tr>
					<tr><td align="right">CDN ID</td><td class="dataitem cdn_id"><?php echo get_option( $this->option_name . '_distribution_id' ); ?></td></tr>
					<tr><td align="right">CDN UID</td><td class="dataitem cdn_uid"><?php echo get_option( $this->option_name . '_uid' ); ?></td></tr>
					<tr><td align="right">CDN ALIAS</td><td class="dataitem cdn_alias"><?php echo get_option( $this->option_name . '_subdomain' ); ?>.godspeedcdn.com</td></tr>
				</tbody>
			</table>
		</div>
	</div>