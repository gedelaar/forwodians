<?php

class FMControllerManage_fm {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function execute() {
    $task = WDW_FM_Library::get('task');
    $id = (int)WDW_FM_Library::get('current_id', 0);
    $message = WDW_FM_Library::get('message');
    echo WDW_FM_Library::message_id($message);
    if (method_exists($this, $task)) {
      check_admin_referer('nonce_fm', 'nonce_fm');
      $this->$task($id);
    }
    else {
      $this->display();
    }
  }

  public function undo() {
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";

    global $wpdb;	
    $backup_id = (int)WDW_FM_Library::get('backup_id');
    $id = (int)WDW_FM_Library::get('id');
	
    $query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id < $backup_id AND id = $id ORDER BY backup_id DESC LIMIT 0 , 1 ";
    $backup_id = $wpdb->get_var($query);
	
    $view = new FMViewManage_fm($model);
    $view->edit($backup_id);
  }

  public function redo() {
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    global $wpdb;	
    $backup_id = (int)WDW_FM_Library::get('backup_id');
    $id = (int)WDW_FM_Library::get('id');
	
    $query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id > $backup_id AND id = $id ORDER BY backup_id ASC LIMIT 0 , 1 ";
    $backup_id = $wpdb->get_var($query);
   
    $view = new FMViewManage_fm($model);
    $view->edit($backup_id);
  }

  public function display() {
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    $view = new FMViewManage_fm($model);
    $view->display();
  }

  public function add() {
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    $view = new FMViewManage_fm($model);
    $view->edit(0);
  }

  public function edit() {
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    $view = new FMViewManage_fm($model);
    $id = (int)WDW_FM_Library::get('current_id', 0);
	
    global $wpdb;	
    $query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE cur=1 and id=".$id;
    $backup_id = $wpdb->get_var($query);
	
    if(!$backup_id) {
      $query = "SELECT max(backup_id) FROM ".$wpdb->prefix."formmaker_backup";
      $backup_id = $wpdb->get_var($query);
      if($backup_id)
        $backup_id++;
      else
        $backup_id=1;
      $query = "INSERT INTO ".$wpdb->prefix."formmaker_backup SELECT ".$backup_id." AS backup_id, 1 AS cur, formmakerbkup.id, formmakerbkup.title, formmakerbkup.type, formmakerbkup.mail, formmakerbkup.form_front, formmakerbkup.theme, formmakerbkup.javascript, formmakerbkup.submit_text, formmakerbkup.url, formmakerbkup.submit_text_type, formmakerbkup.script_mail, formmakerbkup.script_mail_user, formmakerbkup.counter, formmakerbkup.published, formmakerbkup.label_order, formmakerbkup.label_order_current, formmakerbkup.article_id, formmakerbkup.pagination, formmakerbkup.show_title, formmakerbkup.show_numbers, formmakerbkup.public_key, formmakerbkup.private_key, formmakerbkup.recaptcha_theme, formmakerbkup.paypal_mode, formmakerbkup.checkout_mode, formmakerbkup.paypal_email, formmakerbkup.payment_currency, formmakerbkup.tax, formmakerbkup.form_fields, formmakerbkup.savedb, formmakerbkup.sendemail, formmakerbkup.requiredmark, formmakerbkup.from_mail, formmakerbkup.from_name, formmakerbkup.reply_to, formmakerbkup.send_to, formmakerbkup.autogen_layout, formmakerbkup.custom_front, formmakerbkup.mail_from_user, formmakerbkup.mail_from_name_user, formmakerbkup.reply_to_user, formmakerbkup.condition, formmakerbkup.mail_cc, formmakerbkup.mail_cc_user, formmakerbkup.mail_bcc, formmakerbkup.mail_bcc_user, formmakerbkup.mail_subject, formmakerbkup.mail_subject_user, formmakerbkup.mail_mode, formmakerbkup.mail_mode_user, formmakerbkup.mail_attachment, formmakerbkup.mail_attachment_user, formmakerbkup.user_id_wd, formmakerbkup.sortable, formmakerbkup.frontend_submit_fields, formmakerbkup.frontend_submit_stat_fields, formmakerbkup.mail_emptyfields, formmakerbkup.mail_verify, formmakerbkup.mail_verify_expiretime, formmakerbkup.mail_verification_post_id, formmakerbkup.save_uploads, formmakerbkup.header_title, formmakerbkup.header_description, formmakerbkup.header_image_url, formmakerbkup.header_image_animation, formmakerbkup.header_hide_image FROM ".$wpdb->prefix."formmaker as formmakerbkup WHERE id=".$id;
      $wpdb->query($query);
    }
    $view->edit($backup_id);
  }

  public function form_layout() {
    if (!isset($_GET['task'])) {
      $this->save_db();
    }
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    $view = new FMViewManage_fm($model);
    global $wpdb;
    $id = (int)WDW_FM_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    $view->form_layout($id);
  }

  public function save_layout() {
    $message = $this->save_db_layout();
    $page = WDW_FM_Library::get('page');
    $current_id = (int)WDW_FM_Library::get('current_id', 0);
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function apply_layout() {
    $message = $this->save_db_layout();
    $page = WDW_FM_Library::get('page');
    $current_id = (int)WDW_FM_Library::get('current_id', 0);
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'form_layout', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function save_db_layout() {
    global $wpdb;
    $id = (int)WDW_FM_Library::get('current_id', 0);
    $custom_front = (isset($_POST['custom_front']) ? stripslashes($_POST['custom_front']) : '');
    $autogen_layout = (isset($_POST['autogen_layout']) ? 1 : 0);
    $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
      'custom_front' => $custom_front,
      'autogen_layout' => $autogen_layout
    ), array('id' => $id));
    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function form_options() {
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    $view = new FMViewManage_fm($model);
    
    global $wpdb;
    $id = (int)WDW_FM_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    $view->form_options($id);
  }

  public function display_options() {
		require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewManage_fm.php";
    $view = new FMViewManage_fm($model);
   
		global $wpdb;
		$id = (int)WDW_FM_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
		$view->display_options($id);
	}

  public function save_options() {
    $message = $this->save_db_options();
    // $this->edit();
    $page = WDW_FM_Library::get('page');
    $current_id = (int)WDW_FM_Library::get('current_id', 0);
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function save_display_options() {
		$message = $this->save_dis_options();
		$page = WDW_FM_Library::get('page');
		$current_id = (int)WDW_FM_Library::get('current_id', 0);
		WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
	}

  public function view_options() {
    if (!isset($_GET['task'])) {
      $this->save_db();
    }
    global $wpdb;
    $page = WDW_FM_Library::get('page');
    $current_id = (int)WDW_FM_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'form_options', 'current_id' => $current_id), admin_url('admin.php')));
  }

  public function view_display_options() {
    if (!isset($_GET['task'])) {
      $this->save_db();
    }
    global $wpdb;
    $page = WDW_FM_Library::get('page');
    $current_id = (int)WDW_FM_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display_options', 'current_id' => $current_id), admin_url('admin.php')));
  }
  
  public function apply_options() {
    $message = $this->save_db_options();
    $page = WDW_FM_Library::get('page');
    $current_id = (int)WDW_FM_Library::get('current_id', 0);
    $fieldset_id = WDW_FM_Library::get('fieldset_id', 'general');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'form_options', 'current_id' => $current_id, 'message' => $message, 'fieldset_id' => $fieldset_id), admin_url('admin.php')));
  }

  public function apply_display_options() {
		$message = $this->save_dis_options();
		$page = WDW_FM_Library::get('page');
		$current_id = (int)WDW_FM_Library::get('current_id', 0);
		WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display_options', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
	}

  public function remove_query() {
    global $wpdb;
    $cid = ((isset($_POST['cid']) && $_POST['cid'] != '') ? $_POST['cid'] : NULL); 
    if (count($cid)) {
      array_walk($cid, create_function('&$value', '$value = (int)$value;')); 
      $cids = implode(',', $cid);
      $query = 'DELETE FROM ' . $wpdb->prefix . 'formmaker_query WHERE id IN ( ' . $cids . ' )';
      if ($wpdb->query($query)) {
        echo WDW_FM_Library::message('Items Succesfully Deleted.', 'updated');
      }
      else {
        echo WDW_FM_Library::message('Error. Please install plugin again.', 'error');
      }
    }
    else {
      echo WDW_FM_Library::message('You must select at least one item.', 'error');
    }
    $this->apply_options();
  }
  
  public function cancel_options() {
    $this->edit();
  }

  public function save_db_options() {
    $javascript = "// Occurs before the form is loaded
function before_load() {
  
}	
// Occurs just before submitting  the form
function before_submit() {
	// IMPORTANT! If you want to interrupt (stop) the submitting of the form, this function should return true. You don't need to return any value if you don't want to stop the submission.
}	
// Occurs just before resetting the form
function before_reset() {
  
}";
    global $wpdb;
    // $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes($_POST['current_id'])) : 0);
    $id = (int)WDW_FM_Library::get('current_id', 0);
    $published = (isset($_POST['published']) ? esc_html(stripslashes($_POST['published'])) : 1);
    $savedb = (isset($_POST['savedb']) ? esc_html(stripslashes($_POST['savedb'])) : 1);
    $theme = (int)((isset($_POST['theme']) && (esc_html($_POST['theme']) != 0)) ? esc_html(stripslashes($_POST['theme'])) : $wpdb->get_var("SELECT id FROM " . $wpdb->prefix . "formmaker_themes WHERE `default`='1'"));
    $requiredmark = (isset($_POST['requiredmark']) ? esc_html(stripslashes($_POST['requiredmark'])) : '*');
    $sendemail = (isset($_POST['sendemail']) ? esc_html(stripslashes($_POST['sendemail'])) : 1);
    $save_uploads = (isset($_POST['save_uploads']) ? esc_html(stripslashes($_POST['save_uploads'])) : 1);
    $mail = (isset($_POST['mail']) ? esc_html(stripslashes($_POST['mail'])) : '');
    if (isset($_POST['mailToAdd']) && esc_html(stripslashes($_POST['mailToAdd'])) != '') {
      $mail .= esc_html(stripslashes($_POST['mailToAdd'])) . ',';
    }
    $from_mail = (isset($_POST['from_mail']) ? esc_html(stripslashes($_POST['from_mail'])) : '');
    $from_name = (isset($_POST['from_name']) ? esc_html(stripslashes($_POST['from_name'])) : '');
    $reply_to = (isset($_POST['reply_to']) ? esc_html(stripslashes($_POST['reply_to'])) : '');
    if ($from_mail == "other") {
      $from_mail = (isset($_POST['mail_from_other']) ? esc_html(stripslashes($_POST['mail_from_other'])) : '');
    }
    if ($reply_to == "other") {
      $reply_to = (isset($_POST['reply_to_other']) ? esc_html(stripslashes($_POST['reply_to_other'])) : '');
    }
    $script_mail = (isset($_POST['script_mail']) ? stripslashes($_POST['script_mail']) : '%all%');
    $mail_from_user = (isset($_POST['mail_from_user']) ? esc_html(stripslashes($_POST['mail_from_user'])) : '');
    $mail_from_name_user = (isset($_POST['mail_from_name_user']) ? esc_html(stripslashes($_POST['mail_from_name_user'])) : '');
    $reply_to_user = (isset($_POST['reply_to_user']) ? esc_html(stripslashes($_POST['reply_to_user'])) : '');
    $condition = (isset($_POST['condition']) ? esc_html(stripslashes($_POST['condition'])) : '');
    $mail_cc = (isset($_POST['mail_cc']) ? esc_html(stripslashes($_POST['mail_cc'])) : '');
    $mail_cc_user = (isset($_POST['mail_cc_user']) ? esc_html(stripslashes($_POST['mail_cc_user'])) : '');
    $mail_bcc = (isset($_POST['mail_bcc']) ? esc_html(stripslashes($_POST['mail_bcc'])) : '');
    $mail_bcc_user = (isset($_POST['mail_bcc_user']) ? esc_html(stripslashes($_POST['mail_bcc_user'])) : '');
    $mail_subject = (isset($_POST['mail_subject']) ? esc_html(stripslashes($_POST['mail_subject'])) : '');
    $mail_subject_user = (isset($_POST['mail_subject_user']) ? esc_html(stripslashes($_POST['mail_subject_user'])) : '');
    $mail_mode = (isset($_POST['mail_mode']) ? esc_html(stripslashes($_POST['mail_mode'])) : 1);
    $mail_mode_user = (isset($_POST['mail_mode_user']) ? esc_html(stripslashes($_POST['mail_mode_user'])) : 1);
    $mail_attachment = (isset($_POST['mail_attachment']) ? esc_html(stripslashes($_POST['mail_attachment'])) : 1);
    $mail_attachment_user = (isset($_POST['mail_attachment_user']) ? esc_html(stripslashes($_POST['mail_attachment_user'])) : 1);
    $script_mail_user = (isset($_POST['script_mail_user']) ? stripslashes($_POST['script_mail_user']) : '%all%');
    $submit_text = (isset($_POST['submit_text']) ? stripslashes($_POST['submit_text']) : '');
    $url = (isset($_POST['url']) ? esc_html(stripslashes($_POST['url'])) : '');
    $tax = (isset($_POST['tax']) ? esc_html(stripslashes($_POST['tax'])) : 0);
    $payment_currency = (isset($_POST['payment_currency']) ? stripslashes($_POST['payment_currency']) : '');
    $paypal_email = (isset($_POST['paypal_email']) ? esc_html(stripslashes($_POST['paypal_email'])) : '');
    $checkout_mode = (isset($_POST['checkout_mode']) ? esc_html(stripslashes($_POST['checkout_mode'])) : 'testmode');
    $paypal_mode = (isset($_POST['paypal_mode']) ? esc_html(stripslashes($_POST['paypal_mode'])) : 0);
    $javascript = (isset($_POST['javascript']) ? stripslashes($_POST['javascript']) : $javascript);
    $user_id_wd = (isset($_POST['user_id_wd']) ? stripslashes($_POST['user_id_wd']) : 'administrator,');
    $frontend_submit_fields = (isset($_POST['frontend_submit_fields']) ? stripslashes($_POST['frontend_submit_fields']) : '');
    $frontend_submit_stat_fields = (isset($_POST['frontend_submit_stat_fields']) ? stripslashes($_POST['frontend_submit_stat_fields']) : '');
    $mail_emptyfields = (isset($_POST['mail_emptyfields']) ? esc_html(stripslashes($_POST['mail_emptyfields'])) : 0);
    $mail_verify = (isset($_POST['mail_verify']) ? esc_html(stripslashes($_POST['mail_verify'])) : 0);
    $mail_verify_expiretime = (isset($_POST['mail_verify_expiretime']) ? esc_html(stripslashes($_POST['mail_verify_expiretime'])) : '');
    $send_to = '';
    for ($i = 0; $i < 20; $i++) {
      if (isset($_POST['send_to' . $i])) {
        $send_to .= '*' . esc_html(stripslashes($_POST['send_to' . $i])) . '*';
      }
    }
    if (isset($_POST['submit_text_type'])) {
      $submit_text_type = esc_html(stripslashes($_POST['submit_text_type']));
      if ($submit_text_type == 5) {
        $article_id = (isset($_POST['page_name']) ? esc_html(stripslashes($_POST['page_name'])) : 0);
      }
      else {
        $article_id = (isset($_POST['post_name']) ? esc_html(stripslashes($_POST['post_name'])) : 0);
      }
    }
    else {
      $submit_text_type = 1;
      $article_id = 0;
    }
	
	$mail_verification_post_id = (int)$wpdb->get_var('SELECT mail_verification_post_id FROM ' . $wpdb->prefix . 'formmaker WHERE mail_verification_post_id!=0');
	if($mail_verify) {
		$email_verification_post = array(
		  'post_title'    => 'Email Verification',
		  'post_content'  => '[email_verification]',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type'   => 'fmemailverification',
		);

		if(!$mail_verification_post_id || get_post( $mail_verification_post_id )===NULL)
			$mail_verification_post_id = wp_insert_post( $email_verification_post );
	}
  $paypal_mode = $paypal_mode == 'paypal' ? 1 : 0;	
    $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
      'published' => $published,
      'savedb' => $savedb,
      'theme' => $theme,
      'requiredmark' => $requiredmark,
      'sendemail' => $sendemail,
      'save_uploads' => $save_uploads,
      'mail' => $mail,
      'from_mail' => $from_mail,
      'from_name' => $from_name,
      'reply_to' => $reply_to,
      'script_mail' => $script_mail,
      'mail_from_user' => $mail_from_user,
      'mail_from_name_user' => $mail_from_name_user,
      'reply_to_user' => $reply_to_user,
      'condition' => $condition,
      'mail_cc' => $mail_cc,
      'mail_cc_user' => $mail_cc_user,
      'mail_bcc' => $mail_bcc,
      'mail_bcc_user' => $mail_bcc_user,
      'mail_subject' => $mail_subject,
      'mail_subject_user' => $mail_subject_user,
      'mail_mode' => $mail_mode,
      'mail_mode_user' => $mail_mode_user,
      'mail_attachment' => $mail_attachment,
      'mail_attachment_user' => $mail_attachment_user,
      'script_mail_user' => $script_mail_user,
      'submit_text' => $submit_text,
      'url' => $url,
      'submit_text_type' => $submit_text_type,
      'article_id' => $article_id,
      'tax' => $tax,
      'payment_currency' => $payment_currency,
      'paypal_email' => $paypal_email,
      'checkout_mode' => $checkout_mode,
      'paypal_mode' => $paypal_mode,
      'javascript' => $javascript,
      'user_id_wd' => $user_id_wd,
      'send_to' => $send_to,
      'frontend_submit_fields' => $frontend_submit_fields,
      'frontend_submit_stat_fields' => $frontend_submit_stat_fields,
      'mail_emptyfields' => $mail_emptyfields,
      'mail_verify' => $mail_verify,
      'mail_verify_expiretime' => $mail_verify_expiretime,
      'mail_verification_post_id' => $mail_verification_post_id,
    ), array('id' => $id));
    if ($save !== FALSE) {
		$save_theme_in_backup = $wpdb->update($wpdb->prefix . 'formmaker_backup', array(
			'theme' => $theme
		), array('id' => $id));
		return 8;
    }
    else {
		return 2;
    }
  }

  public function save_dis_options() {
		global $wpdb;
		$id = (int)WDW_FM_Library::get('current_id', 0);
		$scrollbox_loading_delay = (isset($_POST['scrollbox_loading_delay']) ? esc_html(stripslashes($_POST['scrollbox_loading_delay'])) : 0);
		$popover_animate_effect = (isset($_POST['popover_animate_effect']) ? esc_html(stripslashes($_POST['popover_animate_effect'])) : '');
		$popover_loading_delay = (isset($_POST['popover_loading_delay']) ? esc_html(stripslashes($_POST['popover_loading_delay'])) : 0);
		$popover_frequency = (isset($_POST['popover_frequency']) ? esc_html(stripslashes($_POST['popover_frequency'])) : 0);
		$topbar_position = (isset($_POST['topbar_position']) ? esc_html(stripslashes($_POST['topbar_position'])) : 1);
		$topbar_remain_top = (isset($_POST['topbar_remain_top']) ? esc_html(stripslashes($_POST['topbar_remain_top'])) : 1);
		$topbar_closing = (isset($_POST['topbar_closing']) ? esc_html(stripslashes($_POST['topbar_closing'])) : 1);
		$topbar_hide_duration = (isset($_POST['topbar_hide_duration']) ? esc_html(stripslashes($_POST['topbar_hide_duration'])) : 0);
		$scrollbox_position = (isset($_POST['scrollbox_position']) ? esc_html(stripslashes($_POST['scrollbox_position'])) : 1);
		$scrollbox_trigger_point = (isset($_POST['scrollbox_trigger_point']) ? esc_html(stripslashes($_POST['scrollbox_trigger_point'])) : 20);
		$scrollbox_hide_duration = (isset($_POST['scrollbox_hide_duration']) ? esc_html(stripslashes($_POST['scrollbox_hide_duration'])) : 0);
		$scrollbox_auto_hide = (isset($_POST['scrollbox_auto_hide']) ? esc_html(stripslashes($_POST['scrollbox_auto_hide'])) :1);
		$hide_mobile = (isset($_POST['hide_mobile']) ? esc_html(stripslashes($_POST['hide_mobile'])) : 0);
		$scrollbox_closing = (isset($_POST['scrollbox_closing']) ? esc_html(stripslashes($_POST['scrollbox_closing'])) : 1);
		$scrollbox_minimize = (isset($_POST['scrollbox_minimize']) ? esc_html(stripslashes($_POST['scrollbox_minimize'])) : 1);
		$scrollbox_minimize_text = (isset($_POST['scrollbox_minimize_text']) ? esc_html(stripslashes($_POST['scrollbox_minimize_text'])) : '');
		
		$type = (isset($_POST['form_type']) ? esc_html(stripslashes($_POST['form_type'])) : 'embadded');
		$display_on = (isset($_POST['display_on']) ? esc_html(implode(',', $_POST['display_on'])) : '');
		$posts_include = (isset($_POST['posts_include']) ? esc_html(stripslashes($_POST['posts_include'])) : '');
		$pages_include = (isset($_POST['pages_include']) ? esc_html(stripslashes($_POST['pages_include'])) : '');
		$display_on_categories = (isset($_POST['display_on_categories']) ? esc_html(implode(',', $_POST['display_on_categories'])) : '');
		$current_categories = (isset($_POST['current_categories']) ? esc_html(stripslashes($_POST['current_categories'])) : '');
		$show_for_admin = (isset($_POST['show_for_admin']) ? esc_html(stripslashes($_POST['show_for_admin'])) : 0);

		$save = $wpdb->replace($wpdb->prefix . 'formmaker_display_options', array(
      'form_id' => $id,
			'type' => $type,
			'scrollbox_loading_delay' => $scrollbox_loading_delay,
			'popover_animate_effect' => $popover_animate_effect,
			'popover_loading_delay' => $popover_loading_delay,
			'popover_frequency' => $popover_frequency,
			'topbar_position' => $topbar_position,
			'topbar_remain_top' => $topbar_remain_top,
			'topbar_closing' => $topbar_closing,
			'topbar_hide_duration' => $topbar_hide_duration,
			'scrollbox_position' => $scrollbox_position,
			'scrollbox_trigger_point' => $scrollbox_trigger_point,
			'scrollbox_hide_duration' => $scrollbox_hide_duration,
			'scrollbox_auto_hide' => $scrollbox_auto_hide,
			'hide_mobile' => $hide_mobile,
			'scrollbox_closing' => $scrollbox_closing,
			'scrollbox_minimize' => $scrollbox_minimize,
			'scrollbox_minimize_text' => $scrollbox_minimize_text,
			'display_on' => $display_on,
			'posts_include' => $posts_include,
			'pages_include' => $pages_include,
			'display_on_categories' => $display_on_categories,
			'current_categories' => $current_categories,
			'show_for_admin' => $show_for_admin,
		));
		
		if ($save !== FALSE) {
			$save_in_backup = $wpdb->update($wpdb->prefix . 'formmaker_backup', array(
				'type' => $type
			), array('id' => $id));
			
			$save_in_form = $wpdb->update($wpdb->prefix . 'formmaker', array(
				'type' => $type
			), array('id' => $id));
			return 8;
		}
		else {
			return 2;
		}	
	}

  public function save_as_copy() {
    $message = $this->save_db_as_copy();
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function save() {
    $message = $this->save_db();
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function apply() {
    $message = $this->save_db();
    global $wpdb;
    $id = (int) $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
    $current_id = (int)WDW_FM_Library::get('current_id', $id);
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function save_db() {
    global $wpdb;
    $javascript = "// Occurs before the form is loaded
function before_load() {	
}	
// Occurs just before submitting  the form
function before_submit() {
	// IMPORTANT! If you want to interrupt (stop) the submitting of the form, this function should return true. You don't need to return any value if you don't want to stop the submission.
}	
// Occurs just before resetting the form
function before_reset() {	
}";
    $id = (int)WDW_FM_Library::get('current_id', 0);
    $title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
    $theme = (isset($_POST['theme']) ? esc_html(stripslashes($_POST['theme'])) : $wpdb->get_var("SELECT id FROM " . $wpdb->prefix . "formmaker_themes WHERE `default`='1'"));
    $form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
    $sortable = (isset($_POST['sortable']) ? stripslashes($_POST['sortable']) : 1);
    $counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
    $label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
    $pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
    $show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
    $show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes($_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ? esc_html(stripslashes($_POST['private_key'])) : '');
    $recaptcha_theme = (isset($_POST['recaptcha_theme']) ? esc_html(stripslashes($_POST['recaptcha_theme'])) : '');
    $label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
    $form_fields = (isset($_POST['form_fields']) ? stripslashes($_POST['form_fields']) : '');
        
    $header_title = (isset($_POST['header_title']) ? esc_html(stripslashes($_POST['header_title'])) : '');
    $header_description = (isset($_POST['header_description']) ? htmlspecialchars_decode(esc_html(stripslashes($_POST['header_description']))) : '');
    $header_image_url = (isset($_POST['header_image_url']) ? esc_html(stripslashes($_POST['header_image_url'])) : '');
    $header_image_animation = (isset($_POST['header_image_animation']) ? esc_html(stripslashes($_POST['header_image_animation'])) : '');
    $header_hide_image = (isset($_POST['header_hide_image']) ? esc_html(stripslashes($_POST['header_hide_image'])) : 0);
    $type = (isset($_POST['form_type']) ? esc_html(stripslashes($_POST['form_type'])) : 'embedded');
		$scrollbox_minimize_text = $header_title ? $header_title : 'The form is minimized.';

    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
        'title' => $title,
        'theme' => $theme,
        'form_front' => $form_front,
        'sortable' => $sortable,
        'counter' => $counter,
        'label_order' => $label_order,
        'label_order_current' => $label_order_current,
        'pagination' => $pagination,
        'show_title' => $show_title,
        'show_numbers' => $show_numbers,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'recaptcha_theme' => $recaptcha_theme,
        'form_fields' => $form_fields,
        'header_title' => $header_title,
        'header_description' => $header_description,
        'header_image_url' => $header_image_url,
        'header_image_animation' => $header_image_animation,
        'header_hide_image' => $header_hide_image,
      ), array('id' => $id));
    }
    else {
      $save = $wpdb->insert($wpdb->prefix . 'formmaker', array(
        'title' => $title,
        'type' => $type,
        'mail' => '',
        'form_front' => $form_front,
        'theme' => $theme,
        'counter' => $counter,
        'label_order' => $label_order,
        'pagination' => $pagination,
        'show_title' => $show_title,
        'show_numbers' => $show_numbers,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'recaptcha_theme' => $recaptcha_theme,
        'javascript' => $javascript,
        'submit_text' => '',
        'url' => '',
        'article_id' => 0,
        'submit_text_type' => 1,
        'script_mail' => '%all%',
        'script_mail_user' => '%all%',
        'label_order_current' => $label_order_current,
        'tax' => 0,
        'payment_currency' => '',
        'paypal_email' => '',
        'checkout_mode' => 'testmode',
        'paypal_mode' => 0,
        'published' => 1,
        'form_fields' => $form_fields,
        'savedb' => 1,
        'sendemail' => 1,
        'requiredmark' => '*',
        'from_mail' => '',
        'from_name' => '',
        'reply_to' => '',
        'send_to' => '',
        'autogen_layout' => 1,
        'custom_front' => '',
        'mail_from_user' => '',
        'mail_from_name_user' => '',
        'reply_to_user' => '',
        'condition' => '',
        'mail_cc' => '',
        'mail_cc_user' => '',
        'mail_bcc' => '',
        'mail_bcc_user' => '',
        'mail_subject' => '',
        'mail_subject_user' => '',
        'mail_mode' => 1,
        'mail_mode_user' => 1,
        'mail_attachment' => 1,
        'mail_attachment_user' => 1,
        'sortable' => $sortable,
        'user_id_wd' => 'administrator,',
        'frontend_submit_fields' => '',
        'frontend_submit_stat_fields' => '',
        'save_uploads' => 1,

        'header_title' => $header_title,
        'header_description' => $header_description,
        'header_image_url' => $header_image_url,
        'header_image_animation' => $header_image_animation,
        'header_hide_image' => $header_hide_image,
      ));
      $id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
      // $_POST['current_id'] = $id;
      $save_display_options = $wpdb->insert($wpdb->prefix . 'formmaker_display_options', array(	
				'form_id' => $id,
				'type' => $type,
				'scrollbox_loading_delay' => 0,
				'popover_animate_effect' => '',
				'popover_loading_delay' => 0,
				'popover_frequency' => 0,
				'topbar_position' => 1,
				'topbar_remain_top' => 1,
				'topbar_closing' => 1,
				'topbar_hide_duration' => 0,
				'scrollbox_position' => 1,
				'scrollbox_trigger_point' => 20,
				'scrollbox_hide_duration' => 0,
				'scrollbox_auto_hide' => 1,
				'hide_mobile' => 0,
				'scrollbox_closing' => 1,
				'scrollbox_minimize' => 1,
				'scrollbox_minimize_text' => $scrollbox_minimize_text,
				'display_on' => 'home,post,page',
				'posts_include' => '',
				'pages_include' => '',
				'display_on_categories' => '',
				'current_categories' => '',
				'show_for_admin' => 0,
			), array(
				'%d',
				'%s',
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			));
      $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
        'form_id' => $id,
        'views' => 0
        ), array(
          '%d',
          '%d'
      ));
    }
	
    $backup_id = (isset($_POST['backup_id']) ? (int)esc_html(stripslashes($_POST['backup_id'])) : '');
	
	if($backup_id)
	{
		$query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id > ".$backup_id." AND id = ".$id." ORDER BY backup_id ASC LIMIT 0 , 1 ";

		if($wpdb->get_var($query))
		{
			$query = "DELETE FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id > ".$backup_id." AND id = ".$id;
			$wpdb->query($query);
		}

		$row = $wpdb->get_row($wpdb->prepare("SELECT form_fields, form_front FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id = '%d'", $backup_id));

		if($row->form_fields==$form_fields and $row->form_front==$form_front)
		{
			  $save = $wpdb->update($wpdb->prefix . 'formmaker_backup', array(
				'cur' => 1,
				'title' => $title,
				'theme' => $theme,
				'form_front' => $form_front,
				'sortable' => $sortable,
				'counter' => $counter,
				'label_order' => $label_order,
				'label_order_current' => $label_order_current,
				'pagination' => $pagination,
				'show_title' => $show_title,
				'show_numbers' => $show_numbers,
				'public_key' => $public_key,
				'private_key' => $private_key,
				'recaptcha_theme' => $recaptcha_theme,
				'form_fields' => $form_fields,
        'header_title' => $header_title,
        'header_description' => $header_description,
        'header_image_url' => $header_image_url,
        'header_image_animation' => $header_image_animation,
        'header_hide_image' => $header_hide_image,
			  ), array('backup_id' => $backup_id));
			
		
			if ($save !== FALSE) {
			  return 1;
			}
			else {
			  return 2;
			}
		}
	}
	
	$wpdb->query("UPDATE ".$wpdb->prefix."formmaker_backup SET cur=0 WHERE id=".$id ); 

	$save = $wpdb->insert($wpdb->prefix . 'formmaker_backup', array(
        'cur' => 1,
        'id' => $id,
        'title' => $title,
        'mail' => '',
        'form_front' => $form_front,
        'theme' => $theme,
        'counter' => $counter,
        'label_order' => $label_order,
        'pagination' => $pagination,
        'show_title' => $show_title,
        'show_numbers' => $show_numbers,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'recaptcha_theme' => $recaptcha_theme,
        'javascript' => $javascript,
        'submit_text' => '',
        'url' => '',
        'article_id' => 0,
        'submit_text_type' => 1,
        'script_mail' => '%all%',
        'script_mail_user' => '%all%',
        'label_order_current' => $label_order_current,
        'tax' => 0,
        'payment_currency' => '',
        'paypal_email' => '',
        'checkout_mode' => 'testmode',
        'paypal_mode' => 0,
        'published' => 1,
        'form_fields' => $form_fields,
        'savedb' => 1,
        'sendemail' => 1,
        'requiredmark' => '*',
        'from_mail' => '',
        'from_name' => '',
        'reply_to' => '',
        'send_to' => '',
        'autogen_layout' => 1,
        'custom_front' => '',
        'mail_from_user' => '',
        'mail_from_name_user' => '',
        'reply_to_user' => '',
        'condition' => '',
        'mail_cc' => '',
        'mail_cc_user' => '',
        'mail_bcc' => '',
        'mail_bcc_user' => '',
        'mail_subject' => '',
        'mail_subject_user' => '',
        'mail_mode' => 1,
        'mail_mode_user' => 1,
        'mail_attachment' => 1,
        'mail_attachment_user' => 1,
        'sortable' => $sortable,
        'user_id_wd' => 'administrator,',
        'frontend_submit_fields' => '',
        'frontend_submit_stat_fields' => '',
        'header_title' => $header_title,
        'header_description' => $header_description,
        'header_image_url' => $header_image_url,
        'header_image_animation' => $header_image_animation,
        'header_hide_image' => $header_hide_image,
      ), array(
        '%d',
        '%d',
		'%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
     ))	;
  
	$query = "SELECT count(backup_id) FROM ".$wpdb->prefix."formmaker_backup WHERE id = ".$id;
	$wpdb->get_var($query);
	if($wpdb->get_var($query)>10)
	{
		$query = "DELETE FROM ".$wpdb->prefix."formmaker_backup WHERE id = ".$id." ORDER BY backup_id ASC LIMIT 1 ";
		$wpdb->query($query);
	}

    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function save_db_as_copy() {
    global $wpdb;
    $id = (int)WDW_FM_Library::get('current_id', 0);
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
    require_once WD_FM_DIR . "/admin/models/FMModelManage_fm.php";
    $model = new FMModelManage_fm();
    $row_display = $model->get_display_options($id);
    $title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
    $theme = (isset($_POST['theme']) ? esc_html(stripslashes($_POST['theme'])) : 0);
    $form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
    $sortable = (isset($_POST['sortable']) ? stripslashes($_POST['sortable']) : 1);
    $counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
    $label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
    $label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
    $pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
    $show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
    $show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes($_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ? esc_html(stripslashes($_POST['private_key'])) : '');
    $recaptcha_theme = (isset($_POST['recaptcha_theme']) ? esc_html(stripslashes($_POST['recaptcha_theme'])) : '');
    $form_fields = (isset($_POST['form_fields']) ? stripslashes($_POST['form_fields']) : '');

    $save = $wpdb->insert($wpdb->prefix . 'formmaker', array(
      'title' => $title,
      'type' => $row->type,
      'mail' => $row->mail,
      'form_front' => $form_front,
      'theme' => $theme,
      'counter' => $counter,
      'label_order' => $label_order,
      'pagination' => $pagination,
      'show_title' => $show_title,
      'show_numbers' => $show_numbers,
      'public_key' => $public_key,
      'private_key' => $private_key,
      'recaptcha_theme' => $recaptcha_theme,
      'javascript' => $row->javascript,
      'submit_text' => $row->submit_text,
      'url' => $row->url,
      'article_id' => $row->article_id,
      'submit_text_type' => $row->submit_text_type,
      'script_mail' => $row->script_mail,
      'script_mail_user' => $row->script_mail_user,
      'label_order_current' => $label_order_current,
      'tax' => $row->tax,
      'payment_currency' => $row->payment_currency,
      'paypal_email' => $row->paypal_email,
      'checkout_mode' => $row->checkout_mode,
      'paypal_mode' => $row->paypal_mode,
      'published' => $row->published,
      'form_fields' => $form_fields,
      'savedb' => $row->savedb,
      'sendemail' => $row->sendemail,
      'requiredmark' => $row->requiredmark,
      'from_mail' => $row->from_mail,
      'from_name' => $row->from_name,
      'reply_to' => $row->reply_to,
      'send_to' => $row->send_to,
      'autogen_layout' => $row->autogen_layout,
      'custom_front' => $row->custom_front,
      'mail_from_user' => $row->mail_from_user,
      'mail_from_name_user' => $row->mail_from_name_user,
      'reply_to_user' => $row->reply_to_user,
      'condition' => $row->condition,
      'mail_cc' => $row->mail_cc,
      'mail_cc_user' => $row->mail_cc_user,
      'mail_bcc' => $row->mail_bcc,
      'mail_bcc_user' => $row->mail_bcc_user,
      'mail_subject' => $row->mail_subject,
      'mail_subject_user' => $row->mail_subject_user,
      'mail_mode' => $row->mail_mode,
      'mail_mode_user' => $row->mail_mode_user,
      'mail_attachment' => $row->mail_attachment,
      'mail_attachment_user' => $row->mail_attachment_user,
      'sortable' => $sortable,
      'user_id_wd' => $row->user_id_wd,
      'frontend_submit_fields' => $row->frontend_submit_fields,
      'frontend_submit_stat_fields' => $row->frontend_submit_stat_fields,
      'save_uploads' => $row->save_uploads,
      
      'header_title' => $row->header_title,
      'header_description' => $row->header_description,
      'header_image_url' => $row->header_image_url,
      'header_image_animation' => $row->header_image_animation,
      'header_hide_image' => $row->header_hide_image,
    ));
    $new_id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
    $save = $wpdb->insert($wpdb->prefix . 'formmaker_display_options', array(
			'form_id' => $new_id,
			'type' => $row_display->type,
			'scrollbox_loading_delay' => $row_display->scrollbox_loading_delay,
			'popover_animate_effect' => $row_display->popover_animate_effect,
			'popover_loading_delay' => $row_display->popover_loading_delay,
			'popover_frequency' => $row_display->popover_frequency,
			'topbar_position' => $row_display->topbar_position,
			'topbar_remain_top' => $row_display->topbar_remain_top,
			'topbar_closing' => $row_display->topbar_closing,
			'topbar_hide_duration' => $row_display->topbar_hide_duration,
			'scrollbox_position' => $row_display->scrollbox_position,
			'scrollbox_trigger_point' => $row_display->scrollbox_trigger_point,
			'scrollbox_hide_duration' => $row_display->scrollbox_hide_duration,
			'scrollbox_auto_hide' => $row_display->scrollbox_auto_hide,
			'hide_mobile' => $row_display->hide_mobile,
			'scrollbox_closing' => $row_display->scrollbox_closing,
			'scrollbox_minimize' => $row_display->scrollbox_minimize,
			'scrollbox_minimize_text' => $row_display->scrollbox_minimize_text,
			'display_on' => $row_display->display_on,
			'posts_include' => $row_display->posts_include,
			'pages_include' => $row_display->pages_include,
			'display_on_categories' => $row_display->display_on_categories,
			'current_categories' => $row_display->current_categories,
			'show_for_admin' => $row_display->show_for_admin,
		), array(
			'%d',
			'%s',
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d'
		));
    $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
      'form_id' => $new_id,
      'views' => 0
      ), array(
        '%d',
        '%d'
    ));
    if ($save !== FALSE) {
		$addons = array('WD_FM_EMAIL_COND' => 'Conditional Emails', 'WD_FM_PDF' => 'PDF Integration', 'WD_FM_SAVE_PROG' => 'Save Form Progress', 'WD_FM_CALCULATOR' => 'Calculator');
		$addons_array = array();
		foreach($addons as $addon => $addon_name) {	
			if (defined($addon) && is_plugin_active(constant($addon))) {
				$_GET['addon_task'] = 'save_as_copy';
				$_GET['form_id'] = $id;
				$_GET['form_id_new'] = $new_id;
				do_action($addon.'_init');
			}				
		}
		return 1;
    }
    else {
      return 2;
    }
  }

  public function delete($id) {
    global $wpdb;
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id);
    if ($wpdb->query($query)) {
      $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_views WHERE form_id="%d"', $id));
      $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id="%d"', $id));
      $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_sessions WHERE form_id="%d"', $id));
			$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_backup WHERE id="%d"', $id));
      $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_display_options WHERE form_id="%d"', $id));
      $addons = array('WD_FM_EMAIL_COND' => 'Conditional Emails', 'WD_FM_PDF' => 'PDF Integration', 'WD_FM_SAVE_PROG' => 'Save Form Progress', 'WD_FM_CALCULATOR' => 'Calculator');
      $addons_array = array();
      foreach($addons as $addon => $addon_name) {	
        if (defined($addon) && is_plugin_active(constant($addon))) {
          $_GET['addon_task'] = 'delete';
          $_GET['form_id'] = $id;
          do_action($addon.'_init');
        }				
      }
      $message = 3;
    }
    else {
      $message = 2;
    }
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $isDefault = FALSE;
    $form_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker');
    foreach ($form_ids_col as $form_id) {
      if (isset($_POST['check_' . $form_id])) {
        $flag = TRUE;
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $form_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_views WHERE form_id="%d"', $form_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id="%d"', $form_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_sessions WHERE form_id="%d"', $form_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_backup WHERE id="%d"', $form_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_display_options WHERE form_id="%d"', $form_id));
      }
    }
    if ($flag) {
      $message = 5;
    }
    else {
      $message = 6;
    }
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function fm_live_search() {
		$search_string = ! empty( $_POST['pp_live_search'] ) ? sanitize_text_field( $_POST['pp_live_search'] ) : '';
		$post_type = ! empty( $_POST['pp_post_type'] ) ? sanitize_text_field( $_POST['pp_post_type'] ) : 'any';
		$full_content = ! empty( $_POST['pp_full_content'] ) ? sanitize_text_field( $_POST['pp_full_content'] ) : 'true';

		$args['s'] = $search_string;

		$results = $this->fm_posts_query( $args, $post_type );
		/* if ('true' === $full_content) { */
			$output = '<ul class="pp_search_results">';
		/* } else {
			$output = '';
		} */

		if (empty($results)) {
			/* if ( 'true' === $full_content ) { */
				$output .= sprintf(
					'<li class="pp_no_res">%1$s</li>',
					esc_html__( 'No results found', 'fm-text' )
				);
			/* } */
		} else {
			foreach( $results as $single_post ) {
				$output .= sprintf(
					'<li data-post_id="%2$s">[%3$s] - %1$s</li>',
					esc_html( $single_post['title'] ),
					esc_attr( $single_post['id'] ),
					esc_html( $single_post['post_type'] )
				);
			}
		}

		/* if ( 'true' === $full_content ) { */
			$output .= '</ul>';
		/* } */

		die( $output );
	}
	
	public function fm_posts_query( $args = array(), $include_post_type = '' ) {
		if ( 'only_pages' === $include_post_type ) {
			$pt_names = array( 'page' );
		} elseif ( 'any' === $include_post_type || 'only_posts' === $include_post_type ) {
			$default_post_types = array( 'post', 'page' );
			$custom_post_types = get_post_types( array(
				'public'   => true,
				'_builtin' => false,
			) );

			$post_types = array_merge($default_post_types, $custom_post_types);
			$pt_names = array_values($post_types);

			if ( 'only_posts' === $include_post_type ) {
				unset($pt_names[1]);
			}
		} else {
			$pt_names = $include_post_type;
		}

		$query = array(
			'post_type' => $pt_names,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		if ( isset( $args['s'] ) ) {
			$query['s'] = $args['s'];
		}

		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		if ( ! $get_posts->post_count ) {
			return false;
		}

		$results = array();
		foreach ($posts as $post) {
			$results[] = array(
				'id' => (int) $post->ID,
				'title' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'post_type' => $post->post_type,
			);
		}

		wp_reset_postdata();

		return $results;
	}

  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}