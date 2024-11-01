<?php
/*
Plugin Name: Wp image slideshow
Plugin URI: http://www.gopiplus.com/work/2011/05/06/wordpress-plugin-wp-image-slideshow/
Description: This is advanced version of my drop in image slideshow gallery. In this gallery each image is dropped into view. Slideshow will pause on mouse over.
Author: Gopi Ramasamy
Version: 12.1
Author URI: http://www.gopiplus.com/work/2011/05/06/wordpress-plugin-wp-image-slideshow/
Donate link: http://www.gopiplus.com/work/2011/05/06/wordpress-plugin-wp-image-slideshow/
Tags: image, slideshow, gallery, dropin, drop in
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-image-slideshow
Domain Path: /languages
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb, $wp_version;
define("WP_wpis_TABLE", $wpdb->prefix . "wpis_plugin");
define('WP_wpis_FAV', 'http://www.gopiplus.com/work/2011/05/06/wordpress-plugin-wp-image-slideshow/');

if ( ! defined( 'WP_wpis_BASENAME' ) )
	define( 'WP_wpis_BASENAME', plugin_basename( __FILE__ ) );
	
if ( ! defined( 'WP_wpis_PLUGIN_NAME' ) )
	define( 'WP_wpis_PLUGIN_NAME', trim( dirname( WP_wpis_BASENAME ), '/' ) );
	
if ( ! defined( 'WP_wpis_PLUGIN_URL' ) )
	define( 'WP_wpis_PLUGIN_URL', plugins_url() . '/' . WP_wpis_PLUGIN_NAME );
	
if ( ! defined( 'WP_wpis_ADMIN_URL' ) )
	define( 'WP_wpis_ADMIN_URL', admin_url() . 'options-general.php?page=wp-image-slideshow' );

function wpis() 
{
	global $wpdb;
	$wpis_title = get_option('wpis_title');
	$wpis_width = get_option('wpis_width');
	$wpis_height = get_option('wpis_height');
	$wpis_pause = get_option('wpis_pause');
	$wpis_random = get_option('wpis_random');
	$wpis_type = get_option('wpis_type');
	
	if(!is_numeric($wpis_width)) { $wpis_width = 200 ;}
	if(!is_numeric($wpis_height)) { $wpis_height = 200; }
	if(!is_numeric($wpis_pause)) { $wpis_pause = 3000; }
	
	$sSql = "select wpis_path,wpis_link,wpis_target,wpis_title from ".WP_wpis_TABLE." where 1=1";
	
	if($wpis_type <> ""){ 
		$sSql = $sSql . " and wpis_type = %s "; 
		$sSql = $wpdb->prepare($sSql, $wpis_type);
	}
	
	if($wpis_random == "YES"){ $sSql = $sSql . " ORDER BY RAND()"; }else{ $sSql = $sSql . " ORDER BY wpis_order"; }
	
	$data = $wpdb->get_results($sSql);
	$wpis_count = 0;
	$wpis_returnstr = "";
	if ( ! empty($data) ) 
	{
		foreach ( $data as $data ) 
		{
			$wpis_str =  '["' . $data->wpis_path . '", "'. $data->wpis_link .'", "'. $data->wpis_target .'"]';
			$wpis_returnstr = $wpis_returnstr . "wpis_images[$wpis_count]=$wpis_str; ";
			$wpis_count++;
		}
		
		?>
		<script type="text/javascript">
		var wpis_images=new Array()
		<?php echo $wpis_returnstr; ?>
		new wpis(wpis_images, <?php echo $wpis_width; ?>, <?php echo $wpis_height; ?>, <?php echo $wpis_pause; ?>)
		</script>
		<?php
	}	
	else
	{
		_e('No image(s) available in this Gallery Type. Please check admin widget setting page.', 'wp-image-slideshow');
		echo " Gallery Type: " . $wpis_type;
	}
}

function wpis_install() 
{
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_wpis_TABLE . "'") != WP_wpis_TABLE) 
	{
		$sSql = "CREATE TABLE IF NOT EXISTS `". WP_wpis_TABLE . "` (";
		$sSql = $sSql . "`wpis_id` INT NOT NULL AUTO_INCREMENT ,";
		$sSql = $sSql . "`wpis_path` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "`wpis_link` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "`wpis_target` VARCHAR( 50 ) NOT NULL ,";
		$sSql = $sSql . "`wpis_title` VARCHAR( 500 ) NOT NULL ,";
		$sSql = $sSql . "`wpis_order` INT NOT NULL ,";
		$sSql = $sSql . "`wpis_status` VARCHAR( 10 ) NOT NULL ,";
		$sSql = $sSql . "`wpis_type` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`wpis_extra1` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`wpis_extra2` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`wpis_date` datetime NOT NULL default '0000-00-00 00:00:00' ,";
		$sSql = $sSql . "PRIMARY KEY ( `wpis_id` )";
		$sSql = $sSql . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$wpdb->query($sSql);
		
		$IsSql = "INSERT INTO `". WP_wpis_TABLE . "` (`wpis_path`, `wpis_link`, `wpis_target` , `wpis_title` , `wpis_order` , `wpis_status` , `wpis_type` , `wpis_date`)"; 
		
		$sSql = $IsSql . " VALUES ('".WP_wpis_PLUGIN_URL."/images/250x167_1.jpg', '#', '_blank', 'Image 1', '1', 'YES', 'Widget', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		
		$sSql = $IsSql . " VALUES ('".WP_wpis_PLUGIN_URL."/images/250x167_2.jpg' ,'#', '_blank', 'Image 2', '2', 'YES', 'Widget', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		
		$sSql = $IsSql . " VALUES ('".WP_wpis_PLUGIN_URL."/images/250x167_3.jpg', '#', '_blank', 'Image 3', '1', 'YES', 'Sample', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		
		$sSql = $IsSql . " VALUES ('".WP_wpis_PLUGIN_URL."/images/250x167_4.jpg', '#', '_blank', 'Image 4', '2', 'YES', 'Sample', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);

	}
	add_option('wpis_title', "WP image slideshow");
	add_option('wpis_width', "250");
	add_option('wpis_height', "170");
	add_option('wpis_pause', "3000");
	add_option('wpis_random', "NO");
	add_option('wpis_type', "widget");
}

function wpis_control() 
{
	echo '<p><b>';
	 _e('wp image slideshow', 'wp-image-slideshow');
	echo '.</b> ';
	_e('Check official website for more information', 'wp-image-slideshow');
	?> <a target="_blank" href="<?php echo WP_wpis_FAV; ?>"><?php _e('click here', 'wp-image-slideshow'); ?></a></p><?php
}

function wpis_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('wpis_Title');
	echo $after_title;
	wpis();
	echo $after_widget;
}

function wpis_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'edit':
			include('pages/image-management-edit.php');
			break;
		case 'add':
			include('pages/image-management-add.php');
			break;
		case 'set':
			include('pages/image-setting.php');
			break;
		default:
			include('pages/image-management-show.php');
			break;
	}
}

add_shortcode( 'wp-image-gallery', 'wpis_shortcode' );

function wpis_shortcode( $atts ) 
{
	global $wpdb;
	
	//[wp-image-gallery type="sample" width="360" height="170" pause="3000" random="yes"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$wpis_type = $atts['type'];
	$wpis_width = $atts['width'];
	$wpis_height = $atts['height'];
	$wpis_pause = $atts['pause'];
	$wpis_random = $atts['random'];

	if(!is_numeric($wpis_width)) { $wpis_width = 250 ;}
	if(!is_numeric($wpis_height)) { $wpis_height = 200; }
	if(!is_numeric($wpis_pause)) { $wpis_pause = 3000; }
	
	$sSql = "select wpis_path,wpis_link,wpis_target,wpis_title from ".WP_wpis_TABLE." where 1=1";
	
	if($wpis_type <> ""){ 
		$sSql = $sSql . " and wpis_type= %s "; 
		$sSql = $wpdb->prepare($sSql, $wpis_type);
	}
	
	if($wpis_random == "YES"){ $sSql = $sSql . " ORDER BY RAND()"; }else{ $sSql = $sSql . " ORDER BY wpis_order"; }
	
	$data = $wpdb->get_results($sSql);
	$wpis_count = 0;
	$wpis_returnstr  = "";
	$Lr = "";
	if ( ! empty($data) ) 
	{
		foreach ( $data as $data ) 
		{
			$wpis_str =  '["' . $data->wpis_path . '", "'. $data->wpis_link .'", "'. $data->wpis_target .'"]';
			$wpis_returnstr = $wpis_returnstr . "wpis_images[$wpis_count]=$wpis_str; ";
			$wpis_count++;
		}
		
		$Lr = $Lr .'<script type="text/javascript">';
		$Lr = $Lr .'var wpis_images=new Array();';
		$Lr = $Lr . $wpis_returnstr;
		$Lr = $Lr .'new wpis(wpis_images, '.$wpis_width.', '.$wpis_height.', '.$wpis_pause.')';
		$Lr = $Lr .'</script>';
	}
	else
	{
		$Lr = __('No records found. please check your short code.', 'wp-image-slideshow');
	}
	return $Lr;
}

function wpis_add_to_menu() 
{
	if (is_admin()) 
	{
		add_options_page(__('Wp image slideshow', 'wp-image-slideshow'), 
							__('Wp image slideshow', 'wp-image-slideshow'), 'manage_options', "wp-image-slideshow", 'wpis_admin_options' );
	}
}

function wpis_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('wp-image-slideshow', __('Wp image slideshow', 'wp-image-slideshow'), 'wpis_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('wp-image-slideshow', array(__('Wp image slideshow', 'wp-image-slideshow'), 'widgets'), 'wpis_control');
	} 
}

function wpis_deactivation() 
{
	// No action required
}

function wpis_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script( 'wp-image-slideshow', WP_wpis_PLUGIN_URL.'/wp-image-slideshow.js');
	}
}

function wpis_textdomain() 
{
	  load_plugin_textdomain( 'wp-image-slideshow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function wpis_adminscripts() 
{
	if( !empty( $_GET['page'] ) ) 
	{
		switch ( $_GET['page'] ) 
		{
			case 'wp-image-slideshow':
				wp_register_script( 'wpis-adminscripts', plugins_url( 'pages/setting.js', __FILE__ ), '', '', true );
				wp_enqueue_script( 'wpis-adminscripts' );
				$wpis_select_params = array(
					'wpis_path'   	=> __( 'Please enter the image path.', 'wpis-select', 'wp-image-slideshow' ),
					'wpis_link'   	=> __( 'Please enter the target link.', 'wpis-select', 'wp-image-slideshow' ),
					'wpis_target' 	=> __( 'Please select the target option.', 'wpis-select', 'wp-image-slideshow' ),
					'wpis_order'  	=> __( 'Please enter the display order, only number.', 'wpis-select', 'wp-image-slideshow' ),
					'wpis_status' 	=> __( 'Please select the display status.', 'wpis-select', 'wp-image-slideshow' ),
					'wpis_type'  	=> __( 'Please select the gallery type.', 'wpis-select', 'wp-image-slideshow' ),
					'wpis_delete'	=> __( 'Do you want to delete this record?', 'wpis-select', 'wp-image-slideshow' ),
				);
				wp_localize_script( 'wpis-adminscripts', 'wpis_adminscripts', $wpis_select_params );
				break;
		}
	}
}

add_action('plugins_loaded', 'wpis_textdomain');
add_action('admin_menu', 'wpis_add_to_menu');
add_action('wp_enqueue_scripts', 'wpis_add_javascript_files');
add_action("plugins_loaded", "wpis_init");
register_activation_hook(__FILE__, 'wpis_install');
register_deactivation_hook(__FILE__, 'wpis_deactivation');
add_action( 'admin_enqueue_scripts', 'wpis_adminscripts' );
?>
