<?php

class FMModelForm_maker {

  public function showform($id, $type = 'embedded') {
    global $wpdb;
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
    $form_preview = isset($_GET['form_preview']) ? $_GET['form_preview'] : '';
    if (!$row || !$row->published || (!$form_preview && $row->type != $type)) {
      return FALSE;
    }
    if (isset($_GET['test_theme']) && (esc_html(stripslashes($_GET['test_theme'])) != '')) {
      /* From preview.*/
      $theme_id = esc_html(stripslashes($_GET['test_theme']));
    }
    else {
      $theme_id = $row->theme;
    }
    $form_theme = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $theme_id));
    if (!$form_theme) {
      $form_theme = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'formmaker_themes');
      if (!$form_theme) {
        return FALSE;
      }
    }
    
    $params_decoded = json_decode(html_entity_decode($form_theme->params), true);
    if (json_last_error() == JSON_ERROR_NONE) {
      $old = $form_theme->version == 1;
      $form_theme = $params_decoded;
    }
    else {
      $old = true;
      $form_theme = array("CUPCSS" => $form_theme->params);
    }

    $cssver = isset($form_theme['nonce_fm']) ? $form_theme['nonce_fm'] : rand();
		$create_css_data = $this->create_css($theme_id, $form_theme, $old);
		wp_register_style('fm-style-' . $theme_id, WD_FM_URL . '/css/frontend/fm-style-' . $theme_id . '.css', array(), $cssver);
    if ($form_preview) {
      wp_print_styles('fm-style-' . $theme_id);
    }
    else {
      wp_enqueue_style('fm-style-' . $theme_id);
    }
    $label_id = array();
    $label_type = array();
    $label_all = explode('#****#', $row->label_order);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_order_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_type, $label_order_each[1]);
    }
    return array(
      $row,
      1,
      $label_id,
      $label_type,
      $form_theme
    );
  }

  public function create_css($theme_id, $form_theme, $old = true, $force_rewrite = false) {
		$frontend_css = WD_FM_DIR . '/css/frontend/fm-style-' . $theme_id . '.css';
    if (!$force_rewrite && file_exists($frontend_css)) {
      return;
    }
		$prefixes = array('HP', 'AGP', 'GP', 'IP', 'SBP', 'SCP', 'MCP', 'SP', 'SHP', 'BP', 'BHP', 'NBP', 'NBHP', 'PBP', 'PBHP', 'PSAP', 'PSDP', 'CBP', 'CBHP', 'MBP', 'MBHP');
		$border_types = array('top', 'left','right', 'bottom');
		$borders = array();
		foreach ($prefixes as $prefix) {
			$borders[$prefix] = array();
			foreach ($border_types as $border_type) {
				if (isset($form_theme[$prefix.'Border'.ucfirst($border_type)])) {
					array_push($borders[$prefix], $form_theme[$prefix.'Border'.ucfirst($border_type)]);
				}
			}
		}
		clearstatcache();
		$cssfile = fopen($frontend_css, "w");	

		array_walk($form_theme, function(&$value, $key) {
			if(strpos($key, 'Color') > -1 && $value == '') {
        $value = 'transparent';
      }
		});

    $css_content = '';
    if (!$old) {
		$css_content = 
".fm-form-container.fm-theme" . $theme_id . " .fm-form {
	padding:".$form_theme['AGPPadding']." !important;
	border-radius:".$form_theme['AGPBorderRadius']."px;
	box-shadow:".$form_theme['AGPBoxShadow'].";
	background: transparent;
	border:none !important;
	display:table;
	width:".$form_theme['AGPWidth']."%;
	margin:".$form_theme['AGPMargin'].";
}\r\n";

		
	if($borders['AGP']) {
		foreach($borders['AGP'] as $border){
			if($form_theme['AGPBorderType'] == 'inherit' || $form_theme['AGPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form {
	border-".$border.": ".$form_theme['AGPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form {
	border-".$border.": ".$form_theme['AGPBorderWidth']."px ".$form_theme['AGPBorderType']." ".$form_theme['AGPBorderColor']."  !important;
}";	
			}
		}
	}
		
	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header-bg{
	background-color:".$form_theme['HPBGColor'].";
	display: " . ($form_theme['HPAlign'] == 'left' || $form_theme['HPAlign'] == 'right' ? "table-cell" : "block") . ";
  vertical-align: top;
  width: ".$form_theme['HPWidth']."%;
}\r\n";
	
		
	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header{
	width:".$form_theme['HPWidth']."%;
	margin:".$form_theme['HPMargin'].";
	border-radius:".$form_theme['HPBorderRadius']."px;
	text-align: ".$form_theme['HPTextAlign'].";
	padding:".$form_theme['HPPadding']." !important;
	border:none !important;
}\r\n";	

	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .image_left_right.fm-header {
	padding: 0 !important;
}\r\n";	

	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .image_left_right > div {
	padding:".$form_theme['HPPadding']." !important;
}\r\n";	

	
	if($borders['HP']) {
		foreach($borders['HP'] as $border){
			if($form_theme['HPBorderType'] == 'inherit' || $form_theme['HPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header{
	border-".$border.": ".$form_theme['HPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header{
	border-".$border.": ".$form_theme['HPBorderWidth']."px ".$form_theme['HPBorderType']." ".$form_theme['HPBorderColor']."  !important;
}";	
			}
		}
	}

	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form.header_left_right .wdform-page-and-images{
	display: table-cell;
	width:".$form_theme['GPWidth']."%;
}\r\n";		
	
	
	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form.header_left_right .fm-header{
	display: table-cell !important;
	width:".$form_theme['HPWidth']."%;
	vertical-align:middle;
}\r\n";	

	$css_content .= 
".fm-topbar .fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header{
	width:".$form_theme['HTPWidth']."% !important;
}\r\n";

	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header-title{
	font-size:".$form_theme['HTPFontSize']."px;
	color:".$form_theme['HTPColor'].";
}\r\n";	

	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-header-description{
	font-size:".$form_theme['HDPFontSize']."px;
	color:".$form_theme['HDPColor'].";
}\r\n";	
	
	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form-message {
	font-family:".$form_theme['GPFontFamily'].";
	font-size:".$form_theme['GPFontSize']."px;
	font-weight:".$form_theme['GPFontWeight'].";
	width:100%;
	padding:".$form_theme['GPPadding']." !important;
	margin:".$form_theme['GPMargin'].";
	border-radius:".$form_theme['GPBorderRadius']."px;
	border:none !important;
	background-color:".$form_theme['GPBGColor'].";
	text-align: center;
}\r\n";

$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-success.fm-form-message {
	color:".$form_theme['GPColor'].";
}\r\n";

$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-scrollbox {
	width:".$form_theme['AGPSPWidth']."%;
}\r\n";

$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div{
	background-color: #fff;
	font-size:".$form_theme['MBPFontSize']."px;
	font-weight:".$form_theme['MBPFontWeight'].";
	color: #444;
	padding:".$form_theme['MBPPadding']." !important;
	margin:".$form_theme['MBPMargin'].";
	border-radius:".$form_theme['MBPBorderRadius']."px;
	text-align: ".$form_theme['MBPTextAlign'].";
	border:none !important;
	cursor: pointer;
}\r\n";

if($borders['MBP']) {
	foreach($borders['MBP'] as $border){
		if($form_theme['MBPBorderType'] == 'inherit' || $form_theme['MBPBorderType'] == 'initial') {
			$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div{
	border-".$border.": ".$form_theme['MBPBorderType']." !important;
}";
			break;
		} else{
			$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div{
	border-".$border.": ".$form_theme['MBPBorderWidth']."px ".$form_theme['MBPBorderType']." ".$form_theme['MBPBorderColor']."  !important;
}";	
			}
	}
}

$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div:hover {
	background-color:".$form_theme['MBHPBGColor'].";
	color:".$form_theme['MBHPColor'].";
	outline: none;
	border: none !important;
	cursor: pointer;
}\r\n";

if($borders['MBHP']) {
	foreach($borders['MBHP'] as $border){
		if($form_theme['MBHPBorderType'] == 'inherit' || $form_theme['MBHPBorderType'] == 'initial') {
			$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div:hover {
	border-".$border.": ".$form_theme['MBHPBorderType']." !important;
}";
			break;
		} else{
			$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div:hover {
	border-".$border.": ".$form_theme['MBHPBorderWidth']."px ".$form_theme['MBHPBorderType']." ".$form_theme['MBHPBorderColor']."  !important;
}";	
			}
	}
}

	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-page-and-images{
	font-size:".$form_theme['GPFontSize']."px;
	font-weight:".$form_theme['GPFontWeight'].";
	width:".$form_theme['GPWidth']."%;
	color:".$form_theme['GPColor'].";
	padding:".$form_theme['GPPadding'].";
	margin:".$form_theme['GPMargin'].";
	border-radius:".$form_theme['GPBorderRadius']."px;
	border:none !important;
}\r\n";

	$css_content .= 
".fm-topbar .fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-page-and-images{
	width:".$form_theme['GTPWidth']."% !important;
}\r\n";
	
	if($borders['GP']) {
		foreach($borders['GP'] as $border){
			if($form_theme['GPBorderType'] == 'inherit' || $form_theme['GPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-page-and-images,
.fm-form-container.fm-theme" . $theme_id . " .fm-form-message,
.fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text {
	border-".$border.": ".$form_theme['GPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-page-and-images,
.fm-form-container.fm-theme" . $theme_id . " .fm-form-message,
.fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text {
	border-".$border.": ".$form_theme['GPBorderWidth']."px ".$form_theme['GPBorderType']." ".$form_theme['GPBorderColor']."  !important;
}";	
			}
		}
	}
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .mini_label {
	font-size:".$form_theme['GPMLFontSize']."px !important;
	font-weight:".$form_theme['GPMLFontWeight'].";
	color:".$form_theme['GPMLColor'].";
	padding:".$form_theme['GPMLPadding']." !important;
	margin:".$form_theme['GPMLMargin'].";
}";
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .check-rad label{
	font-size:".$form_theme['GPMLFontSize']."px !important;
}\r\n";

	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-page-and-images label{
	font-size:".$form_theme['GPFontSize']."px; 
}\r\n";


	if($form_theme['GPAlign'] == 'center'){
	$css_content .=" 
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_section{
	margin:0 auto;
}\r\n";

/* 	$css_content .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_column{
	float:none;
}\r\n"; */
	} else{
	$css_content .=" 
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_section{
	float:".$form_theme['GPAlign'].";
}\r\n";	
		
	}
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_section {
	padding:".$form_theme['SEPPadding'].";
	margin:".$form_theme['SEPMargin'].";
	background: transparent;
}";	

	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_column {
	padding:".$form_theme['COPPadding'].";
	margin:".$form_theme['COPMargin'].";
}";


	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider {
	background: ".$form_theme['IPBGColor']." !important;
}";

	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-scrollbox .fm-scrollbox-form {
	margin:".$form_theme['AGPMargin'].";
	position: relative;
}";

$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-popover .fm-popover-content {
	margin:".$form_theme['AGPMargin'].";
	position: relative;
}";


	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages.wdform_page_navigation {
	width:".$form_theme['AGPWidth']."%;
	margin:".$form_theme['AGPMargin'].";
}";

$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_footer {
	font-size: ".$form_theme['GPFontSize']."px;
	font-weight: ".$form_theme['GPFontWeight'].";
	color: ".$form_theme['GPColor'].";
	width: ".$form_theme['FPWidth']."%;
	margin: ".$form_theme['FPMargin'].";
	padding: ".$form_theme['FPPadding'].";
	/* clear: both; */
}";

	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_active {
	background-color: ".$form_theme['PSAPBGColor'].";
	font-size: ".$form_theme['PSAPFontSize']."px;
	font-weight: ".$form_theme['PSAPFontWeight'].";
	color: ".$form_theme['PSAPColor'].";
	width: ".$form_theme['PSAPWidth']."px;
	height: ".$form_theme['PSAPHeight']."px;
	line-height: ".$form_theme['PSAPLineHeight']."px;
	margin: ".$form_theme['PSAPMargin'].";
	padding: ".$form_theme['PSAPPadding'].";
	border-radius: ".$form_theme['PSAPBorderRadius']."px;
	cursor: pointer;
}";

	if($borders['PSAP']) {
		foreach($borders['PSAP'] as $border){
			if($form_theme['PSAPBorderType'] == 'inherit' || $form_theme['PSAPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_active {
	border: ".$form_theme['PSAPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_active {
	border-".$border.": ".$form_theme['PSAPBorderWidth']."px ".$form_theme['PSAPBorderType']." ".$form_theme['PSAPBorderColor']."  !important;
}";
			}
		}
	}				

	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_deactive {
	background-color: ".$form_theme['PSDPBGColor'].";
	font-size: ".$form_theme['PSDPFontSize']."px;
	font-weight: ".$form_theme['PSDPFontWeight'].";
	color: ".$form_theme['PSDPColor'].";
	width: ".$form_theme['PSAPWidth']."px;
	height: ".$form_theme['PSDPHeight']."px;
	line-height: ".$form_theme['PSDPLineHeight']."px;
	margin: ".$form_theme['PSDPMargin'].";
	padding: ".$form_theme['PSDPPadding'].";
	border-radius: ".$form_theme['PSAPBorderRadius']."px;
	cursor: pointer;
}";

	if($borders['PSDP']) {
		foreach($borders['PSDP'] as $border){
			if($form_theme['PSDPBorderType'] == 'inherit' || $form_theme['PSDPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_deactive {
	border: ".$form_theme['PSDPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_deactive {
	border-".$border.": ".$form_theme['PSDPBorderWidth']."px ".$form_theme['PSDPBorderType']." ".$form_theme['PSDPBorderColor']."  !important;
}";
			}
		}
	}
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_percentage_active {
	background-color: ".$form_theme['PSAPBGColor'].";
	font-size: ".$form_theme['PSAPFontSize']."px;
	font-weight: ".$form_theme['PSAPFontWeight'].";
	color: ".$form_theme['PSAPColor'].";
	width: ".$form_theme['PSAPWidth'].";
	height: ".$form_theme['PSAPHeight']."px;
	line-height: ".$form_theme['PSAPLineHeight']."px;
	margin: ".$form_theme['PSAPMargin'].";
	padding: ".$form_theme['PSAPPadding'].";
	border-radius: ".$form_theme['PSAPBorderRadius']."px;
	min-width: 7%;
}";

	if($borders['PSAP']) {
		foreach($borders['PSAP'] as $border){
			if($form_theme['PSAPBorderType'] == 'inherit' || $form_theme['PSAPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_percentage_active {
	border: ".$form_theme['PSAPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_percentage_active {
	border-".$border.": ".$form_theme['PSAPBorderWidth']."px ".$form_theme['PSAPBorderType']." ".$form_theme['PSAPBorderColor']."  !important;
}";
			}
		}
	}
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_percentage_deactive {
	background-color: ".$form_theme['PSDPBGColor'].";
	font-size: ".$form_theme['PSDPFontSize']."px;
	font-weight: ".$form_theme['PSDPFontWeight'].";
	color: ".$form_theme['PSDPColor'].";
	width: ".$form_theme['PPAPWidth'].";
	height: ".$form_theme['PSDPHeight']."px;
	line-height: ".$form_theme['PSDPLineHeight']."px;
	margin: ".$form_theme['PSDPMargin'].";
	padding: ".$form_theme['PSDPPadding'].";
	border-radius: ".$form_theme['PSDPBorderRadius']."px;
}";

	if($borders['PSDP']) {
		foreach($borders['PSDP'] as $border){
			if($form_theme['PSDPBorderType'] == 'inherit' || $form_theme['PSDPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_percentage_deactive {
	border: ".$form_theme['PSDPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-pages .page_percentage_deactive {
	border-".$border.": ".$form_theme['PSDPBorderWidth']."px ".$form_theme['PSDPBorderType']." ".$form_theme['PSDPBorderColor']."  !important;
}";
			}
		}
	}
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-action-buttons {
	font-size:".$form_theme['CBPFontSize']."px;
	font-weight:".$form_theme['CBPFontWeight'].";
	color:".$form_theme['CBPColor'].";
	text-align: center;
  cursor: pointer;
	font-family: monospace;
}";

$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form,
.fm-form-container.fm-theme" . $theme_id . " .minimize-form {
	position: ".$form_theme['CBPPosition'].";
	background:".$form_theme['CBPBGColor'].";
	padding:".$form_theme['CBPPadding'].";
	margin:".$form_theme['CBPMargin'].";
	border-radius:".$form_theme['CBPBorderRadius']."px;
	border:none;
}";

	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form {
	top: ".$form_theme['CBPTop'].";
	right: ".$form_theme['CBPRight'].";
	bottom: ".$form_theme['CBPBottom'].";
	left: ".$form_theme['CBPLeft'].";
}";


$for_mini = $form_theme['CBPLeft'] ? 'left' : 'right';
$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .minimize-form {
	top: ".$form_theme['CBPTop'].";
	".$for_mini.": ".(2 * $form_theme['CBP'.ucfirst($for_mini)] + $form_theme['CBPFontSize'] + 3)."px;
	bottom: ".$form_theme['CBPBottom'].";
}";
	
	if($borders['CBP']) {
		foreach($borders['CBP'] as $border){
			if($form_theme['CBPBorderType'] == 'inherit' || $form_theme['CBPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form,
.fm-form-container.fm-theme" . $theme_id . " .minimize-form {
	border-".$border.": ".$form_theme['CBPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form,
.fm-form-container.fm-theme" . $theme_id . " .minimize-form {
	border-".$border.": ".$form_theme['CBPBorderWidth']."px ".$form_theme['CBPBorderType']." ".$form_theme['CBPBorderColor']."  !important;
}";	
			}
		}
	} 
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form:hover,
.fm-form-container.fm-theme" . $theme_id . " .minimize-form:hover {
	background:".$form_theme['CBHPBGColor'].";
	color:".$form_theme['CBHPColor'].";
	border:none;
}";
	
	if($borders['CBHP']) {
		foreach($borders['CBHP'] as $border){
			if($form_theme['CBHPBorderType'] == 'inherit' || $form_theme['CBHPBorderType'] == 'initial') {
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form:hover,
.fm-form-container.fm-theme" . $theme_id . " .minimize-form:hover {
	border-".$border.": ".$form_theme['CBHPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .closing-form:hover,
.fm-form-container.fm-theme" . $theme_id . " .minimize-form:hover {
	border-".$border.": ".$form_theme['CBHPBorderWidth']."px ".$form_theme['CBHPBorderType']." ".$form_theme['CBHPBorderColor']."  !important;
}";	
			}
		}
	}
	
	$user_agent = $_SERVER['HTTP_USER_AGENT']; 
    if(stripos( $user_agent, 'Safari') !== false && stripos( $user_agent, 'Chrome') === false) {
        $css_content .="
.fm-popover-container:before  { 
	position:absolute;
}";	
	}
	
	$css_content .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-required {
	color: ".$form_theme['OPRColor'].";
}

.fm-form-container.fm-theme" . $theme_id . " .fm-form .input_deactive {
	color: ".$form_theme['OPDeInputColor']." !important;
	font-style: ".$form_theme['OPFontStyle'].";
}


.fm-form-container.fm-theme" . $theme_id . " .fm-form .file-picker{
	background: url(".WD_FM_URL.'/'.$form_theme['OPFBgUrl'].") ".$form_theme['OPFBGRepeat']." ".$form_theme['OPFPos1']." ".$form_theme['OPFPos2'].";
	display: inline-block;
}

.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform-calendar-button {
	background: url(".WD_FM_URL.'/'.$form_theme['OPDPIcon'].") ".$form_theme['OPDPRepeat']." ".$form_theme['OPDPPos1']." ".$form_theme['OPDPPos2']."; 
	margin: ".$form_theme['OPDPMargin'].";
    position: absolute;
}

.fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-subscribe-reset{
	float: ".$form_theme['SPAlign'].";
}

.fm-form-container.fm-theme" . $theme_id . " .fm-form .fm-subscribe-reset div{
	text-align: ".$form_theme['SPAlign'].";
}

.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit {
	margin-right: 15px;
}

";

		$defaultStyles = 
".fm-form-container.fm-theme" . $theme_id . " .fm-form{
	font-family:".$form_theme['GPFontFamily'].";
	background:".$form_theme['GPBGColor'].";
}\r\n


.fm-form-container.fm-theme" . $theme_id . " .fm-form .wdform_section {
	background:".($form_theme['GPBGColor'] != $form_theme['SEPBGColor'] ? $form_theme['SEPBGColor'] : 'transparent').";
}\r\n	

.fm-form-container.fm-theme" . $theme_id . " .fm-form .captcha_img{
	height:".$form_theme['IPHeight']."px;
}

.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type='text'],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=password],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=url],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=email],
.fm-form-container.fm-theme" . $theme_id . " .fm-form textarea,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-spinner-input,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .file-upload-status,
.fm-form-container.fm-theme" . $theme_id . " .fm-form select {
	font-size:".$form_theme['IPFontSize']."px;
	font-weight:".$form_theme['IPFontWeight'].";
	height:".$form_theme['IPHeight']."px;
	line-height:".$form_theme['IPHeight']."px;
	background-color:".$form_theme['IPBGColor'].";
	color:".$form_theme['IPColor'].";
	padding:".$form_theme['IPPadding'].";
	margin:".$form_theme['IPMargin'].";
	border-radius:".$form_theme['IPBorderRadius']."px !important;
	box-shadow:".$form_theme['IPBoxShadow'].";
	border:none;
}";

	if($borders['IP']) {
		foreach($borders['IP'] as $border){
			if($form_theme['IPBorderType'] == 'inherit' || $form_theme['IPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type='text']:not(.ui-spinner-input),
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=password],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=url],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=email],
.fm-form-container.fm-theme" . $theme_id . " .fm-form textarea,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-spinner,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider-handle,
.fm-form-container.fm-theme" . $theme_id . " .fm-form select {
	border-".$border.": ".$form_theme['IPBorderType']." !important;
}";

		$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-spinner-button,
	border-left: ".$form_theme['IPBorderType']." !important;
}";

$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider-range {
}";
				break;
			} else {
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type='text']:not(.ui-spinner-input),
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=password],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=url],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type=email],
.fm-form-container.fm-theme" . $theme_id . " .fm-form textarea,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-spinner,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider,
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider-handle,
.fm-form-container.fm-theme" . $theme_id . " .fm-form select {
	border-".$border.": ".$form_theme['IPBorderWidth']."px ".$form_theme['IPBorderType']." ".$form_theme['IPBorderColor']."  !important;
}";
	if($border == 'left'){
		$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-spinner-button {
	border-left: ".$form_theme['IPBorderWidth']."px ".$form_theme['IPBorderType']." ".$form_theme['IPBorderColor']."  !important;
}";
				}
				
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .ui-slider-range {
	background: ".$form_theme['IPBorderColor']."  !important;
}";

			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form select {
	appearance: ".$form_theme['SBPAppearance'].";
	-moz-appearance: ".$form_theme['SBPAppearance'].";
	-webkit-appearance: ".$form_theme['SBPAppearance'].";
	background:".$form_theme['IPBGColor']." url(".WD_FM_URL.'/'.$form_theme['SBPBackground'].") ".$form_theme['SBPBGRepeat']." ".$form_theme['SBPBGPos1']." ".$form_theme['SBPBGPos2'].";
	background-size: ".$form_theme['SBPBGSize1']." ".$form_theme['SBPBGSize2'].";
}";


	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .radio-div label span {
	height:".$form_theme['SCPHeight']."px;
	width:".$form_theme['SCPWidth']."px;
	background-color:".$form_theme['SCPBGColor'].";
	margin:".$form_theme['SCPMargin'].";
	box-shadow:".$form_theme['SCPBoxShadow'].";
	border-radius: ".$form_theme['SCPBorderRadius']."px;
	border: none;
	display: inline-block;
	vertical-align: middle;
}";

	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .radio-div input[type='radio']:checked + label span:after {
	content: '';
	width:".$form_theme['SCCPWidth']."px;
	height:".$form_theme['SCCPHeight']."px;
	background:".$form_theme['SCCPBGColor'].";
  border-radius: ".$form_theme['SCCPBorderRadius']."px;
  margin: ".$form_theme['SCCPMargin']."px;
	display: block;
}";

	if($borders['SCP']) {
		foreach($borders['SCP'] as $border){
			if($form_theme['SCPBorderType'] == 'inherit' || $form_theme['SCPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .radio-div label span {
	border-".$border.": ".$form_theme['SCPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .radio-div label span {
	border-".$border.": ".$form_theme['SCPBorderWidth']."px ".$form_theme['SCPBorderType']." ".$form_theme['SCPBorderColor']."  !important;
}";
			}
		}
	}

	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .checkbox-div label span {
	height:".$form_theme['MCPHeight']."px;
	width:".$form_theme['MCPWidth']."px;
	background-color:".$form_theme['MCPBGColor'].";
	margin:".$form_theme['MCPMargin'].";
	box-shadow:".$form_theme['MCPBoxShadow'].";
	border-radius: ".$form_theme['MCPBorderRadius']."px;
	border: none;
	display: inline-block;
	vertical-align: middle;
}";

	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .checkbox-div input[type='checkbox']:checked + label span:after {
	content: '';
	width:".$form_theme['MCCPWidth']."px;
	height:".$form_theme['MCCPHeight']."px;
	background:".($form_theme['MCCPBackground'] ? ($form_theme['MCCPBGColor']." url(".WD_FM_URL.'/'.$form_theme['MCCPBackground'].") ".$form_theme['MCCPBGRepeat']." ".$form_theme['MCCPBGPos1']." ".$form_theme['MCCPBGPos2']) : $form_theme['MCCPBGColor']).";
  border-radius: ".$form_theme['MCCPBorderRadius']."px;
  margin: ".$form_theme['MCCPMargin']."px;
	display: block;
}";

	if($borders['MCP']) {
		foreach($borders['MCP'] as $border){
			if($form_theme['MCPBorderType'] == 'inherit' || $form_theme['MCPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .checkbox-div label span {
	border-".$border.": ".$form_theme['MCPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .checkbox-div label span {
	border-".$border.": ".$form_theme['MCPBorderWidth']."px ".$form_theme['MCPBorderType']." ".$form_theme['MCPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit {
	background-color:".$form_theme['SPBGColor']." !important;
	font-size:".$form_theme['SPFontSize']."px !important;
	font-weight:".$form_theme['SPFontWeight']." !important;
	color:".$form_theme['SPColor']." !important;
	height:".$form_theme['SPHeight']."px !important;
	width:".$form_theme['SPWidth']."px !important;
	margin:".$form_theme['SPMargin']." !important;
	padding:".$form_theme['SPPadding']." !important;
	box-shadow:".$form_theme['SPBoxShadow']." !important;
	border-radius: ".$form_theme['SPBorderRadius']."px;
	border: none !important;
}";

	if($borders['SP']) {
		foreach($borders['SP'] as $border){
			if($form_theme['SPBorderType'] == 'inherit' || $form_theme['SPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit {
	border-".$border.": ".$form_theme['SPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit {
	border-".$border.": ".$form_theme['SPBorderWidth']."px ".$form_theme['SPBorderType']." ".$form_theme['SPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit:hover {
	background-color:".$form_theme['SHPBGColor']." !important;
	color:".$form_theme['SHPColor']." !important;
}";

	if($borders['SHP']) {
		foreach($borders['SHP'] as $border){
			if($form_theme['SHPBorderType'] == 'inherit' || $form_theme['SHPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit:hover {
	border-".$border.": ".$form_theme['SHPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-submit:hover {
	border-".$border.": ".$form_theme['SHPBorderWidth']."px ".$form_theme['SHPBorderType']." ".$form_theme['SHPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-reset,
.fm-form-container.fm-theme" . $theme_id . " .fm-form button {
	background-color:".$form_theme['BPBGColor']." !important;
	font-size:".$form_theme['BPFontSize']."px !important;
	font-weight:".$form_theme['BPFontWeight']." !important;
	color:".$form_theme['BPColor']." !important;
	height:".$form_theme['BPHeight']."px !important;
	width:".$form_theme['BPWidth']."px !important;
	margin:".$form_theme['BPMargin']." !important;
	padding:".$form_theme['BPPadding']." !important;
	box-shadow:".$form_theme['BPBoxShadow']." !important;
	border-radius: ".$form_theme['BPBorderRadius']."px;
	border: none !important;
}";

	if($borders['BP']) {
		foreach($borders['BP'] as $border){
			if($form_theme['BPBorderType'] == 'inherit' || $form_theme['BPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-reset,
.fm-form-container.fm-theme" . $theme_id . " .fm-form button {
	border-".$border.": ".$form_theme['BPBorderType']." !important;
}";
				break;
			} else{
			$defaultStyles .= "
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-reset,
.fm-form-container.fm-theme" . $theme_id . " .fm-form button {
	border-".$border.": ".$form_theme['BPBorderWidth']."px ".$form_theme['BPBorderType']." ".$form_theme['BPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-reset:hover,
.fm-form-container.fm-theme" . $theme_id . " .fm-form button:hover {
	background-color:".$form_theme['BHPBGColor']." !important;
	color:".$form_theme['BHPColor']." !important;
}";

	if($borders['BHP']) {
		foreach($borders['BHP'] as $border){
			if($form_theme['BHPBorderType'] == 'inherit' || $form_theme['BHPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-reset:hover,
.fm-form-container.fm-theme" . $theme_id . " .fm-form button:hover{
	border-".$border.": ".$form_theme['BHPBorderType']." !important;
}";
				break;
			} else {
			$defaultStyles .= "
.fm-form-container.fm-theme" . $theme_id . " .fm-form .button-reset:hover,
.fm-form-container.fm-theme" . $theme_id . " .fm-form button:hover {
	border-".$border.": ".$form_theme['BHPBorderWidth']."px ".$form_theme['BHPBorderType']." ".$form_theme['BHPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .next-page div.wdform-page-button {
	background-color:".$form_theme['NBPBGColor']." !important;
	font-size:".$form_theme['BPFontSize']."px !important;
	font-weight:".$form_theme['BPFontWeight']." !important;
	color:".$form_theme['NBPColor']." !important;
	height:".$form_theme['NBPHeight']."px !important;
	width:".$form_theme['NBPWidth']."px !important;
	margin:".$form_theme['NBPMargin']." !important;
	padding:".$form_theme['NBPPadding']." !important;
	box-shadow:".$form_theme['NBPBoxShadow']." !important;
	border-radius: ".$form_theme['NBPBorderRadius']."px;
	border: none !important;
}";

	if($borders['NBP']) {
		foreach($borders['NBP'] as $border){
			if($form_theme['NBPBorderType'] == 'inherit' || $form_theme['NBPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .next-page div.wdform-page-button {
	border-".$border.": ".$form_theme['NBPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .next-page div.wdform-page-button {
	border-".$border.": ".$form_theme['NBPBorderWidth']."px ".$form_theme['NBPBorderType']." ".$form_theme['NBPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .next-page div.wdform-page-button:hover {
	background-color:".$form_theme['NBHPBGColor']." !important;
	color:".$form_theme['NBHPColor']." !important;
}";

	$defaultStyles .= 
".fm-form-container.fm-theme" . $theme_id . " .fm-minimize-text div{
	background-color:".$form_theme['MBPBGColor'].";
	color:".$form_theme['MBPColor'].";
}\r\n";

	if($borders['NBHP']) {
		foreach($borders['NBHP'] as $border){
			if($form_theme['NBHPBorderType'] == 'inherit' || $form_theme['NBHPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .next-page div.wdform-page-button:hover {
	border-".$border.": ".$form_theme['NBHPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .next-page div.wdform-page-button:hover {
	border-".$border.": ".$form_theme['NBHPBorderWidth']."px ".$form_theme['NBHPBorderType']." ".$form_theme['NBHPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .previous-page div.wdform-page-button {
	background-color:".$form_theme['PBPBGColor']." !important;
	font-size:".$form_theme['BPFontSize']."px !important;
	font-weight:".$form_theme['BPFontWeight']." !important;
	color:".$form_theme['PBPColor']." !important;
	height:".$form_theme['PBPHeight']."px !important;
	width:".$form_theme['PBPWidth']."px !important;
	margin:".$form_theme['PBPMargin']." !important;
	padding:".$form_theme['PBPPadding']." !important;
	box-shadow:".$form_theme['PBPBoxShadow']." !important;
	border-radius: ".$form_theme['PBPBorderRadius']."px;
	border: none !important;
}";

	if($borders['PBP']) {
		foreach($borders['PBP'] as $border){
			if($form_theme['PBPBorderType'] == 'inherit' || $form_theme['PBPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .previous-page div.wdform-page-button {
	border-".$border.": ".$form_theme['PBPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .previous-page div.wdform-page-button {
	border-".$border.": ".$form_theme['PBPBorderWidth']."px ".$form_theme['PBPBorderType']." ".$form_theme['PBPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .previous-page div.wdform-page-button:hover {
	background-color:".$form_theme['PBHPBGColor']." !important;
	color:".$form_theme['PBHPColor']." !important;
}";

	if($borders['PBHP']) {
		foreach($borders['PBHP'] as $border){
			if($form_theme['PBHPBorderType'] == 'inherit' || $form_theme['PBHPBorderType'] == 'initial') {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .previous-page div.wdform-page-button:hover {
	border-".$border.": ".$form_theme['PBHPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form .previous-page div.wdform-page-button:hover{
	border-".$border.": ".$form_theme['PBHPBorderWidth']."px ".$form_theme['PBHPBorderType']." ".$form_theme['PBHPBorderColor']."  !important;
}";
			}
		}
	}

	$defaultStyles .="
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type='checkbox'],
.fm-form-container.fm-theme" . $theme_id . " .fm-form input[type='radio'] {
    display:none;
}";


	if($theme_id != 0){
		$css_content .= $defaultStyles;
	}
  else {
    $css_content .="
    .fm-form-container.fm-theme" . $theme_id . " .fm-form .check-rad.fm-right input[type='checkbox'],
    .fm-form-container.fm-theme" . $theme_id . " .fm-form .check-rad.fm-right input[type='radio'] {
    float: right;
    margin: 5px;
    }";
  }
  }
    if($form_theme['CUPCSS']) {
      $pattern = '/\/\/(.+)(\r\n|\r|\n)/';
      $form_theme_css = $form_theme['CUPCSS'];
      if (strpos($form_theme_css, ':checked + label') !== false) {
        $form_theme_css .= '
        .checkbox-div label span {
          border: 1px solid #868686  !important;
          display: inline-block;
          height: 16px;
          width: 16px;
        }
        .radio-div label span {
          border: 1px solid #868686  !important;
          border-radius: 100%;
          display: inline-block;
          height: 16px;
          width: 16px;
        }
        .checkbox-div input[type=\'checkbox\']:checked + label span:after {
          content: \'\';
          width: 16px;
          height: 16px;
          background:transparent url([SITE_ROOT]/images/themes/checkboxes/1.png) no-repeat;
          background-size: 100%;
          border-radius: 0px;
          margin: 0px;
          display: block;
        }
        .radio-div input[type=\'radio\']:checked + label span:after {
          content: \'\';
          width: 6px;
          height: 6px;
          background: #777777;
          border-radius: 10px;
          margin: 5px;
          display: block;
        }
        .checkbox-div, .radio-div {
          border: none;
          box-shadow: none;
          height: 17px;
          background: none;
        }
        .checkbox-div label, .radio-div label, .checkbox-div label:hover, .radio-div label:hover {
          opacity: 1;
          background: none;
          border: none;
          min-width: 140px;
          line-height: 13px;
        }';
      }
      $form_theme_css = explode('{', $form_theme_css);
      $count_after_explod_theme = count($form_theme_css);
      for ($i = 0; $i < $count_after_explod_theme; $i++) {
        $body_or_classes[$i] = explode('}', $form_theme_css[$i]);
      }
      for ($i = 0; $i < $count_after_explod_theme; $i++) {
        if ($i == 0) {
          $body_or_classes[$i][0] = ".fm-form-container.fm-theme" . $theme_id . " .fm-form" . ' ' . str_replace(',', ", .fm-form-container.fm-theme" . $theme_id . " .fm-form", $body_or_classes[$i][0]);
        }
        else {
          $body_or_classes[$i][1] = ".fm-form-container.fm-theme" . $theme_id . " .fm-form" . ' ' . str_replace(',', ", .fm-form-container.fm-theme" . $theme_id . " .fm-form", $body_or_classes[$i][1]);
        }
      }
      for ($i = 0; $i < $count_after_explod_theme; $i++) {
        $body_or_classes_implode[$i] = implode('}', $body_or_classes[$i]);
      }
      $theme = implode('{', $body_or_classes_implode);
      $theme = preg_replace($pattern, ' ', $theme);
      $css_content .=  str_replace('[SITE_ROOT]', WD_FM_URL, $theme);
    }
	
		$txtfilecontent = fwrite($cssfile, $css_content);
		fclose($cssfile);
		clearstatcache();
	}

  public function savedata($form, $id) {
    $fm_settings = get_option('fm_settings');
    $all_files = array();
    $correct = FALSE;
    $id_for_old = $id;
    if (!$form->form_front) {
      $id = '';
    }
    if (isset($_POST["counter" . $id])) {
      $counter = esc_html($_POST["counter" . $id]);

      if (isset($_POST["captcha_input"])) {
        $captcha_input = esc_html($_POST["captcha_input"]);
        $session_wd_captcha_code = isset($_SESSION[$id . '_wd_captcha_code']) ? $_SESSION[$id . '_wd_captcha_code'] : '-';
        if (md5($captcha_input) == $session_wd_captcha_code) {
          $correct = TRUE;
        }
        else {
          $_SESSION['massage_after_submit' . $id] = addslashes(addslashes(__('Error, incorrect Security code.', 'form_maker')));
          $_SESSION['error_or_no' . $id] = 1;
        }
      }
      elseif (isset($_POST["arithmetic_captcha_input"])) {
        $arithmetic_captcha_input = esc_html($_POST["arithmetic_captcha_input"]);
        $session_wd_arithmetic_captcha_code = isset($_SESSION[$id . '_wd_arithmetic_captcha_code']) ? $_SESSION[$id . '_wd_arithmetic_captcha_code'] : '-';
        if (md5($arithmetic_captcha_input) == $session_wd_arithmetic_captcha_code) {
          $correct = TRUE;
        }
        else {
          $_SESSION['massage_after_submit' . $id] = addslashes(addslashes(__('Error, incorrect Security code.', 'form_maker')));
          $_SESSION['error_or_no' . $id] = 1;
        }
      }
      elseif (isset($_POST["g-recaptcha-response"])) {
        $privatekey = isset($fm_settings['private_key']) ? $fm_settings['private_key'] : '';
        $captcha = $_POST['g-recaptcha-response'];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
          'secret' => $privatekey,
          'response' => $captcha,
          'remoteip' => $_SERVER['REMOTE_ADDR']
        );

        $curlConfig = array(
          CURLOPT_URL => $url,
          CURLOPT_POST => true,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_POSTFIELDS => $data
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlConfig);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response);
        if ($jsonResponse->success == "true") {
          $correct = TRUE;
        }
        else {
          $_SESSION['massage_after_submit' . $id] = addslashes(addslashes(__('Error, incorrect Security code.', 'form_maker')));
          $_SESSION['error_or_no' . $id] = 1;
        }
      }
      else {
        if (preg_match('(type_arithmetic_captcha|type_captcha|type_recaptcha)', $form->label_order_current) === 1) {
          $_SESSION['massage_after_submit' . $id] = addslashes(addslashes(__('Error, incorrect Security code.', 'form_maker')));
          $_SESSION['error_or_no' . $id] = 1;
          $correct = false;
        }
        else {
          $correct = true;
        }
      }
      if ($correct) {
        $ip = $_SERVER['REMOTE_ADDR'];
        global $wpdb;
        $blocked_ip = $wpdb->get_var($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE ip="%s"', $ip));
        if ($blocked_ip) {
          $_SESSION['massage_after_submit' . $id] = addslashes(__('Your ip is blacklisted. Please contact the website administrator.', 'form_maker'));
          wp_redirect($_SERVER["REQUEST_URI"]);//to be checked
          exit;
        }

        if (isset($_POST["save_or_submit" . $id]) && $_POST["save_or_submit" . $id] == 'save') {
          if (defined('WD_FM_SAVE_PROG') && is_plugin_active(constant('WD_FM_SAVE_PROG'))) {
            $_GET['addon_task'] = 'save_progress';
            $_GET['form_id'] = $id;
            $_GET['form_currency'] = $form->payment_currency;
            do_action('WD_FM_SAVE_PROG_init');

            return $all_files;
          }
        }
        else {
          $result_temp = $this->save_db($counter, $id_for_old);

          $all_files = $result_temp[0];
          if (is_numeric($all_files)) {
            $this->remove($all_files, $id_for_old);

            $_SESSION['massage_after_submit' . $id] = $result_temp[1];
            $_SESSION['error_or_no' . $id] = 1;

          }
          else {
            if (defined('WD_FM_SAVE_PROG') && is_plugin_active(constant('WD_FM_SAVE_PROG'))) {
              $_GET['addon_task'] = 'clear_data';
              $_GET['form_id'] = $id;
              do_action('WD_FM_SAVE_PROG_init');
            }
            if (isset($counter)) {
              $this->gen_mail($counter, $all_files, $id_for_old, $result_temp[1]);
            }
          }
        }
      }
    }
    return $all_files;
  }
  
   public function select_data_from_db_for_labels($db_info,$label_column, $table, $where, $order_by) {
        global $wpdb;
        
		$query = "SELECT `".$label_column."` FROM ".$table.$where." ORDER BY ".$order_by;
		if($db_info)
			{ 
			
			    $temp		= explode('@@@wdfhostwdf@@@',$db_info);
				$host		= $temp[0];
				$temp		= explode('@@@wdfportwdf@@@',$temp[1]);
				$port		= $temp[0];
				$temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
				$username	= $temp[0];
				$temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
				$password	= $temp[0];
				$temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
				$database	= $temp[0];
				 
				$wpdb_temp = new wpdb($username, $password, $database, $host);
				$choices_labels = $wpdb_temp->get_results($query,ARRAY_N);				
			}
		else{
            $choices_labels = $wpdb->get_results($query,ARRAY_N);
        }
        return $choices_labels;
  } 
  
  public function select_data_from_db_for_values($db_info,$value_column, $table, $where, $order_by) {
    global $wpdb;
		$query = "SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by;
		if($db_info) {
      $temp		= explode('@@@wdfhostwdf@@@',$db_info);
      $host		= $temp[0];
      $temp		= explode('@@@wdfportwdf@@@',$temp[1]);
      $port		= $temp[0];
      $temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
      $username	= $temp[0];
      $temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
      $password	= $temp[0];
      $temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
      $database	= $temp[0];
      $wpdb_temp = new wpdb($username, $password, $database, $host);
      $choices_values = $wpdb_temp->get_results($query,ARRAY_N);				
    }
		else {
      $choices_values = $wpdb->get_results($query,ARRAY_N);
    }
    return $choices_values; 
  }
  
  public function save_db($counter, $id) {
    global $wpdb;
	$current_user =  wp_get_current_user();
	if ($current_user->ID != 0)
	{
		$wp_userid =  $current_user->ID;
		$wp_username =  $current_user->display_name;
		$wp_useremail =  $current_user->user_email;
	}
	else
	{
		$wp_userid = '';
		$wp_username = '';
		$wp_useremail = '';
	}
	$ip = $_SERVER['REMOTE_ADDR']; 
	 
    $chgnac = TRUE;
    $all_files = array();
    $paypal = array();
    $paypal['item_name'] = array();
    $paypal['quantity'] = array();
    $paypal['amount'] = array();
    $is_amount=false;
    $paypal['on_os'] = array();
    $total = 0;
    $form_currency = '$';
    $currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
    $currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
    $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id= %d", $id));
    $id_old = $id;
    if (!$form->form_front) {
      $id = '';
    }
    if ($form->payment_currency) {
      $form_currency = $currency_sign[array_search($form->payment_currency, $currency_code)];
    }
    $label_id = array();
    $label_label = array();
    $label_type = array();
    
    $disabled_fields = explode(',', (isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : ""));
    $disabled_fields = array_slice($disabled_fields,0, count($disabled_fields)-1);
    $label_all	= explode('#****#',$form->label_order_current);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_order_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_label, $label_order_each[0]);
      array_push($label_type, $label_order_each[1]);
    }
    $max = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix . "formmaker_submits");
    $fvals=array();
		foreach ($label_type as $key => $type) {
			$value = '';
			if ($type == "type_submit_reset" or $type == "type_map" or $type == "type_editor" or  $type == "type_captcha" or $type == "type_arithmetic_captcha" or  $type == "type_recaptcha" or  $type == "type_button" or $type == "type_paypal_total" or $type == "type_send_copy") 
				continue;
        
			$i = $label_id[$key];
			if(!in_array($i,$disabled_fields)) {
				switch ($type) {
				case 'type_text':
				case 'type_password':
				case "type_submitter_mail":
				case "type_own_select":					
				case "type_country":				
				case "type_number": 
				case "type_phone_new": 
				{
				  $value = isset($_POST['wdform_'.$i."_element".$id]) ? esc_html($_POST['wdform_'.$i."_element".$id]) : "";
				  break;
				}
				
				case "type_date":{
					$value = isset($_POST['wdform_'.$i."_element".$id]) ? esc_html($_POST['wdform_'.$i."_element".$id]) : "";
					$date_format = isset($_POST['wdform_'.$i."_date_format".$id]) ? esc_html($_POST['wdform_'.$i."_date_format".$id]) : "";
					if($value){
						if(!$this->fm_validateDate($value, $date_format)){
							echo "<script> alert('" . addslashes(__("This is not a valid date format.", 'form_maker')) . "');</script>";
							return array($max + 1);
						}
					}
					
					break;
				}
				
				case "type_date_new":{
					$value = isset($_POST['wdform_'.$i."_element".$id]) ? esc_html($_POST['wdform_'.$i."_element".$id]) : "";
					
					break;
				}
				
				case "type_date_range":{
					$value = (isset($_POST['wdform_'.$i."_element".$id."0"]) ? esc_html($_POST['wdform_'.$i."_element".$id."0"]) : "").' - '.(isset($_POST['wdform_'.$i."_element".$id."1"]) ? esc_html($_POST['wdform_'.$i."_element".$id."1"]) : "");
					
					break;
				}
				
				case 'type_textarea': {
					$value = isset($_POST['wdform_'.$i."_element".$id]) ? wpautop($_POST['wdform_'.$i."_element".$id]) : "";
					break;		
				}
				
				case "type_wdeditor": {
				  $value = isset($_POST['wdform_'.$i.'_wd_editor'.$id]) ? esc_html($_POST['wdform_'.$i.'_wd_editor'.$id]) : "";
				  break;
				}
				case "type_mark_map": {
				  $value = (isset($_POST['wdform_'.$i."_long".$id]) ? $_POST['wdform_'.$i."_long".$id] : "") . '***map***' . (isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : "");
				  break;
				}
				case "type_date_fields": {
				  $value = (isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : "") . '-' . (isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : "") . '-' . (isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : "");
				  break;
				}					
				case "type_time": {
				  $ss = isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL;
				  if(isset($ss)) {
					$value = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : "");
				  }
				  else {
					$value = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
				  }								
				  $am_pm = isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL;
				  if(isset($am_pm)) {
					$value = $value . ' ' . $am_pm;
				  }
				  break;
				}					
				case "type_phone": {
				  $value = (isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : "") . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "");							
				  break;
				}		
				case "type_name": {				
				  $element_title = isset($_POST['wdform_'.$i."_element_title".$id]) ? esc_html($_POST['wdform_'.$i."_element_title".$id]) : NULL;
				  $element_middle = isset($_POST['wdform_'.$i."_element_middle".$id]) ? esc_html($_POST['wdform_'.$i."_element_middle".$id]) : NULL;
				  if(isset($element_title) || isset($element_middle)) {
					$value = (isset($_POST['wdform_'.$i."_element_title".$id]) ? esc_html($_POST['wdform_'.$i."_element_title".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_first".$id]) ? esc_html($_POST['wdform_'.$i."_element_first".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? esc_html($_POST['wdform_'.$i."_element_last".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_middle".$id]) ? esc_html($_POST['wdform_'.$i."_element_middle".$id]) : "");
				  }
				  else {
					$value = (isset($_POST['wdform_'.$i."_element_first".$id]) ? esc_html($_POST['wdform_'.$i."_element_first".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? esc_html($_POST['wdform_'.$i."_element_last".$id]) : "");
				  }
				  break;
				}		
				case "type_file_upload": {
					if(isset($_POST['wdform_'.$i."_file_url".$id.'_save'])) {
						$file_url = isset($_POST['wdform_'.$i."_file_url".$id.'_save']) ? stripslashes($_POST['wdform_'.$i."_file_url".$id.'_save']) : NULL;
						if(isset($file_url)) {
							$all_files = isset($_POST['wdform_'.$i."_all_files".$id.'_save']) ? json_decode(stripslashes($_POST['wdform_'.$i."_all_files".$id.'_save']), true) : array();
							$value = $file_url;
						}
					}
					else {
					
					
				  $files = isset($_FILES['wdform_'.$i.'_file'.$id]) ? $_FILES['wdform_'.$i.'_file'.$id] : NULL;
				  foreach($files['name'] as $file_key => $file_name) {
					if($file_name) {
					  $untilupload = $form->form_fields;
					  $untilupload = substr($untilupload, strpos($untilupload,$i.'*:*id*:*type_file_upload'), -1);
					  $untilupload = substr($untilupload, 0, strpos($untilupload,'*:*new_field*:'));
					  $untilupload = explode('*:*w_field_label_pos*:*',$untilupload);
					  $untilupload = $untilupload[1];
					  $untilupload = explode('*:*w_destination*:*',$untilupload);
					  $destination = explode('*:*w_hide_label*:*' ,$untilupload[0]);
					  $destination =  $destination[1];
					  $destination = str_replace(site_url() . '/', '', $destination);
					  $untilupload = $untilupload[1];
					  $untilupload = explode('*:*w_extension*:*',$untilupload);
					  $extension 	 = $untilupload[0];
					  $untilupload = $untilupload[1];
					  $untilupload = explode('*:*w_max_size*:*',$untilupload);
					  $max_size 	 = $untilupload[0];
					  $untilupload = $untilupload[1];
					  $fileName = $files['name'][$file_key];
					  $fileSize = $files['size'][$file_key];

					  if($fileSize > $max_size * 1024) {
						return array($max+1, addslashes(__('The file exceeds the allowed size of', 'form_maker')) . $max_size . ' KB');
					  }

					  $uploadedFileNameParts = explode('.',$fileName);
					  $uploadedFileExtension = array_pop($uploadedFileNameParts);
					  $to = strlen($fileName) - strlen($uploadedFileExtension) - 1;
					  
					  $fileNameFree = substr($fileName, 0, $to);
					  $invalidFileExts = explode(',', $extension);
					  $extOk = false;

					  foreach($invalidFileExts as $key => $valuee) {
						if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension)))) {
						  $extOk = true;
						}
					  }
					   
					  if ($extOk == false) {
						return array($max+1, addslashes(__('Can not upload this type of file', 'form_maker')));
					  }
					  
					  $fileTemp = $files['tmp_name'][$file_key];
					  $p = 1;
					  
					  if(!file_exists($destination))
						mkdir($destination , 0777);
					  if (file_exists($destination . "/" . $fileName)) {
						$fileName1 = $fileName;
						while (file_exists($destination . "/" . $fileName1)) {
						  $to = strlen($file_name) - strlen($uploadedFileExtension) - 1;
						  $fileName1 = substr($fileName, 0, $to) . '(' . $p . ').' . $uploadedFileExtension;
						//  $file['name'] = $fileName;
						  $p++;
						}
						$fileName = $fileName1;
					  }
                                          
						// for dropbox & google drive integration addons
						  $check_both = 0;
							if($form->save_uploads == 0){
								if(defined('WD_FM_DBOX_INT') && is_plugin_active(constant('WD_FM_DBOX_INT'))){
									$enable = $wpdb->get_var("SELECT enable FROM " . $wpdb->prefix ."formmaker_dbox_int WHERE form_id=" . $form->id);
									if($enable == 1){
										$selectable_upload = $wpdb->get_var("SELECT selectable_upload FROM " . $wpdb->prefix .	"formmaker_dbox_int WHERE form_id=" . $form->id);

										if((int)$selectable_upload == 1){
											$temp_dir_dbox =  explode('\\', $fileTemp);
											$temp_dir_dbox = implode('%%', $temp_dir_dbox);

											$value.= $temp_dir_dbox . '*@@url@@*' . $fileName;
										}
										else{
											$dlink_dbox = '<a href="'.add_query_arg(array('action' => 'WD_FM_DBOX_INT', 'addon_task' => 'upload_dbox_file', 'id' => $form->id), admin_url('admin-ajax.php')).'&dbox_file_name=' . $fileName . '&dbox_folder_name=/'.$form->title.'" >' . $fileName . '</a>';

											$value.= $dlink_dbox;
										}

										$files['tmp_name'][$file_key]=$fileTemp;
										$temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key], 'field_key' => $i);
									}
									else
										$check_both ++;
                                              
								}
								else
									$check_both ++;
								if(defined('WD_FM_GDRIVE_INT') && is_plugin_active(constant('WD_FM_GDRIVE_INT'))){
									$enable = $wpdb->get_var("SELECT enable FROM " . $wpdb->prefix ."formmaker_gdrive_int WHERE form_id=" . $form->id);
									if($enable == 1){
										$selectable_upload = $wpdb->get_var("SELECT selectable_upload FROM " . $wpdb->prefix ."formmaker_gdrive_int WHERE form_id=" . $form->id);

										if((int)$selectable_upload == 1){
											$temp_dir_dbox =  explode('\\', $fileTemp);
											$temp_dir_dbox = implode('%%', $temp_dir_dbox);
											$value.= 'wdCloudAddon' . $temp_dir_dbox . '*@@url@@*' . $fileName . '*@@url@@*' . $files['type'][$file_key];
										}
										else{                                          
											$dlink_dbox = '<a target="_blank" href="'.add_query_arg(array('action' => 'WD_FM_GDRIVE_INT', 'addon_task' => 'create_drive_link', 'id' => $form->id), admin_url('admin-ajax.php')).'&gdrive_file_name=' . $fileName . '&gdrive_folder_name='.$form->title.'" >' . $fileName . '</a>';                                                        
											$value.= $dlink_dbox;
										}

										$files['tmp_name'][$file_key]=$fileTemp;
										$temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key], 'field_key' => $i);
									}
									else
										$check_both ++;
                                              
								}
								else
									$check_both ++;
                                         
							}
//                                           
							if($check_both != 0){
								$value.= '';
								$files['tmp_name'][$file_key]=$fileTemp;
								$temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key], 'field_key' => $i);
							}
							// dropbox and google drive integration addons
							if($form->save_uploads == 1){
								if(!move_uploaded_file($fileTemp, ABSPATH . $destination . '/' . $fileName)) {	
									return array($max+1, addslashes(__('Error, file cannot be moved.', 'form_maker')));
								}
								$value.= site_url() . '/' . $destination . '/' . $fileName . '*@@url@@*';
								$files['tmp_name'][$file_key]=$destination . "/" . $fileName;
								$temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key], 'field_key' => $i);

							}
							array_push($all_files, $temp_file);
						}	
					}	
				}
				break;
			}
				
				case 'type_address': {
				  $value = '*#*#*#';
				  $element = isset($_POST['wdform_'.$i."_street1".$id]) ? esc_html($_POST['wdform_'.$i."_street1".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_street2".$id]) ? esc_html($_POST['wdform_'.$i."_street2".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_city".$id]) ? esc_html($_POST['wdform_'.$i."_city".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_state".$id]) ? esc_html($_POST['wdform_'.$i."_state".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_postal".$id]) ? esc_html($_POST['wdform_'.$i."_postal".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_country".$id]) ? esc_html($_POST['wdform_'.$i."_country".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }						
				  break;
				}
				
				case "type_hidden": {
				  $value = isset($_POST[$label_label[$key]]) ? esc_html($_POST[$label_label[$key]]) : "";
				  break;
				}
				
				case "type_radio": {
				  $element = isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL;
				  if(isset($element)) {
					$value = $element;	
					break;
				  }						
				  $value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
				  break;
				}
				
				case "type_checkbox": {
				  $start = -1;
				  $value = '';
				  for($j = 0; $j < 100; $j++) {						
					$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					if(isset($element)) {
					  $start = $j;
					  break;
					}
				  }
					
				  $other_element_id = -1;
				  $is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
				  if($is_other == "yes") {
					$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
				  }
				  
				  if($start != -1) {
					for($j = $start; $j < 100; $j++) {
					  $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					  if(isset($element)) {
						if($j == $other_element_id) {
						  $value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '***br***';
						}
						else {								
						  $value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '***br***';
						}
					  }
					}
				  }						
				  break;
				}
				
				case "type_paypal_price":	{
				  $value = isset($_POST['wdform_'.$i."_element_dollars".$id]) ? $_POST['wdform_'.$i."_element_dollars".$id] : 0;

				  $value = (int) preg_replace('/\D/', '', $value);
				  
				  if(isset($_POST['wdform_'.$i."_element_cents".$id])) {
					$value = $value . '.' . ( preg_replace('/\D/', '', $_POST['wdform_'.$i."_element_cents".$id]));
				  }
				  
				  $total += (float)($value);						
				  $paypal_option = array();

				  if($value != 0) {
					$quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
					array_push ($paypal['item_name'], $label_label[$key]);
					array_push ($paypal['quantity'], $quantity);
					array_push ($paypal['amount'], $value);
					$is_amount=true;
					array_push ($paypal['on_os'], $paypal_option);
				  }
				  $value = $value . $form_currency;
				  break;
				}
				
				case "type_paypal_price_new":	{
				  $value = isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] ? $_POST['wdform_'.$i."_element".$id] : 0;  
				  $total += (float)($value);						
				  $paypal_option = array();

				  if($value != 0) {
					$quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
					array_push ($paypal['item_name'], $label_label[$key]);
					array_push ($paypal['quantity'], $quantity);
					array_push ($paypal['amount'], $value);
					$is_amount=true;
					array_push ($paypal['on_os'], $paypal_option);
				  }
				  $value = $form_currency.$value;
				  break;
				}
				
				
				case "type_paypal_select": {
					if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] !='') {
						$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $form_currency.(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") ;
					}
					else {
						$value = '';
					}
				  $quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
				  $total += (float)(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0) * $quantity;
				  array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : ""));
				  array_push ($paypal['quantity'], $quantity);
				  array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : ""));
				  if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != 0) {
					$is_amount=true;
				  }
				  $element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
				  if(isset($element_quantity) && $value != '') {
					$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
				  }
				  $paypal_option = array();
				  $paypal_option['on'] = array();
				  $paypal_option['os'] = array();

				  for($k = 0; $k < 50; $k++) {
					$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
					if(isset($temp_val) && $value != '') {
					  array_push ($paypal_option['on'], (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : ""));
					  array_push ($paypal_option['os'], (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : ""));
					  $value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : "") . '***property***';
					}
				  }
				  array_push ($paypal['on_os'], $paypal_option);
				  break;
				}
            
				case "type_paypal_radio": {
					$element = isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] ? $_POST['wdform_'.$i."_element".$id] : '';
					if($element) {
						$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : '') . ' : ' . $form_currency . $element;
					}
					else {
						$value = '';
					}
				  $quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
				  $total += (float)(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0) * $quantity;
				  array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : ""));
				  array_push ($paypal['quantity'], $quantity);
				  array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0));
				  if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != 0) {
					$is_amount=true;
				  }
				  
				  $element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
				  if(isset($element_quantity) && $value != '') {
					$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
				  }					
				  
				  $paypal_option = array();
				  $paypal_option['on'] = array();
				  $paypal_option['os'] = array();

				  for($k = 0; $k < 50; $k++) {
					$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
					if(isset($temp_val) && $value != '') {
					  array_push ($paypal_option['on'], (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : ""));
					  array_push ($paypal_option['os'], $_POST['wdform_'.$i."_property".$id.$k]);
					  $value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $_POST['wdform_'.$i."_property".$id.$k] . '***property***';
					}
				  }
				  array_push ($paypal['on_os'], $paypal_option);
				  break;
				}

				case "type_paypal_shipping": {
				  $element = isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] ? $_POST['wdform_'.$i."_element".$id] : '';
					if($element) {
						$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : '') . ' : ' . $form_currency . $element;
					}
					else {
						$value = '';
					}
				  $paypal['shipping'] = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
				  break;
				}

				case "type_paypal_checkbox": {
				  $start = -1;
				  $value = '';
				  for($j = 0; $j < 100; $j++) {						
					$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					if(isset($element)) {
					  $start = $j;
					  break;
					}
				  }
				  
				  $other_element_id = -1;
				  $is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
				  if($is_other == "yes") {
					$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
				  }
				  
				  if($start != -1) {
					for($j = $start; $j < 100; $j++) {
					  $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					  if(isset($element)) {
						if($j == $other_element_id) {
						  $value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '***br***';									
						}
						else {
							$element = (isset($_POST['wdform_'.$i."_element".$id.$j]) && $_POST['wdform_'.$i."_element".$id.$j] ? $_POST['wdform_'.$i."_element".$id.$j] : 0);
							$value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "") . ' - ' .  $form_currency . $element. '***br***';
						  
						  $quantity = ((isset($_POST['wdform_' . $i . "_element_quantity" . $id]) && ($_POST['wdform_' . $i . "_element_quantity" . $id] >= 1)) ? $_POST['wdform_'.$i . "_element_quantity" . $id] : 1);
						  $total += (float)(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : 0) * (float)($quantity);
						  array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : ""));
						  array_push ($paypal['quantity'], $quantity);
						  array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id.$j]) ? ($_POST['wdform_'.$i."_element".$id.$j] == '' ? '0' : $_POST['wdform_'.$i."_element".$id.$j]) : ""));
						  if (isset($_POST['wdform_'.$i."_element".$id.$j]) && $_POST['wdform_'.$i."_element".$id.$j] != 0) {
							$is_amount = TRUE;
						  }
						  $paypal_option = array();
						  $paypal_option['on'] = array();
						  $paypal_option['os'] = array();

						  for($k = 0; $k < 50; $k++) {
							$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
							if(isset($temp_val)) {
							  array_push ($paypal_option['on'], isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "");
							  array_push ($paypal_option['os'], $_POST['wdform_'.$i."_property".$id.$k]);
							}
						  }
						  array_push ($paypal['on_os'], $paypal_option);
						}
					  }
					}
					
					$element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
					if(isset($element_quantity)) {
					  $value .= (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
					}
					for($k = 0; $k < 50; $k++) {
					  $temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
					  if(isset($temp_val)) {
						$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $_POST['wdform_'.$i."_property".$id.$k] . '***property***';
					  }
					}							
				  }
				  break;
				}
				
				case "type_star_rating": {
				  if(isset($_POST['wdform_'.$i."_selected_star_amount".$id]) && $_POST['wdform_'.$i."_selected_star_amount".$id] == "") {
					$selected_star_amount = 0;
				  }
				  else {
					$selected_star_amount = isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : 0;
				  }						
				  $value = $selected_star_amount . '/' . (isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : "");
				  break;
				}
			  
				case "type_scale_rating": {
				  $value = (isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0) . '/' . (isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : "");
				  break;
				}
				
				case "type_spinner": {
				  $value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
				  break;
				}
				
				case "type_slider": {
				  $value = isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : "";
				  break;
				}
				
				case "type_range": {
				  $value = (isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : "") . '-' . (isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : "");
				  break;
				}
				
				case "type_grading": {
				  $value = "";
				  $items = explode(":", isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "");
				  for($k = 0; $k < sizeof($items) - 1; $k++) {
					$value .= (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : "") . ':';
				  }
				  $value .= (isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "") . '***grading***';				
				  break;
				}
				
				case "type_matrix": {
				  $rows_of_matrix = explode("***", isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "");
				  $rows_count = sizeof($rows_of_matrix) - 1;
				  $column_of_matrix = explode("***", isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "");
				  $columns_count = sizeof($column_of_matrix) - 1;						
				
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "radio") {
					$input_value = "";
					for($k = 1; $k <= $rows_count; $k++) {
					  $input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0) . "***";
					}
				  }
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "checkbox") {
					$input_value = "";							
					for($k = 1; $k <= $rows_count; $k++) {
					  for($j = 1; $j <= $columns_count; $j++) {
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : 0)."***";
					  }
					}
				  }
				  
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "text") {
					$input_value = "";
					for($k = 1; $k <= $rows_count; $k++) {
					  for($j = 1; $j <= $columns_count; $j++) {
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? esc_html($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) : "") . "***";
					  }
					}
				  }
				  
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "select") {
					$input_value = "";
					for($k = 1; $k <= $rows_count; $k++) {
					  for($j = 1; $j <= $columns_count; $j++) {
						$input_value .= (isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : "") . "***";	
					  }
					}
				  }
				  
				  $value = $rows_count . (isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "") . '***' . $columns_count . (isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "") . '***' . (isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : "") . '***' . $input_value . '***matrix***';
				  break;
				}
				
			  }

			  if($type == "type_address") {
				if(	$value == '*#*#*#') {
				  continue;
				}
			  }
			  if($type == "type_text" or $type == "type_password" or $type == "type_textarea" or $type == "type_name" or $type == "type_submitter_mail" or $type == "type_number" or $type == "type_phone" or $type == "type_phone_new")
			  {		

				$untilupload = $form->form_fields;
				$untilupload = substr($untilupload, strpos($untilupload, $i.'*:*id*:*'.$type), -1);
				$untilupload = substr($untilupload, 0, strpos($untilupload, '*:*new_field*:'));
				$untilupload = explode('*:*w_required*:*', $untilupload);
				$untilupload = $untilupload[1];
				$untilupload = explode('*:*w_unique*:*', $untilupload);
				$unique_element = $untilupload[0];
				if(strlen($unique_element)>3)
				$unique_element = substr($unique_element, -3);

				if($unique_element == 'yes') {						
				  $unique = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "formmaker_submits WHERE form_id= %d  and element_label= %s and element_value= %s", $id, $i, addslashes($value)));
				  if ($unique) {
					return array(($max + 1), addslashes(addslashes(__('This field ' . $label_label[$key] . ' requires a unique entry.', 'form_maker'))));
				  }
				}
			  }
			  $save_or_no = TRUE;
			  $fvals['{'.$i.'}']=str_replace(array("***map***", "*@@url@@*", "@@@@@@@@@", "@@@", "***grading***", "***br***"), array(" ", "", " ", " ", " ", ", "), addslashes($value));
			  
			  if ($form->savedb) {
				$save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
				  'form_id' => $id,
				  'element_label' => $i,
				  'element_value' => stripslashes($value),
				  'group_id' => ($max + 1),
				  'date' => date('Y-m-d H:i:s'),
				  'ip' => $_SERVER['REMOTE_ADDR'],
				  'user_id_wd' => $current_user->ID,
				), array(
				  '%d',
				  '%s',
				  '%s',
				  '%d',
				  '%s',
				  '%s',
				  '%d'
				));
				}
				if (!$save_or_no) {
					return FALSE;
				}
				$chgnac = FALSE;
			}
			else{
				$fvals['{'.$i.'}']='';
			}
		}

		$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."formmaker_submits" );
		$user_fields = array("subid"=>$subid, "ip"=>$ip, "userid"=>$wp_userid, "username"=>$wp_username, "useremail"=>$wp_useremail);

		$queries = $wpdb->get_results( $wpdb->prepare("SELECT * FROM " .$wpdb->prefix. "formmaker_query WHERE form_id=%d",(int)$id ));
		if($queries)
		{
			foreach($queries as $query)
			{
				$temp		= explode('***wdfcon_typewdf***',$query->details);
				$con_type	= $temp[0];
				$temp		= explode('***wdfcon_methodwdf***',$temp[1]);
				$con_method	= $temp[0];
				$temp		= explode('***wdftablewdf***',$temp[1]);
				$table_cur	= $temp[0];
				$temp		= explode('***wdfhostwdf***',$temp[1]);
				$host		= $temp[0];
				$temp		= explode('***wdfportwdf***',$temp[1]);
				$port		= $temp[0];
				$temp		= explode('***wdfusernamewdf***',$temp[1]);
				$username	= $temp[0];
				$temp		= explode('***wdfpasswordwdf***',$temp[1]);
				$password	= $temp[0];
				$temp		= explode('***wdfdatabasewdf***',$temp[1]);
				$database	= $temp[0];
				
				$query=str_replace(array_keys($fvals), $fvals ,$query->query);		
				foreach($user_fields as $user_key=>$user_field)
					$query=str_replace('{'.$user_key.'}', $user_field , $query);		
		
				if($con_type == 'remote')
				{ 
					$wpdb_temp = new wpdb($username, $password, $database, $host);
					$wpdb_temp->query($query);				
				}
				else {
          $wpdb->query($query);
        }
			}
      // $wpdb= new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		}
		
	$addons = array('WD_FM_MAILCHIMP' => 'MailChimp', 'WD_FM_REG' => 'Registration');
	foreach($addons as $addon => $addon_name)
	{	
		if (defined($addon) && is_plugin_active(constant($addon))) {
			$_GET['addon_task']='frontend';
			$_GET['form_id']=$id;
			$GLOBALS['fvals']=$fvals;
			do_action($addon.'_init');
		}				
	}
  
  $addons = array('WD_FM_STRIPE' => 'Stripe');
    foreach ($addons as $addon => $addon_name) {
        if (defined($addon) && is_plugin_active(constant($addon))) {
            $_GET['addon_task'] = 'pay_with_stripe';
            $_GET['form_id'] = $id;      
           
            $GLOBALS['wdstripe_stripeToken'] = isset($_POST['stripeToken'.$id]) ? $_POST['stripeToken'.$id] : '';
            
            if ($is_amount && isset($_POST['stripeToken'.$id])) {
                $wdstripe_products_data = new stdClass();
                $tax = $form->tax;
                $total = $total + ($total * $tax) / 100;
                $shipping = isset($paypal['shipping']) ? $paypal['shipping'] : 0;
                $total = $total + $shipping;
                $total = round($total, 2);
                $wdstripe_products_data->currency = $form->payment_currency;
                $wdstripe_products_data->amount = $total;
                $wdstripe_products_data->shipping = $shipping;
                $wdstripe_products_data = json_encode($wdstripe_products_data);
                $GLOBALS['wdstripe_products_data'] = $wdstripe_products_data;
                do_action($addon . '_init');
                if (isset($GLOBALS['wdstripe_error'])) {
                  return array($max + 1);
                }
            }
        }
    }
	
    $str = '';
    
    if ($form->paypal_mode)	{
      if ($paypal['item_name'])	{
        if ($is_amount)	{
          $tax = $form->tax;
          $currency = $form->payment_currency;
          $business = $form->paypal_email;
          $ip = $_SERVER['REMOTE_ADDR'];       
          $total2 = round($total, 2);
          $save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
            'form_id' => $id,
            'element_label' => 'item_total',
            'element_value' => $form_currency . $total2,
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
          if (!$save_or_no) {
            return false;
          }
          $total = $total + ($total * $tax) / 100;
          if (isset($paypal['shipping'])) {
            $total = $total + $paypal['shipping'];
          }
          $total = round($total, 2);        
          $save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
            'form_id' => $id,
            'element_label' => 'total',
            'element_value' => $form_currency . $total,
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
          if (!$save_or_no) {
            return false;
          }
          $save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
            'form_id' => $id,
            'element_label' => '0',
            'element_value' => 'In progress',
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
          if (!$save_or_no) {
            return false;
          }
          $str = '';
          
		if ($form->checkout_mode==1 || $form->checkout_mode == "production") {
           $str .= "https://www.paypal.com/cgi-bin/webscr?";
         }
         else {
           $str .= "https://www.sandbox.paypal.com/cgi-bin/webscr?";
         }
          $str .= "currency_code=" . $currency;
          $str .= "&business=" . urlencode($business);
          $str .= "&cmd=" . "_cart";
          $str .= "&notify_url=" . admin_url('admin-ajax.php?action=checkpaypal%26form_id=' . $id . '%26group_id=' . ($max + 1));
          $str .= "&upload=" . "1";
          $str .= "&charset=UTF-8";
          if (isset($paypal['shipping'])) {
            $str = $str . "&shipping_1=" . $paypal['shipping'];
            //	$str=$str."&weight_cart=".$paypal['shipping'];
            //	$str=$str."&shipping2=3".$paypal['shipping'];
            $str = $str."&no_shipping=2";
          }
          $i=0;
          foreach ($paypal['item_name'] as $pkey => $pitem_name) {
            if($paypal['amount'][$pkey]) {
              $i++;
              $str = $str."&item_name_".$i."=".urlencode($pitem_name);
              $str = $str."&amount_".$i."=".$paypal['amount'][$pkey];
              $str = $str."&quantity_".$i."=".$paypal['quantity'][$pkey];
              if ($tax) {
                $str = $str . "&tax_rate_" . $i . "=" . $tax;
              }
              if ($paypal['on_os'][$pkey]) {
                foreach ($paypal['on_os'][$pkey]['on'] as $on_os_key => $on_item_name) {
                  $str = $str."&on".$on_os_key."_".$i."=".$on_item_name;
                  $str = $str."&os".$on_os_key."_".$i."=".$paypal['on_os'][$pkey]['os'][$on_os_key];
                }
              }
            }
          }
        }
      }
    }
	
	if($form->mail_verify){	
		unset($_SESSION['hash']);
		unset($_SESSION['gid']);
		$ip = $_SERVER['REMOTE_ADDR'];	
		$_SESSION['gid']  = $max+1;
		$send_tos = explode('**',$form->send_to);
		if($send_tos){
			foreach($send_tos as $send_index => $send_to)
			{
				$_SESSION['hash'][] = md5($ip.time().rand());
				$send_to = str_replace('*', '',$send_to);		
				$save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
					'form_id' => $id,
					'element_label' => 'verifyInfo@'.$send_to,
					'element_value' => $_SESSION['hash'][$send_index]."**".$form->mail_verify_expiretime."**".$send_to,
					'group_id' => ($max + 1),
					'date' => date('Y-m-d H:i:s'),
					'ip' => $ip,
					'user_id_wd' => $current_user->ID,
				  ), array(
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%d'
				  ));
				if (!$save_or_no) {
					return false;
				}  
			}
		}
	}
	
	
    if ($chgnac) {
		global $wpdb;
		if ($form->submit_text_type != 4) {
			$_SESSION['massage_after_submit' . $id] = addslashes(addslashes(__('Nothing was submitted.', 'form_maker')));
		}
		$_SESSION['error_or_no' . $id] = 1;
		$_SESSION['form_submit_type' . $id] = $form->submit_text_type . "," . $form->id;
		wp_redirect($_SERVER["REQUEST_URI"]);
		exit;
    }

    $addons = array('WD_FM_GDRIVE_INT' => 'GDriveInt', 'WD_FM_DBOX_INT' => 'DboxInt', 'WD_FM_POST_GEN' => 'PostGen', 'WD_FM_PUSHOVER' => 'Pushover'); // the sequence is important for google drive and drop box addons !!!!!!!!!!
    foreach($addons as $addon => $addon_name) {	
        if (defined($addon) && is_plugin_active(constant($addon))) {
            $_GET['addon_task'] = 'frontend';
            $_GET['form_id'] = $id;
            $GLOBALS['all_files'] = json_encode($all_files);
            $GLOBALS['form_currency'] = $form_currency;
            do_action($addon.'_init');
        }				
    }
    return array($all_files, $str);
  }

  public function remove($group_id) {
    global $wpdb;
    $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id= %d', $group_id));
  }
  
   public function get_after_submission_text($form_id) {
    global $wpdb;
	$submit_text = $wpdb->get_var("SELECT submit_text FROM " . $wpdb->prefix . "formmaker WHERE id='" . $form_id . "'");
	$current_user =  wp_get_current_user();
	if ($current_user->ID != 0){
		$userid =  $current_user->ID;
		$username =  $current_user->display_name;
		$useremail =  $current_user->user_email;
	} else{
		$userid =  '';
		$username = '';
		$useremail = '';
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."formmaker_submits" );	
	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id=%d", $form_id));

	$label_order_original = array();
	$label_order_ids = array();
	$submission_array = array();
  $label_all = explode('#****#',$row->label_order_current);
	$label_all = array_slice($label_all, 0, count($label_all) - 1);
	foreach ($label_all as $key => $label_each) {
		$label_id_each = explode('#**id**#', $label_each);
		$label_id = $label_id_each[0];
		array_push($label_order_ids, $label_id);
		$label_order_each = explode('#**label**#', $label_id_each[1]);
		$label_order_original[$label_id] = $label_order_each[0];
	}
	
	$submissions_row = $wpdb->get_results($wpdb->prepare("SELECT `element_label`, `element_value` FROM " . $wpdb->prefix . "formmaker_submits WHERE form_id=%d AND group_id=%d", $form_id, $subid));
	foreach ($submissions_row as $sub_row){
		$submission_array[$sub_row->element_label] = $sub_row->element_value;
	}

	foreach($label_order_original as $key => $label_each) {
		if(strpos($submit_text, "%".$label_each."%")>-1)	 {				
			$submit_text = str_replace("%".$label_each."%", $submission_array[$key], $submit_text);
		}
	}
	
	$custom_fields = array( "subid"=>$subid, "ip"=>$ip, "userid"=>$userid, "username"=>$username, "useremail"=>$useremail);	
	foreach($custom_fields as $key=>$custom_field)
	{
		if(strpos($submit_text, "%".$key."%")>-1)
			$submit_text = str_replace("%".$key."%", $custom_field, $submit_text);
	}
	$submit_text = str_replace(array("***map***", "*@@url@@*", "@@@@@@@@@", "@@@", "***grading***", "***br***", "***star_rating***"), array(" ", "", " ", " ", " ", ", ", " "), $submit_text);
	
    return $submit_text;
  }
  
  public function increment_views_count($id) {
    global $wpdb;
    $vives_form = $wpdb->get_var($wpdb->prepare("SELECT views FROM " . $wpdb->prefix . "formmaker_views WHERE form_id=%d", $id));
    if (isset($vives_form)) {
    $vives_form = $vives_form + 1;
    $wpdb->update($wpdb->prefix . "formmaker_views", array(
        'views' => $vives_form,
      ), array('form_id' => $id), array(
        '%d',
      ), array('%d'));
    }
    else {
      $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
        'form_id' => $id,
        'views' => 1
        ), array(
          '%d',
          '%d'
      ));
    }
  }

	public function gen_mail($counter, $all_files, $id, $str) {
            // checking save uploads option
            global $wpdb;           
            $save_uploads = $wpdb->get_var("SELECT save_uploads FROM " . $wpdb->prefix ."formmaker WHERE id=" . $id);
            if($save_uploads == 0){
                $destination = 'wp-content/uploads/tmpAddon';
                if(!file_exists($destination))
                    mkdir($destination , 0777);
            
                foreach($all_files as &$all_file){
                    $fileTemp = $all_file['tmp_name'];
                    $fileName = $all_file['name'];
                    if(!move_uploaded_file($fileTemp, ABSPATH . $destination . '/' . $fileName)) {	
                        return array($max+1, addslashes(__('Error, file cannot be moved.', 'form_maker')));
                    }
                    
                    $all_file['tmp_name'] = $destination . "/" . $fileName;
                }
            }
		$ip = $_SERVER['REMOTE_ADDR'];
		$replyto = '';
		global $wpdb;
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id=%d", $id));
		if (!$row->form_front) {
			$id = '';
		}

		$custom_fields = array('ip', 'useremail', 'username', 'subid', 'all' );
		$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."formmaker_submits" );
		
		$current_user =  wp_get_current_user();
		if ($current_user->ID != 0)
		{
			$username =  $current_user->display_name;
			$useremail =  $current_user->user_email;
		}
		else
		{
			$username = '';
			$useremail = '';
		}
		
		$label_order_original = array();
		$label_order_ids = array();
		$label_label = array();
		$label_type = array();
		$total = 0;
		$form_currency = '$';
		$currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
		if ($row->payment_currency) {
		  $form_currency = $currency_sign[array_search($row->payment_currency, $currency_code)];
		}

		$cc = array();
		$row_mail_one_time = 1;
		$label_type = array();
    $label_all	= explode('#****#',$row->label_order_current);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			$label_id = $label_id_each[0];
			array_push($label_order_ids, $label_id);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			$label_order_original[$label_id] = $label_order_each[0];
			$label_type[$label_id] = $label_order_each[1];
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}

		$disabled_fields = explode(',', isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : "");
		$disabled_fields = array_slice($disabled_fields,0, count($disabled_fields)-1);   
		
		$list='<table border="1" cellpadding="3" cellspacing="0" style="width:600px;">';
		$list_text_mode = '';
    foreach($label_order_ids as $key => $label_order_id) {
      $i = $label_order_id;
      $type = $label_type[$i];

      if($type != "type_map" and  $type != "type_submit_reset" and  $type != "type_editor" and  $type != "type_captcha" and $type != "type_arithmetic_captcha" and  $type != "type_recaptcha" and  $type != "type_button") {	
        $element_label=$label_order_original[$i];
        if(!in_array($i,$disabled_fields)) {
          switch ($type) {
            case 'type_text':
            case 'type_password':
            case "type_date":
            case "type_date_new":
            case "type_own_select":					
            case "type_country":				
            case "type_number": 
							case "type_phone_new": 
            {
              $element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }	
            
              break;
            }
            case "type_date_range":{
              $element0 = isset($_POST['wdform_'.$i."_element".$id."0"]) ? $_POST['wdform_'.$i."_element".$id."0"] : NULL;
              $element1 = isset($_POST['wdform_'.$i."_element".$id."1"]) ? $_POST['wdform_'.$i."_element".$id."1"] : NULL;
              
              if(isset($element0) &&  $this->empty_field($element0, $row->mail_emptyfields) && $this->empty_field($element1, $row->mail_emptyfields)) {
                $element = $element0.' - '.$element1;
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }	
            
              break;
            }
            case 'type_textarea': {
              $element = isset($_POST['wdform_'.$i."_element".$id]) ? wpautop($_POST['wdform_'.$i."_element".$id]) : NULL;
              
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }	
              break;
            }
            
            case "type_hidden": {
              $element = isset($_POST[$element_label]) ? $_POST[$element_label] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }
              break;
            }
            case "type_mark_map": {
              $element = isset($_POST['wdform_'.$i."_long".$id]) ? $_POST['wdform_'.$i."_long".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>Longitude:' . $element . '<br/>Latitude:' . (isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : "") . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - Longitude:'.$element.' Latitude:'.(isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : "")."\r\n";
              }
              break;		
            }
            case "type_submitter_mail": {
              $element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }
              break;		
            }						
            
            case "type_time": {							
              $hh = isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : NULL;
              if(isset($hh) && ($this->empty_field($hh, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_mm".$id], $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_ss".$id], $row->mail_emptyfields))) {
                $ss = isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL;
                if(isset($ss)) {
                  $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . $ss;
                  $list_text_mode=$list_text_mode.$element_label.' - '.$hh.':'.(isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "").':'.$ss;
                }
                else {
                  $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
                  $list_text_mode=$list_text_mode.$element_label.' - '.$hh.':'.(isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
                }
                $am_pm = isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL;
                if(isset($am_pm)) {
                  $list = $list . ' ' . $am_pm . '</td></tr>';
                  $list_text_mode=$list_text_mode.$am_pm."\r\n";
                }
                else {
                  $list = $list.'</td></tr>';
                  $list_text_mode=$list_text_mode."\r\n";
                }
              }								
              break;
            }
            
            case "type_phone": {
              $element_first = isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : NULL;
              if(isset($element_first) && $this->empty_field($element_first, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element_first . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "") . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element_first.' '.(isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "")."\r\n";
              }	
              break;
            }
            
            case "type_name": {
              $element_first = isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : NULL;
              if(isset($element_first)) {
                $element_title = isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : NULL;
                $element_middle = isset($_POST['wdform_'.$i."_element_middle".$id]) ? esc_html($_POST['wdform_'.$i."_element_middle".$id]) : NULL;
                if((isset($element_title) || isset($element_middle))  && ($this->empty_field($element_title, $row->mail_emptyfields) || $this->empty_field($element_first, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_element_last".$id], $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_element_middle".$id], $row->mail_emptyfields))) {
                  $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . (isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : '') . ' ' . $element_first . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "") . ' ' . (isset($_POST['wdform_'.$i."_element_middle".$id]) ? $_POST['wdform_'.$i."_element_middle".$id] : "") . '</td></tr>';
                  $list_text_mode=$list_text_mode.$element_label.' - '.(isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : '').' '.$element_first.' '.(isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "").' '.(isset($_POST['wdform_'.$i."_element_middle".$id]) ? $_POST['wdform_'.$i."_element_middle".$id] : "")."\r\n";
                }
                else {
                  if($this->empty_field($element_first, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_element_last".$id], $row->mail_emptyfields)) {
                    $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element_first . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "") . '</td></tr>';
                    $list_text_mode=$list_text_mode.$element_label.' - '.$element_first.' '.(isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "")."\r\n";
                  }
                }
              }	   
              break;		
            }
            
            case "type_address": {
              $element = isset($_POST['wdform_'.$i."_street1".$id]) ? $_POST['wdform_'.$i."_street1".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
                break;
              }
              $element = isset($_POST['wdform_'.$i."_street2".$id]) ? $_POST['wdform_'.$i."_street2".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
                break;
              }
              $element = isset($_POST['wdform_'.$i."_city".$id]) ? $_POST['wdform_'.$i."_city".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
                break;
              }
              $element = isset($_POST['wdform_'.$i."_state".$id]) ? $_POST['wdform_'.$i."_state".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
                break;
              }
              $element = isset($_POST['wdform_'.$i."_postal".$id]) ? $_POST['wdform_'.$i."_postal".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
                break;
              }
              $element = isset($_POST['wdform_'.$i."_country".$id]) ? $_POST['wdform_'.$i."_country".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
                break;
              }
              break;							
            }
            
            case "type_date_fields": {
              $day = isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : NULL;
              $month = isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : "";
              $year = isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : "";
              if(isset($day) && $this->empty_field($day, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' .(($day || $month || $year) ? $day . '-' . $month . '-' . $year : '' ). '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.(($day || $month || $year) ? $day.'-'.$month.'-'.$year : '')."\r\n";
              }
              break;
            }
            
            case "type_radio": {
              $element = isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
                break;
              }								
              $element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }
              break;	
            }	
            
            case "type_checkbox": {
              $start = -1;
              for($j = 0; $j < 100; $j++) {
                $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
                if(isset($element)) {
                  $start = $j;
                  break;
                }
              }								
              $other_element_id = -1;
              $is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
              if($is_other == "yes") {
                $other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
              }
      
              if($start != -1 || ($start == -1 && $row->mail_emptyfields))
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';
                $list_text_mode=$list_text_mode.$element_label.' - '; 
              }
              
              if($start != -1) {
                for($j = $start; $j < 100; $j++) {									
                  $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
                  if(isset($element)) {
                    if($j == $other_element_id) {
                      $list = $list . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '<br>';
                      $list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "").', ';	
                    }
                    else {									
                      $list = $list . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '<br>';
                      $list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "").', ';
                    }
                  }
                }
              }
              
              if($start != -1 || ($start == -1 && $row->mail_emptyfields))
              {
                $list = $list . '</td></tr>';
                $list_text_mode=$list_text_mode."\r\n";
              }	
              break;
            }
            
            case "type_paypal_price":	{
              $value = 0;
              if(isset($_POST['wdform_'.$i."_element_dollars".$id])) {
                $value = $_POST['wdform_'.$i."_element_dollars".$id];
              }
              if(isset($_POST['wdform_'.$i."_element_cents".$id]) && $_POST['wdform_'.$i."_element_cents".$id]) {
                $value = $value . '.' . $_POST['wdform_'.$i."_element_cents".$id];
              }
            
              if($this->empty_field($value, $row->mail_emptyfields) && $value!='.')
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . $form_currency . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$value.$form_currency."\r\n";
              }	
              break;
            }		

            case "type_paypal_price_new":	{
              $value = 0;
              if(isset($_POST['wdform_'.$i."_element".$id])) {
                $value = $_POST['wdform_'.$i."_element".$id];
              }
          
              if($this->empty_field($value, $row->mail_emptyfields) && $value!='.')
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . ($value == '' ? '' : $form_currency) . $value . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$value.$form_currency."\r\n";
              }	
              break;
            }			
          
            case "type_paypal_select": {
              $value='';
              if(isset($_POST['wdform_'.$i."_element_label".$id]) && $_POST['wdform_'.$i."_element".$id] != '') {
                $value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $form_currency . $_POST['wdform_'.$i."_element".$id];
              }
              
              $element_quantity_label = (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) && $_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL;
              $element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
              if($value != ''){

                if(isset($element_quantity)) {
                  $value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
                }
                
                for($k = 0; $k < 50; $k++) {
                  $temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
                  if(isset($temp_val)) {			
                    $value .= '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
                  }
                }
              }	

              if($this->empty_field($value, $row->mail_emptyfields))
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";
              }	
              
              break;
            }
          
            case "type_paypal_radio": {
              if(isset($_POST['wdform_'.$i."_element".$id])) {
                $value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $form_currency . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "");
              
                $element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL;
                $element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
                if (isset($element_quantity)) {
                  $value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
                }
                for($k = 0; $k < 50; $k++) {
                  $temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
                  if(isset($temp_val)) {
                    $value .= '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
                  }
                }
              }
              else {
                $value='';
              }

              if($this->empty_field($value, $row->mail_emptyfields))		
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";
              }
              break;	
            }

            case "type_paypal_shipping": {						
              if(isset($_POST['wdform_'.$i."_element".$id])) {
                $value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $form_currency . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "");
                
                if($this->empty_field($value, $row->mail_emptyfields))		
                {
                  $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
                  $list_text_mode=$list_text_mode.$element_label.' - '.$value."\r\n";
                }	
              }
              else {
                $value='';
              }	
              
              break;
            }

            case "type_paypal_checkbox": {
                            
              $start = -1;
              for($j = 0; $j < 300; $j++) {
                $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
                if(isset($element)) {
                  $start=$j;
                  break;
                }
              }	
            
              if($start != -1 || ($start == -1 && $row->mail_emptyfields))
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';		
                $list_text_mode=$list_text_mode.$element_label.' - ';  
              }
              if($start!=-1) {
                for($j = $start; $j < 300; $j++) {									
                  $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
                  if(isset($element)) {
                    $list = $list . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "") . ' - ' . $form_currency . ($element == '' ? '0' : $element) . '<br>';
                    $list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "").' - '.($element == '' ? '0' . $form_currency : $element).$form_currency.', ';
                  }
                }
              }
              $element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL;
              $element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
              if (isset($element_quantity)) {
                $list = $list . '<br/>' . $element_quantity_label . ': ' . $element_quantity;
                $list_text_mode=$list_text_mode.$element_quantity_label . ': ' . $element_quantity.', ';		
              }
              for($k = 0; $k < 50; $k++) {
                $temp_val = isset($_POST['wdform_'.$i."_element_property_value".$id.$k]) ? $_POST['wdform_'.$i."_element_property_value".$id.$k] : NULL;
                if(isset($temp_val)) {			
                  $list = $list . '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
                  $list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val.', ';	
                }
              }
              if($start != -1 || ($start == -1 && $row->mail_emptyfields))
              {
                $list = $list . '</td></tr>';
                $list_text_mode=$list_text_mode."\r\n";	
              }
              break;
            }
            
            case "type_paypal_total": {
              $element = isset($_POST['wdform_'.$i."_paypal_total".$id]) ? $_POST['wdform_'.$i."_paypal_total".$id] : "";
              if($this->empty_field($element, $row->mail_emptyfields))		
              {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }	
              break;
            }
          
            case "type_star_rating": {
              $element = isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : NULL;
              $selected = isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : 0;
              if(isset($element) && $this->empty_field($selected, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $selected . '/' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";
              }
              break;
            }
            
            case "type_scale_rating": {
              $element = isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : NULL;
              $selected = isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0;
              if(isset($element) && $this->empty_field($selected, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $selected . '/' . $element . '</td></tr>';	
                $list_text_mode=$list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";
              }
              break;
            }
            
            case "type_spinner": {
              $element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }
              break;
            }
            
            case "type_slider": {
              $element = isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : NULL;
              if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }
              break;
            }
            
            case "type_range": {
              $element0 = isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : NULL;
              $element1 = isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : NULL;
              if((isset($element0) && $this->empty_field($element0, $row->mail_emptyfields)) || (isset($element1) && $this->empty_field($element1, $row->mail_emptyfields))) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >From:' . $element0 . '<span style="margin-left:6px">To</span>:' . $element1 . '</td></tr>';					
                $list_text_mode=$list_text_mode.$element_label.' - From:'.$element0.' To:'.$element1."\r\n";
              }
              break;
            }
            
            case "type_grading": {
              $element = isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "";
              $grading = explode(":", $element);
              $items_count = sizeof($grading) - 1;							
              $element = "";
              $total = "";	
              $form_empty_field = 1;
              for($k = 0;$k < $items_count; $k++) {
                $element .= $grading[$k] . ":" . (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : "") . " ";
                $total += (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : 0);
                if(isset($_POST['wdform_'.$i."_element".$id.'_'.$k]))
                  $form_empty_field = 0;
              }
              $element .= "Total:" . $total;
              if(isset($element) && $this->empty_field($form_empty_field, $row->mail_emptyfields)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
                $list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
              }
              break;
            }
          
            case "type_matrix": {
              $input_type = isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : "";
              $mat_rows = explode("***", isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "");
              $rows_count = sizeof($mat_rows) - 1;
              $mat_columns = explode("***", isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "");
              $columns_count = sizeof($mat_columns) - 1;
              $matrix = "<table>";
              $matrix .= '<tr><td></td>';							
              for($k = 1; $k < count($mat_columns); $k++) {
                $matrix .= '<td style="background-color:#BBBBBB; padding:5px; ">' . $mat_columns[$k] . '</td>';
              }
              $matrix .= '</tr>';							
              $aaa = Array();							
              for($k = 1; $k <= $rows_count; $k++) {
                $matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px;">' . $mat_rows[$k] . '</td>';
                if($input_type == "radio") {
                  $mat_radio = isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0;
                  if($mat_radio == 0) {
                    $checked = "";
                    $aaa[1] = "";
                  }
                  else {
                    $aaa = explode("_", $mat_radio);
                  }
                  for($j = 1; $j <= $columns_count; $j++) {
                    if($aaa[1] == $j) {
                      $checked = "checked";
                    }
                    else {
                      $checked = "";
                    }
                    $sign = $checked == 'checked' ? '&#x2714;' : '';
                    $matrix .= '<td style="text-align:center">'.$sign.'</td>';
                  }
                }
                else {
                  if($input_type == "checkbox") {                
                    for($j = 1; $j <= $columns_count; $j++) {
                      $checked = isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : "";
                      if($checked == 1) {
                        $checked = "checked";
                      }
                      else {
                        $checked = "";
                      }
                      $sign = $checked == 'checked' ? '&#x2714;' : '';
                      $matrix .= '<td style="text-align:center">'.$sign.'</td>';								
                    }								
                  }
                  else {
                    if($input_type == "text") {																  
                      for($j = 1; $j <= $columns_count; $j++) {
                        $checked = isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? esc_html($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) : "";
                        $matrix .= '<td style="text-align:center"><input  type="text" value="' . $checked . '" disabled /></td>';
                      }										
                    }
                    else {
                      for($j = 1; $j <= $columns_count; $j++) {
                        $checked = isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : "";
                        $matrix .= '<td style="text-align:center">' . $checked . '</td>';
                      }
                    }									
                  }									
                }
                $matrix .= '</tr>';							
              }
              $matrix .= '</table>';	
              if(isset($matrix)) {
                $list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $matrix . '</td></tr>';
              }						
              break;
            }
            default: break;
          }
        }
      }				
    }

    $list = $list . '</table>';
    if($row->sendemail) {
      $fromname = $row->mail_from_name_user;        
      if($row->mail_subject_user)	
        $subject 	= $row->mail_subject_user;
      else
        $subject 	= $row->title;
      if($row->reply_to_user) {
        $replyto = $row->reply_to_user;
      }
      $attachment_user = array(); 	
      if ($row->mail_attachment_user) {
        for ($k = 0; $k < count($all_files); $k++) {
          if (isset($all_files[$k]['tmp_name'])) {
            if(!isset($attachment_user[$all_files[$k]['field_key']]))
              $attachment_user[$all_files[$k]['field_key']] = array();
            array_push($attachment_user[$all_files[$k]['field_key']], $all_files[$k]['tmp_name']);
          }
        }
      }
  
      if ($row->mail_mode_user) {
        $content_type = "text/html";
        $mode = 1;
        $list_user = wordwrap($list, 100, "\n", true);
        $new_script = wpautop($row->script_mail_user);
      }	
      else {
        $content_type = "text/plain";
        $mode = 0; 
        $list_user = wordwrap($list_text_mode, 1000, "\n", true);
        $new_script = str_replace(array('<p>','</p>'),'',$row->script_mail_user);
      }
      
      foreach($label_order_original as $key => $label_each) {
        $type=$label_type[$key];
        $key1 = $type == 'type_hidden' ? $label_each : $key;
        if(strpos($new_script, "%".$label_each."%") > -1) {
          $new_value = $this->custom_fields_mail($type, $key1, $id, $attachment_user, $form_currency);	
          $new_script = str_replace("%".$label_each."%", $new_value, $new_script);	
        }
      
        if($type == "type_file_upload" && strpos($new_script, "%".$label_each."(link)%") > -1) {
          $new_value = $this->custom_fields_mail($type, $key, $id, $attachment_user, $form_currency, 1);	
          $new_script = str_replace("%".$label_each."(link)%", $new_value, $new_script);	
        }
        
        
        if(strpos($fromname, "%".$label_each."%")>-1) {	
          $new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', ''));		
          if(substr($new_value, -2)==', ') {
            $new_value = substr($new_value, 0, -2);
          }
          $fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
        }	
        
        if(strpos($subject, "%".$label_each."%")>-1) {	
          $new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', $form_currency));		
          if(substr($new_value, -2)==', ') {
            $new_value = substr($new_value, 0, -2);		
          }
          $subject = str_replace("%".$label_each."%", $new_value, $subject);							
        }
      }

      $recipient = '';
      $cca = $row->mail_cc_user;
      $bcc = $row->mail_bcc_user;
      if ($row->mail_from_user != '') {
        if ($fromname != '') {
          $from = "From: '" . $fromname . "' <" . $row->mail_from_user . ">" . "\r\n";
        }	
        else {
          $from = "From: '' <" . $row->mail_from_user . ">" . "\r\n";
        }
      }
      else {
        $from = '';
      }
      
      $headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
      if ($replyto) {
        $headers .= "Reply-To: <" . $replyto . ">\r\n";
      }
      if ($cca) {
        $headers .= "Cc: " . $cca . "\r\n";          
      }
      if ($bcc) {
        $headers .= "Bcc: " . $bcc . "\r\n";          
      }

      $custom_fields_value = array( $ip, $useremail, $username, $subid, $list_user );	
      foreach($custom_fields as $key=>$custom_field)
      {
        if(strpos($new_script, "%".$custom_field."%")>-1)
        $new_script = str_replace("%".$custom_field."%", $custom_fields_value[$key], $new_script);

        if($key==2 || $key==3)
        {
          if(strpos($fromname, "%".$custom_field."%")>-1)
            $fromname = str_replace("%".$custom_field."%", $custom_fields_value[$key], $fromname);
            
          if(strpos($subject, "%".$custom_field."%")>-1)
            $subject = str_replace("%".$custom_field."%", $custom_fields_value[$key], $subject);
        }
      }
      $body = $new_script;
      $GLOBALS['attachment_user'] = '';
      $GLOBALS['attachment'] = '';
      if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))) {
        $_GET['addon_task'] = 'frontend';
        $_GET['form_id'] = $id;
        $_GET['form_currency'] = $form_currency;
        $GLOBALS['custom_fields_value'] = $custom_fields_value;
        do_action('WD_FM_PDF_init');
      }
      
      if($GLOBALS['attachment_user']){
        array_push($attachment_user, $GLOBALS['attachment_user']);
      }
      
      if($row->send_to) {
        $send_tos = explode('**',$row->send_to);
        $send_copy = isset($_POST["wdform_send_copy_".$id]) ? $_POST["wdform_send_copy_".$id] : NULL;
        if(isset($send_copy)) {
          $send=true;
        }
        else {
          $mail_verification_post_id = (int)$wpdb->get_var($wpdb->prepare('SELECT mail_verification_post_id FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
          $verification_link = get_post( $mail_verification_post_id );
          foreach($send_tos as $index => $send_to) {
            $recipient = isset($_POST['wdform_'.str_replace('*', '', $send_to)."_element".$id]) ? $_POST['wdform_'.str_replace('*', '', $send_to)."_element".$id] : NULL;
            if(strpos($new_script, "%Verification link%")>-1 && $verification_link !== NULL) {
              $ver_link = $row->mail_mode_user ? "<a href =".add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'][$index].'@'.str_replace("*", "", $send_to)), get_post_permalink($mail_verification_post_id)).">".add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'][$index].'@'.str_replace("*", "", $send_to)), get_post_permalink($mail_verification_post_id))."</a><br/>" : add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'][$index].'@'.str_replace("*", "", $send_to)), get_post_permalink($mail_verification_post_id));
              
              $body = $row->mail_verify ? str_replace("%Verification link%", $ver_link, $new_script) : str_replace("%Verification link%", '', $new_script);
            }
            
          
            
            if($recipient) {
              $remove_parrent_array_user = new RecursiveIteratorIterator(new RecursiveArrayIterator($attachment_user));
              
              
              
              $attachment_user = iterator_to_array($remove_parrent_array_user, false);
              $send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($body), $headers, $attachment_user);
            }
          }
        }
      }
    }
    
    if($row->sendemail) {
      if($row->reply_to) {
        $replyto = isset($_POST['wdform_'.$row->reply_to."_element".$id]) ? $_POST['wdform_'.$row->reply_to."_element".$id] : NULL;
        if(!isset($replyto)) {
          $replyto = $row->reply_to;
        }
      }
      $recipient = $row->mail;
      if($row->mail_subject) {
        $subject 	= $row->mail_subject;
      }
      else {
        $subject 	= $row->title;
      }

      $fromname = $row->from_name;
      $attachment = array(); 
      if ($row->mail_attachment) {
        for ($k = 0; $k < count($all_files); $k++) {
          if (isset($all_files[$k]['tmp_name'])) {
            if(!isset($attachment[$all_files[$k]['field_key']]))
              $attachment[$all_files[$k]['field_key']] = array();
            array_push($attachment[$all_files[$k]['field_key']], $all_files[$k]['tmp_name']);
          }
        }
      }
      if($GLOBALS['attachment']){
        array_push($attachment, $GLOBALS['attachment']);
      }
      
      if ($row->mail_mode) {
        $content_type = "text/html";
        $mode = 1; 
        $list = wordwrap($list, 100, "\n", true);
        $new_script = wpautop($row->script_mail);
      }	
      else {
        $content_type = "text/plain";
        $mode = 0; 
        $list = $list_text_mode;
        $list = wordwrap($list, 1000, "\n", true);
        $new_script = str_replace(array('<p>','</p>'),'',$row->script_mail);
      }

    
      foreach($label_order_original as $key => $label_each) {							
        $type=$label_type[$key];
        $key1 = $type == 'type_hidden' ? $label_each : $key;
        if(strpos($new_script, "%".$label_each."%") > -1) {
          $new_value = $this->custom_fields_mail($type, $key1, $id, $attachment, $form_currency);	
          $new_script = str_replace("%".$label_each."%", $new_value, $new_script);	
        }
        
        if($type == "type_file_upload" && strpos($new_script, "%".$label_each."(link)%") > -1) {
            
          $new_value = $this->custom_fields_mail($type, $key, $id, $attachment, $form_currency, 1);	
          $new_script = str_replace("%".$label_each."(link)%", $new_value, $new_script);	
        }

        if(strpos($fromname, "%".$label_each."%")>-1) {
          $new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', $form_currency));		
          if(substr($new_value, -2)==', ') {
            $new_value = substr($new_value, 0, -2);
          }
          $fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
        }
        
        if(strpos($fromname, "%username%")>-1){
          $fromname = str_replace("%username%", $username, $fromname);
        }
    
        if(strpos($subject, "%".$label_each."%")>-1) {
          $new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', $form_currency));		
          if(substr($new_value, -2)==', ') {
            $new_value = substr($new_value, 0, -2);				
          }
          $subject = str_replace("%".$label_each."%", $new_value, $subject);							
        }
      }
    
      if ($row->from_mail) {
        $from = isset($_POST['wdform_'.$row->from_mail."_element".$id]) ? $_POST['wdform_'.$row->from_mail."_element".$id] : NULL;
        if (!isset($from)) {
          $from = $row->from_mail;
        }
        
        if ($fromname != '') {
          $from = "From: '" . $fromname . "' <" . $from . ">" . "\r\n";
        }	
        else {
          $from = "From: '' <" . $from . ">" . "\r\n";
        }
      }
      else {
        $from = "";
      }
        
      $cca = $row->mail_cc;
      $bcc = $row->mail_bcc;
      $headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
      if ($replyto) {
        $headers .= "Reply-To: <" . $replyto . ">\r\n";
      }
      if ($cca) {
        $headers .= "Cc: " . $cca . "\r\n";          
      }
      if ($bcc) {
        $headers .= "Bcc: " . $bcc . "\r\n";          
      }
    
      $custom_fields_value = array( $ip, $useremail, $username, $subid, $list );	
      foreach($custom_fields as $key=>$custom_field)
      {
        if(strpos($new_script, "%".$custom_field."%")>-1)
        $new_script = str_replace("%".$custom_field."%", $custom_fields_value[$key], $new_script);

        if($key==2 || $key==3)
        {
          if(strpos($fromname, "%".$custom_field."%")>-1)
            $fromname = str_replace("%".$custom_field."%", $custom_fields_value[$key], $fromname);
            
          if(strpos($subject, "%".$custom_field."%")>-1)
            $subject = str_replace("%".$custom_field."%", $custom_fields_value[$key], $subject);
        }
      }
      $admin_body = $new_script;
      $remove_parrent_array = new RecursiveIteratorIterator(new RecursiveArrayIterator($attachment));
      $attachment = iterator_to_array($remove_parrent_array, false);
      
      if($recipient) {
        $send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($admin_body), $headers, $attachment);
      }
    }
    
    $_SESSION['error_or_no' . $id] = 0;
    $msg = addslashes(__('Your form was successfully submitted.', 'form_maker'));
    $succes = 1;

    if($row->sendemail)
      if($row->mail || $row->send_to) {
        if ($send) {
          if ($send !== true ) {
            $_SESSION['error_or_no' . $id] = 1;
            $msg = addslashes(__('Error, email was not sent.', 'form_maker'));
            $succes = 0;
          }
          else {
            $_SESSION['error_or_no' . $id] = 0;
            $msg = addslashes(__('Your form was successfully submitted.', 'form_maker'));
          }
        }
      }
      
    $fm_email_params = $row->sendemail ? array('admin_body' => $admin_body, 'body' => $body, 'subject' => $subject, 'headers' => $headers, 'attachment' => $attachment, 'attachment_user' => $attachment_user) : array();
                     
    $addons = array('WD_FM_EMAIL_COND' => 'Email Conditions');
    $addons_array = array();
    foreach($addons as $addon => $addon_name) {	
      if (defined($addon) && is_plugin_active(constant($addon))) {
        $_GET['addon_task'] = 'frontend';
        $_GET['form_id'] = $id;
        $GLOBALS['fm_email_params'] = $fm_email_params;
        $GLOBALS['form_currency'] = $form_currency;
        $GLOBALS['custom_fields_value'] = isset($custom_fields_value) ? $custom_fields_value : array();
        do_action($addon.'_init');
      }				
    }
		// delete files from uploads (save_upload = 0)
		if($row->save_uploads == 0){
			foreach ($all_files as &$all_file) {
				if (file_exists(ABSPATH.'/'.$all_file['tmp_name'])) {
					unlink(ABSPATH.'/'.$all_file['tmp_name']);
				}
			}
			
		}
		$https = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');
		if ($row->submit_text_type == 0 || $row->submit_text_type == 1 || $row->submit_text_type == 3) {
      $_SESSION['massage_after_submit' . $id] = $msg;
      if($row->type == 'popover' || $row->type == 'topbar' || $row->type == 'scrollbox'){
        $_SESSION['fm_hide_form_after_submit' . $id] = 1;
      }
    }
		switch ($row->submit_text_type) {
			case "2":
			case "5": {
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				if ($row->article_id) {
				  $redirect_url = $row->article_id;
				}
				else {
				  $redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				}
				break;
			}
			case "3": {
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				break;
			}
			case "4": {
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				$redirect_url = $row->url;
				break;
			}
			default: {
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				break;
			}
		}
		if (!$str) {
			wp_redirect(html_entity_decode($redirect_url));
			exit;
		}
		else {
			$_SESSION['redirect_paypal'.$id] = 1;
		  
			$str .= "&return=" . urlencode($redirect_url);
			wp_redirect($str);
			exit;
		}
	}

	public static function custom_fields_mail($type, $key, $id, $attachment, $form_currency, $file_upload_link = 0)
	{
		$new_value ="";
		
		$disabled_fields	= explode(',', isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : "");
		$disabled_fields 	= array_slice($disabled_fields,0, count($disabled_fields)-1);
    
    if($type!="type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or $type != "type_arithmetic_captcha" or  $type!="type_recaptcha" or  $type!="type_button") {
      switch ($type) {
        case 'type_text':
        case 'type_password':
        case 'type_textarea':
        case "type_date":
		case "type_date_new":
        case "type_own_select":					
        case "type_country":				
        case "type_number": 
        {
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;	
          }
          break;
        }
		case 'type_date_range' :{
			$element0 = isset($_POST['wdform_'.$key."_element".$id."0"]) ? $_POST['wdform_'.$key."_element".$id."0"] : NULL;
			$element1 = isset($_POST['wdform_'.$key."_element".$id."1"]) ? $_POST['wdform_'.$key."_element".$id."1"] : NULL;
			$element = $element0.' - '.$element1;
			$new_value = $element;
		}
        case "type_file_upload": {
			if(isset($attachment[$key])) {
				foreach($attachment[$key] as $attach) {
					$uploadedFileNameParts = explode('.', $attach);
					$uploadedFileExtension = array_pop($uploadedFileNameParts);
					if($file_upload_link == 1){
						$new_value .= '<a href="'.site_url().'/'.$attach.'"/>'.site_url().'/'.$attach.'</a><br />';
					}
					else {
						$invalidFileExts = array('gif', 'jpg', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc', 'aiff', 'wbmp', 'xbm' );
						$extOk = false;

						foreach($invalidFileExts as $key => $valuee)
						{
							if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension) )) )
								$extOk = true;
						}
						
						if ($extOk == true) 
							$new_value .= '<img src="'.site_url().'/'.$attach.'" alt="'.$attach.'"/>';
						
					}
				}	
			} 
          	
			break;
        }
        case "type_hidden": {
          $element = isset($_POST[$key]) ? $_POST[$key] : NULL;
          if(isset($element)) {
            $new_value = $element;	
          }
          break;
        }
        case "type_mark_map": {
          $element = isset($_POST['wdform_'.$key."_long".$id]) ? $_POST['wdform_'.$key."_long".$id] : NULL;
          if(isset($element)) {
            $new_value = 'Longitude:' . $element . '<br/>Latitude:' . (isset($_POST['wdform_'.$key."_lat".$id]) ? $_POST['wdform_'.$key."_lat".$id] : "");
          }
          break;		
        }
        case "type_submitter_mail": {
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;					
          }
          break;		
        }								
        case "type_time": {
          $hh = isset($_POST['wdform_'.$key."_hh".$id]) ? $_POST['wdform_'.$key."_hh".$id] : NULL;
          if(isset($hh)) {
            $ss = isset($_POST['wdform_'.$key."_ss".$id]) ? $_POST['wdform_'.$key."_ss".$id] : NULL;
            if(isset($ss)) {
              $new_value = $hh . ':' . (isset($_POST['wdform_'.$key."_mm".$id]) ? $_POST['wdform_'.$key."_mm".$id] : "") . ':' . $ss;
            }
            else {
              $new_value = $hh . ':' . (isset($_POST['wdform_'.$key."_mm".$id]) ? $_POST['wdform_'.$key."_mm".$id] : "");
            }
            $am_pm = isset($_POST['wdform_'.$key."_am_pm".$id]) ? $_POST['wdform_'.$key."_am_pm".$id] : NULL;
            if(isset($am_pm)) {
              $new_value = $new_value . ' ' . $am_pm;
            }
          }
          break;
        }
        
        case "type_phone": {
          $element_first = isset($_POST['wdform_'.$key."_element_first".$id]) ? $_POST['wdform_'.$key."_element_first".$id] : NULL;
          if(isset($element_first)) {
              $new_value = $element_first . ' ' . (isset($_POST['wdform_'.$key."_element_last".$id]) ? $_POST['wdform_'.$key."_element_last".$id] : "");
          }	
          break;
        }								
        case "type_name": {
          $element_first = isset($_POST['wdform_'.$key."_element_first".$id]) ? $_POST['wdform_'.$key."_element_first".$id] : NULL;
          if(isset($element_first)) {
            $element_title = isset($_POST['wdform_'.$key."_element_title".$id]) ? $_POST['wdform_'.$key."_element_title".$id] : NULL;
            if(isset($element_title)) {
              $new_value = $element_title . ' ' . $element_first . ' ' . (isset($_POST['wdform_'.$key."_element_last".$id]) ? $_POST['wdform_'.$key."_element_last".$id] : "") . ' ' . (isset($_POST['wdform_'.$key."_element_middle".$id]) ? $_POST['wdform_'.$key."_element_middle".$id] : "");
            }
            else {
              $new_value = $element_first . ' ' . (isset($_POST['wdform_'.$key."_element_last".$id]) ? $_POST['wdform_'.$key."_element_last".$id] : "");
            }
          }	   
          break;		
        }								
        case "type_address": {
          $street1 = isset($_POST['wdform_'.$key."_street1".$id]) ? $_POST['wdform_'.$key."_street1".$id] : NULL;
          if(isset($street1)) {
            $new_value = $street1;
            break;
          }                  
          $street2 = isset($_POST['wdform_'.$key."_street2".$id]) ? $_POST['wdform_'.$key."_street2".$id] : NULL;
          if(isset($street2)) {
            $new_value = $street2;
            break;
          }
          $city = isset($_POST['wdform_'.$key."_city".$id]) ? $_POST['wdform_'.$key."_city".$id] : NULL;
          if(isset($city)) {
            $new_value = $city;
            break;
          }                  
          $state = isset($_POST['wdform_'.$key."_state".$id]) ? $_POST['wdform_'.$key."_state".$id] : NULL;
          if(isset($state)) {
            $new_value = $state;
            break;
          }
          $postal = isset($_POST['wdform_'.$key."_postal".$id]) ? $_POST['wdform_'.$key."_postal".$id] : NULL;
          if(isset($postal)) {
            $new_value = $postal;
            break;
          }
          $country = isset($_POST['wdform_'.$key."_country".$id]) ? $_POST['wdform_'.$key."_country".$id] : NULL;
          if(isset($country)) {
            $new_value = $country;
            break;
          }
          break;
        }
        case "type_date_fields": {
          $day = isset($_POST['wdform_'.$key."_day".$id]) ? $_POST['wdform_'.$key."_day".$id] : NULL;
          if(isset($day)) {
            $new_value = $day . '-' . (isset($_POST['wdform_'.$key."_month".$id]) ? $_POST['wdform_'.$key."_month".$id] : "") . '-' . (isset($_POST['wdform_'.$key."_year".$id]) ? $_POST['wdform_'.$key."_year".$id] : "");
          }
          break;
        }
        
        case "type_radio": {
          $element = isset($_POST['wdform_'.$key."_other_input".$id]) ? $_POST['wdform_'.$key."_other_input".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;
            break;
          }									
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;					
          }
          break;	
        }								
        case "type_checkbox": {
          $start = -1;
          for($j = 0; $j < 100; $j++) {
            $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
            if(isset($element)) {
              $start = $j;
              break;
            }
          }									
          $other_element_id = -1;
          $is_other = isset($_POST['wdform_'.$key."_allow_other".$id]) ? $_POST['wdform_'.$key."_allow_other".$id] : "";
          if($is_other == "yes") {
            $other_element_id = isset($_POST['wdform_'.$key."_allow_other_num".$id]) ? $_POST['wdform_'.$key."_allow_other_num".$id] : "";
          }
          if($start != -1) {
            for($j = $start; $j < 100; $j++) {											
              $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
              if(isset($element)) {
                if($j == $other_element_id) {
                  $new_value = $new_value . (isset($_POST['wdform_'.$key."_other_input".$id]) ? $_POST['wdform_'.$key."_other_input".$id] : "") . '<br>';
                }
                else {											
                  $new_value = $new_value . $element . '<br>';
                }
              }
            }										
          }
          break;
        }
        case "type_paypal_price": {		
          $new_value = 0;
          if(isset($_POST['wdform_'.$key."_element_dollars".$id])) {
            $new_value = $_POST['wdform_'.$key."_element_dollars".$id];
          }
          if(isset($_POST['wdform_'.$key."_element_cents".$id])) {
            $new_value = $new_value . '.' . $_POST['wdform_'.$key."_element_cents".$id];
          }
          $new_value = $new_value . $form_currency;
          break;
        }	
		
		case "type_paypal_price_new": {		
			$new_value = '';
			if(isset($_POST['wdform_'.$key."_element".$id] ) && $_POST['wdform_'.$key."_element".$id]) {
				$new_value = $form_currency . $_POST['wdform_'.$key."_element".$id];
			}
			$new_value = $new_value;
			break;
        }

        case "type_paypal_select": {
			$element = isset($_POST['wdform_'.$key."_element".$id]) && $_POST['wdform_'.$key."_element".$id] ? $_POST['wdform_'.$key."_element".$id] : '';
			if($element){
				$new_value = (isset($_POST['wdform_'.$key."_element_label".$id]) ? $_POST['wdform_'.$key."_element_label".$id] : "") . ' : ' . $form_currency. $element;
			
				$element_quantity_label = isset($_POST['wdform_'.$key."_element_quantity_label".$id]) ? $_POST['wdform_'.$key."_element_quantity_label".$id] : NULL;
				$element_quantity = (isset($_POST['wdform_'.$key."_element_quantity".$id]) && $_POST['wdform_'.$key."_element_quantity".$id]) ? $_POST['wdform_'.$key."_element_quantity".$id] : NULL;
				if (isset($element_quantity)) {
					$new_value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
				}
				for($k = 0; $k < 50; $k++) {
					$temp_val = isset($_POST['wdform_'.$key."_property".$id.$k]) ? $_POST['wdform_'.$key."_property".$id.$k] : NULL;
					if(isset($temp_val)) {
						$new_value .= '<br/>' . (isset($_POST['wdform_'.$key."_element_property_label".$id.$k]) ? $_POST['wdform_'.$key."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
					}
				}
			}
			break;
        }								
        case "type_paypal_radio": {
		
		
          $new_value = (isset($_POST['wdform_'.$key."_element_label".$id]) ? $_POST['wdform_'.$key."_element_label".$id] : "") . (isset($_POST['wdform_'.$key."_element".$id]) && $_POST['wdform_'.$key."_element".$id] ? ' - ' . $form_currency . $_POST['wdform_'.$key."_element".$id] : "") ;									
          $element_quantity_label = isset($_POST['wdform_'.$key."_element_quantity_label".$id]) ? $_POST['wdform_'.$key."_element_quantity_label".$id] : NULL;
          $element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
          if (isset($element_quantity)) {
            $new_value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
          }
          for($k = 0; $k < 50; $k++) {
            $temp_val = isset($_POST['wdform_'.$key."_property".$id.$k]) ? $_POST['wdform_'.$key."_property".$id.$k] : NULL;
            if(isset($temp_val)) {
              $new_value .= '<br/>' . (isset($_POST['wdform_'.$key."_element_property_label".$id.$k]) ? $_POST['wdform_'.$key."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
            }
          }							
          break;
        }
        case "type_paypal_shipping": {									
          $new_value = (isset($_POST['wdform_'.$key."_element_label".$id]) ? $_POST['wdform_'.$key."_element_label".$id] : "") . (isset($_POST['wdform_'.$key."_element".$id]) && $_POST['wdform_'.$key."_element".$id] ? ' : ' . $form_currency . $_POST['wdform_'.$key."_element".$id] : "");		
          break;
        }
        case "type_paypal_checkbox": {
          $start = -1;
          for($j = 0; $j < 100; $j++) {
            $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
            if(isset($element)) {
              $start = $j;
              break;
            }
          }									
          if($start != -1) {
            for($j = $start; $j < 100; $j++) {											
              $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
              if(isset($element)) {
                $new_value = $new_value . (isset($_POST['wdform_'.$key."_element".$id.$j."_label"]) ? $_POST['wdform_'.$key."_element".$id.$j."_label"] : "") . ' - ' . (isset($element) ? $form_currency . ($element == '' ? '0' : $element) : "") . '<br>';
              }
            }
          }									
          $element_quantity_label = isset($_POST['wdform_'.$key."_element_quantity_label".$id]) ? $_POST['wdform_'.$key."_element_quantity_label".$id] : NULL;
          $element_quantity = (isset($_POST['wdform_'.$key."_element_quantity".$id]) && $_POST['wdform_'.$key."_element_quantity".$id]) ? $_POST['wdform_'.$key."_element_quantity".$id] : NULL;
          if (isset($element_quantity)) {
            $new_value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
          }
          for($k = 0; $k < 50; $k++) {
            $temp_val = isset($_POST['wdform_'.$key."_property".$id.$k]) ? $_POST['wdform_'.$key."_property".$id.$k] : NULL;
            if(isset($temp_val)) {
              $new_value .= '<br/>' . (isset($_POST['wdform_'.$key."_element_property_label".$id.$k]) ? $_POST['wdform_'.$key."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
            }
          }									
          break;
        }								
        case "type_paypal_total": {
          $element = isset($_POST['wdform_'.$key."_paypal_total".$id]) ? $_POST['wdform_'.$key."_paypal_total".$id] : "";
          $new_value = $new_value . $element;
          break;
        }
        case "type_star_rating": {
          $element = isset($_POST['wdform_'.$key."_star_amount".$id]) ? $_POST['wdform_'.$key."_star_amount".$id] : NULL;
          $selected = isset($_POST['wdform_'.$key."_selected_star_amount".$id]) ? $_POST['wdform_'.$key."_selected_star_amount".$id] : 0;									
          if(isset($element)) {
            $new_value = $new_value . $selected . '/' . $element;					
          }
          break;
        }
        case "type_scale_rating": {
          $element = isset($_POST['wdform_'.$key."_scale_amount".$id]) ? $_POST['wdform_'.$key."_scale_amount".$id] : NULL;
          $selected = isset($_POST['wdform_'.$key."_scale_radio".$id]) ? $_POST['wdform_'.$key."_scale_radio".$id] : 0;
          if(isset($element)) {
            $new_value = $new_value . $selected . '/' . $element;					
          }
          break;
        }								
        case "type_spinner": {
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $new_value . $element;					
          }
          break;
        }								
        case "type_slider": {
          $element = isset($_POST['wdform_'.$key."_slider_value".$id]) ? $_POST['wdform_'.$key."_slider_value".$id] : NULL;
          if(isset($element)) {
            $new_value = $new_value . $element;					
          }
          break;
        }
        case "type_range": {
          $element0 = isset($_POST['wdform_'.$key."_element".$id.'0']) ? $_POST['wdform_'.$key."_element".$id.'0'] : NULL;
          $element1 = isset($_POST['wdform_'.$key."_element".$id.'1']) ? $_POST['wdform_'.$key."_element".$id.'1'] : NULL;
          if(isset($element0) || isset($element1)) {
            $new_value = $new_value . $element0 . '-' . $element1;					
          }
          break;
        }								
        case "type_grading": {
          $element = isset($_POST['wdform_'.$key."_hidden_item".$id]) ? $_POST['wdform_'.$key."_hidden_item".$id] : "";
          $grading = explode(":", $element);
          $items_count = sizeof($grading) - 1;									
          $element = "";
          $total = "";									
          for($k = 0;$k < $items_count; $k++) {
            $element .= $grading[$k] . ":" . (isset($_POST['wdform_'.$key."_element".$id.'_'.$k]) ? $_POST['wdform_'.$key."_element".$id.'_'.$k] : "") . " ";
            $total += (isset($_POST['wdform_'.$key."_element".$id.'_'.$k]) ? $_POST['wdform_'.$key."_element".$id.'_'.$k] : 0);
          }
          $element .="Total:" . $total;
          if(isset($element)) {
            $new_value = $new_value . $element;
          }
          break;
        }						
        case "type_matrix": {
          $input_type = isset($_POST['wdform_'.$key."_input_type".$id]) ? $_POST['wdform_'.$key."_input_type".$id] : "";
          $mat_rows = explode("***", isset($_POST['wdform_'.$key."_hidden_row".$id]) ? $_POST['wdform_'.$key."_hidden_row".$id] : "");
          $rows_count = sizeof($mat_rows) - 1;
          $mat_columns = explode("***", isset($_POST['wdform_'.$key."_hidden_column".$id]) ? $_POST['wdform_'.$key."_hidden_column".$id] : "");
          $columns_count = sizeof($mat_columns) - 1;												
          $matrix="<table>";												
          $matrix .='<tr><td></td>';
          for( $k=1;$k< count($mat_columns) ;$k++) {
            $matrix .= '<td style="background-color:#BBBBBB; padding:5px; ">' . $mat_columns[$k] . '</td>';
          }
          $matrix .= '</tr>';										
          $aaa=Array();										
            for($k=1; $k<=$rows_count; $k++) {
              $matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px;">' . $mat_rows[$k] . '</td>';										
              if($input_type=="radio") {
                $mat_radio = isset($_POST['wdform_'.$key."_input_element".$id.$k]) ? $_POST['wdform_'.$key."_input_element".$id.$k] : 0;											
                if($mat_radio == 0) {
                  $checked = "";
                  $aaa[1] = "";
                }
                else {
                  $aaa = explode("_", $mat_radio);
                }
                
                for($j = 1; $j <= $columns_count; $j++) {
                  if($aaa[1]==$j) {
                    $checked="checked";
                  }
                  else {
                    $checked="";
                  }
                  $matrix .= '<td style="text-align:center"><input  type="radio" ' . $checked . ' disabled /></td>';												
                }
              }
              else {
                if($input_type == "checkbox") {                
                  for($j = 1; $j <= $columns_count; $j++) {
                    $checked = isset($_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j] : 0;
                    if($checked==1) {
                      $checked = "checked";				
                    }
                    else {
                      $checked = "";
                    }
                    $matrix .= '<td style="text-align:center"><input  type="checkbox" ' . $checked . ' disabled /></td>';												
                  }
                }
                else {
                  if($input_type == "text") {																			  
                    for($j = 1; $j <= $columns_count; $j++) {
                      $checked = isset($_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j]) ? esc_html($_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j]) : "";
                      $matrix .= '<td style="text-align:center"><input  type="text" value="' . $checked . '" disabled /></td>';											
                    }													
                  }
                  else {
                    for($j = 1; $j <= $columns_count; $j++) {
                      $checked = isset($_POST['wdform_'.$key."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$key."_select_yes_no".$id.$k.'_'.$j] : "";
                      $matrix .= '<td style="text-align:center">' . $checked . '</td>';
                    }
                  }
                }
              }
              $matrix .= '</tr>';
            }
            $matrix .= '</table>';
            if(isset($matrix)) {
              $new_value = $new_value . $matrix;
            }
          break;
        }
        default: break;
      }
      // $new_script = str_replace("%" . $label_each . "%", $new_value, $new_script);	
    }
    
    return $new_value;
  }

	public function empty_field($element, $mail_emptyfields) {		
		if(!$mail_emptyfields)
			if(empty($element))
				return 0;

		return 1;
	}
	
	public function fm_validateDate($date, $format = 'Y-m-d H:i:s'){
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

  public function all_forms() {		
		global $wpdb;
		$forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix .'formmaker_display_options as display INNER JOIN ' . $wpdb->prefix . 'formmaker as forms ON display.form_id = forms.id WHERE display.type != "embedded" and forms.published=1');
		return $forms;
	}

  public function display_options($id){
		global $wpdb;
		$row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix .'formmaker_display_options as display WHERE form_id = '.(int)$id);
    if (!$row) {
      $row = new stdClass();
      $row->form_id = $id;
      $row->type = 'embedded';
      $row->scrollbox_loading_delay = 0;
      $row->popover_animate_effect = '';
      $row->popover_loading_delay = 0;
      $row->popover_frequency = 0;
      $row->topbar_position = 1;
      $row->topbar_remain_top = 1;
      $row->topbar_closing = 1;
      $row->topbar_hide_duration = 0;
      $row->scrollbox_position = 1;
      $row->scrollbox_trigger_point = 20;
      $row->scrollbox_hide_duration = 0;
      $row->scrollbox_auto_hide = 1;
      $row->hide_mobile = 0;
      $row->scrollbox_closing = 1;
      $row->scrollbox_minimize = 1;
      $row->scrollbox_minimize_text = '';
      $row->display_on = 'everything';
      $row->posts_include = '';
      $row->pages_include = '';
      $row->display_on_categories = '';
      $row->current_categories = '';
      $row->show_for_admin = 0;
    }
		return $row;
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