<?php
/**
 * Plugin Name: Form Maker Pro
 * Plugin URI: https://web-dorado.com/products/form-maker-wordpress.html
 * Description: This plugin is a modern and advanced tool for easy and fast creating of a WordPress Form. The backend interface is intuitive and user friendly which allows users far from scripting and programming to create WordPress Forms.
 * Version: 2.11.1
 * Author: WebDorado
 * Author URI: https://web-dorado.com/
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
define('WD_FM_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define('WD_FM_URL', plugins_url(plugin_basename(dirname(__FILE__))));
define('WD_MAIN_FILE', plugin_basename(__FILE__));
define('WD_FM_VERSION', '2.11.1');
// Plugin menu.
function form_maker_options_panel() {

  add_menu_page('Form Maker', 'Form Maker', 'manage_options', 'manage_fm', 'form_maker', WD_FM_URL . '/images/FormMakerLogo-16.png', 105.105);
  add_menu_page('Form Maker Add-ons', 'Form Maker &nbsp;&nbsp;&nbsp;&nbsp; Add-ons', 'manage_options', 'extensions_fm', 'fm_extensions', WD_FM_URL . '/assets/add-ons-icon.png');

  $manage_page = add_submenu_page('manage_fm', 'Manager', 'Manager', 'manage_options', 'manage_fm', 'form_maker');
  add_action('admin_print_styles-' . $manage_page, 'form_maker_manage_styles');
  add_action('admin_print_scripts-' . $manage_page, 'form_maker_manage_scripts');

  $submissions_page = add_submenu_page('manage_fm', 'Submissions', 'Submissions', 'manage_options', 'submissions_fm', 'form_maker');
  add_action('admin_print_styles-' . $submissions_page, 'form_maker_submissions_styles');
  add_action('admin_print_scripts-' . $submissions_page, 'form_maker_submissions_scripts');

  if (defined('WD_FM_SAVE_PROG') && is_plugin_active(constant('WD_FM_SAVE_PROG'))) {
	$saved_entries_page = add_submenu_page('manage_fm', 'Saved Entries', 'Saved Entries', 'manage_options', 'saved_entries', 'fm_saved_entries');
	add_action('admin_print_styles-' . $saved_entries_page, 'form_maker_submissions_styles');
    add_action('admin_print_scripts-' . $saved_entries_page, 'form_maker_submissions_scripts');
  }
  
  $blocked_ips_page = add_submenu_page('manage_fm', 'Blocked IPs', 'Blocked IPs', 'manage_options', 'blocked_ips_fm', 'form_maker');
  add_action('admin_print_styles-' . $blocked_ips_page, 'form_maker_manage_styles');
  add_action('admin_print_scripts-' . $blocked_ips_page, 'form_maker_manage_scripts');

  $themes_page = add_submenu_page('manage_fm', 'Themes', 'Themes', 'manage_options', 'themes_fm', 'form_maker');
  add_action('admin_print_styles-' . $themes_page, 'form_maker_manage_styles');
  add_action('admin_print_scripts-' . $themes_page, 'form_maker_manage_scripts');

  $global_options_page = add_submenu_page('manage_fm', 'Global Options', 'Global Options', 'manage_options', 'goptions_fm', 'form_maker');
  add_action('admin_print_styles-' . $global_options_page, 'form_maker_manage_styles');
  add_action('admin_print_scripts-' . $global_options_page, 'form_maker_manage_scripts');
  
  add_submenu_page('manage_fm', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'featured_plugins_fm', 'fm_featured');
  add_submenu_page('manage_fm', 'Featured Themes', 'Featured Themes', 'manage_options', 'featured_themes_fm', 'fm_featured_themes');
}
add_action('admin_menu', 'form_maker_options_panel');

function form_maker() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FM_DIR . '/framework/WDW_FM_Library.php');
  $page = WDW_FM_Library::get('page');
  if (($page != '') && (($page == 'manage_fm') || ($page == 'goptions_fm') || ($page == 'submissions_fm') || ($page == 'blocked_ips_fm') || ($page == 'themes_fm') || ($page == 'featured_plugins_fm') || ($page == 'featured_themes_fm') || ($page == 'uninstall_fm') || ($page == 'formmakerwindow') || ($page == 'extensions_fm'))) {
    require_once (WD_FM_DIR . '/admin/controllers/FMController' . ucfirst(strtolower($page)) . '.php');
    $controller_class = 'FMController' . ucfirst(strtolower($page));
    $controller = new $controller_class();
    $controller->execute();
  }
}

function fm_featured() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FM_DIR . '/featured/featured.php');
  wp_register_style('fm_featured', WD_FM_URL . '/featured/style.css', array(), WD_FM_VERSION);
  wp_print_styles('fm_featured');
  fm_featured_page('form-maker');
}

function fm_featured_themes() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FM_DIR . '/featured/featured_themes.php');
  wp_register_style('fm_featured_themes', WD_FM_URL . '/featured/featured_themes.css', array(), WD_FM_VERSION);
  wp_print_styles('fm_featured_themes');
  fm_featured_themes_page('form-maker');
}


function fm_extensions() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FM_DIR . '/featured/featured.php');
  wp_register_style('fm_featured', WD_FM_URL . '/featured/style.css', array(), WD_FM_VERSION);
  wp_print_styles('fm_featured');
  fm_extensions_page('form-maker');
}

function updates_fm() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FM_DIR . '/featured/updates.php');
  wp_register_style('fm_featured', WD_FM_URL . '/featured/style.css', array(), WD_FM_VERSION);
  wp_print_styles('fm_featured');
}

add_action('wp_ajax_get_stats', 'form_maker'); //Show statistics
add_action('wp_ajax_generete_csv', 'form_maker_ajax'); // Export csv.
add_action('wp_ajax_generete_xml', 'form_maker_ajax'); // Export xml.
add_action('wp_ajax_FormMakerPreview', 'form_maker_ajax');
add_action('wp_ajax_formmakerwdcaptcha', 'form_maker_ajax'); // Generete captcha image and save it code in session.
add_action('wp_ajax_nopriv_formmakerwdcaptcha', 'form_maker_ajax'); // Generete captcha image and save it code in session for all users.
add_action('wp_ajax_formmakerwdmathcaptcha', 'form_maker_ajax'); // Generete math captcha image and save it code in session.
add_action('wp_ajax_nopriv_formmakerwdmathcaptcha', 'form_maker_ajax'); // Generete math captcha image and save it code in session for all users.
add_action('wp_ajax_paypal_info', 'form_maker_ajax'); // Paypal info in submissions page.
add_action('wp_ajax_fromeditcountryinpopup', 'form_maker_ajax'); // Open country list.
add_action('wp_ajax_product_option', 'form_maker_ajax'); // Open product options on add paypal field.
add_action('wp_ajax_frommapeditinpopup', 'form_maker_ajax'); // Open map in submissions.
add_action('wp_ajax_fromipinfoinpopup', 'form_maker_ajax'); // Open ip in submissions.
add_action('wp_ajax_show_matrix', 'form_maker_ajax'); // Edit matrix in submissions.
add_action('wp_ajax_FormMakerSubmits', 'form_maker_ajax'); // Open submissions in submissions.
add_action('wp_ajax_FormMakerSQLMapping', 'form_maker_ajax'); // Add/Edit SQLMaping from form options.

add_action('wp_ajax_checkpaypal', 'form_maker_ajax'); // Notify url from Paypal Sandbox.
add_action('wp_ajax_nopriv_checkpaypal', 'form_maker_ajax'); // Notify url from Paypal Sandbox for all users.

add_action('wp_ajax_select_data_from_db', 'form_maker_ajax'); // select data from db.

add_action('wp_ajax_get_frontend_stats', 'form_maker_ajax_frontend'); //Show statistics frontend
add_action('wp_ajax_nopriv_get_frontend_stats', 'form_maker_ajax_frontend'); //Show statistics frontend
add_action('wp_ajax_frontend_show_map', 'form_maker_ajax_frontend'); //Show map frontend
add_action('wp_ajax_nopriv_frontend_show_map', 'form_maker_ajax_frontend'); //Show map frontend
add_action('wp_ajax_frontend_show_matrix', 'form_maker_ajax_frontend'); //Show matrix frontend
add_action('wp_ajax_nopriv_frontend_show_matrix', 'form_maker_ajax_frontend'); //Show matrix frontend
add_action('wp_ajax_frontend_paypal_info', 'form_maker_ajax_frontend'); //Show paypal info frontend
add_action('wp_ajax_nopriv_frontend_paypal_info', 'form_maker_ajax_frontend'); //Show paypal info frontend
add_action('wp_ajax_frontend_generate_csv', 'form_maker_ajax_frontend'); //generate csv frontend
add_action('wp_ajax_nopriv_frontend_generate_csv', 'form_maker_ajax_frontend'); //generate csv frontend
add_action('wp_ajax_frontend_generate_xml', 'form_maker_ajax_frontend'); //generate xml frontend
add_action('wp_ajax_nopriv_frontend_generate_xml', 'form_maker_ajax_frontend'); //generate xml frontend
add_action('wp_ajax_manage_fm', 'form_maker_ajax'); //Show statistics

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( 'fm_admin_class.php' );
	include_once('form_maker_notices_class.php');
	add_action( 'plugins_loaded', array( 'FM_Admin', 'get_instance' ) );
}

function form_maker_ajax() {
  require_once(WD_FM_DIR . '/framework/WDW_FM_Library.php');
  $page = WDW_FM_Library::get('action');
  if ($page != 'formmakerwdcaptcha' && $page != 'formmakerwdmathcaptcha' && $page != 'checkpaypal') {
    if (function_exists('current_user_can')) {
      if (!current_user_can('manage_options')) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
  }
  if ($page != '') {
    require_once (WD_FM_DIR . '/admin/controllers/FMController' . ucfirst($page) . '.php');
    $controller_class = 'FMController' . ucfirst($page);
    $controller = new $controller_class();
    $controller->execute();
  }
}

function form_maker_ajax_frontend() {
  require_once(WD_FM_DIR . '/framework/WDW_FM_Library.php');
  $page = WDW_FM_Library::get('page');
  if ($page != '') {
    require_once (WD_FM_DIR . '/frontend/controllers/FMController' . ucfirst($page) . '.php');
    $controller_class = 'FMController' . ucfirst($page);
    $controller = new $controller_class();
    $controller->execute();
  }
}

// Add the Form Maker button.
function form_maker_add_button($buttons) {
  array_push($buttons, "Form_Maker_mce");
  return $buttons;
}

// Register Form Maker button.
function form_maker_register($plugin_array) {
  $url = WD_FM_URL . '/js/form_maker_editor_button.js';
  $plugin_array["Form_Maker_mce"] = $url;
  return $plugin_array;
}

function form_maker_admin_ajax() {
  ?>
  <script>
    var form_maker_admin_ajax = '<?php echo add_query_arg(array('action' => 'formmakerwindow'), admin_url('admin-ajax.php')); ?>';
    var plugin_url = '<?php echo WD_FM_URL; ?>';
    var content_url = '<?php echo content_url() ?>';
    var admin_url = '<?php echo admin_url('admin.php'); ?>';
    var nonce_fm = '<?php echo wp_create_nonce('nonce_fm') ?>';
  </script>
  <?php
}
add_action('admin_head', 'form_maker_admin_ajax');

function fm_output_buffer() {
  ob_start();
}
add_action('init', 'fm_output_buffer');
 
add_shortcode('Form', 'fm_shortcode');

function fm_shortcode($attrs) {
	$fm_settings = get_option('fm_settings');
	$fm_shortcode = isset($fm_settings['fm_shortcode']) ? $fm_settings['fm_shortcode'] : '';
	if($fm_shortcode){
		$new_shortcode = '[Form';
		foreach ($attrs as $key=>$value) {
			$new_shortcode .= ' ' . $key . '="' . $value . '"';
		}
		$new_shortcode .= ']'; 
		return $new_shortcode;
	}
	else {
		ob_start();
		FM_front_end_main($attrs, 'embedded');
		return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
	}
}
if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
	add_action('wp_footer', 'FM_front_end_main');
	add_action('wp_enqueue_scripts', 'form_maker_front_end_scripts');
}
function FM_front_end_main($params = array(), $type = '') {
	if(!isset($params['type'])){
		$form_id =  isset($params['id']) ? (int)$params['id'] : 0;
    wd_form_maker($form_id, $type);
	}
	else{
		require_once (WD_FM_DIR . '/frontend/controllers/FMControllerForm_submissions.php');
		$controller = new FMControllerForm_submissions();
		$form_id = isset($params['id']) ? (int)$params['id'] : 1;
		$startdate = isset($params['startdate']) ? $params['startdate'] : '';
		$enddate = isset($params['enddate']) ? $params['enddate'] : '';
		$submit_date = isset($params['submit_date']) ? $params['submit_date'] : '';
		$submitter_ip = isset($params['submitter_ip']) ? $params['submitter_ip'] : '';
		$username = isset($params['username']) ? $params['username'] : '';
		$useremail = isset($params['useremail']) ? $params['useremail'] : '';
		$form_fields = isset($params['form_fields']) ? $params['form_fields'] : '1';
		$show = isset($params['show']) ? $params['show'] : '1,1,1,1,1,1,1,1,1,1';
        $submissions = $controller->execute($form_id, $startdate, $enddate, $submit_date, $submitter_ip, $username, $useremail, $form_fields, $show);
		echo $submissions;
	}

	return;
}

add_shortcode('email_verification', 'fm_email_verification_shortcode');
function fm_email_verification_shortcode() {
	require_once(WD_FM_DIR . '/framework/WDW_FM_Library.php');
	require_once(WD_FM_DIR . '/frontend/controllers/FMControllerVerify_email.php');
  $controller_class = 'FMControllerVerify_email';
  $controller = new $controller_class();
  $controller->execute();
}

function wd_form_maker($id, $type = 'embedded') {
  require_once (WD_FM_DIR . '/frontend/controllers/FMControllerForm_maker.php');
  $controller = new FMControllerForm_maker();
  $form = $controller->execute($id, $type);
  echo $form;
}

function Form_maker_fornt_end_main($content) {
  global $form_maker_generate_action;
  if ($form_maker_generate_action) {
    $pattern = '[\[Form id="([0-9]*)"\]]';
    $count_forms_in_post = preg_match_all($pattern, $content, $matches_form);
    if ($count_forms_in_post) {
      require_once (WD_FM_DIR . '/frontend/controllers/FMControllerForm_maker.php');
      $controller = new FMControllerForm_maker();
      for ($jj = 0; $jj < $count_forms_in_post; $jj++) {
        $padron = $matches_form[0][$jj];
        $replacment = $controller->execute($matches_form[1][$jj]);
        $content = str_replace($padron, $replacment, $content);
      }
    }
    
    $pattern = '[\[Form id="([0-9]*)" type="submission" startdate="([0-9,\-]*)" enddate="([0-9,\-]*)" submit_date="([0,1])" submitter_ip="([0,1])" username="([0,1])" useremail="([0,1])" form_fields="([0,1])" show="([0,1,\,]*)"\]]';
    $count_forms_in_post = preg_match_all($pattern, $content, $matches_form);
    if ($count_forms_in_post) {
      require_once (WD_FM_DIR . '/frontend/controllers/FMControllerForm_submissions.php');
      $controller = new FMControllerForm_submissions();
      for ($jj = 0; $jj < $count_forms_in_post; $jj++) {
        $padron = $matches_form[0][$jj];
        $form_id = $matches_form[1][$jj];
        $startdate = $matches_form[2][$jj];
        $enddate = $matches_form[3][$jj];
        $submit_date = $matches_form[4][$jj];
        $submitter_ip = $matches_form[5][$jj];
        $username = $matches_form[6][$jj];
        $useremail = $matches_form[7][$jj];
        $form_fields = $matches_form[8][$jj];
        $show = $matches_form[9][$jj];
        ob_start();
        $controller->execute($form_id, $startdate, $enddate, $submit_date, $submitter_ip, $username, $useremail, $form_fields, $show);
        $replacment = ob_get_clean();
        $content = str_replace($padron, $replacment, $content);
      }
    }
  }
  return $content;
}

$fm_settings = get_option('fm_settings');
if(isset($fm_settings['fm_shortcode']) && $fm_settings['fm_shortcode']!= '')
	add_filter('the_content', 'Form_maker_fornt_end_main', 5000);

function xapel_shortcode_1($content) {
  $pattern = '[\[(contact_form|wd_contact_form) id="([0-9]*)"\]]';
  $count_forms_in_post = preg_match_all($pattern, $content, $matches_form);
  if ($count_forms_in_post) {
    require_once (WD_FM_DIR . '/frontend/controllers/FMControllerForm_maker.php');
    $controller = new FMControllerForm_maker();
    for ($jj = 0; $jj < $count_forms_in_post; $jj++) {
      $padron = $matches_form[0][$jj];
      $replacment = '[Form id="' . $matches_form[2][$jj] . '"]';
      $content = str_replace($padron, $replacment, $content);
    }
  }
  return $content;
}
add_filter('the_content', 'xapel_shortcode_1', 1);

// Add the Form Maker button to editor.
add_action('wp_ajax_formmakerwindow', 'form_maker_ajax');
add_filter('mce_external_plugins', 'form_maker_register');
add_filter('mce_buttons', 'form_maker_add_button', 0);

// Form Maker Widget.
if (class_exists('WP_Widget')) {
  require_once(WD_FM_DIR . '/admin/controllers/FMControllerWidget.php');
  add_action('widgets_init', create_function('', 'return register_widget("FMControllerWidget");'));
}

// Register fmemailverification post type
add_action('init', 'register_fmemailverification_cpt');
function register_fmemailverification_cpt(){
	$args = array(
	  'public' => true,
	  'label'  => 'FM Email Verification'
	);
	
	register_post_type( 'fmemailverification', $args );
	if(!get_option('fm_emailverification')) {	
		flush_rewrite_rules();
		add_option('fm_emailverification', true);
	}
}

// Activate plugin.
function form_maker_activate() {
	deactivate_plugins("contact-form-maker/contact-form-maker.php");
	$version = get_option("wd_form_maker_version");
	$new_version = '1.11.1';

	global $wpdb;
	if (!$version) {
		add_option("wd_form_maker_version", $new_version, '', 'no');
		if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "formmaker'") == $wpdb->prefix . "formmaker") {
			require_once WD_FM_DIR . "/form_maker_update.php";
			$recaptcha_keys = $wpdb->get_row('SELECT `public_key`, `private_key` FROM ' . $wpdb->prefix . 'formmaker WHERE public_key!="" and private_key!=""', ARRAY_A);
			$public_key = isset($recaptcha_keys['public_key']) ? $recaptcha_keys['public_key'] : '';
			$private_key = isset($recaptcha_keys['private_key']) ? $recaptcha_keys['private_key'] : '';
			if (FALSE === $fm_settings = get_option('fm_settings')) {
				add_option('fm_settings', array('public_key' => $public_key, 'private_key' => $private_key, 'csv_delimiter' => ',', 'map_key' => ''));	
			}
			form_maker_update_until_mvc();
			form_maker_update_contact();
			form_maker_update('');
		}
		else {
			require_once WD_FM_DIR . "/form_maker_insert.php";
			from_maker_insert();
			$email_verification_post = array(
				'post_title'    => 'Email Verification',
				'post_content'  => '[email_verification]',
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'   => 'fmemailverification',
			);
			$mail_verification_post_id = wp_insert_post( $email_verification_post );
		
			add_option('fm_settings', array('public_key' => '', 'private_key' => '', 'csv_delimiter' => ',', 'map_key' => ''));
			$wpdb->update($wpdb->prefix . "formmaker", array(
				'mail_verification_post_id' => $mail_verification_post_id,
			), array('id' => 1), array(
				'%d',
			), array('%d'));
		}
	}
	elseif (version_compare($version, $new_version, '<')) {
		require_once WD_FM_DIR . "/form_maker_update.php";
		$mail_verification_post_ids = $wpdb->get_results($wpdb->prepare('SELECT mail_verification_post_id FROM ' . $wpdb->prefix . 'formmaker WHERE mail_verification_post_id!="%d"',0));
		if($mail_verification_post_ids)
			foreach($mail_verification_post_ids as $mail_verification_post_id) {
				 $update_email_ver_post_type = array(
				  'ID'           => (int)$mail_verification_post_id->mail_verification_post_id,
				  'post_type'   => 'fmemailverification',
				);

				wp_update_post( $update_email_ver_post_type ); 
			}
		form_maker_update($version);
		update_option("wd_form_maker_version", $new_version);
		
		$recaptcha_keys = $wpdb->get_row('SELECT `public_key`, `private_key` FROM ' . $wpdb->prefix . 'formmaker WHERE public_key!="" and private_key!=""', ARRAY_A);
		$public_key = isset($recaptcha_keys['public_key']) ? $recaptcha_keys['public_key'] : '';
		$private_key = isset($recaptcha_keys['private_key']) ? $recaptcha_keys['private_key'] : '';
		if (FALSE === $fm_settings = get_option('fm_settings')) {
			add_option('fm_settings', array('public_key' => $public_key, 'private_key' => $private_key, 'csv_delimiter' => ',', 'map_key' => ''));	
		}
	}
}

function del_trans() {
	delete_transient( 'fm_update_check' );
}
register_activation_hook(__FILE__, 'form_maker_activate');
register_activation_hook(__FILE__, 'del_trans');

if (!isset($_GET['action']) || $_GET['action'] != 'deactivate') {
  add_action('admin_init', 'form_maker_activate');
}

// Deactivate plugin.
function form_maker_deactivate() {
  if (isset($_GET['form_maker_uninstall'])) {
    if ($_GET['form_maker_uninstall'] == 1) {
      delete_option('formmaker_cureent_version');
      delete_option('contact_form_themes');
      delete_option('contact_form_forms');
    }
  }
  delete_option('fm_emailverification');
}
register_deactivation_hook(__FILE__, 'form_maker_deactivate');

// Form Maker manage page styles.
function form_maker_manage_styles() {
  wp_admin_css('thickbox');
  wp_enqueue_style('form_maker_tables', WD_FM_URL . '/css/form_maker_tables.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_first', WD_FM_URL . '/css/form_maker_first.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_calendar-jos', WD_FM_URL . '/css/calendar-jos.css', array(), WD_FM_VERSION);
  wp_enqueue_style('phone_field_css', WD_FM_URL . '/css/intlTelInput.css', array(), WD_FM_VERSION);
  wp_enqueue_style('jquery-ui', WD_FM_URL . '/css/jquery-ui-1.10.3.custom.css', array(), WD_FM_VERSION);
  wp_enqueue_style('jquery-ui-spinner', WD_FM_URL . '/css/jquery-ui-spinner.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_style', WD_FM_URL . '/css/style.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_codemirror', WD_FM_URL . '/css/codemirror.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_layout', WD_FM_URL . '/css/form_maker_layout.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-bootstrap', WD_FM_URL . '/css/fm-bootstrap.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-colorpicker', WD_FM_URL . '/css/spectrum.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-font-awesome', WD_FM_URL . '/css/frontend/font-awesome/font-awesome.css', array(), WD_FM_VERSION);
}

// Form Maker manage page scripts.
function form_maker_manage_scripts() {
  wp_enqueue_script('thickbox');
  $fm_settings = get_option('fm_settings');
  $map_key = isset($fm_settings['map_key']) ? $fm_settings['map_key'] : '';

  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-slider');
  wp_enqueue_script('jquery-ui-spinner');
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_media();

  // wp_enqueue_script('mootools', WD_FM_URL . '/js/mootools.js', array(), '1.12');
  if($_GET['page'] == 'manage_fm'){
    wp_enqueue_script('gmap_form_api', 'https://maps.google.com/maps/api/js?v=3.exp&key='.$map_key);
  }
  wp_enqueue_script('gmap_form', WD_FM_URL . '/js/if_gmap_back_end.js', array(), WD_FM_VERSION);
  wp_enqueue_script('phone_field', WD_FM_URL . '/js/intlTelInput.js', array(), '11.0.0');

  wp_enqueue_script('form_maker_admin', WD_FM_URL . '/js/form_maker_admin.js', array(), WD_FM_VERSION);
  wp_enqueue_script('form_maker_manage', WD_FM_URL . '/js/form_maker_manage.js', array(), WD_FM_VERSION);

  wp_enqueue_script('form_maker_codemirror', WD_FM_URL . '/js/layout/codemirror.js', array(), '2.3');
  wp_enqueue_script('form_maker_clike', WD_FM_URL . '/js/layout/clike.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_formatting', WD_FM_URL . '/js/layout/formatting.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_css', WD_FM_URL . '/js/layout/css.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_javascript', WD_FM_URL . '/js/layout/javascript.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_xml', WD_FM_URL . '/js/layout/xml.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_php', WD_FM_URL . '/js/layout/php.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_htmlmixed', WD_FM_URL . '/js/layout/htmlmixed.js', array(), '1.0.0');

  wp_enqueue_script('Calendar', WD_FM_URL . '/js/calendar/calendar.js', array(), '1.0');
  wp_enqueue_script('calendar_function', WD_FM_URL . '/js/calendar/calendar_function.js', array(), WD_FM_VERSION);

  // wp_enqueue_script('form_maker_calendar_setup', WD_FM_URL . '/js/calendar/calendar-setup.js');
  wp_enqueue_script('fm-colorpicker', WD_FM_URL . '/js/spectrum.js', array(), WD_FM_VERSION);
}

// Form Maker submissions page styles.
function form_maker_submissions_styles() {
  wp_admin_css('thickbox');
  wp_enqueue_style('form_maker_tables', WD_FM_URL . '/css/form_maker_tables.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_calendar-jos', WD_FM_URL . '/css/calendar-jos.css', array(), WD_FM_VERSION);

  wp_enqueue_style('jquery-ui', WD_FM_URL . '/css/jquery-ui-1.10.3.custom.css', array(), '1.10.3');
  wp_enqueue_style('jquery-ui-spinner', WD_FM_URL . '/css/jquery-ui-spinner.css', array(), '1.10.3');
  wp_enqueue_style('jquery.fancybox', WD_FM_URL . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5');
  wp_enqueue_style('form_maker_style', WD_FM_URL . '/css/style.css', array(), WD_FM_VERSION);
}
// Form Maker submissions page scripts.
function form_maker_submissions_scripts() {
  wp_enqueue_script('thickbox');
  wp_enqueue_script('jquery');
  wp_enqueue_script( 'jquery-ui-progressbar' ); 
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-slider');
  wp_enqueue_script('jquery-ui-spinner');
  wp_enqueue_script('jquery-ui-mouse');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-datepicker');

  // wp_enqueue_script('mootools', WD_FM_URL . '/js/mootools.js', array(), '1.12');

  wp_enqueue_script('form_maker_admin', WD_FM_URL . '/js/form_maker_admin.js', array(), WD_FM_VERSION);
  wp_enqueue_script('form_maker_manage', WD_FM_URL . '/js/form_maker_manage.js', array(), WD_FM_VERSION);
  wp_enqueue_script('form_maker_submissions', WD_FM_URL . '/js/form_maker_submissions.js', array(), WD_FM_VERSION);

  wp_enqueue_script('main_div_front_end', WD_FM_URL . '/js/main_div_front_end.js', array(), WD_FM_VERSION);

  wp_enqueue_script('Calendar', WD_FM_URL . '/js/calendar/calendar.js', array(), '1.0');
  wp_enqueue_script('calendar_function', WD_FM_URL . '/js/calendar/calendar_function.js', array(), WD_FM_VERSION);

  // wp_enqueue_script('form_maker_calendar_setup', WD_FM_URL . '/js/calendar/calendar-setup.js');
  
  // Fancybox.
  wp_enqueue_script('jquery.fancybox.pack', WD_FM_URL . '/js/fancybox/jquery.fancybox.pack.js', array(), '2.1.5');
  wp_localize_script('main_div_front_end', 'fm_objectL10n', array(
    'plugin_url' => WD_FM_URL
  ));
}

function form_maker_styles() {
  wp_enqueue_style('form_maker_tables', WD_FM_URL . '/css/form_maker_tables.css', array(), WD_FM_VERSION);
}
function form_maker_scripts() {
  wp_enqueue_script('form_maker_admin', WD_FM_URL . '/js/form_maker_admin.js', array(), WD_FM_VERSION);
}

$form_maker_generate_action = 0;
function form_maker_generate_action() {
  global $form_maker_generate_action;
  $form_maker_generate_action = 1;
}
add_filter('wp_head', 'form_maker_generate_action', 10000);

function form_maker_front_end_scripts() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-slider');
  wp_enqueue_script('jquery-ui-spinner');
  wp_enqueue_script('jquery-effects-shake');
  wp_enqueue_script('jquery-ui-datepicker');

  wp_register_style('fm-jquery-ui', WD_FM_URL . '/css/jquery-ui-1.10.3.custom.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-jquery-ui');
  wp_register_style('fm-jquery-ui-spinner', WD_FM_URL . '/css/jquery-ui-spinner.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-jquery-ui-spinner');

  wp_register_script('gmap_form', WD_FM_URL . '/js/if_gmap_front_end.js', array(), WD_FM_VERSION);
  wp_enqueue_script('gmap_form');
  wp_register_script('phone_field', WD_FM_URL . '/js/intlTelInput.js', array(), WD_FM_VERSION);
  wp_enqueue_script('phone_field');

  wp_register_script('fm-file-upload', WD_FM_URL . '/js/file-upload.js', array(), WD_FM_VERSION);
  wp_enqueue_script('fm-file-upload');

  wp_register_script('fm-Calendar', WD_FM_URL . '/js/calendar/calendar.js', array(), WD_FM_VERSION);
  wp_enqueue_script('fm-Calendar');
  wp_register_script('calendar_function', WD_FM_URL . '/js/calendar/calendar_function.js', array(), WD_FM_VERSION);
  wp_enqueue_script('calendar_function');

  wp_register_style('form_maker_calendar-jos', WD_FM_URL . '/css/calendar-jos.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_calendar-jos');
  wp_register_style('phone_field_css', WD_FM_URL . '/css/intlTelInput.css', array(), WD_FM_VERSION);
  wp_enqueue_style('phone_field_css');
  wp_register_style('form_maker_frontend', WD_FM_URL . '/css/form_maker_frontend.css', array(), WD_FM_VERSION);
  wp_enqueue_style('form_maker_frontend');
  wp_register_style('style_submissions', WD_FM_URL . '/css/style_submissions.css', array(), WD_FM_VERSION);
  wp_enqueue_style('style_submissions');

  wp_register_script('main_div_front_end', WD_FM_URL . '/js/main_div_front_end.js', array(), WD_FM_VERSION);
  wp_enqueue_script('main_div_front_end');
  wp_localize_script('main_div_front_end', 'fm_objectL10n', array(
    'plugin_url' => WD_FM_URL,
    'fm_file_type_error' => addslashes(__('Can not upload this type of file', 'form_maker')),
    'fm_field_is_required' => addslashes(__('Field is required', 'form_maker')),
    'fm_min_max_check_1' => addslashes((__('The ', 'form_maker'))),
    'fm_min_max_check_2' => addslashes((__(' value must be between ', 'form_maker'))),
    'fm_spinner_check' => addslashes((__('Value must be between ', 'form_maker'))),
  ));

  require_once(WD_FM_DIR . '/framework/WDW_FM_Library.php');
	$google_fonts = WDW_FM_Library::get_google_fonts();
	$fonts = implode("|", str_replace(' ', '+', $google_fonts));
	wp_register_style('fm_googlefonts', 'https://fonts.googleapis.com/css?family=' . $fonts . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic', null, null);
	wp_enqueue_style('fm_googlefonts');

  wp_register_style('fm-animate', WD_FM_URL . '/css/frontend/fm-animate.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-animate');
  wp_register_style('fm-font-awesome', WD_FM_URL . '/css/frontend/font-awesome/font-awesome.css', array(), WD_FM_VERSION);
  wp_enqueue_style('fm-font-awesome');
}
// add_action('wp_enqueue_scripts', 'form_maker_front_end_scripts');

// Languages localization.
function form_maker_language_load() {
  load_plugin_textdomain('form_maker', FALSE, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'form_maker_language_load');

function fm_topic() {
  $page = isset($_GET['page']) ? $_GET['page'] : '';
  $task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
  $user_guide_link = 'https://web-dorado.com/wordpress-form-maker/';
  $support_forum_link = 'https://wordpress.org/support/plugin/form-maker';
  $pro_link = 'https://web-dorado.com/files/fromFormMaker.php';
  $support_icon = WD_FM_URL . '/images/support.png';
  $prefix = 'fm';
  $is_free = FALSE;
  switch ($page) {
    case 'blocked_ips_fm': {
      $help_text = 'block IPs';
      $user_guide_link .= 'blocking-ips.html';
      break;
    }
    case 'goptions_fm': {
      $help_text = 'edit form settings';
      $user_guide_link .= 'configuring-form-options.html';
      break;
    }
    case 'licensing_fm': {
      $help_text = '';
      $user_guide_link .= '';
      break;
    }
    case 'manage_fm': {
      switch ($task) {
        case 'edit':
        case 'edit_old': {
          $help_text = 'add fields to your form';
          $user_guide_link .= 'description-of-form-fields.html';
          break;
        }
        case 'form_options':
        case 'form_options_old': {
          $help_text = 'edit form options';
          $user_guide_link .= 'configuring-form-options.html';
          break;
        }
        default: {
          $help_text = 'create, edit forms';
          $user_guide_link .= 'creating-form.html';
        }
      }
      break;
    }
    case 'submissions_fm': {
      $help_text = 'view and manage form submissions';
      $user_guide_link .= 'managing-submissions.html';
      break;
    }
    case 'themes_fm': {
      $help_text = 'create, edit form themes';
      $user_guide_link .= 'creating-form.html';
      break;
    }
    default: {
      return '';
    }
  }
  ob_start();
  ?>
  <style>
    .wd_topic {
      background-color: #ffffff;
      border: none;
      box-sizing: border-box;
      display: inline-block;
      clear: both;
      color: #6e7990;
      font-size: 14px;
      font-weight: bold;
      line-height: 30px;
      padding: 10px 15px;
      vertical-align: middle;
      width: 98%;
    }

    .wd_topic .wd_help_topic {
      float: left;
    }

    .wd_topic .wd_help_topic a {
      color: #0073aa;
    }

    .wd_topic .wd_help_topic a:hover {
      color: #00A0D2;
    }

    .wd_topic .wd_support {
      float: right;
      margin: 0 10px;
    }

    .wd_topic .wd_support img {
      vertical-align: middle;
    }

    .wd_topic .wd_support a {
      text-decoration: none;
      color: #6E7990;
    }

    .wd_topic .wd_pro {
      float: right;
      background-color: #45A6B7;
      padding: 0 10px;
    }

    .wd_topic .wd_pro a {
      border: none;
      box-shadow: none !important;
      color: #FFFFFF;
      text-decoration: none;
    }
  </style>
  <div class="wd_topic">
    <?php
    if ($help_text) {
      ?>
      <span class="wd_help_topic">
        <?php echo sprintf(__('This section allows you to %s.', $prefix), $help_text); ?>
        <a target="_blank" href="<?php echo $user_guide_link; ?>">
          <?php _e('Read More in User Manual', $prefix); ?>
        </a>
      </span>
      <?php
    }
    if ($is_free) {
      $text = strtoupper(__('Upgrade to paid version', $prefix));
      ?>
      <span class="wd_pro">
        <a target="_blank" href="<?php echo $pro_link; ?>">
          <span><?php echo $text; ?></span>
        </a>
      </span>
      <?php
    }
    ?>
    <span class="wd_support">
      <a target="_blank" href="<?php echo $support_forum_link; ?>">
        <img src="<?php echo $support_icon; ?>"/>
        <?php _e('Support Forum', $prefix); ?>
      </a>
    </span>
  </div>
  <?php
  echo ob_get_clean();
}

add_action('admin_notices', 'fm_topic', 11);
