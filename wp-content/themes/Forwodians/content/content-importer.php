<?php
locate_template(array('/content/widgets-importer.php'), true);
locate_template(array('/content/content-parser.php'), true);

if (!is_admin() || !current_user_can('administrator')) {
	return;
}

class Theme_Content_Import {

	private $upload_dir;
	private $sample_data = array(
		'posts' => array(),
		'pages' => array(),
		'widgets' => array(),
		'vmenu' => array(),
	);
	private $vmenu_id = null;
	private $inserted_name = array();
	private $inserted_ids = array();
	private $inserted_content = array();

	public function go() {
		update_option('blogname', '');
		update_option('blogdescription', '');
		$this->upload_dir = wp_upload_dir();
		$parser = new Theme_Content_Parser(get_template_directory() . '/content/content.xml');

		// parses content.xml
		$pages_info = $parser->get_pages();
		$posts_info = $parser->get_posts();
		$sidebars_info = $parser->get_sidebars();
		$collages_info = $parser->get_collages();

		// add content to the theme
		$this->move_images();
		if ($pages_info) {
			$this->insert_pages($pages_info);
		}
		if ($posts_info) {
			$this->insert_posts($posts_info);
		}
		$this->update_collages_meta($collages_info);
		$this->update_links_url();
		if ($sidebars_info) {
			theme_deactivate_all_widgets();
			$this->insert_sidebars($sidebars_info, $collages_info);
		}
		update_option('theme_sample_data', $this->sample_data);
	}

	private function insert_pages($pages_info) {
		global $theme_default_meta_options;
		$posts_page_id = 0;
		$front_page_id = 0;
		$menu_order = 0;
		$vmenu = array();
		$post_time = time() - count($pages_info);
		foreach ($pages_info as $num => $page) {
			$parent_id = $this->get_page_parent_id($page['path'], $this->inserted_ids);
			$page_content = wp_slash($this->replace_image_sources($page['content']));
			$post_date = gmdate( 'Y-m-d H:i:s', ($post_time++) + get_option( 'gmt_offset' ) * 3600 );
			$page_attributes = array(
				'post_type' => 'page',
				'post_name' => $page['name'],
				'post_title' => $page['title'],
				'post_content' => $page_content,
				'post_status' => 'publish',
				'post_parent' => $parent_id,
				'menu_order' => ++$menu_order,
				'comment_status' => 'closed',
				'post_date' => $post_date,
				'post_date_gmt' => get_gmt_from_date($post_date),
			);
			$_POST = $theme_default_meta_options;
			$_POST['post_type'] = 'page';
			$_POST['theme_meta_noncename'] = wp_create_nonce('theme_meta_options');
			$_POST['theme_show_in_menu'] = $page['show_in_menu'];
			$_POST['theme_title_in_menu'] = $page['title_in_menu'];
			$_POST['theme_show_page_title'] = $page['show_page_title'];
			$id = wp_insert_post($page_attributes);
			$_POST = array();
			$this->inserted_ids[$page['path']] = $id;
			$this->inserted_content[$id] = $page_content;

			$pageHead = $page['pageHead'];
			$pageHead = preg_replace('/\.art-postcontent-\d+/', '.post-' . $id, $pageHead);
			$pageHead = $this->replace_image_sources($pageHead);
			if (!empty($pageHead)) { 
				add_post_meta($id, 'theme_head', $pageHead);
			}
      
			$pageTitle = $page['pageTitle'];
			if (!empty($pageTitle)) { 
				add_post_meta($id, 'page_title', $pageTitle);	
			}
      
			$pageKeywords = $page['pageKeywords'];
			if (!empty($pageKeywords)) { 
				add_post_meta($id, 'page_keywords', $pageKeywords);	
			}
      
			$pageDescription = $page['pageDescription'];
			if (!empty($pageDescription)) { 
				add_post_meta($id, 'page_description', $pageDescription);	
			}	
      
			$pageMetaTags = $page['pageMetaTags'];
			if (!empty($pageMetaTags)) {
				add_post_meta($id, 'page_metaTags', $pageMetaTags);	
			}
      
			$pageCustomHeadHtml = $page['pageCustomHeadHtml'];
			if (!empty($pageCustomHeadHtml)) {
				add_post_meta($id, 'page_customHeadHtml', $pageCustomHeadHtml);
			}
      
			$pageHasCustomHeadHtml = $page['pageHasCustomHeadHtml'];
			if (!empty($pageHasCustomHeadHtml)) {
				add_post_meta($id, 'page_hasCustomHeadHtml', $pageHasCustomHeadHtml);
			}
						
			theme_set_meta_option($id, 'theme_use_wpautop', '0');
						
			$this->sample_data['pages'][] = $id;
			$custom_url = null;
			if (isset($page['posts_page']) && $page['posts_page'] && $num === 0) {
				$custom_url = get_home_url();
			}
			if ($page['show_in_vmenu']) {
				$this->add_to_menu('vmenu',  $id, $parent_id, $page['title_in_menu'], $custom_url);
			}
			if ($page['show_in_menu']) {
				$this->add_to_menu('hmenu',  $id, $parent_id, $page['title_in_menu'], $custom_url);
			}
			if ($num === 0) {
				$front_page_id = $id;
			}
			if (isset($page['posts_page']) && $page['posts_page']) {
				$posts_page_id = $id;
			}
		}
		// Set Front Page and Posts Page
		if ($front_page_id !== 0 && $posts_page_id != $front_page_id) {
			update_option('show_on_front', 'page');
			update_option('page_on_front', $front_page_id);
		} else {
			update_option('show_on_front', 'post');
		}
		update_option('page_for_posts', $posts_page_id);
		$this->create_menu('vmenu');
		$this->create_menu('hmenu');
		
	}
	
	private $menu = array(
		'hmenu' => array(),
		'vmenu' => array()
	);
	private function add_to_menu($type, $id, $parent_id, $title, $url) {
		if ($url == null) {
			$this->menu[$type][] = array(
				'menu-item-object-id' => $id,
				'menu-item-object' => 'page',
				'menu-item-parent-id' => $parent_id,
				'menu-item-type' => 'post_type',
				'menu-item-title' => $title,
				'menu-item-status' => 'publish',
			);
		} else {
			$this->menu[$type][] = array(
				'menu-item-object-id' => $id,
				'menu-item-object' => 'custom',
				'menu-item-parent-id' => $parent_id,
				'menu-item-type' => 'custom',
				'menu-item-title' => $title,
				'menu-item-status' => 'publish',
				'menu-item-url' => $url
			);
		}
	}
	
	private function create_menu($type) {
		$menu_name = $type == 'vmenu' ? 'Sample VMenu' : 'Sample HMenu';
		for ($i = 0;; $i++) {
			$_possible_existing = get_term_by('name', $menu_name . ($i ? ' #' . $i : ''), 'nav_menu');
			if (!$_possible_existing || is_wp_error($_possible_existing) || !isset($_possible_existing->term_id))
				break;
		}
		$page_to_menu_ids = array();
		$nav_menu_selected_id = wp_update_nav_menu_object(0, array('menu-name' => $menu_name . ($i ? ' #' . $i : '')));
		if (!is_wp_error($nav_menu_selected_id)) {
			$this->sample_data['menu'][] = $nav_menu_selected_id;
			foreach ($this->menu[$type] as $menu_item_data) {
				if (0 != $menu_item_data['menu-item-parent-id']) {
					if (isset($page_to_menu_ids[$menu_item_data['menu-item-parent-id']])) {
						$menu_item_data['menu-item-parent-id'] = $page_to_menu_ids[$menu_item_data['menu-item-parent-id']];
					} else {
						$menu_item_data['menu-item-parent-id'] = 0;
					}
				}
				$menu_item_id = wp_update_nav_menu_item($nav_menu_selected_id, 0, $menu_item_data);
				$page_to_menu_ids[$menu_item_data['menu-item-object-id']] = $menu_item_id;
			}
			$this->{$type . '_id'} = $nav_menu_selected_id;
		}
		
		if('hmenu' == $type) {
			$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
			$nav_menu_locations['primary-menu'] = $nav_menu_selected_id;
			set_theme_mod( 'nav_menu_locations', $nav_menu_locations );
		}
	}

	private function get_page_parent_id($page_path, $pages_id) {
		$page_path = substr($page_path, 0, strrpos($page_path, '/'));
		if ($page_path !== '') {
			return $pages_id[$page_path];
		}
		return 0;
	}

	private function insert_posts($posts_info) {
		$post_time = time() - count($posts_info);
		foreach ($posts_info as $post) {
			$post_content = $this->replace_image_sources($post['content']);
			$post_content = wp_slash(str_replace('<!--CUT-->', '<!--more-->', $post_content));
			$post_status = $post['status'];
			if ($post_status == 'published') {
				$post_status = 'publish';
			}

			$post_date = gmdate( 'Y-m-d H:i:s', ($post_time++) + get_option( 'gmt_offset' ) * 3600 );
			$post_attributes = array(
				'post_name' => $post['name'],
				'post_title' => $post['title'],
				'post_content' => $post_content,
				'post_status' => $post_status,
				'comment_status' => 'closed',
				'post_date' => $post_date,
				'post_date_gmt' => get_gmt_from_date($post_date),
			);
			$_POST['post_type'] = 'post';
			$id = wp_insert_post($post_attributes);
			$this->inserted_ids[$post['path']] = $id;
			$this->inserted_content[$id] = $post_content;
			$postHead = $post['pageHead'];
			$postHead = preg_replace('/\.art-postcontent-\d+/', '.post-' . $id, $postHead);
			$postHead = $this->replace_image_sources($postHead);
			add_post_meta($id, 'theme_head', $postHead);
			theme_set_meta_option($id, 'theme_use_wpautop', '0');
			$this->sample_data['posts'][] = $id;
		}
	}
	
	private function update_collages_meta($collages_info) {
		foreach ($this->inserted_content as $id => $content) {
			$collages = array();
			preg_match_all('/\{([\w_]+)\}/', $content, $matches);
			foreach($matches[1] as $collage_id) {
				if(isset($collages_info[$collage_id])) {
					$collages[$collage_id] = $collages_info[$collage_id];
				}
			}
			if(!empty($collages)) {
				foreach($collages as $collage_id => $collage_content) {
					$content = str_replace('{' . $collage_id . '}' , '[collage id="' . $collage_id . '"]', $content);
				}
				add_post_meta($id, 'theme_collages', $collages);
				wp_update_post(array(
					'ID' => $id,
					'post_content' => $content
				));
				$this->inserted_content[$id] = $content;
			}
		}
	}
	
	private function update_links_url() {
		foreach ($this->inserted_content as $id => $content) {
			$old_content = $content;
			$content = $this->replace_posts_sources($content);
			if($old_content != $content) {
				wp_update_post(array(
					'ID' => $id,
					'post_content' => $content
				));
				$this->inserted_content[$id] = $content;
			}
		}
	}
	
	private function replace_posts_sources($content) {
		$content = preg_replace_callback('/(href=)([\'"])(?!https?:\/\/)(.*?)\2()/', array($this, 'real_posts_sources'), $content);
		return $content;
	}

	private function real_posts_sources($match) {
		list($str, $start, $quote, $filename, $end) = $match;
		if(isset($this->inserted_ids[$filename])) {
			$id = $this->inserted_ids[$filename];
			return $start . $quote . get_permalink($this->inserted_ids[$filename])  . $quote . $end;
		} else {
			return $start . $quote . $filename . $quote . $end;
		}
	}

	private function insert_sidebars($sidebars_info, $collages_info) {
		foreach ($sidebars_info as $sidebar) {
			foreach ($sidebar['blocks'] as $block) {
				$content = $block['content'];
				if (isset($content)) {
					$content = $this->replace_image_sources($content);
					$content = $this->replace_posts_sources($content);
					$collages = array();
                    preg_match_all('/\{([\w_]+)\}/', $content, $matches);
                    foreach($matches[1] as $collage_id) {
                        if(isset($collages_info[$collage_id])) {
                            $collages[$collage_id] = $collages_info[$collage_id];
                        }
                    }
                    if(!empty($collages)) {
                        foreach($collages as $collage_id => $collage_content) {
                            $content = str_replace('{' . $collage_id . '}' , $collage_content, $content);
                        }
                    }
				}

				$widget_id = theme_add_widget($sidebar['name'], $block['type'], $block['name']);
				if ('menuWidget' == $block['type'] && null != $this->vmenu_id) {
					$args = array(
						'source' => 'Custom Menu',
						'nav_menu' => $this->vmenu_id
					);
					theme_update_widget($widget_id, $block['title'], $content, $args);
				} else {
					theme_update_widget($widget_id, $block['title'], $content);
				}
				if(!empty($block['pageHead'])) {
        			$theme_widget_styling = $this->replace_image_sources($block['pageHead']);
					theme_set_widget_meta_option($widget_id, 'theme_widget_styling', $theme_widget_styling);
				}
				$this->sample_data['widgets'][] = $widget_id;
			}
		}
	}

	private function replace_image_sources($content) {
		$content = preg_replace_callback('/(src=)([\'"])(?:\.?[\/\\\]?images[\/\\\]?)?(?!https?:\/\/)(.*?)\2()/', array($this, 'real_sources'), $content);
		$content = preg_replace_callback('/(url\()([\'"])(?:\.?[\/\\\]?images[\/\\\]?)?(?!https?:\/\/)(.*?)\2(\))/', array($this, 'real_sources'), $content);
		return $content;
	}

	private function real_sources($match) {
		list($str, $start, $quote, $filename, $end) = $match;
		if (isset($this->inserted_name[rawurldecode($filename)])) {
			return $start . $quote . $this->inserted_name[rawurldecode($filename)] . $quote . $end;
		} else {
			return $start . $quote . $this->upload_dir['url'] . '/' . $filename . $quote . $end;
		}
	}

	private function move_images() {
		$images_dir = get_template_directory() . '/content/images';

		if (!is_dir($images_dir)) {
			return false;
		}

		$op_dir = opendir($images_dir);
		while (true) {
			$file = readdir($op_dir);
			if ($file === false) {
				break;
			}
			if (!is_file($images_dir . '/' . $file)) {
				continue;
			}

			// paths to the source and destination files
			$image_source = $images_dir . '/' . $file;
			$image_dest = $this->upload_dir['path'] . '/' . $file;
			if (file_exists($image_dest) && md5_file($image_source) == md5_file($image_dest)) {
				$this->inserted_name[$file] = $this->upload_dir['url'] . '/' . $file;
				continue;
			}
			$new_file = wp_unique_filename($this->upload_dir['path'], $file);
			$image_dest = $this->upload_dir['path'] . '/' . $new_file;

			// copies image to image destination defined by $image_dest
			if (!copy($image_source, $image_dest)) {
				continue;
			}

			// generates  wp attachment
			$wp_filetype = wp_check_filetype(basename($image_source), null);
			$attachment = array(
				'guid' => $this->upload_dir['url'] . '/' . $new_file,
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
				'post_content' => '',
			);
			$attach_id = wp_insert_attachment($attachment, $image_dest);
			$attach_data = wp_generate_attachment_metadata($attach_id, $image_dest);
			wp_update_attachment_metadata($attach_id, $attach_data);
			$this->inserted_name[$file] = wp_get_attachment_url($attach_id);
		}
		return true;
	}

}

if (!function_exists('wp_generate_attachment_metadata')) {
	include( ABSPATH . 'wp-admin/includes/image.php' );
}

add_action('init', 'theme_check_content_import', 199);
function theme_check_content_import() {
    if (!file_exists(get_template_directory() . '/content/content.xml')) return;
	if (!get_option('theme_content_import')) {
		add_action('admin_head', 'theme_notice_style');
		add_action('admin_notices', 'theme_content_import_notice');
		add_action('wp_ajax_theme_content_hide_notice', 'theme_content_hide_notice');
	}
	add_action('theme_options', 'theme_options_import_notice');
	add_action('wp_ajax_theme_content_start_import', 'theme_content_start_import');
    add_action('wp_ajax_theme_content_start_import_without_cleanup', 'theme_content_start_import_without_cleanup');
}

function theme_options_import_notice() {
    ?>
    <h3><?php echo __('Do you want to import Content?', THEME_NS); ?></h3>
    <p id="theme-options-content-import-notice">
        <button class="button start-import-without-cleanup"><?php echo __('Add Content', THEME_NS); ?></button>
        <button class="button start-import"><?php echo __('Replace imported Content', THEME_NS); ?></button>
    </p>
    <script>
			jQuery(document).ready(function ($) {
				$('#theme-options-content-import-notice button.button').unbind("click").click(function() {
				
					var command = $(this).clone().removeClass('button').attr('class').replace(/-/g,'_');
					this.disabled = true
					$(this).prepend('<image style="height:50%;margin-right:5px" src="<?php echo get_bloginfo('template_url', 'display') . '/images/preloader-01.gif'; ?>" />');
					var that = this;
					$.ajax({
						url: '<?php echo admin_url("admin-ajax.php"); ?>',
						type: 'GET',
						context: this,
						data: ({
							action: 'theme_content_' + command,
							_ajax_nonce: '<?php echo wp_create_nonce('theme_content_importer'); ?>'
						}),
						success: function(data) {
							$("img", that).remove();
							that.disabled = false;
							
						},
						error: function(data) {
							$("img", that).remove();
							that.disabled = false;
						}
					});
				});
			});
	</script>
    <?php
}

function theme_notice_style() {
	echo '<style>' . PHP_EOL .
	'#content-import-notice p {' . PHP_EOL .
	'	font-family: "HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",sans-serif;' . PHP_EOL .
	'	font-size: 20px;' . PHP_EOL .
	'	font-weight: normal;' . PHP_EOL .
	'	line-height: 1.6em;' . PHP_EOL .
	'	margin-top: 8px;' . PHP_EOL .
	'	margin-bottom: 8px;' . PHP_EOL .
	'}' . PHP_EOL .
	'</style>';
}

function theme_content_import_notice() {
	?>
	<div id="content-import-notice" class="updated">
		<p>
			<?php echo __('Do you want to import Content?', THEME_NS); ?>
			&nbsp; &nbsp; &nbsp; &nbsp;
			<button class="button start-import-without-cleanup"><?php echo __('Add Content', THEME_NS); ?></button>
			<button class="button start-import"><?php echo __('Replace imported Content', THEME_NS); ?></button>
			<button class="button hide-notice"><?php echo __('Close', THEME_NS); ?></button>
		</p>
		<script>
			jQuery(document).ready(function ($) {
				$('#content-import-notice button.button').unbind("click").click(function() {
					var command = $(this).clone().removeClass('button').attr('class').replace(/-/g,'_');
					$.ajax({
						url: '<?php echo admin_url("admin-ajax.php"); ?>',
						type: 'GET',
						context: this,
						data: ({
							action: 'theme_content_' + command,
							_ajax_nonce: '<?php echo wp_create_nonce('theme_content_importer'); ?>'
						}),
						success: function(data) {
							$("#content-import-notice").remove();
						},
						error: function(data) {
							$("#content-import-notice").remove();
						}
					});
					$('#content-import-notice button.button').last().after('<image src="<?php echo get_bloginfo('template_url', 'display') . '/images/preloader-01.gif'; ?>" />').end().remove();
				});
			});
		</script>
	</div>
	<?php
}

function theme_content_start_import() {
	check_ajax_referer('theme_content_importer');
	if ($sample_data = get_option('theme_sample_data')) {
		foreach ($sample_data['posts'] as $post_id) {
			wp_delete_post($post_id, true);
		}
		foreach ($sample_data['pages'] as $page_id) {
			wp_delete_post($page_id, true);
		}
		foreach ($sample_data['widgets'] as $widget_id) {
			theme_delete_widget($widget_id, true);
		}
		foreach ($sample_data['menu'] as $menu_id) {
			wp_delete_nav_menu($menu_id);
		}
	}
	$theme_content_importer = new Theme_Content_Import();
	$theme_content_importer->go();
	update_option('theme_content_import', true);
	die();
}

function theme_content_start_import_without_cleanup() {
	check_ajax_referer('theme_content_importer');
	$theme_content_importer = new Theme_Content_Import();
	$theme_content_importer->go();
	update_option('theme_content_import', true);
	die();
}

function theme_content_hide_notice() {
	check_ajax_referer('theme_content_importer');
	update_option('theme_content_import', true);
	die();
}


// removes the status notice from the database
add_action('after_switch_theme', 'theme_delete_option');

function theme_delete_option() {
	delete_option('theme_content_import');
}
?>
