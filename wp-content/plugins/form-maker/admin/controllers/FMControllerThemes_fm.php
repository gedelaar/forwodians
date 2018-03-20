<?php

class FMControllerThemes_fm {

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

  public function display() {
    require_once WD_FM_DIR . "/admin/models/FMModelThemes_fm.php";
    $model = new FMModelThemes_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewThemes_fm.php";
    $view = new FMViewThemes_fm($model);
    $view->display();
  }

  public function add() {
    require_once WD_FM_DIR . "/admin/models/FMModelThemes_fm.php";
    $model = new FMModelThemes_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewThemes_fm.php";
    $view = new FMViewThemes_fm($model);
    $view->edit(0, FALSE);
  }

  public function edit() {
    require_once WD_FM_DIR . "/admin/models/FMModelThemes_fm.php";
    $model = new FMModelThemes_fm();

    require_once WD_FM_DIR . "/admin/views/FMViewThemes_fm.php";
    $view = new FMViewThemes_fm($model);
    $id = (int)WDW_FM_Library::get('current_id', 0);
    $view->edit($id, FALSE);
  }

  public function save() {
    $message = $this->save_db();
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function apply() {
    $message = $this->save_db();
    global $wpdb;
    $id = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'formmaker_themes');
    $current_id = (int)WDW_FM_Library::get('current_id', $id);
    $page = WDW_FM_Library::get('page');
    $active_tab = WDW_FM_Library::get('active_tab');
		$pagination = WDW_FM_Library::get('pagination-type');
		$form_type = WDW_FM_Library::get('form_type');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message, 'active_tab' => $active_tab, 'pagination' => $pagination, 'form_type' => $form_type), admin_url('admin.php')));
  }

  public function copy_themes() {
		global $wpdb;
		$theme_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker_themes');
		foreach ($theme_ids_col as $theme_id) {
			if (isset($_POST['check_' . $theme_id])) {
				$theme = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'formmaker_themes where id=' . $theme_id);
				$title = $theme->title;
				$params = $theme->params;
				$save = $wpdb->insert($wpdb->prefix . 'formmaker_themes', 
					array(
						'title' => $title,                       
						'params' => $params,         
						'default' => 0
					), array(
						'%s',
						'%s',
						'%d'
					));
			}
		}
		
		if ($save !== FALSE) {
			$message = 1;
		}
		else {
			$message = 2;
		}
		
		$page = WDW_FM_Library::get('page');
		WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
	}

  public function save_as_copy() {
		$message = $this->save_db_as_copy();
		$page = WDW_FM_Library::get('page');
		WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
	}

  public function save_db() {
    global $wpdb;
    $id = (int) WDW_FM_Library::get('current_id', 0);
    $title = (isset($_POST['title']) ? esc_html(stripslashes( $_POST['title'])) : '');
    $version = 1;
    $params = (isset($_POST['params']) ? stripslashes(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $_POST['params'])) : '');
    $default = (isset($_POST['default']) ? esc_html(stripslashes( $_POST['default'])) : 0);
    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'formmaker_themes', array(
        'title' => $title,
        'params' => $params,
        'default' => $default,
      ), array('id' => $id));
      $version = $wpdb->get_var($wpdb->prepare("SELECT version FROM " . $wpdb->prefix . "formmaker_themes WHERE id=%d", $id));
    }
    else {
      $save = $wpdb->insert($wpdb->prefix . 'formmaker_themes', array(
        'title' => $title,                       
        'params' => $params,         
        'default' => $default,
      ), array(
        '%s',
        '%s',
        '%d',
      ));
      $id = $wpdb->insert_id;
    }
    if ($save !== FALSE) {
      require_once WD_FM_DIR . "/frontend/models/FMModelForm_maker.php";
      $model_frontend = new FMModelForm_maker();
      $form_theme = json_decode(html_entity_decode($params), true);
      $model_frontend->create_css($id, $form_theme, $version == 1, true);
      return 1;
    }
    else {
      return 2;
    }
  }

  public function save_db_as_copy() {
		global $wpdb;
		$id = (int) WDW_FM_Library::get('current_id', 0);
		$title = isset($_POST['title']) ? esc_html(stripslashes( $_POST['title'])) : '';
		$params = isset($_POST['params']) ? stripslashes(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $_POST['params'])) : '';

		$save = $wpdb->insert($wpdb->prefix . 'formmaker_themes', 
			array(
				'title' => $title,                       
				'params' => $params,         
				'default' => 0
			), array(
				'%s',
				'%s',
				'%d'
			));

		if ($save !== FALSE) {
			return 1;
		}
		else {
			return 2;
		}
	}

  public function delete($id) {
    global $wpdb;
    $isDefault = $wpdb->get_var($wpdb->prepare('SELECT `default` FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $id));
    if ($isDefault) {
      $message = 4;
    }
    else {
      $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $id);
      if ($wpdb->query($query)) {
        $message = 3;
      }
      else {
        $message = 2;
      }
    }
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $isDefault = FALSE;
    $theme_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker_themes');
    foreach ($theme_ids_col as $theme_id) {
      if (isset($_POST['check_' . $theme_id])) {
        $isDefault = $wpdb->get_var($wpdb->prepare('SELECT `default` FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $theme_id));
        if ($isDefault) {
          $message = 4;
        }
        else {
          $flag = TRUE;
          $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $theme_id));
        }
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

  public function setdefault($id) {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'formmaker_themes', array('default' => 0), array('default' => 1));
    $save = $wpdb->update($wpdb->prefix . 'formmaker_themes', array('default' => 1), array('id' => $id));
    if ($save !== FALSE) {
      $message = 7;
    }
    else {
      $message = 2;
    }
    $page = WDW_FM_Library::get('page');
    WDW_FM_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

}
