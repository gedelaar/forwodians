<?php

class FMModelUninstall_fm {

  public function delete_db_tables() {
    global $wpdb;
    $email_verification_post_id = $wpdb->get_var( "SELECT mail_verification_post_id  FROM " . $wpdb->prefix . "formmaker WHERE mail_verification_post_id != 0");
    delete_option("wd_form_maker_version");
    delete_option('formmaker_cureent_version');
    delete_option('contact_form_themes');
    delete_option('contact_form_forms');
    delete_option('form_maker_pro_active');
    delete_option('fm_emailverification');
    delete_option('fm_admin_notice');
    delete_option('fm_settings');
    wp_delete_post($email_verification_post_id);
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_submits");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_views");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_themes");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_sessions");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_blocked");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_query");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_backup");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_mailchimp");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_reg");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_post_gen_options");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_email_conditions");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_dbox_int");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_pdf_options");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_pdf");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_pushover");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_stripe");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_save_options");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_saved_entries");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_saved_attributes");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_calculator");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_gdrive_int");
    $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_display_options");
  }

}
