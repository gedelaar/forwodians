<?php

class  FMViewSubmissions_fm {

	private $model;

	public function __construct($model) {
    $this->model = $model;
	}

	public function display($form_id) {
		global $wpdb;
		$forms = $this->model->get_form_titles();
		$statistics = $this->model->get_statistics($form_id);
		$labels_parameters = $this->model->get_labels_parameters($form_id);

		$sorted_labels_id = $labels_parameters[0]; 
		$sorted_label_types = $labels_parameters[1]; 
		$lists = $labels_parameters[2];
		$sorted_label_names = $labels_parameters[3]; 
		$sorted_label_names_original = $labels_parameters[4]; 
		$rows = ((isset($labels_parameters[5])) ? $labels_parameters[5] : NULL);
		$group_ids = ((isset($labels_parameters[6])) ? $labels_parameters[6] : NULL);
		$where_choices = $labels_parameters[7];	
		$searched_ids = $labels_parameters[8] ? implode(',', $labels_parameters[8]) : '';	
		$order_by = (isset($_POST['order_by']) ? esc_html(stripslashes($_POST['order_by'])) : 'group_id');
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'asc') ? 'asc' : 'desc');
		$style_id = $this->model->hide_or_not($lists['hide_label_list'], '@submitid@'); 
		$style_date = $this->model->hide_or_not($lists['hide_label_list'], '@submitdate@');
		$style_ip = $this->model->hide_or_not($lists['hide_label_list'], '@submitterip@');

		$style_username = $this->model->hide_or_not($lists['hide_label_list'], '@submitterusername@');
		$style_useremail = $this->model->hide_or_not($lists['hide_label_list'], '@submitteremail@');

		$oder_class_default = "manage-column column-autor sortable desc";
		$oder_class = "manage-column column-title sorted " . $asc_or_desc; 
		$ispaypal = FALSE;
		$temp = array();
		$m = count($sorted_label_names);
		$n = count($rows);
		$group_id_s = array();	
		$group_id_s = $this->model->sort_group_ids(count($sorted_label_names),$group_ids);  
		$ka_fielderov_search = (($lists['ip_search'] || $lists['startdate'] || $lists['enddate'] || $lists['username_search'] || $lists['useremail_search'] || $lists['id_search']) ? TRUE : FALSE);
		$is_stats = false;
		$blocked_ips = $this->model->blocked_ips();
		
		if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))) {
			require_once(WD_FM_PDF_DIR.'/model.php');
			$pdf_data = WD_FM_PDF_model::get_pdf_data($form_id);
		}
		$subs_count = $this->model->get_subs_count($form_id);
		$chosen_form_title = '';
		if ($forms) { 
			foreach($forms as $form) {
				if ($form_id == $form->id) { 
					$chosen_form_title = $form->title;
				}
			}
		}
		$verified_emails = array();
		?>
		<script type="text/javascript">
			
			function export_submissions(type, limit) {
				var progressbar = jQuery( "#fm-progressbar" );
				var progressLabel = jQuery( ".fm-progress-label" );
				progressbar.progressbar({
					max: <?php echo $subs_count; ?>
				});

			 	jQuery.ajax({
			    type: "POST",
					url:"<?php echo add_query_arg(array('form_id' => $form_id, 'send_header' => 0), admin_url('admin-ajax.php')); ?>&action=generete_"+type+"&limitstart="+limit,
					data: {search_labels : '<?php echo $searched_ids; ?>', verified_emails: jQuery('#verified_emails').val() },
					beforeSend: function() {
						if(<?php echo $subs_count; ?> >= 1000 )
							jQuery('.fm_modal').show();
		    	},
			    success: function(data) {
							if(limit < <?php echo $subs_count; ?>) {
								limit += 1000;
								export_submissions(type, limit);
								progressbar.progressbar( "value",  limit);
								loaded_percent = Math.round((progressbar.progressbar( "value" ) * 100)/ parseInt(<?php echo $subs_count; ?>));
								progressLabel.text( loaded_percent + ' %');
								progressbarValue = progressbar.find( ".fm-progress-label" );
								if( loaded_percent >= 46 ) {
									progressbarValue.css({
										"color": '#fff',
									});
								}
								else {
									progressbarValue.css({
										"color": '#444',
									});
								}
							}
							else{
								jQuery('.fm_modal').hide();
								progressbar.progressbar( "value",  0);
								progressLabel.text( 'Loading ...' );
								progressbarValue = progressbar.find( ".fm-progress-label" );
								progressbarValue.css({
									"color": '#444',
								});
								window.location = "<?php echo add_query_arg(array('form_id' => $form_id, 'send_header' => 1), admin_url('admin-ajax.php')); ?>&action=generete_"+type+"&limitstart="+limit;
							}

			    }
			});
		}
	
			function clickLabChBAll(ChBAll) {
				<?php
					if (isset($sorted_label_names)) {
						$templabels = array_merge(array(
							'submitid',
							'submitdate',
							'submitterip',
							'submitterusername',
							'submitteremail'
						), $sorted_labels_id);
						$sorted_label_names_for_check = array_merge(array(
							'ID',
							'Submit date',
							"Submitter's IP",
							"Submitter's Username",
							"Submitter's Email Address"
						), $sorted_label_names_original);
					}
					else {
						$templabels = array(
							'submitid',
							'submitdate',
							'submitterip',
							'submitterusername',
							'submitteremail'
						);
						$sorted_label_names_for_check = array(
							'ID',
							'Submit date',
							"Submitter's IP",
							'Submitter\'s Username',
							'Submitter\'s Email Address'
						);
					}
				?>
				if (ChBAll.checked) {
					document.forms.admin_form.hide_label_list.value = '';
					for (i = 0; i <= ChBAll.form.length; i++) {
						if (typeof(ChBAll.form[i]) != "undefined") {
							if (ChBAll.form[i].type == "checkbox") {
								ChBAll.form[i].checked = true;
							}
						}
					}
				}
				else {
					document.forms.admin_form.hide_label_list.value = '@<?php echo implode($templabels, '@@') ?>@' + '@payment_info@';
					for (i = 0; i <= ChBAll.form.length; i++) {
						if (typeof(ChBAll.form[i]) != "undefined") {
							if (ChBAll.form[i].type == "checkbox") {
								ChBAll.form[i].checked = false;
							}
						}
					}
				}
				renderColumns();
			}
			
			function remove_all() {
				if(document.getElementById('startdate'))
					document.getElementById('startdate').value='';
				if(document.getElementById('enddate'))
					document.getElementById('enddate').value='';
				if(document.getElementById('id_search'))
					document.getElementById('id_search').value='';
				if(document.getElementById('ip_search'))
					document.getElementById('ip_search').value='';
				if(document.getElementById('username_search'))
					document.getElementById('username_search').value='';
				if(document.getElementById('useremail_search'))
					document.getElementById('useremail_search').value='';
				<?php
				$n = count($rows);
				for ($i = 0; $i < count($sorted_label_names); $i++) {
					if ($sorted_label_types[$i] != "type_mark_map") { ?>
						document.getElementById('<?php echo $form_id . '_' . $sorted_labels_id[$i] . '_search'; ?>').value='';
						<?php
					}
					if ($sorted_label_types[$i] == "type_submitter_mail") { ?>
						if(document.getElementById('<?php echo $form_id . '_' . $sorted_labels_id[$i] . '_search_verified'; ?>')) {
							document.getElementById('<?php echo $form_id . '_' . $sorted_labels_id[$i] . '_search_verified'; ?>').checked = false;
						}	
						<?php
					}
				}
				?>
			}
			function show_hide_filter() {
				if (document.getElementById('fields_filter').style.display == "none") {
					document.getElementById('fields_filter').style.display = '';
				}
				else {
					document.getElementById('fields_filter').style.display = "none";
				}
				return false;
			}
			jQuery(document).ready(function () { 
				jQuery('.theme-detail').click(function () {
					jQuery(this).siblings('.themedetaildiv').toggle();
					return false;
				});
			});
		</script>
		<div class="fm_modal"> 
			<div id="fm-progressbar" >
				<div class="fm-progress-label">Loading...</div>
			</div> 
		</div> 
		<div class="export_progress">
			<span class="exp_count"><?php echo $subs_count; ?></span> left from <?php echo $subs_count; ?>
		</div> 
		<div id="sbox-overlay" onclick="toggleChBDiv(false);">
		</div>
		<div id="ChBDiv">
			<form action="#">
				<?php wp_nonce_field('nonce_fm', 'nonce_fm'); ?>
				<p style="font-weight: bold; font-size: 18px; margin-top: 0px;">Select Columns</p>
				<div class="fm_check_labels"><input type="checkbox" <?php echo ($lists['hide_label_list'] === '') ? 'checked="checked"' : ''; ?> onclick="clickLabChBAll(this)" id="ChBAll"/><label for="ChBAll"> All</label></div>
				<?php
				foreach ($templabels as $key => $curlabel) {
					if (strpos($lists['hide_label_list'], '@' . $curlabel . '@') === FALSE) {
						?>
						<div class="fm_check_labels"><input type="checkbox" checked="checked" onclick="clickLabChB('<?php echo $curlabel; ?>', this)" id="fm_check_id_<?php echo $curlabel; ?>" /><label for="fm_check_id_<?php echo $curlabel; ?>"> <?php echo stripslashes($sorted_label_names_for_check[$key]); ?></label></div>
						<?php
					}		  
					else {
						?>
						<div class="fm_check_labels"><input type="checkbox" onclick="clickLabChB('<?php echo $curlabel; ?>', this)" id="fm_check_id_<?php echo $curlabel; ?>"/><label for="fm_check_id_<?php echo $curlabel; ?>"> <?php echo stripslashes($sorted_label_names_for_check[$key]); ?></label></div>
						<?php  
					}
				}
				$ispaypal = FALSE;
				for ($i = 0; $i < count($sorted_label_names); $i++) {
					if ($sorted_label_types[$i] == 'type_paypal_payment_status' || $sorted_label_types[$i] == 'type_stripe') {
						$ispaypal = TRUE;
					}
				}
				if ($ispaypal) {
					?>
					<div class="fm_check_labels">
						<input type="checkbox" onclick="clickLabChB('payment_info', this)" id="fm_check_payment_info" <?php echo (strpos($lists['hide_label_list'], '@payment_info@') === FALSE) ? 'checked="checked"' : ''; ?> />
						<label for="fm_check_payment_info"> Payment Info</label>
					</div>
					<?php
				}
				?>
				<div style="text-align: center; padding-top: 20px;">
					<button onclick="toggleChBDiv(false); return false;" style="background: #4EC0D9; width: 78px; height: 32px; border: 1px solid #4EC0D9; border-radius: 0px; color: #fff; cursor: pointer;">Done</button>
				</div>
			</form>
		</div>
		<form action="admin.php?page=submissions_fm" method="post" id="admin_form" name="admin_form">
			<?php wp_nonce_field('nonce_fm', 'nonce_fm'); ?>
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" id="current_id" name="current_id" value="" />
			<input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php echo $asc_or_desc; ?>" />
			<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>" />
				
			<div class="fm-submissions-page wrap">
				<div class="submissions-actions">
					<div class="fm-form-title">
						<?php echo $chosen_form_title; ?>	
					</div>
					<div class="fm-page-actions">
						<button class="fm-button block-button medium" onclick="fm_set_input_value('task', 'block_ip'); fm_form_submit(event, 'admin_form');">
							<span></span>
							Block IP
						</button>
						<button class="fm-button unblock-button medium" onclick="fm_set_input_value('task', 'unblock_ip'); fm_form_submit(event, 'admin_form');">
							<span></span>
							Unblock IP
						</button>
						<button class="fm-button delete-button medium" onclick="if (confirm('Do you want to delete selected items?')) { fm_set_input_value('task', 'delete_all'); fm_form_submit(event, 'admin_form'); } else { return false; }">
							<span></span>
							Delete
						</button>
					</div>
				</div>
				<div class="submissions-toolbar">
					<div class="submissions-tools">
						<select name="form_id" id="form_id" onchange="document.admin_form.submit();">
							<option value="0" selected="selected"> - Select a Form - </option>
							<?php if ($forms) { 
								foreach($forms as $form) {
									?>
									<option value="<?php echo $form->id; ?>" <?php if ($form_id == $form->id) { echo 'selected="selected"'; }?>> <?php echo $form->title ?> </option>
									<?php
								}
							} ?>
						</select>
						<div class="fm-reports">
							<div class="fm-tools-button"><div class="fm-total_entries"><?php echo $statistics["total_entries"]; ?></div>Entries</div>
							<div class="fm-tools-button"><div class="fm-total_rate"><?php echo $statistics["conversion_rate"]; ?></div>Conversion Rate</div>
							<div class="fm-tools-button"><div class="fm-total_views"><?php echo $statistics["total_views"] ? $statistics["total_views"] : 0; ?></div>Views</div>
						</div>
						
						<div class="fm-export-tools">
							<span class="exp_but_span">Export to</span>
							&nbsp;
							<button class="fm-tools-button" onclick="export_submissions('csv', 0); return false;">
								CSV
							</button>
							<button class="fm-tools-button" onclick="export_submissions('xml', 0); return false;">
								XML
							</button>
						</div>
					</div>
				</div>
				<div class="tablenav top">
					<div class="fm-filters">
						<div class="fm-search-tools fm-page-actions" style="float:none;">
							<input type="hidden" name="hide_label_list" value="<?php echo $lists['hide_label_list']; ?>"> 
							<button class="fm-button show-filter-button medium" onclick="show_hide_filter(); return false;" title="Show Filters">
								<span></span>
								Show Filters
							</button>
							<button class="fm-button search-button medium" onclick="fm_form_submit(event, 'admin_form'); return false;" title="Search">
								<span></span>
								Search
							</button>
							<button class="fm-button reset-button medium" onclick="remove_all(); fm_set_input_value('order_by', 'group_id'); fm_set_input_value('asc_or_desc', 'desc'); fm_form_submit(event, 'admin_form'); return false;" title="Reset">
								<span></span>
								Reset
							</button>
						</div>
						<div class="fm-add-remove">
							<?php if (isset($sorted_label_names)) { ?>
							<button class="fm-button" onclick="toggleChBDiv(true); return false;">
								Add/Remove Columns
							</button>
							<?php 
							WDW_FM_Library::html_page_nav($lists['total'], $lists['limit'], 'admin_form'); ?>
							<?php } ?>
							<input type="hidden" name="pagination_clicked" id="pagination_clicked" value=""/>
						</div>
					</div>
					<div class="fm-clear"></div>
				</div>

				<div class="fm-loading-container" style="display:none;">
					<div class="fm-loading-content">
					</div>
				</div>
				<div class="submit_content" id="fm-scroll" style="width: 100%;">
					<table class="wp-list-table widefat fixed posts table_content">
						<thead>
							<tr>
								<?php if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))): ?> 
								<th class="table_small_col count_col sub-align">PDF</th>
								<?php endif; ?>
								<th class="table_small_col count_col sub-align">#</th>
								<th scope="col" id="cb" class="manage-column column-cb check-column table_small_col sub-align form_check"><input id="check_all" type="checkbox"></th>
								<th scope="col" id="submitid_fc" class="table_small_col sub-align submitid_fc <?php if ($order_by == "group_id") echo $oder_class; else echo $oder_class_default; ?>" <?php echo $style_id;?>>
									<a href="" class="sub_id" onclick="fm_set_input_value('order_by', 'group_id');
													   fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'group_id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
													   fm_form_submit(event, 'admin_form')">
										<span>ID</span>
										<span class="sorting-indicator" style="margin-top: 8px;"></span>
									</a>
								</th>
								<th class="table_small_col sub-align">View</th>
								<th class="table_small_col sub-align">Edit</th>
								<th class="table_small_col sub-align">Delete</th>
								<th scope="col" id="submitdate_fc" class="table_large_col submitdate_fc <?php if ($order_by == "date") echo $oder_class; else echo $oder_class_default; ?>" <?php echo $style_date;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'date');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'date' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submit date</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th scope="col" id="submitterip_fc" class="table_medium_col_uncenter submitterip_fc <?php if ($order_by == "ip")echo $oder_class; else echo $oder_class_default;  ?>" <?php echo $style_ip;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'ip');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'ip' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submitter's IP</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>	
								<th scope="col" id="submitterusername_fc" class="table_medium_col_uncenter submitterusername_fc <?php if ($order_by == "display_name")echo $oder_class; else echo $oder_class_default;  ?>" <?php echo $style_username;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'display_name');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'display_name' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submitter's Username</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>	
								<th scope="col" id="submitteremail_fc" class="table_medium_col_uncenter submitteremail_fc <?php if ($order_by == "user_email")echo $oder_class; else echo $oder_class_default;  ?>" <?php echo $style_useremail ;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'user_email');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'user_email' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submitter's Email Address</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>	
								<?php
                $ispaypal = FALSE;
								for ($i = 0; $i < count($sorted_label_names); $i++) {
									$styleStr = $this->model->hide_or_not($lists['hide_label_list'], $sorted_labels_id[$i]); 
									$styleStr2 = $this->model->hide_or_not($lists['hide_label_list'] , '@payment_info@');		   
									$field_title = $this->model->get_type_address($sorted_label_types[$i], $sorted_label_names_original[$i]);
									if ($sorted_label_types[$i] == 'type_paypal_payment_status' || $sorted_label_types[$i] == 'type_stripe') {
                    if($sorted_label_types[$i] != 'type_stripe'){
                      ?>
                      <th <?php echo $styleStr; ?> id="<?php echo $sorted_labels_id[$i] . '_fc'; ?>" class="table_large_col <?php echo $sorted_labels_id[$i] . '_fc'; if ($order_by == $sorted_labels_id[$i] . "_field") echo $oder_class . '"';else echo $oder_class_default . '"'; ?>">
                        <a href="" onclick="fm_set_input_value('order_by', '<?php echo $sorted_labels_id[$i] . '_field'; ?>'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == $sorted_labels_id[$i] . '_field' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'admin_form')">	
                          <span><?php echo $field_title; ?></span>
                          <span class="sorting-indicator"></span>
                        </a>
                      </th>
                      <?php
                    }
                    if ($ispaypal) {
                      ?>
                      <th class="table_large_col payment_info_fc" <?php echo $styleStr2; ?>>Payment Info</th>
                      <?php  
                    }
										$ispaypal = TRUE;
									}
									else {
										?>
										<th <?php echo $styleStr; ?> id="<?php  echo $sorted_labels_id[$i] . '_fc';?>" class="<?php echo ($sorted_label_types[$i] == 'type_mark_map' || $sorted_label_types[$i] == 'type_matrix') ? 'table_large_col ' : ''; echo $sorted_labels_id[$i] . '_fc'; if ($order_by == $sorted_labels_id[$i] . "_field") echo $oder_class . '"';else echo $oder_class_default . '"'; ?>">
											<a href="" onclick="fm_set_input_value('order_by', '<?php echo $sorted_labels_id[$i] . '_field'; ?>'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == $sorted_labels_id[$i] . '_field' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'admin_form')">
												<span><?php echo $field_title; ?></span>
												<span class="sorting-indicator"></span>
											</a>
										</th>
										<?php
									}
								}			
								?>		           
							</tr>
							<tr id="fields_filter" style="display: none;">
								<?php if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))): ?> 
								<th></th>
								<?php endif; ?>
								
								<th></th> 
								<th></th>
								<th class="submitid_fc" <?php echo $style_id; ?> >
									<input type="text" name="id_search" id="id_search" value="<?php echo $lists['id_search'] ?>" onChange="this.form.submit();" style="width:30px"/>
								</th>
								<th></th> 
								<th></th>
								<th></th>
								<th width="150" class="submitdate_fc" <?php echo $style_date; ?>>
									<table align="center" style="margin:auto" class="simple_table">
										<tr class="simple_table">
											<td class="simple_table" style="text-align: left;">From:</td>
											<td style="text-align: center;" class="simple_table">
												<input class="inputbox" type="text" name="startdate" id="startdate" size="10" maxlength="10" value="<?php echo $lists['startdate']; ?>" />
											</td>
											<td style="text-align: center;" class="simple_table">
												<input type="reset" style="width: 22px; border-radius: 3px !important;" class="button" value="..." onclick="return showCalendar('startdate','%Y-%m-%d');" />
											</td>
										</tr>
										<tr class="simple_table">
											<td style="text-align: left;" class="simple_table">To:</td>
											<td style="text-align: center;" class="simple_table">
												<input class="inputbox" type="text" name="enddate" id="enddate" size="10" maxlength="10" value="<?php echo $lists['enddate']; ?>" />
											</td>
											<td style="text-align: center;" class="simple_table">
												<input type="reset" style="width: 22px; border-radius: 3px !important;" class="button" value="..." onclick="return showCalendar('enddate','%Y-%m-%d');" />
											</td>
										</tr>
									</table>
								</th>
								<th class="table_medium_col_uncenter submitterip_fc" <?php echo $style_ip; ?>>
									<input type="text" name="ip_search" id="ip_search" value="<?php echo $lists['ip_search']; ?>" onChange="this.form.submit();" />
								</th>
								<th class="table_medium_col_uncenter submitterusername_fc" <?php echo $style_username; ?>>
									<input type="text" name="username_search" id="username_search" value="<?php echo $lists['username_search']; ?>" onChange="this.form.submit();" />
								</th>
								<th class="table_medium_col_uncenter submitteremail_fc" <?php echo $style_useremail; ?>>
									<input type="text" name="useremail_search" id="useremail_search" value="<?php echo $lists['useremail_search']; ?>" onChange="this.form.submit();" />
								</th>
								<?php
								for ($i = 0; $i < count($sorted_label_names); $i++) {
									if($sorted_label_types[$i] == 'type_stripe')
										continue;
									$styleStr = $this->model->hide_or_not($lists['hide_label_list'], $sorted_labels_id[$i]);
									if (!$ka_fielderov_search) {
										if ($lists[$form_id . '_' . $sorted_labels_id[$i] . '_search'] || isset($lists[$form_id . '_' . $sorted_labels_id[$i] . '_search_verified'])) {
											$ka_fielderov_search = TRUE;
										}
									}	
									switch ($sorted_label_types[$i]) {
										case 'type_mark_map': ?>
											<th class="table_large_col <?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>></th>
											<?php
										break;
										case 'type_paypal_payment_status': ?>
											<th class="table_large_col <?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>>
												<select style="font-size: 11px; margin: 0; padding: 0; height: inherit;" name="<?php echo $form_id . '_' . $sorted_labels_id[$i]; ?>_search" id="<?php echo $form_id.'_'.$sorted_labels_id[$i]; ?>_search" onChange="this.form.submit();" value="<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>" >
													<option value="" ></option>
													<option value="canceled" >Canceled</option>
													<option value="cleared" >Cleared</option>
													<option value="cleared by payment review" >Cleared by payment review</option>
													<option value="completed" >Completed</option>
													<option value="denied" >Denied</option>
													<option value="failed" >Failed</option>
													<option value="held" >Held</option>
													<option value="in progress" >In progress</option>
													<option value="on hold" >On hold</option>
													<option value="paid" >Paid</option>
													<option value="partially refunded" >Partially refunded</option>
													<option value="pending verification" >Pending verification</option>
													<option value="placed" >Placed</option>
													<option value="processing" >Processing</option>
													<option value="refunded" >Refunded</option>
													<option value="refused" >Refused</option>
													<option value="removed" >Removed</option>
													<option value="returned" >Returned</option>
													<option value="reversed" >Reversed</option>
													<option value="temporary hold" >Temporary hold</option>
													<option value="unclaimed" >Unclaimed</option>
												</select>	
												<script> 
													var element = document.getElementById('<?php echo $form_id.'_'.$sorted_labels_id[$i]; ?>_search');
													element.value = '<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>';
												</script>
											</th>
											<th class="table_large_col  payment_info_fc" <?php echo $styleStr2; ?>></th>
											<?php				
										break;
										case 'type_submitter_mail': 
											$query = $wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id ="%d" AND element_label="verifyInfo" AND element_value="verified**%d"', $form_id, $sorted_labels_id[$i]);
											$is_verified_exist = $wpdb->get_var($query);
										
										?>
											<th class="<?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>>
												<div style="width:220px;">
													<input name="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search'; ?>" id="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search'; ?>" type="text" value="<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>"  onChange="this.form.submit();" >
													<?php if($is_verified_exist) { ?>
													<label for="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search_verified'; ?>">Verified</label> <input name="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search_verified'; ?>" id="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search_verified'; ?>" type="checkbox" <?php if(isset($lists[$form_id.'_'.$sorted_labels_id[$i].'_search_verified'])) echo "checked='checked'";?> onChange="this.form.submit();">
													<?php } ?>
												</div>
											</th>
											<?php
										break;
										default: ?>
											<th class="<?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>>
												<input name="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search'; ?>" id="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search'; ?>" type="text" value="<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>"  onChange="this.form.submit();" >
											</th>
											<?php	
										break;			
									}
								}
								?>
							</tr>
						</thead>
						<?php
						$k = 0;
						for ($www = 0, $qqq = count($group_id_s); $www < $qqq; $www++) {
							$i = $group_id_s[$www];
							$alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
							$temp = $this->model->array_for_group_id($group_id_s[$www], $rows);
							$data = $temp[0];
							$userinfo=get_userdata($data->user_id_wd);
							$useremail=$userinfo ? $userinfo->user_email : "";
							$username=$userinfo ? $userinfo->display_name : "";
							?>
							<tr <?php echo $alternate; ?>>
								<?php if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))): ?> 
								<td class="table_small_col pdf_col sub-align">
									<?php if($pdf_data && isset($pdf_data[$group_id_s[$www]])): ?> 
									<a href="<?php echo site_url().'/'.$pdf_data[$group_id_s[$www]]; ?>" style="display: block;" download><img src="<?php echo WD_FM_PDF_URL . '/images/pdf-icon.png'; ?>" /></a>
									<?php endif; ?>
								</td>
								<?php endif; ?>
								<td class="table_small_col count_col sub-align"><?php echo $www + 1; ?></td>
								<td class="check-column table_small_col sub-align" style="padding: 0;">
									<input type="checkbox" name="post[]" value="<?php echo $data->group_id; ?>">
								</td>   
								<td class="table_small_col sub-align submitid_fc" id="submitid_fc" <?php echo $style_id; ?>>
									<a href="" onclick="fm_set_input_value('task', 'edit');						   fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form');" >
										<?php echo $data->group_id; ?>
									</a>
								</td>
								<td  class="table_small_col submitdate_fc sub-align" id="view_submissions">
									<a href="<?php echo add_query_arg(array('action' => 'FormMakerSubmits', 'form_id' => $data->form_id, 'group_id' => $data->group_id, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>"
									   class="thickbox thickbox-preview"
									   title="<?php echo __("Show submission","form_maker"); ?>"
									   onclick="return false;"><img src="<?php echo WD_FM_URL . '/images/view-icon.png'; ?>" />
									</a>
								</td>								
								<td class="table_small_col sub-align">
									<a href="" onclick="fm_set_input_value('task', 'edit');						   fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form');">Edit
									</a>
								</td>
								<td class="table_small_col sub-align">
									<a href="" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete'); fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form'); } else { return false; }">Delete
									</a>
								</td>		 
								<td  class="table_large_col submitdate_fc sub-align" id="submitdate_fc" <?php echo $style_date; ?>>
									<a href="" onclick="fm_set_input_value('task', 'edit'); fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form');" ><?php echo $data->date ;?>
									</a>
								</td>
								<td class="table_medium_col_uncenter submitterip_fc sub-align" id="submitterip_fc" <?php echo $style_ip; ?>>
									<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'fromipinfoinpopup', 'data_ip' => $data->ip, 'width' => '450', 'height' => '300', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" title="Show submitter information" <?php echo (!in_array($data->ip, $blocked_ips)) ? '' : 'style="color: #FF0000;"'; ?>><?php echo $data->ip; ?></a>
								</td>
								<td  class="table_large_col submitterusername_fc sub-align" id="submitterusername_fc" <?php echo $style_username; ?>>
									<?php   echo  $username; ?>
								</td>
								<td  class="table_large_col submitteremail_fc sub-align" id="submitteremail_fc" <?php echo $style_useremail; ?>>
									<?php  echo $useremail; ?>
								</td>
								<?php
								for ($h = 0; $h < $m; $h++) {
                  if($sorted_label_types[$h] == 'type_stripe')
                    continue;
									$not_label = TRUE;
									for ($g = 0; $g < count($temp); $g++) {
										$styleStr = $this->model->hide_or_not($lists['hide_label_list'], $sorted_labels_id[$h]);
										if ($temp[$g]->element_label == $sorted_labels_id[$h]) {
											if (strpos($temp[$g]->element_value, "***map***")) {
												$map_params = explode('***map***', $temp[$g]->element_value);
												?>
												<td class="table_large_col <?php echo $sorted_labels_id[$h]; ?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'frommapeditinpopup', 'long' => $map_params[0], 'lat' => $map_params[1], 'width' => '620', 'height' => '550', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" title="Show on Map">Show on Map</a>
												</td>
												<?php 
											}
											elseif (strpos($temp[$g]->element_value, "*@@url@@*")) {
												?>
												<td class="<?php echo $sorted_labels_id[$h]; ?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<?php
													$new_files = explode("*@@url@@*", $temp[$g]->element_value);
													foreach ($new_files as $new_file) {
														if ($new_file) {
															$new_filename = explode('/', $new_file);
															$new_filename = $new_filename[count($new_filename) - 1];
															?>
															<a target="_blank" class="fm_fancybox" rel="group_<?php echo $www; ?>" href="<?php echo $new_file; ?>"><?php echo $new_filename; ?></a><br />
															<?php
														}
													}
													?>
												</td>
												<?php
											}
											elseif (strpos($temp[$g]->element_value, "***star_rating***")) {
												$view_star_rating_array = $this->model->view_for_star_rating($temp[$g]->element_value, $temp[$g]->element_label);
												$stars = $view_star_rating_array[0];
												?>
												<td align="center" class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>><?php echo $stars; ?></td>
												<?php  
											}
											elseif (strpos($temp[$g]->element_value, "***matrix***")) {
												?>   
												<td class="table_large_col <?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'show_matrix', 'matrix_params' => str_replace('#', '%23', urlencode($temp[$g]->element_value)), 'width' => '620', 'height' => '550', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" title="Show Matrix">Show Matrix</a>
												</td>
												<?php
											}
											elseif (strpos($temp[$g]->element_value, "@@@") !== FALSE || $temp[$g]->element_value == "@@@" || $temp[$g]->element_value == "@@@@@@@@@") {
												?>
												<td class="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<p><?php echo str_replace("@@@", " ", $temp[$g]->element_value); ?></p>
												</td>
												<?php
											}
											elseif (strpos($temp[$g]->element_value, "***grading***")) {
												$view_grading_array = $this->model->view_for_grading($temp[$g]->element_value);
												$items = $view_grading_array[0];
												?>
												<td class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<p><?php echo $items; ?></p>
												</td>
												<?php
											}
											else {
												if (strpos($temp[$g]->element_value, "***quantity***")) {
													$temp[$g]->element_value = str_replace("***quantity***", " ", $temp[$g]->element_value);
												}
												if (strpos($temp[$g]->element_value, "***property***")) {
													$temp[$g]->element_value = str_replace("***property***", " ", $temp[$g]->element_value);
												}

												if($sorted_label_types[$h]=="type_submitter_mail"){	
													$query = $wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id ="%d" AND group_id="%d" AND element_value="verified**%d"', $form_id, $i, $sorted_labels_id[$h]);
													$isverified = $wpdb->get_var($query);
								
													if($isverified) { 
														if(!isset($verified_emails[$sorted_labels_id[$h]]))
															$verified_emails[$sorted_labels_id[$h]] = array();
										
														$verified_emails[$sorted_labels_id[$h]][] = $i;
													?>
														<td class="<?php echo $sorted_labels_id[$h];?>_fc" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
															<p><?php echo $temp[$g]->element_value; ?> <span style="color:#2DA068;">( Verified <img src="<?php echo WD_FM_URL . '/images/verified.png'; ?>" /> )</span></p>
														</td>
													<?php }	
													else {?>
														<td class="<?php echo $sorted_labels_id[$h];?>_fc" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
															<p><?php echo $temp[$g]->element_value; ?></p>
														</td>	
													<?php }	
												}	
												else{
													?>
													<td class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
														<p><?php echo str_replace("***br***", '<br>', stripslashes($temp[$g]->element_value)) ; ?></p>
													</td>
													<?php   
												}
											}	
											$not_label = FALSE;
										}
									}
									if ($not_label) {
										?>
										<td class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>><p>&nbsp;</p></td>
										<?php
									}
								}
								if ($ispaypal) {
									$styleStr = $this->model->hide_or_not($lists['hide_label_list'], '@payment_info@');
									?>
									<td class="table_large_col payment_info_fc sub-align" id="payment_info_fc" <?php echo $styleStr; ?>>
										<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'paypal_info', 'id' => $i, 'width' => '600', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>">
											<img src="<?php echo WD_FM_URL . '/images/info.png'; ?>" />
										</a>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							$k = 1 - $k;
						}
						?>
					</table>
				</div>	 
				<?php
				if ($sorted_label_types) {
					foreach ($sorted_label_types as $key => $sorted_label_type) {
						if ($this->model->check_radio_type($sorted_label_type)) {
							$is_stats = true;
							break;
						}
					}
					if ($is_stats) {
						$ajax_nonce = wp_create_nonce( "nonce_fm_ajax" );
						?>
						<br/>
						<div class="fm-statistics">
							<h1>Statistics</h1>		
							<table class="stats">
								<tr>
									<td>
										<label for="sorted_label_key">Select a Field:</label>
									</td>
									<td>
										<select id="sorted_label_key">
											<option value="">Select a Field</option>
											<?php 
											foreach ($sorted_label_types as $key => $sorted_label_type) {
												if ($sorted_label_type=="type_checkbox" || $sorted_label_type=="type_radio" || $sorted_label_type=="type_own_select" || $sorted_label_type=="type_country" || $sorted_label_type=="type_paypal_select" || $sorted_label_type=="type_paypal_radio" || $sorted_label_type=="type_paypal_checkbox" || $sorted_label_type=="type_paypal_shipping") {				  
													?>
													<option value="<?php echo $key; ?>"><?php echo $sorted_label_names_original[$key]; ?></option>
													<?php
												}
											}
											?>
										</select>
									</td>
									<td></td>
								</tr>
								<tr>
									<td>
										<label>Select a Date:</label>
									</td>
									<td>
										From: <input class="inputbox"  type="text" name="startstats" id="startstats" size="9" maxlength="9" />
										  <input type="reset" class="button" style="width: 22px;"  value="..." name="startstats_but" id="startstats_but" onclick="return showCalendar('startstats','%Y-%m-%d');" /> 
											 
										To: <input class="inputbox" type="text" name="endstats" id="endstats" size="9" maxlength="9" />
										<input type="reset" class="button" style="width: 22px;"  value="..." name="endstats_but" id="endstats_but" onclick="return showCalendar('endstats','%Y-%m-%d');" />
									</td>
									<td>
										<button onclick="show_stats(); return false;">Show</button>
									</td>
								</tr>
							</table>
							
							<div id="div_stats"></div>	
						</div>
						<script>
						function show_stats() { 
							jQuery('#div_stats').html('<div class="fm-loading-container"><div class="fm-loading-content"></div></div>');
							if(jQuery('#sorted_label_key').val()!="") {	 	  
								jQuery('#div_stats').load('<?php echo add_query_arg(array('action' => 'get_stats', 'page' => 'submissions_fm'), admin_url('admin-ajax.php')); ?>', { 
									'task': 'show_stats',
									'form_id' : '<?php echo $form_id; ?>',
									'sorted_label_key' : jQuery('#sorted_label_key').val(),
									'startdate' : jQuery('#startstats').val(), 
									'enddate' : jQuery('#endstats').val(),
									'nonce_fm_ajax': '<?php echo $ajax_nonce; ?>'
								});
							}		
							else {
								jQuery('#div_stats').html("<div style='padding:10px 5px; color:red; font-size:14px;'>Please select the field!</div>");
							}	
							jQuery("#div_stats").removeClass("fm_loading");
						}
						</script>
						<?php	
					}
				}
				?>
			</div>
			<?php
			$in_already_verified = $verified_emails  ? 'AND REPLACE(element_label, "verifyInfo@", "") NOT IN ('.implode(',', array_keys( $verified_emails )).')' : '';
			$query = $wpdb->prepare('SELECT REPLACE(element_label, "verifyInfo@", "") as label FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id ="%d" AND element_label REGEXP "verifyInfo@" '.$in_already_verified, $form_id);
			$not_verified_labels = $wpdb->get_col($query);
			if($not_verified_labels){
				
				foreach($not_verified_labels as $key => $value ){
					$verified_emails[$value] = array();
				}
				
			} 
			?>
			<input type="hidden" name="verified_emails" id="verified_emails" value='<?php echo json_encode($verified_emails); ?>' />
		</form>	
		<script> 
		function fm_scroll(element) {
			var scrollbar= document.createElement('div');
			scrollbar.appendChild(document.createElement('div'));
			scrollbar.style.overflow= 'auto';
			scrollbar.style.overflowY= 'hidden';
			scrollbar.firstChild.style.width= element.scrollWidth+'px';
			scrollbar.firstChild.style.paddingTop= '1px';
			scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
			scrollbar.onscroll= function() {
				element.scrollLeft= scrollbar.scrollLeft;
			};
			element.onscroll= function() {
				scrollbar.scrollLeft= element.scrollLeft;
			};
			element.parentNode.insertBefore(scrollbar, element);
		}
		jQuery(window).load(function() {
			fm_popup();
			fm_scroll(document.getElementById('fm-scroll'));
			if (typeof jQuery().fancybox !== 'undefined' && jQuery.isFunction(jQuery().fancybox)) {
				jQuery(".fm_fancybox").fancybox({
					'maxWidth ' : 600,
					'maxHeight' : 500
				});
			}
		});
		<?php if ($ka_fielderov_search) { ?> 
			document.getElementById('fields_filter').style.display = '';
        <?php } ?>
		</script>
		<?php
	}

	public function show_stats($form_id) {
		$key = (isset($_POST['sorted_label_key']) ? esc_html(stripslashes($_POST['sorted_label_key'])) : ''); 
		$labels_parameters = $this->model->get_labels_parameters($form_id);
		$where_choices = $labels_parameters[7];
		$sorted_label_names_original = $labels_parameters[4];
		$sorted_labels_id = $labels_parameters[0];	 
		if(count($sorted_labels_id)!=0 && $key < count($sorted_labels_id)  ) { 
			$choices_params = $this->model->statistic_for_radio($where_choices, $sorted_labels_id[$key]);
			$sorted_label_name_original = $sorted_label_names_original[$key];
			$choices_count = $choices_params[0];
			$choices_labels = $choices_params[1];
			$unanswered = $choices_params[2];
			$all = $choices_params[3];
			$colors = $choices_params[4];	  
			$choices_colors = $choices_params[5];	  
		}
		else {
			$choices_labels = array();
			$sorted_label_name_original = '';
			$unanswered = NULL;
			$all = 0;
		}
		?>
		<br/>
		<br/>
		<div class="field-label"><?php echo stripslashes($sorted_label_name_original); ?></div>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20%">Choices</th>
					<th>Percentage</th>
					<th width="10%">Count</th>
				</tr>
			</thead>
			<?php
			$k=0;
			foreach ($choices_labels as $key => $choices_label) {
				if (strpos($choices_label, "***quantity***")) {
					$choices_label = str_replace("***quantity***", " ", $choices_label);
				}
				if (strpos($choices_label, "***property***")) {
					$choices_label = str_replace("***property***", " ", $choices_label);
				}
				?>
				<tr>
					<td class="label<?php echo $k; ?>"><?php echo str_replace("***br***",'<br>', $choices_label)?></td>
					<td>
						<div class="bordered" style="width:<?php echo ($choices_count[$key]/($all-$unanswered))*100; ?>%; height:16px; background-color:<?php echo $colors[$key % 2]; ?>; float: left;">
						</div>
						<div <?php echo ($choices_count[$key]/($all-$unanswered)!=1 ? 'class="bordered'.$k.'"' : "") ?> style="width:<?php echo 100-($choices_count[$key]/($all-$unanswered))*100; ?>%; height:16px; background-color:#F2F0F1; float: left;">
						</div>
					</td>
					<td>
						<div>
							<div style="width: 0; height: 0; border-top: 8px solid transparent;border-bottom: 8px solid transparent; border-right:8px solid <?php echo $choices_colors[$key % 2]; ?>; float:left;">
							</div>
							<div style="background-color:<?php echo $choices_colors[$key % 2]; ?>; height:16px; width:16px; text-align: center; margin-left:8px; color: #fff;">
							<?php echo $choices_count[$key]?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3">
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			if($unanswered){
				?>
				<tr>
					<td colspan="2" style="text-align:right; color: #000;">Unanswered</th>
					<td><strong style="margin-left:10px;"><?php echo $unanswered;?></strong></th>
				</tr>
				<?php	
			}
			?>
			<tr>
				<td colspan="2" style="text-align:right; color: #000;"><strong>Total</strong></th>
				<td><strong style="margin-left:10px;"><?php echo $all;?></strong></th>
			</tr>
		</table>
		<?php
		die();
	}

  public function edit($id) {
    $current_id = ((isset($id)) ? $id : 0);
    $params = $this->model->get_data_of_group_id($current_id);
    $rows = $params[0];
    $labels_id = $params[1];
    $labels_name = $params[2];
    $labels_type = $params[3];
    $ispaypal = $params[4];
	$userinfo = get_userdata($rows[0]->user_id_wd);
	$username = $userinfo ? $userinfo->display_name : "";
	$useremail = $userinfo ? $userinfo->user_email : "";
	
    ?>
    <form action="admin.php?page=submissions_fm" method="post" id="adminForm" name="adminForm">
      <?php wp_nonce_field('nonce_fm', 'nonce_fm'); ?>
      <table width="99%">
        <tbody>
          <tr>
            <td width="100%"><h2>Edit Submission</h2></td>
            <td align="right">
              <input type="button" onclick="fm_set_input_value('task', 'save');						  
                                            fm_set_input_value('current_id', <?php echo $current_id; ?>);
                                            fm_form_submit(event, 'adminForm');" value="Save" class="button-secondary action">
            </td>
            <td align="right">
              <input type="button" onclick="fm_set_input_value('task', 'apply');						  
                                            fm_set_input_value('current_id', <?php echo $current_id ;?>);
                                            fm_form_submit(event, 'adminForm');" value="Apply" class="button-secondary action">
            </td>
            <td align="right">
              <input type="button" onclick="fm_set_input_value('task', '');fm_form_submit(event, 'adminForm');" value="Cancel" class="button-secondary action">
            </td>
          </tr>
        </tbody>
      </table>
      <table class="admintable">
        <tr>
          <td class="key"><label for="ID">ID: </label></td>
          <td><?php echo $rows[0]->group_id; ?></td>
        </tr>
        <tr>
          <td class="key"><label for="Date">Date: </label></td>
          <td><?php echo $rows[0]->date; ?></td>
        </tr>
        <tr>
          <td class="key"><label for="IP">IP: </label></td>
          <td><?php echo $rows[0]->ip; ?></td>
        </tr>
		
		
		<tr>
          <td class="key"><label for="Submitter's Username">Submitter's Username: </label></td>
          <td><?php echo $username; ?></td>
        </tr>
		<tr>
          <td class="key"><label for="Submitter's Email Address">Submitter's Email Address: </label></td>
          <td><?php echo $useremail; ?></td>
        </tr>
        <?php
        foreach ($labels_id as $key => $label_id) {
          if ($this->model->check_type_for_edit_function($labels_type[$key])) {
            $element_value = $this->model->check_for_submited_label($rows, $label_id);
            if ($element_value == "continue") {
              continue;
            }
            switch ($labels_type[$key]) {
              case 'type_checkbox':
                $choices = explode('***br***', $element_value);
                $choices = array_slice($choices, 0, count($choices) - 1);
                ?>
				<tr>
          <td class="key" rowspan="<?php echo count($choices); ?>">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
                <?php
                foreach ($choices as $choice_key => $choice) {
                  ?>
				  <td>
            <input type="text" name="submission_<?php echo $label_id.'_'.$choice_key; ?>" id="submission_<?php echo $label_id.'_'.$choice_key; ?>" value="<?php echo $choice; ?>" size="80" />
				  </td>
				</tr>
                  <?php
                }
                break;
              case 'type_paypal_payment_status':
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <select name="submission_0" id="submission_0" >
              <option value=""></option>
              <option value="Canceled" >Canceled</option>
              <option value="Cleared" >Cleared</option>
              <option value="Cleared by payment review" >Cleared by payment review</option>
              <option value="Completed" >Completed</option>
              <option value="Denied" >Denied</option>
              <option value="Failed" >Failed</option>
              <option value="Held" >Held</option>
              <option value="In progress" >In progress</option>
              <option value="On hold" >On hold</option>
              <option value="Paid" >Paid</option>
              <option value="Partially refunded" >Partially refunded</option>
              <option value="Pending verification" >Pending verification</option>
              <option value="Placed" >Placed</option>
              <option value="Processing" >Processing</option>
              <option value="Refunded" >Refunded</option>
              <option value="Refused" >Refused</option>
              <option value="Removed" >Removed</option>
              <option value="Returned" >Returned</option>
              <option value="Reversed" >Reversed</option>
              <option value="Temporary hold" >Temporary hold</option>
              <option value="Unclaimed" >Unclaimed</option>
            </select>	
            <script> 
              var element = document.getElementById("submission_0");
              element.value = "<?php echo $element_value; ?>";
            </script>
			    </td>
				</tr>
                <?php
                break;
              case 'type_star_rating':
                $star_rating_array = $this->model->images_for_star_rating($element_value, $label_id);
                $edit_stars = $star_rating_array[0];
                $stars_value = $star_rating_array[1];
                ?>
        <tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="hidden" id="<?php echo $label_id; ?>_star_amountform_id_temp" name="<?php echo $label_id; ?>_star_amountform_id_temp" value="<?php echo $stars_value[0]; ?>">
            <input type="hidden" name="<?php echo $label_id; ?>_star_colorform_id_temp" id="<?php echo $label_id; ?>_star_colorform_id_temp" value="<?php echo $stars_value[2]; ?>">
            <input type="hidden" id="<?php echo $label_id; ?>_selected_star_amountform_id_temp" name="<?php echo $label_id; ?>_selected_star_amountform_id_temp" value="<?php echo $stars_value[1]; ?>">
            <?php echo $edit_stars; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
        </tr>
                <?php
                break;
              case "type_scale_rating":
                $scale_rating_array = $this->model->params_for_scale_rating($element_value, $label_id);
                $scale = $scale_rating_array[0];
                $scale_radio = $scale_rating_array[1];
                $checked = $scale_rating_array[2];
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="hidden" id="<?php echo $label_id; ?>_scale_checkedform_id_temp" name="<?php echo $label_id; ?>_scale_checkedform_id_temp" value="<?php echo $scale_radio[1]; ?>">
            <?php echo $scale; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              case 'type_range':
                $range = $this->model->params_for_type_range($element_value, $label_id);
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <?php echo $range; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              case 'type_spinner':
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="text" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo str_replace("*@@url@@*", '', $element_value); ?>" size="20" />
          </td>
        </tr>
                <?php
                break;
              case 'type_grading':
                $type_grading_array = $this->model->params_for_type_grading($element_value, $label_id);
                $garding = $type_grading_array[0];
                $garding_value = $type_grading_array[1];
                $sum = $type_grading_array[2];
                $items_count = $type_grading_array[3];
                $element_value1 = $type_grading_array[4];
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <?php echo $garding; ?>
            <span id="<?php echo $label_id; ?>_grading_sumform_id_temp"><?php echo $sum; ?></span>/<span id="<?php echo $label_id; ?>_grading_totalform_id_temp"><?php echo $garding_value[$items_count]; ?></span><span id="<?php echo $label_id; ?>_text_elementform_id_temp"></span>
            <input type="hidden"  id="<?php echo $label_id; ?>_element_valueform_id_temp" name="<?php echo $label_id; ?>_element_valueform_id_temp" value="<?php echo $element_value1; ?>" />
            <input type="hidden"  id="<?php echo $label_id; ?>_grading_totalform_id_temp" name="<?php echo $label_id; ?>_grading_totalform_id_temp" value="<?php echo $garding_value[$items_count]; ?>" />
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              case 'type_matrix':
                $type_matrix_array = $this->model->params_for_type_matrix($element_value, $label_id);
                $matrix = $type_matrix_array[0];
                $new_filename = $type_matrix_array[1];
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="hidden"  id="<?php echo $label_id; ?>_matrixform_id_temp" name="<?php echo $label_id; ?>_matrixform_id_temp" value="<?php echo $new_filename; ?>">
            <?php echo $matrix; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              default: 
              ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="text" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo str_replace("*@@url@@*", '', $element_value); ?>" size="80" />
          </td>
        </tr>
              <?php
                break;
            }
          }
        }
        ?>
      </table>
      <input type="hidden" name="option" value="com_formmaker"/>
      <input type="hidden" id="current_id" name="current_id" value="<?php echo $rows[0]->group_id; ?>" />
      <input type="hidden" name="form_id" value="<?php echo $rows[0]->form_id; ?>" />
      <input type="hidden" name="date" value="<?php echo $rows[0]->date; ?>" />
      <input type="hidden" name="ip" value="<?php echo $rows[0]->ip; ?>" />
      <input type="hidden" id="task" name="task" value="" />
      <input type="hidden" value="<?php echo WD_FM_URL; ?>" id="form_plugins_url" />
      <script>
        plugin_url = document.getElementById('form_plugins_url').value;
      </script>
    </form>
	  <?php
  }
}

?>
