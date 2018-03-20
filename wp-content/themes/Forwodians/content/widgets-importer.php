<?php

function theme_add_widget($sidebar, $block_type, $name) {
	$sidebar = _theme_convert_sidebars_name($sidebar);

	// gets the list of current sidebars and widgets from blog options
	$wp_sidebars = get_option('sidebars_widgets');

	if (!isset($wp_sidebars[$sidebar])) {
        $wp_sidebars[$sidebar] = array();
	}

	if (($block_type == 'widget') && preg_match('/^\D+[^\d\-]/', $name, $matches) && isset($matches[0])) {
		$type = $matches[0];
	} elseif ($block_type == 'menuWidget') {
		$type = 'vmenuwidget';
	} else {
		$type = 'text';
	}

	if ($type == 'archive') {
		$type = 'archives';
	}
	if ($type == 'login') {
		$type = 'loginwidget';
	}
	// returns false if widget type is not allowed
	$theme_allowed_widgets = array(
		// default Artisteer widgets
		'archives',
		'categories',
		'search',
		'text',
		'vmenuwidget',
		'loginwidget',
	);
	if (!$type || !in_array($type, $theme_allowed_widgets)) {
		$type = 'text';
	}

	// gets the widget data
	$wp_widget = get_option('widget_' . $type);
	$wp_widget = $wp_widget ? $wp_widget : array();

	// new widget id is always unique
	$new_widget_id = 0;
	foreach ($wp_widget as $widget_id => $widget) {
		if (is_int($widget_id))
			$new_widget_id = max($new_widget_id, $widget_id);
	}
	$new_widget_id++;
	$new_widget_name = $type . '-' . $new_widget_id;

	// gets widgets from the selected sidebar
	$wp_sidebar_widgets = $wp_sidebars[$sidebar];

	$wp_sidebar_widgets[] = $new_widget_name;

	// puts new sidebar widgets in the list of sidebars 
	$wp_sidebars[$sidebar] = $wp_sidebar_widgets;

	update_option('sidebars_widgets', $wp_sidebars);

	// creates new widget
	$wp_widget[$new_widget_id] = array();

	// default Artisteer widgets
	if ($type == 'text') {
		$wp_widget[$new_widget_id]['text'] = '';
		$wp_widget[$new_widget_id]['filter'] = false;
	}
	if ($type == 'vmenuwidget') {
		$wp_widget[$new_widget_id]['source'] = 'Pages';
		$wp_widget[$new_widget_id]['nav_menu'] = 0;
	}
	if ($type == 'archives') {
		$wp_widget[$new_widget_id]['count'] = 0;
		$wp_widget[$new_widget_id]['dropdown'] = 0;
	}
	if ($type == 'categories') {
		$wp_widget[$new_widget_id]['count'] = '0';
		$wp_widget[$new_widget_id]['dropdown'] = '0';
		$wp_widget[$new_widget_id]['hierarchical'] = '0';
	}

	$wp_widget[$new_widget_id]['title'] = '';

	if (!isset($wp_widget['_multiwidget'])) {
		$wp_widget['_multiwidget'] = 1;
	}

	update_option('widget_' . $type, $wp_widget);
	return $new_widget_name;
}

function theme_update_widget($widget_id, $title, $content = null, $args = null) {

	if (!preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) || !isset($matches[1]) || !isset($matches[2])) {
		return false;
	}

	$type = $matches[1];
	$id = $matches[2];

	$wp_widget = get_option('widget_' . $type);

	if (!$wp_widget || !isset($wp_widget[$id])) {
		return false;
	}

	if (isset($title) && (strlen($title) > 0)) {
		$wp_widget[$id]['title'] = $title;
	}

	if (isset($content) && (strlen($content) > 0) && ($type == 'text')) {
		$wp_widget[$id]['text'] = $content;
	}

	if (isset($args) && is_array($args)) {
		$wp_widget[$id] = array_merge($wp_widget[$id], $args);
	}

	if (!isset($wp_widget['_multiwidget'])) {
		$wp_widget['_multiwidget'] = 1;
	}

	update_option('widget_' . $type, $wp_widget);
}

function theme_delete_widget($widget_id, $force_delete = false) {
	$widget_exist = false;
	$wp_sidebars = get_option('sidebars_widgets');
	foreach ($wp_sidebars as $sidebar_id => $widgets) {
		if (is_array($widgets)) {
			$new_widgets = array();
			foreach ($widgets as $widget) {
				if ($widget != $widget_id) {
					$new_widgets[] = $widget;
					$widget_exist = true;
				}
			}
			$wp_sidebars[$sidebar_id] = $new_widgets;
		}
	}
	if (!$force_delete && $widget_exist) {
		if (!is_array($wp_sidebars['wp_inactive_widgets'])) {
			$wp_sidebars['wp_inactive_widgets'] = array();
		}
		$wp_sidebars['wp_inactive_widgets'][] = $widget_id;
	}
	update_option('sidebars_widgets', $wp_sidebars);

	if ($force_delete && $widget_exist) {
		if (!preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) || !isset($matches[1]) || !isset($matches[2])) {
			return false;
		}
		$type = $matches[1];
		$id = $matches[2];
		$wp_widget = get_option('widget_' . $type);
		if (!$wp_widget || !isset($wp_widget[$id])) {
			return false;
		}
		unset($wp_widget[$id]);
		if (!isset($wp_widget['_multiwidget'])) {
			$wp_widget['_multiwidget'] = 1;
		}
		update_option('widget_' . $type, $wp_widget);
	}
}

function theme_deactivate_all_widgets() {
	$wp_sidebars = get_option('sidebars_widgets');
	if (!is_array($wp_sidebars['wp_inactive_widgets'])) {
		$wp_sidebars['wp_inactive_widgets'] = array();
	}
	foreach ($wp_sidebars as $sidebar_id => $widgets) {
		if('wp_inactive_widgets' != $sidebar_id && is_array($widgets)) {
			$wp_sidebars['wp_inactive_widgets'] = array_merge($wp_sidebars['wp_inactive_widgets'], $widgets);
			$wp_sidebars[$sidebar_id] = array();
		}
	}
	update_option('sidebars_widgets', $wp_sidebars);
}

function _theme_convert_sidebars_name($sidebar) {
	return _theme_convert_sidebars($sidebar);
}

function _theme_convert_sidebars($sidebar_key, $search_by_value = false) {
	$sidebars = array(
		'sidebar1' => 'primary-widget-area',
		'sidebar2' => 'secondary-widget-area',
		'content-before' => 'first-top-widget-area',
		'content-after' => 'first-bottom-widget-area',
		'inactive' => 'wp_inactive_widgets'
	);

	if ($search_by_value) {
		$sidebars = array_flip($sidebars);
	}
	$sidebar_name = isset($sidebars[$sidebar_key]) ? $sidebars[$sidebar_key] : $sidebar_key;

	return $sidebar_name;
}

?>