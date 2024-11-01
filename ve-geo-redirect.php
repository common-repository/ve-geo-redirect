<?php
/*
Plugin Name:VE Geo Redirect
Plugin URI: http://www.virtualemployee.com/
Description: A plugin that provides visitor redirect according to their geographical location
Version: 1.2
Author: virtualemployee
Author URI: http://www.virtualemployee.com
Text Domain: ve-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
     * Determine value of option $name from database, $default value or $params,
     * save it to the db if needed and return it.
     *
     * @param string $name
     * @param mixed  $default
     * @param array  $params
     * @return string
     */
    /** Define Action for register "Virtual Ads" Options */
add_action('admin_init','ve_geo_redirect_register_settings_init');
if(!function_exists('ve_geo_redirect_register_settings_init')):
function ve_geo_redirect_register_settings_init(){
 register_setting('ve_geo_redirect_setting_options','ve_geo_redirect_status');
 register_setting('ve_geo_redirect_setting_options','ve_geo_redirect_country_code');
 register_setting('ve_geo_redirect_setting_options','ve_geo_redirect_exclude_ip');  
 register_setting('ve_geo_redirect_setting_options','ve_geo_redirect_redirect_to');  
} 
endif;

    /**
     * Plugin's interface
     *
     * @return void
     */
if(!function_exists('virtaul_geo_redirect_form')):
	function virtaul_geo_redirect_form() {
	?>
		<div id="virtual-settings"> 
		<div class="wrap">
			<h1>Virtual GEO Redirect Settings</h1><a href="http://www.virtualemployee.com/contactus">Click here</a> for send to your query on plugin support<hr />
			 <form action="options.php" method="post" id="ve-geo-redirect-admin-form">
				<table class="form-table">
				<tr valign="top">
				<th scope="row">Status</th>
				<?php $status='';$status=get_option('ve_geo_redirect_status');?>
				<td><input type="radio" name="ve_geo_redirect_status[]" value="enable" <?php if($status[0]=='enable'){ echo 'checked="checked"';};?>>Enable <input type="radio" name="ve_geo_redirect_status[]" value="disable" <?php if($status[0]=='disable'){ echo 'checked="checked"';};?>>Disable
				</td>
				</tr>
				<tr valign="top">
				<th scope="row">Define country code</th>
				<td><input type="text" name="ve_geo_redirect_country_code" size="40" placeholder="Define Country Code" value="<?php echo get_option('ve_geo_redirect_country_code'); ?>"> <a href="http://www.geoplugin.com/iso3166" target="_blank">Click here</a> to find country code.<br><i>(define comman separate country id)</i></td>
				</tr>
				<tr valign="top">
				<th scope="row">Exclude IPs</th>
				<td><input type="text" name="ve_geo_redirect_exclude_ip" size="40" placeholder="Define comman seperate IP address" value="<?php echo get_option('ve_geo_redirect_exclude_ip'); ?>"><br><i>(define comman separate IP address)</i></td>
				</tr>
				<tr valign="top">
				<th scope="row">Redirect To: </th>
				<td><input type="text" name="ve_geo_redirect_redirect_to" size="40" placeholder="Define redirect url Code" value="<?php echo get_option('ve_geo_redirect_redirect_to'); ?>"><br><i>(define comman separate country id)</i></td>
				</tr>
				 
			</table>
				<span class="submit-btn"><?php echo get_submit_button('Save Settings','button-primary','submit','','');?></span>
				 <?php settings_fields('ve_geo_redirect_setting_options'); ?>
			</form>
			
		</div><!-- end wrap -->
		</div>
	<?php
	 }
endif; 
// Add settings link to plugin list page in admin
if(!function_exists('virtaul_geo_redirect_settings_link')):
function virtaul_geo_redirect_settings_link( $links ) {
  $settings_link = '<a href="options-general.php?page=ve-geo-redirect">' . __( 'Settings', 'virtualemployee' ) . '</a>';
   array_unshift( $links, $settings_link );
  return $links;
}
endif;
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'virtaul_geo_redirect_settings_link' );
function vgr_admin_menu() {
    add_submenu_page('options-general.php','Virtual GEO Redirect', 'Virtual GEO Redirect', 'manage_options','ve-geo-redirect','virtaul_geo_redirect_form');
}
add_action('admin_menu', 'vgr_admin_menu');

add_action( 'init', 've_redirect_enqueue_func',  10);

if(!function_exists('ve_redirect_enqueue_func')):
	function ve_redirect_enqueue_func()
	{
		if(!is_admin())
		{
			    $status=$countryCode=$countryCodeList=$redirectTo=$excludeIp=$excludeIps='';
				$status 			= get_option('ve_geo_redirect_status');
				$countryCode 		= get_option('ve_geo_redirect_country_code');
				$countryCodeList	= explode(',',$countryCode);
				$redirectTo 		= get_option('ve_geo_redirect_redirect_to');
				$excludeIp 			= get_option('ve_geo_redirect_exclude_ip');
				$excludeIps    	    = explode(',',$excludeIp);
				$currentIp    	    = $_SERVER['REMOTE_ADDR'];
			
				if($status[0]=='enable' && $countryCode!='' && $redirectTo!=''):
					require_once dirname(__FILE__).'/lib/geoplugin.class.php'; // include library file
					$geoplugin = new geoPlugin();
					$geoplugin->locate($currentIp);
					// create a variable for the country code
					$var_country_code = $geoplugin->countryCode;
					if(in_array($var_country_code,$countryCodeList)) 
					{
						if(!in_array($currentIp,$excludeIps))
							{
							  wp_redirect( $redirectTo );
							  exit;
							}
						 else
							{
							 // silent
							}
					}

				endif;
			}
	}
endif;
