<?php
class FMViewThemes_fm {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	private $model;

	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct($model) {
	$this->model = $model;
	}

  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display() {
    $rows_data = $this->model->get_rows_data();
    $page_nav = $this->model->page_nav();
    $search_value = ((isset($_POST['search_value'])) ? esc_html($_POST['search_value']) : '');
    $search_select_value = ((isset($_POST['search_select_value'])) ? (int)$_POST['search_select_value'] : 0);
    $asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
    $order_by_array = array('id', 'title', 'default');
    $order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
    $order_class = 'manage-column column-title sorted ' . $asc_or_desc;
    $ids_string = '';
    ?>
    <form class="wrap" id="themes_form" method="post" action="admin.php?page=themes_fm">
		<?php wp_nonce_field('nonce_fm', 'nonce_fm'); ?>
		<div class="fm-page-banner themes-banner">
			<div class="theme_icon">
			</div>
			<div class="fm-logo-title">Themes</div>
			<button class="fm-button add-button medium" style="margin-left: 31px;" onclick="fm_set_input_value('task', 'add'); fm_form_submit(event, 'themes_form');">
				<span></span>
				Add New
			</button>
			<div class="fm-page-actions">
				<button class="fm-button save-as-copy-button medium" onclick="fm_set_input_value('task', 'copy_themes');">
					<span></span>
					Copy
				</button>
				<button class="fm-button delete-button medium" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete_all'); } else { return false; }">
					<span></span>
					Delete
				</button>
			</div>
		</div>	
		<div class="fm-clear"></div>		
		<div class="tablenav top">
			<?php
				WDW_FM_Library::search('Title', $search_value, 'themes_form');
				WDW_FM_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'themes_form');
			?>
		</div>
		<table class="wp-list-table widefat fixed pages">
			<thead>
				<th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;"/></th>
				<th class="table_small_col <?php if ($order_by == 'id') { echo $order_class; } ?>">
					<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'id'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'themes_form')" href="">
					<span>ID</span><span class="sorting-indicator"></span></a>
				</th>
				<th class="<?php if ($order_by == 'title') { echo $order_class; } ?>">
					<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'title'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'title' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'themes_form')" href="">
					<span>Title</span><span class="sorting-indicator"></span></a>
				</th>
				<th class="table_big_col <?php if ($order_by == 'default') { echo $order_class; } ?>">
					<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'default'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'default' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'themes_form')" href="">
					<span>Default</span><span class="sorting-indicator"></span></a>
				</th>
				<th class="table_small_col">Edit</th>
				<th class="table_small_col">Delete</th>
			</thead>
			<tbody id="tbody_arr">
			<?php
				if ($rows_data) {
					foreach ($rows_data as $row_data) {
						$alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
						$default_image = (($row_data->default) ? 'default' : 'notdefault');
						$default = (($row_data->default) ? '' : 'setdefault');
						?>
						<tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
							<td class="table_small_col check-column">
								<input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox"/>
							</td>
							<td class="table_small_col"><?php echo $row_data->id; ?></td>
							<td>
								<a onclick="fm_set_input_value('task', 'edit'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form')" href="" title="Edit"><?php echo $row_data->title; ?></a>
							</td>
							<td class="table_big_col">
								<?php if ($default != '') { ?>
									<a onclick="fm_set_input_value('task', '<?php echo $default; ?>'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form')" href="">
								<?php } ?>
									<img src="<?php echo WD_FM_URL . '/images/' . $default_image . '.png?ver='. get_option("wd_form_maker_version").''; ?>" />
								<?php if ($default != '') { ?>
									</a>
								<?php } ?>
							</td>
							<td class="table_small_col">
								<button class="fm-icon edit-icon" onclick="fm_set_input_value('task', 'edit'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form');">
									<span></span>
								</button>
							</td>
							<td class="table_small_col">
								<button class="fm-icon delete-icon" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form'); } else {return false;}">
									<span></span>
								</button>
							</td>
						</tr>
						<?php
						$ids_string .= $row_data->id . ',';
					}
				}
				?>
			</tbody>
		</table>
		<input id="task" name="task" type="hidden" value=""/>
		<input id="current_id" name="current_id" type="hidden" value=""/>
		<input id="ids_string" name="ids_string" type="hidden" value="<?php echo $ids_string; ?>"/>
		<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc"/>
		<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>"/>
    </form>
    <?php
	}

	public function edit($id, $reset) {
		$row = $this->model->get_row_data($id, $reset);
		$page_title = 'Theme: ' . $row->title;
		$param_values = $row->params;
		$border_types = array( 'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed', 'double' => 'Double', 'groove' => 'Groove', 'ridge' => 'Ridge', 'inset' => 'Inset', 'outset' => 'Outset', 'initial' => 'Initial', 'inherit' => 'Inherit', 'hidden' => 'Hidden', 'none' => 'None' );
		$borders = array('top' => 'Top', 'right' => 'Right', 'bottom' => 'Bottom', 'left' => 'Left' );
		$border_values = array('top' => 'BorderTop', 'right' => 'BorderRight', 'bottom' => 'BorderBottom', 'left' => 'BorderLeft' );
		$position_types = array('static' => 'Static', 'relative' => 'Relative', 'fixed' => 'Fixed', 'absolute' => 'Absolute' );

		$font_weights = array( 'normal' => 'Normal', 'bold' => 'Bold', 'bolder' => 'Bolder', 'lighter' => 'Lighter', 'initial' => 'Initial' );
		$aligns = array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' );
		$aligns_no_center = array( 'left' => 'Left', 'right' => 'Right' );

		$basic_fonts = array(  'arial' => 'Arial', 'lucida grande' => 'Lucida grande', 'segoe ui' => 'Segoe ui', 'tahoma' => 'Tahoma', 'trebuchet ms' => 'Trebuchet ms', 'verdana' => 'Verdana', 'cursive' =>'Cursive', 'fantasy' => 'Fantasy','monospace' => 'Monospace', 'serif' => 'Serif' );

		$bg_repeats = array(  'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y', 'no-repeat' => 'no-repeat', 'initial' => 'initial', 'inherit' => 'inherit');

		$google_fonts = WDW_FM_Library::get_google_fonts();
		$font_families = $basic_fonts + $google_fonts;
		$fonts = implode("|", str_replace(' ', '+', $google_fonts));
		wp_enqueue_style('fm_googlefonts', 'https://fonts.googleapis.com/css?family=' . $fonts . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic', null, null);

		$tabs = array(
			'global' => 'Global Parameters',
			'header' => 'Header',
			'content' => 'Content',
			'input_select' => 'Inputbox',
			'choices' => 'Choices',
			'subscribe' => 'General Buttons',
			'paigination' => 'Pagination',
			'buttons' => 'Buttons',
			'close_button' => 'Close(Minimize) Button',
			'minimize' => 'Minimize Text',
			'other' => 'Other',
			'custom_css' => 'Custom CSS'
		);

		$all_params = array(
			'global' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Font Family',
					'name' => 'GPFontFamily',
					'type' => 'select',
					'options' => $font_families,
					'class' => '',
					'value' => isset($param_values['GPFontFamily']) ? $param_values['GPFontFamily'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'AGPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPWidth']) ? $param_values['AGPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Width (for scrollbox, popup form types)',
					'name' => 'AGPSPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPSPWidth']) ? $param_values['AGPSPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Padding',
					'name' => 'AGPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPPadding']) ? $param_values['AGPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'AGPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPMargin']) ? $param_values['AGPMargin'] : '',
					'placeholder' => 'e.g. 5px 10px or 5% 10%',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'AGPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'AGPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['AGPBorderColor']) ? $param_values['AGPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'AGPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['AGPBorderType']) ? $param_values['AGPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'AGPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPBorderWidth']) ? $param_values['AGPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'AGPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPBorderRadius']) ? $param_values['AGPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'AGPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['AGPBoxShadow']) ? $param_values['AGPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => '</div>'
				)
			),
			'header' => array(
				array (
					'label' => 'General Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Alignment',
					'name' => 'HPAlign',
					'type' => 'select',
					'options' => $borders,
					'class' => '',
					'value' => isset($param_values['HPAlign']) ? $param_values['HPAlign'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'HPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['HPBGColor']) ? $param_values['HPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'HPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HPWidth']) ? $param_values['HPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Width (for topbar form type)',
					'name' => 'HTPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HTPWidth']) ? $param_values['HTPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Padding',
					'name' => 'HPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HPPadding']) ? $param_values['HPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'HPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HPMargin']) ? $param_values['HPMargin'] : '',
					'placeholder' => 'e.g. 5px 10px or 5% 10%',
					'after' => 'px/%'
				),
				array (
					'label' => 'Text Align',
					'name' => 'HPTextAlign',
					'type' => 'select',
					'options' => $aligns,
					'class' => '',
					'value' => isset($param_values['HPTextAlign']) ? $param_values['HPTextAlign'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'HPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'HPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['HPBorderColor']) ? $param_values['HPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'HPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['HPBorderType']) ? $param_values['HPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'HPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HPBorderWidth']) ? $param_values['HPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'HPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HPBorderRadius']) ? $param_values['HPBorderRadius'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Title Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'HTPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HTPFontSize']) ? $param_values['HTPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'HTPWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['HTPWeight']) ? $param_values['HTPWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'HTPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['HTPColor']) ? $param_values['HTPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Description Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'HDPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HDPFontSize']) ? $param_values['HDPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Color',
					'name' => 'HDPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['HDPColor']) ? $param_values['HDPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Image Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Alignment',
					'name' => 'HIPAlign',
					'type' => 'select',
					'options' => $borders,
					'class' => '',
					'value' => isset($param_values['HIPAlign']) ? $param_values['HIPAlign'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Width',
					'name' => 'HIPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HIPWidth']) ? $param_values['HIPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'HIPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['HIPHeight']) ? $param_values['HIPHeight'] : '',
					'after' => 'px</div>'
				)
			),
			'content' => array(
				array (
					'label' => 'General Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'GPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['GPBGColor']) ? $param_values['GPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'GPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPFontSize']) ? $param_values['GPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'GPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['GPFontWeight']) ? $param_values['GPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'GPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPWidth']) ? $param_values['GPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Width (for topbar form type)',
					'name' => 'GTPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GTPWidth']) ? $param_values['GTPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Alignment',
					'name' => 'GPAlign',
					'type' => 'select',
					'options' => $aligns,
					'class' => '',
					'value' => isset($param_values['GPAlign']) ? $param_values['GPAlign'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background URL',
					'name' => 'GPBackground',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPBackground']) ? $param_values['GPBackground'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'GPBackgroundRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => isset($param_values['GPBackgroundRepeat']) ? $param_values['GPBackgroundRepeat'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'GPBGPosition1',
					'name2' => 'GPBGPosition2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['GPBGPosition1']) ? $param_values['GPBGPosition1'] : '',
					'value2' => isset($param_values['GPBGPosition2']) ? $param_values['GPBGPosition2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Background Size',
					'name1' => 'GPBGSize1',
					'name2' => 'GPBGSize2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['GPBGSize1']) ? $param_values['GPBGSize1'] : '',
					'value2' => isset($param_values['GPBGSize2']) ? $param_values['GPBGSize2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/px'
				),
				array (
					'label' => 'Color',
					'name' => 'GPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['GPColor']) ? $param_values['GPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'GPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPPadding']) ? $param_values['GPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'GPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPMargin']) ? $param_values['GPMargin'] : '',
					'placeholder' => 'e.g. 5px 10px or 5% 10%',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'GPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'GPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['GPBorderColor']) ? $param_values['GPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'GPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['GPBorderType']) ? $param_values['GPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'GPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPBorderWidth']) ? $param_values['GPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'GPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPBorderRadius']) ? $param_values['GPBorderRadius'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Mini labels (name, phone, address, checkbox, radio) Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'GPMLFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPMLFontSize']) ? $param_values['GPMLFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'GPMLFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['GPMLFontWeight']) ? $param_values['GPMLFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'GPMLColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['GPMLColor']) ? $param_values['GPMLColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'GPMLPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPMLPadding']) ? $param_values['GPMLPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'GPMLMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['GPMLMargin']) ? $param_values['GPMLMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Section Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SEPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SEPBGColor']) ? $param_values['SEPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'SEPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SEPPadding']) ? $param_values['SEPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'SEPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SEPMargin']) ? $param_values['SEPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Section Column Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Padding',
					'name' => 'COPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['COPPadding']) ? $param_values['COPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'COPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['COPMargin']) ? $param_values['COPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Footer Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Width',
					'name' => 'FPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['FPWidth']) ? $param_values['FPWidth'] : '',
					'after' => '%'
				),
				array (
					'label' => 'Padding',
					'name' => 'FPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['FPPadding']) ? $param_values['FPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'FPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['FPMargin']) ? $param_values['FPMargin'] : '',
					'after' => 'px/%</div>'
				)
			),
			'input_select' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Height',
					'name' => 'IPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPHeight']) ? $param_values['IPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Size',
					'name' => 'IPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPFontSize']) ? $param_values['IPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'IPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['IPFontWeight']) ? $param_values['IPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'IPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['IPBGColor']) ? $param_values['IPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'IPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['IPColor']) ? $param_values['IPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'IPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPPadding']) ? $param_values['IPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'IPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPMargin']) ? $param_values['IPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'IPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'IPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['IPBorderColor']) ? $param_values['IPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'IPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['IPBorderType']) ? $param_values['IPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'IPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPBorderWidth']) ? $param_values['IPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'IPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPBorderRadius']) ? $param_values['IPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'IPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['IPBoxShadow']) ? $param_values['IPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => '</div>'
				),
				array (
					'label' => 'Dropdown additional',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Appearance',
					'name' => 'SBPAppearance',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SBPAppearance']) ? $param_values['SBPAppearance'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background URL',
					'name' => 'SBPBackground',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SBPBackground']) ? $param_values['SBPBackground'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'SBPBGRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => isset($param_values['SBPBGRepeat']) ? $param_values['SBPBGRepeat'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'SBPBGPos1',
					'name2' => 'SBPBGPos2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['SBPBGPos1']) ? $param_values['SBPBGPos1'] : '',
					'value2' => isset($param_values['SBPBGPos2']) ? $param_values['SBPBGPos2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Background Size',
					'name1' => 'SBPBGSize1',
					'name2' => 'SBPBGSize2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['SBPBGSize1']) ? $param_values['SBPBGSize1'] : '',
					'value2' => isset($param_values['SBPBGSize2']) ? $param_values['SBPBGSize2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/px'
				),
				array (
					'label' => '',
					'type' => 'label',
					'class' => '',
					'after' => '</div>'
				)
			),
			'choices' => array(
				array (
					'label' => 'Single Choice',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Input Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SCPBGColor']) ? $param_values['SCPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'SCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCPWidth']) ? $param_values['SCPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'SCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCPHeight']) ? $param_values['SCPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border',
					'name' => 'SCPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'SCPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SCPBorderColor']) ? $param_values['SCPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'SCPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['SCPBorderType']) ? $param_values['SCPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'SCPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCPBorderWidth']) ? $param_values['SCPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'SCPMargin',
					'type' => 'text',
					'class' => '5px',
					'value' => isset($param_values['SCPMargin']) ? $param_values['SCPMargin'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'SCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCPBorderRadius']) ? $param_values['SCPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'SCPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCPBoxShadow']) ? $param_values['SCPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Checked Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SCCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SCCPBGColor']) ? $param_values['SCCPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'SCCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCCPWidth']) ? $param_values['SCCPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'SCCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCCPHeight']) ? $param_values['SCCPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'SCCPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCCPMargin']) ? $param_values['SCCPMargin'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'SCCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SCCPBorderRadius']) ? $param_values['SCCPBorderRadius'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Multiple Choice',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Input Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'MCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MCPBGColor']) ? $param_values['MCPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'MCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCPWidth']) ? $param_values['MCPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'MCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCPHeight']) ? $param_values['MCPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border',
					'name' => 'MCPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'MCPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MCPBorderColor']) ? $param_values['MCPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'MCPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['MCPBorderType']) ? $param_values['MCPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'MCPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCPBorderWidth']) ? $param_values['MCPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'MCPMargin',
					'type' => 'text',
					'class' => '5px',
					'value' => isset($param_values['MCPMargin']) ? $param_values['MCPMargin'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'MCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCPBorderRadius']) ? $param_values['MCPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'MCPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCPBoxShadow']) ? $param_values['MCPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Checked Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'MCCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MCCPBGColor']) ? $param_values['MCCPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background URL',
					'name' => 'MCCPBackground',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCCPBackground']) ? $param_values['MCCPBackground'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'MCCPBGRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => isset($param_values['MCCPBGRepeat']) ? $param_values['MCCPBGRepeat'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'MCCPBGPos1',
					'name2' => 'MCCPBGPos2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['MCCPBGPos1']) ? $param_values['MCCPBGPos1'] : '',
					'value2' => isset($param_values['MCCPBGPos2']) ? $param_values['MCCPBGPos2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Width',
					'name' => 'MCCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCCPWidth']) ? $param_values['MCCPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'MCCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCCPHeight']) ? $param_values['MCCPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'MCCPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCCPMargin']) ? $param_values['MCCPMargin'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'MCCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MCCPBorderRadius']) ? $param_values['MCCPBorderRadius'] : '',
					'after' => 'px</div>'
				)
			),
			'subscribe' => array(
				array (
					'label' => 'Global Parameters',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Alignment',
					'name' => 'SPAlign',
					'type' => 'select',
					'options' => $aligns_no_center,
					'class' => '',
					'value' => isset($param_values['SPAlign']) ? $param_values['SPAlign'] : '',
					'after' => '</div>'
				),
				array (
					'label' => 'Subscribe',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SPBGColor']) ? $param_values['SPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'SPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPWidth']) ? $param_values['SPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'SPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPHeight']) ? $param_values['SPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Size',
					'name' => 'SPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPFontSize']) ? $param_values['SPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'SPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['SPFontWeight']) ? $param_values['SPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'SPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SPColor']) ? $param_values['SPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'SPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPPadding']) ? $param_values['SPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'SPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPMargin']) ? $param_values['SPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'SPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'SPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SPBorderColor']) ? $param_values['SPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'SPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['SPBorderType']) ? $param_values['SPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'SPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPBorderWidth']) ? $param_values['SPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'SPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPBorderRadius']) ? $param_values['SPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'SPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SPBoxShadow']) ? $param_values['SPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SHPBGColor']) ? $param_values['SHPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'SHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SHPColor']) ? $param_values['SHPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'SHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'SHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['SHPBorderColor']) ? $param_values['SHPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'SHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['SHPBorderType']) ? $param_values['SHPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'SHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['SHPBorderWidth']) ? $param_values['SHPBorderWidth'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Reset',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'BPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['BPBGColor']) ? $param_values['BPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'BPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPWidth']) ? $param_values['BPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'BPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPHeight']) ? $param_values['BPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Size',
					'name' => 'BPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPFontSize']) ? $param_values['BPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'BPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['BPFontWeight']) ? $param_values['BPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'BPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['BPColor']) ? $param_values['BPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'BPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPPadding']) ? $param_values['BPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'BPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPMargin']) ? $param_values['BPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'BPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'BPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['BPBorderColor']) ? $param_values['BPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'BPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['BPBorderType']) ? $param_values['BPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'BPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPBorderWidth']) ? $param_values['BPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'BPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPBorderRadius']) ? $param_values['BPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'BPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPBoxShadow']) ? $param_values['BPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'BHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['BHPBGColor']) ? $param_values['BHPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'BHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['BHPColor']) ? $param_values['BHPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'BHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'BHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['BHPBorderColor']) ? $param_values['BHPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'BHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['BHPBorderType']) ? $param_values['BHPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'BHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BHPBorderWidth']) ? $param_values['BHPBorderWidth'] : '',
					'after' => 'px</div>'
				)
			),
			'paigination' => array(
				array (
					'label' => 'Active',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'PSAPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PSAPBGColor']) ? $param_values['PSAPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'PSAPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPFontSize']) ? $param_values['PSAPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'PSAPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['PSAPFontWeight']) ? $param_values['PSAPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'PSAPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PSAPColor']) ? $param_values['PSAPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Height',
					'name' => 'PSAPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPHeight']) ? $param_values['PSAPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'PSAPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPLineHeight']) ? $param_values['PSAPLineHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Padding',
					'name' => 'PSAPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPPadding']) ? $param_values['PSAPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'PSAPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPMargin']) ? $param_values['PSAPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'PSAPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PSAPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PSAPBorderColor']) ? $param_values['PSAPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PSAPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['PSAPBorderType']) ? $param_values['PSAPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PSAPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPBorderWidth']) ? $param_values['PSAPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'PSAPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPBorderRadius']) ? $param_values['PSAPBorderRadius'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Deactive',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'PSDPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PSDPBGColor']) ? $param_values['PSDPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'PSDPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPFontSize']) ? $param_values['PSDPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'PSDPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['PSDPFontWeight']) ? $param_values['PSDPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'PSDPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PSDPColor']) ? $param_values['PSDPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Height',
					'name' => 'PSDPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPHeight']) ? $param_values['PSDPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'PSDPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPLineHeight']) ? $param_values['PSDPLineHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Padding',
					'name' => 'PSDPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPPadding']) ? $param_values['PSDPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'PSDPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPMargin']) ? $param_values['PSDPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'PSDPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PSDPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PSDPBorderColor']) ? $param_values['PSDPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PSDPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['PSDPBorderType']) ? $param_values['PSDPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PSDPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPBorderWidth']) ? $param_values['PSDPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'PSDPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSDPBorderRadius']) ? $param_values['PSDPBorderRadius'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Steps',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'fm-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Alignment',
					'name' => 'PSAPAlign',
					'type' => 'select',
					'options' => $aligns ,
					'class' => '',
					'value' => isset($param_values['PSAPAlign']) ? $param_values['PSAPAlign'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'PSAPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PSAPWidth']) ? $param_values['PSAPWidth'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Percentage',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'fm-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'PPAPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PPAPWidth']) ? $param_values['PPAPWidth'] : '',
					'placeholder' => 'e.g. 100% or 500px',
					'after' => 'px/%</div>'
				)
			),
			'buttons' => array(
				array (
					'label' => 'Global Parameters',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'BPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['BPFontSize']) ? $param_values['BPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'BPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['BPFontWeight']) ? $param_values['BPFontWeight'] : '',
					'after' => '</div>'
				),
				array (
					'label' => 'Next Button Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'NBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['NBPBGColor']) ? $param_values['NBPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'NBPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPWidth']) ? $param_values['NBPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'NBPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPHeight']) ? $param_values['NBPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'NBPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPLineHeight']) ? $param_values['NBPLineHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Color',
					'name' => 'NBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['NBPColor']) ? $param_values['NBPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'NBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPPadding']) ? $param_values['NBPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'NBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPMargin']) ? $param_values['NBPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'NBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'NBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['NBPBorderColor']) ? $param_values['NBPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'NBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['NBPBorderType']) ? $param_values['NBPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'NBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPBorderWidth']) ? $param_values['NBPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'NBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPBorderRadius']) ? $param_values['NBPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'NBPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBPBoxShadow']) ? $param_values['NBPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'NBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['NBHPBGColor']) ? $param_values['NBHPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'NBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['NBHPColor']) ? $param_values['NBHPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'NBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'NBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['NBHPBorderColor']) ? $param_values['NBHPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'NBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['NBHPBorderType']) ? $param_values['NBHPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'NBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['NBHPBorderWidth']) ? $param_values['NBHPBorderWidth'] : '',
					'after' => 'px</div>'
				),
				array (
					'label' => 'Previous Button Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'PBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PBPBGColor']) ? $param_values['PBPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'PBPWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPWidth']) ? $param_values['PBPWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'PBPHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPHeight']) ? $param_values['PBPHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'PBPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPLineHeight']) ? $param_values['PBPLineHeight'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Color',
					'name' => 'PBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PBPColor']) ? $param_values['PBPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'PBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPPadding']) ? $param_values['PBPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'PBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPMargin']) ? $param_values['PBPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'PBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PBPBorderColor']) ? $param_values['PBPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['PBPBorderType']) ? $param_values['PBPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPBorderWidth']) ? $param_values['PBPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'PBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPBorderRadius']) ? $param_values['PBPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'PBPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBPBoxShadow']) ? $param_values['PBPBoxShadow'] : '',
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'PBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PBHPBGColor']) ? $param_values['PBHPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'PBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PBHPColor']) ? $param_values['PBHPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'PBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['PBHPBorderColor']) ? $param_values['PBHPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['PBHPBorderType']) ? $param_values['PBHPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['PBHPBorderWidth']) ? $param_values['PBHPBorderWidth'] : '',
					'after' => 'px</div>'
				)
			),
			'close_button' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Position',
					'name' => 'CBPPosition',
					'type' => 'select',
					'options' => $position_types,
					'class' => '',
					'value' => isset($param_values['CBPPosition']) ? $param_values['CBPPosition'] : '',
					'after' => ''
				),
				array (
					'label' => 'Top',
					'name' => 'CBPTop',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPTop']) ? $param_values['CBPTop'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Right',
					'name' => 'CBPRight',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPRight']) ? $param_values['CBPRight'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Bottom',
					'name' => 'CBPBottom',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPBottom']) ? $param_values['CBPBottom'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Left',
					'name' => 'CBPLeft',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPLeft']) ? $param_values['CBPLeft'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Background Color',
					'name' => 'CBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['CBPBGColor']) ? $param_values['CBPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'CBPFontSize',
					'type' => 'text',
					'class' => '13',
					'value' => isset($param_values['CBPFontSize']) ? $param_values['CBPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'CBPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['CBPFontWeight']) ? $param_values['CBPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'CBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['CBPColor']) ? $param_values['CBPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'CBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPPadding']) ? $param_values['CBPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'CBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPMargin']) ? $param_values['CBPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'CBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'CBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['CBPBorderColor']) ? $param_values['CBPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'CBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['CBPBorderType']) ? $param_values['CBPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'CBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPBorderWidth']) ? $param_values['CBPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'CBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBPBorderRadius']) ? $param_values['CBPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'CBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['CBHPBGColor']) ? $param_values['CBHPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'CBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['CBHPColor']) ? $param_values['CBHPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'CBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'CBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['CBHPBorderColor']) ? $param_values['CBHPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'CBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['CBHPBorderType']) ? $param_values['CBHPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'CBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['CBHPBorderWidth']) ? $param_values['CBHPBorderWidth'] : '',
					'after' => 'px</div>'
				)
			),
			'minimize' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'MBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MBPBGColor']) ? $param_values['MBPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'MBPFontSize',
					'type' => 'text',
					'class' => '13',
					'value' => isset($param_values['MBPFontSize']) ? $param_values['MBPFontSize'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'MBPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => isset($param_values['MBPFontWeight']) ? $param_values['MBPFontWeight'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'MBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MBPColor']) ? $param_values['MBPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Text Align',
					'name' => 'MBPTextAlign',
					'type' => 'select',
					'options' => $aligns,
					'class' => '',
					'value' => isset($param_values['MBPTextAlign']) ? $param_values['MBPTextAlign'] : '',
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'MBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MBPPadding']) ? $param_values['MBPPadding'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'MBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MBPMargin']) ? $param_values['MBPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'MBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'MBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MBPBorderColor']) ? $param_values['MBPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'MBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['MBPBorderType']) ? $param_values['MBPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'MBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MBPBorderWidth']) ? $param_values['MBPBorderWidth'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'MBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MBPBorderRadius']) ? $param_values['MBPBorderRadius'] : '',
					'after' => 'px'
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'MBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MBHPBGColor']) ? $param_values['MBHPBGColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'MBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MBHPColor']) ? $param_values['MBHPColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'MBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'MBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['MBHPBorderColor']) ? $param_values['MBHPBorderColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'MBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => isset($param_values['MBHPBorderType']) ? $param_values['MBHPBorderType'] : '',
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'MBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['MBHPBorderWidth']) ? $param_values['MBHPBorderWidth'] : '',
					'after' => 'px</div>'
				)
			),

			'other' => array(
				array (
					'label' => 'Deactive Text',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Color',
					'name' => 'OPDeInputColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['OPDeInputColor']) ? $param_values['OPDeInputColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Font Style',
					'name' => 'OPFontStyle',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['OPFontStyle']) ? $param_values['OPFontStyle'] : '',
					'after' => ''
				),
				array (
					'label' => 'Required',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Color',
					'name' => 'OPRColor',
					'type' => 'text',
					'class' => 'color',
					'value' => isset($param_values['OPRColor']) ? $param_values['OPRColor'] : '',
					'after' => ''
				),
				array (
					'label' => 'Date Picker',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background URL',
					'name' => 'OPDPIcon',
					'type' => 'text',
					'class' => '',
					'placeholder' => '',
					'value' => isset($param_values['OPDPIcon']) ? $param_values['OPDPIcon'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'OPDPRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => isset($param_values['OPDPRepeat']) ? $param_values['OPDPRepeat'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'OPDPPos1',
					'name2' => 'OPDPPos2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['OPDPPos1']) ? $param_values['OPDPPos1'] : '',
					'value2' => isset($param_values['OPDPPos2']) ? $param_values['OPDPPos2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Margin',
					'name' => 'OPDPMargin',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['OPDPMargin']) ? $param_values['OPDPMargin'] : '',
					'after' => 'px/%'
				),
				array (
					'label' => 'File Upload',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background URL',
					'name' => 'OPFBgUrl',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['OPFBgUrl']) ? $param_values['OPFBgUrl'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'OPFBGRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => isset($param_values['OPFBGRepeat']) ? $param_values['OPFBGRepeat'] : '',
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'OPFPos1',
					'name2' => 'OPFPos2',
					'type' => '2text',
					'class' => 'fm-2text',
					'value1' => isset($param_values['OPFPos1']) ? $param_values['OPFPos1'] : '',
					'value2' => isset($param_values['OPFPos2']) ? $param_values['OPFPos2'] : '',
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Grading',
					'type' => 'label',
					'class' => 'fm-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Text Width',
					'name' => 'OPGWidth',
					'type' => 'text',
					'class' => '',
					'value' => isset($param_values['OPGWidth']) ? $param_values['OPGWidth'] : '',
					'after' => 'px</div>'
				)
			),
			'custom_css' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Custom CSS',
					'name' => 'CUPCSS',
					'type' => 'textarea',
					'class' => '',
					'value' => isset($param_values['CUPCSS']) ? $param_values['CUPCSS'] : '',
					'after' => '</div>'
				),
			)
		);
		$active_tab = isset($_REQUEST["active_tab"]) && $_REQUEST["active_tab"] ? $_REQUEST["active_tab"] : ($row->version == 1 ? 'custom_css' : 'global');
		$pagination = isset($_REQUEST["pagination"]) ? $_REQUEST["pagination"] : 'none';

		?>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>

		<div ng-app="ThemeParams">
			<div ng-controller="FMTheme">
				<form id="fm-themes-form" method="post" action="admin.php?page=themes_fm">
					<?php wp_nonce_field('nonce_fm', 'nonce_fm'); ?>
					<div class="fm-page-header">
						<div class="fm-logo">
						</div>
						<div class="fm-page-title">
							<?php echo $page_title; ?>
						</div>
						<div class="fm-page-actions">
							<?php if ($id) { ?>
								<button class="fm-button save-as-copy-button medium" onclick="if (fm_check_required('title', 'Title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'save_as_copy');">
									<span></span>
									Save as Copy
								</button>
							<?php } ?>
							<button class="fm-button save-button medium" onclick="if (fm_check_required('title', 'Title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'save');">
								<span></span>
								Save
							</button>
							<button class="fm-button apply-button medium" onclick="if (fm_check_required('title', 'Title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'apply');">
								<span></span>
								Apply
							</button>
							<button class="fm-button cancel-button medium" onclick="fm_set_input_value('task', 'cancel');">
								<span></span>
								Cancel
							</button>
						</div>
						<div class="fm-clear"></div>
					</div>

					<input type="hidden" id="task" name="task" value=""/>
					<input type="hidden" id="params" name="params" value=""/>
					<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>"/>
					<input type="hidden" id="default" name="default" value="<?php echo $row->default; ?>"/>
					<input type="hidden" name="active_tab" id="active_tab" value="<?php echo $active_tab; ?>" />

					<style>
					.fm-form{
						background-color:{{GPBGColor}} !important;
						font-family:{{GPFontFamily}} !important;
						width:{{AGPWidth}}% !important;
						padding:{{AGPPadding}} !important;
						margin:{{AGPMargin}} !important;
						border-radius:{{AGPBorderRadius}}px !important;
						box-shadow:{{AGPBoxShadow}} !important;
						position: relative !important;
					}

					.fm-form-header.alignLeft,
					.fm-form-content.alignLeft{
						border-radius:{{AGPBorderRadius}}px !important;
					}

					.fm-form.borderRight{
						border-right:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}} !important;
					}

					.fm-form.borderLeft{
						border-left:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}} !important;
					}

					.fm-form.borderTop{
						border-top:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}} !important;
					}

					.fm-form.borderBottom{
						border-bottom:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}} !important;
					}

					.fm-form-content{
						font-size:{{GPFontSize}}px !important;
						font-weight:{{GPFontWeight}} !important;
						width:{{GPWidth}}% !important;
						color:{{GPColor}} !important;
						padding:{{GPPadding}} !important;
						margin:{{GPMargin}} !important;
						border-radius:{{GPBorderRadius}}px !important;
					}

					.fm-form-content.isBG{
						background:url(<?php echo WD_FM_URL; ?>/{{GPBackground}}) {{GPBackgroundRepeat}} {{GPBGPosition1}} {{GPBGPosition2}} !important;
						background-size: {{GPBGSize1}} {{GPPBGSize2}} !important;
					}

					.fm-form-content.borderRight{
						border-right:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}} !important;
					}

					.fm-form-content.borderLeft{
						border-left:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}} !important;
					}

					.fm-form-content.borderTop{
						border-top:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}} !important;
					}

					.fm-form-content.borderBottom{
						border-bottom:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}} !important;
					}

					.fm-form-content label{
						font-size:{{GPFontSize}}px !important;
					}

					.fm-form-content .fm-section{
						background-color:{{SEPBGColor}} !important;
						padding:{{SEPPadding}} !important;
						margin:{{SEPMargin}} !important;
					}


					.fm-form-content .fm-column{
						padding:{{COPPadding}} !important;
						margin:{{COPMargin}} !important;
					}

					.fm-form-content input[type="text"],
					.fm-form-content select{
						font-size:{{IPFontSize}}px !important;
						font-weight:{{IPFontWeight}} !important;
						height:{{IPHeight}}px !important;
						line-height:{{IPHeight}}px !important;
						background-color:{{IPBGColor}} !important;
						color:{{IPColor}} !important;
						padding:{{IPPadding}} !important;
						margin:{{IPMargin}} !important;
						border-radius:{{IPBorderRadius}}px !important;
						box-shadow:{{IPBoxShadow}} !important;
						border:none !important;
					}

					.fm-form-content input[type="text"].borderRight,
					.fm-form-content select.borderRight{
						border-right:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.fm-form-content input[type="text"].borderLeft,
					.fm-form-content select.borderLeft{
						border-left:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.fm-form-content input[type="text"].borderTop,
					.fm-form-content select.borderTop{
						border-top:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.fm-form-content input[type="text"].borderBottom,
					.fm-form-content select.borderBottom{
						border-bottom:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.fm-form-content select{
						appearance: {{SBPAppearance}} !important;
						-moz-appearance: {{SBPAppearance}} !important;
						-webkit-appearance: {{SBPAppearance}} !important;
						background:{{IPBGColor}} !important;
					}

					.fm-form-content select.isBG{
						background:{{IPBGColor}} url(<?php echo WD_FM_URL; ?>/{{SBPBackground}}) {{SBPBGRepeat}} {{SBPBGPos1}} {{SBPBGPos2}} !important;
						background-size: {{SBPBGSize1}} {{SBPBGSize2}} !important;
					}

					.fm-form-example label.mini_label{
						font-size:{{GPMLFontSize}}px !important;
						font-weight:{{GPMLFontWeight}} !important;
						color:{{GPMLColor}} !important;
						padding:{{GPMLPadding}} !important;
						margin:{{GPMLMargin}} !important;
						width: initial !important;
					}

					.fm-button-reset {
						background-color:{{BPBGColor}} !important;
						color:{{BPColor}} !important;
						height:{{BPHeight}}px !important;
						width:{{BPWidth}}px !important;
						margin:{{BPMargin}} !important;
						padding:{{BPPadding}} !important;
						box-shadow:{{BPBoxShadow}} !important;
						border-radius:{{BPBorderRadius}}px !important;
						outline: none !important;
						border: none !important;
					}

					.fm-button-reset.borderRight {
						border-right:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.fm-button-reset.borderLeft {
						border-left:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.fm-button-reset.borderTop {
						border-top:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.fm-button-reset.borderBottom {
						border-bottom:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.fm-button-reset:hover {
						background-color:{{BHPBGColor}} !important;
						color:{{BHPColor}} !important;
						outline: none;
						border: none !important;
					}

					.fm-button-reset.borderHoverRight:hover {
						border-right:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.fm-button-reset.borderHoverLeft:hover {
						border-left:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.fm-button-reset.borderHoverTop:hover {
						border-top:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.fm-button-reset.borderHoverBottom:hover {
						border-bottom:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.fm-form-content button,
					.fm-wdform-page-button{
						font-size: {{BPFontSize}}px !important;
						font-weight: {{BPFontWeight}} !important;
					}

					.fm-previous-page .fm-wdform-page-button{
						background-color:{{PBPBGColor}} !important;
						color:{{PBPColor}} !important;
						height:{{PBPHeight}}px !important;
						line-height:{{PBPLineHeight}}px !important;
						width:{{PBPWidth}}px !important;
						margin:{{PBPMargin}} !important;
						padding:{{PBPPadding}} !important;
						border-radius:{{PBPBorderRadius}}px !important;
						box-shadow:{{PBPBoxShadow}} !important;
						outline: none !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderRight {
						border-right:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderLeft {
						border-left:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderTop {
						border-top:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderBottom {
						border-bottom:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button:hover {
						background-color:{{PBHPBGColor}} !important;
						color:{{PBHPColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderHoverRight:hover {
						border-right:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderHoverLeft:hover {
						border-left:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderHoverTop:hover {
						border-top:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}

					.fm-previous-page .fm-wdform-page-button.borderHoverBottom:hover {
						border-bottom:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}


					.fm-next-page .fm-wdform-page-button{
						background-color:{{NBPBGColor}} !important;
						color:{{NBPColor}} !important;
						height:{{NBPHeight}}px !important;
						line-height:{{NBPLineHeight}}px !important;
						width:{{NBPWidth}}px !important;
						margin:{{NBPMargin}} !important;
						padding:{{NBPPadding}} !important;
						border-radius:{{NBPBorderRadius}}px !important;
						box-shadow:{{NBPBoxShadow}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderRight {
						border-right:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderLeft {
						border-left:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderTop {
						border-top:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderBottom {
						border-bottom:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button:hover {
						background-color:{{NBHPBGColor}} !important;
						color:{{NBHPColor}} !important;
						outline: none !important;
					}

					.fm-next-page .fm-wdform-page-button.borderHoverRight:hover {
						border-right:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderHoverLeft:hover {
						border-left:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderHoverTop:hover {
						border-top:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.fm-next-page .fm-wdform-page-button.borderHoverBottom:hover {
						border-bottom:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.fm-button-subscribe {
						background-color:{{SPBGColor}} !important;
						font-size:{{SPFontSize}}px !important;
						font-weight:{{SPFontWeight}} !important;
						color:{{SPColor}} !important;
						height:{{SPHeight}}px !important;
						width:{{SPWidth}}px !important;
						margin:{{SPMargin}} !important;
						padding:{{SPPadding}} !important;
						box-shadow:{{SPBoxShadow}} !important;
						border-radius: {{SPBorderRadius}}px !important;
						outline: none !important;
						border: none !important;
					}

					.fm-button-subscribe.borderRight {
						border-right:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.fm-button-subscribe.borderLeft {
						border-left:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.fm-button-subscribe.borderTop {
						border-top:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.fm-button-subscribe.borderBottom {
						border-bottom:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.fm-button-subscribe:hover {
						background-color:{{SHPBGColor}} !important;
						color:{{SHPColor}} !important;
						outline: none !important;
						border: none !important;
					}

					.fm-button-subscribe.borderHoverRight:hover {
						border-right:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.fm-button-subscribe.borderHoverLeft:hover {
						border-left:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.fm-button-subscribe.borderHoverTop:hover {
						border-top:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.fm-button-subscribe.borderHoverBottom:hover {
						border-bottom:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.radio-div label span {
						height:{{SCPHeight}}px !important;
						width:{{SCPWidth}}px !important;
						background-color:{{SCPBGColor}} !important;
						margin:{{SCPMargin}} !important;
						box-shadow:{{SCPBoxShadow}} !important;
						border-radius: {{SCPBorderRadius}}px !important;
						border: none !important;
						display: inline-block !important;
						vertical-align: middle !important;
						box-sizing: content-box !important;
					}

					.radio-div input[type='radio']:checked + label span:after {
						content: '';
						width:{{SCCPWidth}}px !important;
						height:{{SCCPHeight}}px !important;
						background:{{SCCPBGColor}} !important;
						border-radius:{{SCCPBorderRadius}}px !important;
						margin:{{SCCPMargin}}px !important;
						display: block !important;
					}

					.radio-div label span.borderRight {
						border-right:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.radio-div label span.borderLeft {
						border-left:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.radio-div label span.borderTop {
						border-top:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.radio-div label span.borderBottom {
						border-bottom:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.checkbox-div label span {
						height:{{MCPHeight}}px !important;
						width:{{MCPWidth}}px !important;
						background-color:{{MCPBGColor}} !important;
						margin:{{MCPMargin}} !important;
						box-shadow:{{MCPBoxShadow}} !important;
						border-radius: {{MCPBorderRadius}}px !important;
						border: none !important;
						display: inline-block !important;
						vertical-align: middle !important;
						box-sizing: content-box !important;
					}

					.checkbox-div input[type='checkbox']:checked + label span:after {
						content: '';
						width:{{MCCPWidth}}px !important;
						height:{{MCCPHeight}}px !important;
						border-radius:{{MCCPBorderRadius}}px !important;
						margin:{{MCCPMargin}}px !important;
						display: block !important;
						background:{{MCCPBGColor}} !important;
					}

					.checkbox-div.isBG input[type='checkbox']:checked + label span:after{
						background:{{MCCPBGColor}} url(<?php echo WD_FM_URL; ?>/{{MCCPBackground}}) {{MCCPBGRepeat}} {{MCCPBGPos1}} {{MCCPBGPos2}} !important;
					}

					.checkbox-div label span.borderRight {
						border-right:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.checkbox-div label span.borderLeft {
						border-left:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.checkbox-div label span.borderTop {
						border-top:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.checkbox-div label span.borderBottom {
						border-bottom:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.fm-form-pagination {
						width:{{AGPWidth}}% !important;
						margin:{{AGPMargin}} !important;
					}

					.fm-footer{
						font-size:{{GPFontSize}}px !important;
						font-weight:{{GPFontWeight}} !important;
						width:{{FPWidth}}% !important;
						padding:{{FPPadding}} !important;
						margin:{{FPMargin}} !important;
						color:{{GPColor}} !important;
						clear: both !important;
					}

					.fm-pages-steps{
						text-align: {{PSAPAlign}} !important;
					}

					.active-step{
						background-color: {{PSAPBGColor}} !important;
						font-size: {{PSAPFontSize}}px !important;
						font-weight: {{PSAPFontWeight}} !important;
						color: {{PSAPColor}} !important;
						width: {{PSAPWidth}}px !important;
						height: {{PSAPHeight}}px !important;
						line-height: {{PSAPLineHeight}}px !important;
						margin: {{PSAPMargin}} !important;
						padding: {{PSAPPadding}} !important;
						border-radius: {{PSAPBorderRadius}}px !important;

						text-align: center !important;
						display: inline-block !important;
						cursor: pointer !important;
					}

					.active-step.borderRight {
						border-right:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-step.borderLeft {
						border-left:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-step.borderTop {
						border-top:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-step.borderBottom {
						border-bottom:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.deactive-step{
						background-color: {{PSDPBGColor}} !important;
						font-size: {{PSDPFontSize}}px !important;
						font-weight: {{PSDPFontWeight}} !important;
						color: {{PSDPColor}} !important;
						width: {{PSAPWidth}}px !important;
						height: {{PSDPHeight}}px !important;
						line-height: {{PSDPLineHeight}}px !important;
						margin: {{PSDPMargin}} !important;
						padding: {{PSDPPadding}} !important;
						border-radius: {{PSDPBorderRadius}}px !important;

						text-align: center !important;
						display: inline-block !important;
						cursor: pointer !important;
					}

					.deactive-step.borderRight {
						border-right:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-step.borderLeft {
						border-left:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-step.borderTop {
						border-top:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-step.borderBottom {
						border-bottom:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.active-percentage {
						background-color: {{PSAPBGColor}} !important;
						font-size: {{PSAPFontSize}}px !important;
						font-weight: {{PSAPFontWeight}} !important;
						color: {{PSAPColor}} !important;
						width: {{PSAPWidth}}px !important;
						height: {{PSAPHeight}}px !important;
						line-height: {{PSAPLineHeight}}px !important;
						margin: {{PSAPMargin}} !important;
						padding: {{PSAPPadding}} !important;
						border-radius: {{PSAPBorderRadius}}px !important;

						display: inline-block !important;
					}

					.active-percentage.borderRight {
						border-right:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-percentage.borderLeft {
						border-left:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-percentage.borderTop {
						border-top:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-percentage.borderBottom {
						border-bottom:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.deactive-percentage {
						background-color: {{PSDPBGColor}} !important;
						font-size: {{PSDPFontSize}}px !important;
						font-weight: {{PSDPFontWeight}} !important;
						color: {{PSDPColor}} !important;
						width: {{PPAPWidth}} !important;
						height: {{PSDPHeight}}px !important;
						line-height: {{PSDPLineHeight}}px !important;
						margin: {{PSDPMargin}} !important;
						padding: {{PSDPPadding}} !important;
						border-radius: {{PSDPBorderRadius}}px !important;

						display: inline-block !important;
					}

					.deactive-percentage.borderRight {
						border-right:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-percentage.borderLeft {
						border-left:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-percentage.borderTop {
						border-top:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-percentage.borderBottom {
						border-bottom:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.fm-close-icon {
						color: {{CBPColor}} !important;
						font-size: {{CBPFontSize}}px !important;
						font-weight: {{CBPFontWeight}} !important;
						text-align: center !important;
					}

					.fm-close {
						position: {{CBPPosition}} !important;
						top: {{CBPTop}} !important;
						right: {{CBPRight}} !important;
						bottom: {{CBPBottom}} !important;
						left: {{CBPLeft}} !important;
						background-color: {{CBPBGColor}} !important;
						padding: {{CBPPadding}} !important;
						margin: {{CBPMargin}} !important;
						border-radius: {{CBPBorderRadius}}px !important;
						border: none !important;
						cursor: pointer !important;
					}

					.fm-close.borderRight{
						border-right:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.fm-close.borderLeft{
						border-left:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.fm-close.borderTop{
						border-top:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.fm-close.borderBottom {
						border-bottom:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.fm-close:hover{
						background-color:{{CBHPBGColor}} !important;
						color:{{CBHPColor}} !important;
						outline: none !important;
						border: none !important;
					}

					.fm-close.borderHoverRight:hover {
						border-right:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.fm-close.borderHoverLeft:hover {
						border-left:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.fm-close.borderHoverTop:hover{
						border-top:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.fm-close.borderHoverBottom:hover {
						border-bottom:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.fm-form-header {
						background-color:{{HPBGColor}} !important;
						width:{{HPWidth}}% !important;
						padding:{{HPPadding}} !important;
						margin:{{HPMargin}} !important;
						border-radius:{{HPBorderRadius}}px !important;
					}

					.fm-form-header .htitle {
						font-size:{{HTPFontSize}}px !important;
						color:{{HTPColor}} !important;
						text-align:{{HPTextAlign}} !important;
						padding: 10px 0 !important;
						line-height:{{HTPFontSize}}px !important;
						font-weight:{{HTPWeight}} !important;
						
					}

					.fm-form-header .himage img {
						width:{{HIPWidth}}px !important;
						height:{{HIPHeight}}px !important;
					}

					.fm-form-header .himage.imageTop,
					.fm-form-header .himage.imageBottom{
						text-align:{{HPTextAlign}} !important;
					}


					.fm-form-header .hdescription {
						font-size:{{HDPFontSize}}px !important;
						color:{{HDPColor}} !important;
						text-align:{{HPTextAlign}} !important;
						padding: 5px 0 !important;
					}

					.fm-form-header.borderRight{
						border-right:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}} !important;
					}

					.fm-form-header.borderLeft{
						border-left:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}} !important;
					}

					.fm-form-header.borderTop{
						border-top:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}} !important;
					}

					.fm-form-header.borderBottom{
						border-bottom:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}} !important;
					}

					.fm-form-header.alignLeft,
					.fm-form-content.alignLeft {
						display: table-cell !important;
						vertical-align:middle !important;
					}

					.wdform-required {
						color: {{OPRColor}} !important;
					}
					
					.fm-calendar-button {
						position: relative !important; 
					}

					.fm-calendar-button span{
						position: absolute !important;
						padding: 10px !important;
						pointer-events: none !important;
						right: 3px !important;
						top: 0px !important;
						
						width: 20px !important;
						height: 20px !important;
						margin: {{OPDPMargin}} !important;
						background: url(<?php echo WD_FM_URL; ?>/{{OPDPIcon}}) {{OPDPRepeat}} {{OPDPPos1}} {{OPDPPos2}} !important;
					}

					.subscribe-reset {
						float: {{SPAlign}} !important;
						margin-right:-15px !important;
					}
					</style>

					<div class="fm-themes fm-mailchimp container-fluid">
						<div class="row">
							<div class="col-md-6 col-sm-5">
								<div class="fm-sidebar">
									<div class="fm-row">
										<label>Theme title: </label>
										<input type="text" id="title" name="title" value="<?php echo $row->title; ?>"/>
									</div>
									<br />
								</div>
								<br />
								<div class="fm-themes-tabs col-md-12">
									<ul>
										<?php
										foreach($tabs as $tkey => $tab) {
											$active_class = $active_tab == $tkey ? "fm-theme-active-tab" : "";
											echo '<li><a id="'.$tkey.'" href="#" class="'.$active_class . ($row->version == 1 && $tkey != 'custom_css' ? ' fm-disabled' : '') . '">'.$tab.'</a></li>';
										}
										?>
									</ul>
									<div class="fm-clear"></div>
									<div class="fm-themes-tabs-container">
										<?php
										$k = 0;
										foreach($all_params as $pkey => $params) {
											$show_hide_class = $active_tab == $pkey ? '' : 'fm-hide';
											echo '<div id="'.$pkey.'-content" class="fm-themes-container '.$show_hide_class.'">';
                      if ($row->version == 1 && $pkey == 'custom_css') {
                        echo '<div class="wd_error"><p>This theme is outdated. Theme Options are only available in new themes provided by Form Maker.
You can use Custom CSS panel to edit form styling, or alternatively select a new theme for your form.</p></div>';
                      }
												foreach($params as $param){
													if($param["type"] == 'panel') {
														echo '<div class="'.$param["class"].'">';
													}
													if($param["type"] != 'panel' || ($param["type"] == 'panel' && $param["label"]) )
														echo '<div class="fm-row">';
													if($param["type"] == 'panel' && $param["label"]) {
														echo '<label class="'.$param["label_class"].'" >'.$param["label"].'</label>'.$param["after"];
													} else {
														if($param["type"] == 'text') {
															echo '<label>'.$param["label"].'</label>
																<input type="'.$param["type"].'" name="'.$param["name"].'" class="'.$param["class"].'" ng-model="'.$param["name"].'" ng-init="'.$param["name"].'=\''.$param["value"].'\'" value="'.$param["value"].'" placeholder="'.(isset($param["placeholder"]) ? $param["placeholder"] : "").'" />'.$param["after"];
														}
														else {
															if($param["type"] == '2text') {
																echo '<label>'.$param["label"].'</label>
																<div class="'.$param["class"].'" style="display:inline-block; vertical-align: middle;">
																	<div style="display:table-row;">
																		<span style="display:table-cell;">'.$param["before1"].'</span><input type="text" name="'.$param["name1"].'" ng-model="'.$param["name1"].'" ng-init="'.$param["name1"].'=\''.$param["value1"].'\'" value="'.$param["value1"].'" placeholder="'.(isset($param["placeholder"]) ? $param["placeholder"] : "").'" style="display:table-cell; "/>'.$param["after"].'
																	</div>
																	<div style="display:table-row;">
																		<span style="display:table-cell;">'.$param["before2"].'</span><input type="text" name="'.$param["name2"].'" class="'.$param["class"].'" ng-model="'.$param["name2"].'" ng-init="'.$param["name2"].'=\''.$param["value2"].'\'" value="'.$param["value2"].'" placeholder="'.(isset($param["placeholder"]) ? $param["placeholder"] : "").'" style="display:table-cell; "/>'.$param["after"].'
																	</div>
																</div>
																';

															}
															else {
																if($param["type"] == 'select') {
																	echo '<label>'.$param["label"].'</label>
																		<select name="'.$param["name"].'" ng-model="'.$param["name"].'" ng-init="'.$param["name"].'=\''.$param["value"].'\'">';
																		foreach($param["options"] as $option_key => $option) {
																			echo '<option value="'.$option_key.'">'.$option.'</option>';
																	}
																	echo '</select>'.$param["after"];
																} else {
																	if($param["type"] == 'label') {
																		echo '<label class="'.$param["class"].'" >'.$param["label"].'</label>'.$param["after"];
																	} else {
																		if($param["type"] == 'checkbox') {
																			echo '<label>'.$param["label"].'</label>
																				<div class="fm-btn-group">';
																			foreach($param["options"] as $op_key => $option){
																				$init = isset($param_values[$param["name"].ucfirst($op_key)]) ? 'true' : 'false';
																				echo '<div class="fm-ch-button">
																						<input type="checkbox" id="'.$param["name"].ucfirst($op_key).'" name="'.$param["name"].ucfirst($op_key).'" value="'.$op_key.'" ng-model="'.$param["name"].ucfirst($op_key).'" ng-checked="'.$param["name"].ucfirst($op_key).'" ng-init="'.$param["name"].ucfirst($op_key).'='.$init.'"><label for="'.$param["name"].ucfirst($op_key).'">'.$option.'</label>
																					</div>';
																			}
																			echo '</div>';

																		} else{
																			if($param["type"] == 'hidden') {
																				echo '<input type="'.$param["type"].'" />'.$param["after"];
																			} else {
																				if($param["type"] == 'textarea') {
																					echo '<label>'.$param["label"].'</label>
																						<textarea name="'.$param["name"].'" rows="5"  columns="10" style="vertical-align:middle;">'.$param["value"].'</textarea>';
																				}
																			}

																		}
																	}
																}
															}
														}
													}
													if($param["type"] != 'panel' || ($param["type"] == 'panel' && $param["label"]) )
														echo '</div>';
												}
											echo '</div>';
										} ?>
									</div>
									</div>
								</div>
							</div>
							<div class="fm-preview-form col-md-6 col-sm-7" style="display:none;">
								<div class="form-example-preview fm-sidebar col-md-12">
									<p>Preview</p>
									<div class="fm-row">
										<label>Pagination Type: </label>
										<div class="pagination-type" ng-init="pagination='<?php echo $pagination; ?>'">
											<input type="radio" id="step" name="pagination-type" value="step" ng-model="pagination"/>
											<label for="step">Step</label>
											<input type="radio" id="percentage" name="pagination-type" value="percentage" ng-model="pagination" />
											<label for="percentage">Percentage</label>
											<input type="radio" id="none" name="pagination-type" value="none" ng-model="pagination" />
											<label for="none">None</label>
										</div>
									</div>
									</div>
								<div class="fm-clear"></div>
								<br />
								<div class="fm-content">
									<div class="fm-form-example form-embedded">
										<div class="fm-form-pagination">
											<div class="fm-pages-steps" ng-show="pagination == 'step'">
												<span class="active-step" ng-class="{borderRight : PSAPBorderRight, borderLeft : PSAPBorderLeft, borderBottom : PSAPBorderBottom, borderTop : PSAPBorderTop}">1(active)</span>
												<span class="deactive-step" ng-class="{borderRight : PSDPBorderRight, borderLeft : PSDPBorderLeft, borderBottom : PSDPBorderBottom, borderTop : PSDPBorderTop}">2</span>
											</div>
											<div class="fm-pages-percentage" ng-show="pagination == 'percentage'">
												<div class="deactive-percentage" ng-class="{borderRight : PSDPBorderRight, borderLeft : PSDPBorderLeft, borderBottom : PSDPBorderBottom, borderTop : PSDPBorderTop}">
													<div class="active-percentage" ng-class="{borderRight : PSAPBorderRight, borderLeft : PSAPBorderLeft, borderBottom : PSAPBorderBottom, borderTop : PSAPBorderTop}" style="width: 50%;">
														<b class="wdform_percentage_text">50%</b>
													</div>
													<div class="wdform_percentage_arrow">
													</div>
												</div>
											</div>
											<div>
											</div>
										</div>

                    <div class="fm-form" ng-class="{borderRight : AGPBorderRight, borderLeft : AGPBorderLeft, borderBottom : AGPBorderBottom, borderTop : AGPBorderTop}">
											<div ng-show="HPAlign != 'bottom' && HPAlign != 'right'" ng-class="{borderRight : HPBorderRight, borderLeft : HPBorderLeft, borderBottom : HPBorderBottom, borderTop : HPBorderTop, alignLeft : HPAlign == 'left'}" class="fm-form-header">
												<div ng-show="HIPAlign != 'bottom' && HIPAlign != 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo WD_FM_URL; ?>/images/preview_header.png" />
												</div>
												<div ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="htext">
													<div class="htitle">Subscribe Our Newsletter </div>
													<div class="hdescription">Join our mailing list to receive the latest news from our team.</div>
												</div>
												<div ng-show="HIPAlign == 'bottom' || HIPAlign == 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo WD_FM_URL; ?>/images/preview_header.png" />
												</div>
											</div>

											<div class="fm-form-content" ng-class="{isBG : GPBackground != '', borderRight : GPBorderRight, borderLeft : GPBorderLeft, borderBottom : GPBorderBottom, borderTop : GPBorderTop, alignLeft : HPAlign == 'left' || HPAlign == 'right'}">
												<div class="container-fluid">
													<div class="embedded-form">
														<div class="fm-section fm-{{GPAlign}}">
															<div class="fm-column">
																<div class="fm-row">
																	<div type="type_submitter_mail" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;"><span class="wdform-label">E-mail:</span><span class="wdform-required">*</span>
																		</div>
																		<div class="wdform-element-section" style="width: 150px;">
																			<input type="text" value="example@example.com" style="width: 100%;" ng-class="{borderRight : IPBorderRight, borderLeft : IPBorderLeft, borderBottom : IPBorderBottom, borderTop : IPBorderTop}" />
																		</div>
																	</div>
																</div>
																<div class="fm-row">
																	<div type="type_country" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Country:</span>
																		</div>
																		<div class="wdform-element-section wdform_select" style=" width: 150px;">
																			<select style="width: 100%;" ng-class="{isBG : SBPBackground != '', borderRight : IPBorderRight, borderLeft : IPBorderLeft, borderBottom : IPBorderBottom, borderTop : IPBorderTop}">
																				<option value="Armenia">Armenia</option>
																			</select>
																		</div>
																	</div>
																</div>
																
																<div class="fm-row">
																	<div type="type_radio" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Radio:</span>
																		</div>
																		<div class="wdform-element-section " style="display:table;">
																			<div style="display: table-row; vertical-align:top">
																				<div style="display: table-cell;">
																					<div class="radio-div check-rad">
																						<input type="radio" id="em-rad-op-1" value="option 1">
																						<label for="em-rad-op-1" class="mini_label">
																							<span ng-class="{borderRight : SCPBorderRight, borderLeft : SCPBorderLeft, borderBottom : SCPBorderBottom, borderTop : SCPBorderTop}"></span>option 1
																						</label>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="fm-row">
																	<div type="type_checkbox" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Checkbox:</span>
																		</div>
																		<div class="wdform-element-section" style="display: table;">
																			<div style="display: table-row; vertical-align:top">
																				<div style="display: table-cell;">
																					<div class="checkbox-div forlabs" ng-class="{isBG : MCCPBackground != ''}">
																						<input type="checkbox" id="em-ch-op-1" value="option 1">
																						<label for="em-ch-op-1" class="mini_label"><span ng-class="{borderRight : MCPBorderRight, borderLeft : MCPBorderLeft, borderBottom : MCPBorderBottom, borderTop : MCPBorderTop}"></span>option 1</label>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="fm-row">
																	<div type="type_date" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Date:</span>
																		</div>
																		<div class="wdform-element-section fm-calendar-button" style="width: 150px;">
																			<input type="text" value="" style="width: 100%;" ng-class="{borderRight : IPBorderRight, borderLeft : IPBorderLeft, borderBottom : IPBorderBottom, borderTop : IPBorderTop}" />
																			<span></span>
																		</div>
																	</div>
																</div>
																<div class="fm-row">
																	<div type="type_submit_reset" class="wdform-field subscribe-reset">
																		<div class="wdform-label-section" style="display: table-cell;"></div>
																		<div class="wdform-element-section" style="display: table-cell;">
																			<button type="button" class="fm-button-subscribe" ng-class="{borderRight : SPBorderRight, borderLeft : SPBorderLeft, borderBottom : SPBorderBottom, borderTop : SPBorderTop, borderHoverRight : SHPBorderRight, borderHoverLeft : SHPBorderLeft, borderHoverBottom : SHPBorderBottom, borderHoverTop : SHPBorderTop}" >Subscribe</button>
																			<button type="button" class="fm-button-reset" ng-class="{borderRight : BPBorderRight, borderLeft : BPBorderLeft, borderBottom : BPBorderBottom, borderTop : BPBorderTop, borderHoverRight : BHPBorderRight, borderHoverLeft : BHPBorderLeft, borderHoverBottom : BHPBorderBottom, borderHoverTop : BHPBorderTop}">Reset</button>
																		</div>
																	</div>
																</div>
																<div class="fm-clear"></div>
															</div>
															
														</div>
														<div class="fm-close-icon" ng-class="{borderRight : CBPBorderRight, borderLeft : CBPBorderLeft, borderBottom : CBPBorderBottom, borderTop : CBPBorderTop, borderHoverRight : CBHPBorderRight, borderHoverLeft : CBHPBorderLeft, borderHoverBottom : CBHPBorderBottom, borderHoverTop : CBHPBorderTop}">
															<span class="fm-close fa fa-close" ng-class="{borderRight : CBPBorderRight, borderLeft : CBPBorderLeft, borderBottom : CBPBorderBottom, borderTop : CBPBorderTop, borderHoverRight : CBHPBorderRight, borderHoverLeft : CBHPBorderLeft, borderHoverBottom : CBHPBorderBottom, borderHoverTop : CBHPBorderTop}"></span>
														</div>
														<div class="fm-footer" ng-show="pagination != 'none'">
															<div style="width: 100%;">
																<div style="width: 100%; display: table;">
																	<div style="display: table-row-group;">
																		<div  style="display: table-row;">
																			<div  class="fm-previous-page" style="display: table-cell; width: 45%;">
																				<div class="fm-wdform-page-button" ng-class="{borderRight : PBPBorderRight, borderLeft : PBPBorderLeft, borderBottom : PBPBorderBottom, borderTop : PBPBorderTop,  borderHoverRight : PBHPBorderRight, borderHoverLeft : PBHPBorderLeft, borderHoverBottom : PBHPBorderBottom, borderHoverTop : PBHPBorderTop}"><span class="fa fa-angle-double-left"></span> Previous</div>
																			</div>
																			<div class="page-numbers text-center" style="display: table-cell;">
																				<span>2/3</span>
																			</div>
																			<div class="fm-next-page" style="display: table-cell; width: 45%; text-align: right;">
																				<div class="fm-wdform-page-button" ng-class="{borderRight : NBPBorderRight, borderLeft : NBPBorderLeft, borderBottom : NBPBorderBottom, borderTop : NBPBorderTop, borderHoverRight : NBHPBorderRight, borderHoverLeft : NBHPBorderLeft, borderHoverBottom : NBHPBorderBottom, borderHoverTop : NBHPBorderTop}">Next <span class="fa fa-angle-double-right"></span></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div ng-show="HPAlign == 'bottom' || HPAlign == 'right'" ng-class="{borderRight : HPBorderRight, borderLeft : HPBorderLeft, borderBottom : HPBorderBottom, borderTop : HPBorderTop, alignLeft : HPAlign == 'right'}" class="fm-form-header">
												<div ng-show="HIPAlign != 'bottom' && HIPAlign != 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo WD_FM_URL; ?>/images/preview_header.png" />
												</div>
												<div ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="htext">
													<div class="htitle">Subscribe Our Newsletter </div>
													<div class="hdescription">Join our mailing list to receive the latest news from our team.</div>
												</div>
												<div ng-show="HIPAlign == 'bottom' || HIPAlign == 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo WD_FM_URL; ?>/images/preview_header.png" />
												</div>
											</div>
										</div>
									</div>
									<div class="fm-clear"></div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<script>
			angular.module('ThemeParams', []).controller('FMTheme', function($scope) {
			});

			(function(jQuery){
				jQuery.fn.serializeObject = function(){

					var self = this,
						json = {},
						push_counters = {},
						patterns = {
							"validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
							"key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
							"push":     /^$/,
							"fixed":    /^\d+$/,
							"named":    /^[a-zA-Z0-9_]+$/
						};

					this.build = function(base, key, value){
						base[key] = value;
						return base;
					};

					this.push_counter = function(key){
						if(push_counters[key] === undefined){
							push_counters[key] = 0;
						}
						return push_counters[key]++;
					};

					jQuery.each(jQuery(this).serializeArray(), function(){

						// skip invalid keys
						if(!patterns.validate.test(this.name)){
							return;
						}

						var k,
							keys = this.name.match(patterns.key),
							merge = this.value,
							reverse_key = this.name;

						while((k = keys.pop()) !== undefined){

							// adjust reverse_key
							reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

							// push
							if(k.match(patterns.push)){
								merge = self.build([], self.push_counter(reverse_key), merge);
							}

							// fixed
							else if(k.match(patterns.fixed)){
								merge = self.build([], k, merge);
							}

							// named
							else if(k.match(patterns.named)){
								merge = self.build({}, k, merge);
							}
						}

						json = jQuery.extend(true, json, merge);
					});

					return json;
				};
			})(jQuery);

			jQuery(".fm-themes-tabs li a").click(function(){
				jQuery(".fm-themes-tabs-container .fm-themes-container").hide();
				jQuery(".fm-themes-tabs li a").removeClass("fm-theme-active-tab");
				jQuery("#"+jQuery(this).attr("id")+'-content').show();
				jQuery(this).addClass("fm-theme-active-tab");
				jQuery("#active_tab").val(jQuery(this).attr("id"));
				return false;
			});


			function submitbutton() {
				var all_params = <?php echo $row->version == 1 ? 'jQuery(\'textarea[name=CUPCSS]\').serializeObject()' : 'jQuery(\'#fm-themes-form\').serializeObject()'; ?>;
				jQuery('#params').val(JSON.stringify(all_params).replace('<?php echo WD_FM_URL ?>', 'WD_FM_URL'));
				return true;
			}

			jQuery('.color').spectrum({
				showAlpha: true,
				showInput: true,
				showSelectionPalette: true,
				preferredFormat: "hex",
				allowEmpty: true,
				move: function(color){
					jQuery(this).val(color.toHexString());
					jQuery(this).trigger("change");
				}
			});

			setTimeout(function(){
				jQuery('.fm-preview-form').show();
			}, 1500);
			
			setTimeout(function(){
				var fm_form_example_pos = jQuery('.fm-content').offset().top;
				jQuery(window).scroll(function() {
					if(jQuery(this).scrollTop() > fm_form_example_pos) {
						jQuery('.fm-content').css({'position' : 'fixed', 'top': '32px', 'z-index' : '10000', 'width' : jQuery(".form-example-preview").outerWidth()+'px'});

					} else{
						jQuery('.fm-content').css({'position' : 'relative', 'top' : '32px', 'z-index' : '', 'width' : ''});
					}
				});

			}, 2500);

		</script>
		<?php
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