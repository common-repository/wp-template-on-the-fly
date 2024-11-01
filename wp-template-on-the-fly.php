<?php
/*
Plugin Name: WP Template On The Fly
Plugin URI: http://alibabaei.com/wp/plugins/wp_template_on_the_fly/
Description: Create templates on the fly, just by clicks, and override your theme's template files, create any number of sidebars, put sidebars in your custom created templates, apply your custom style to sidebares and templates, add any plugins to sidebars and its done, yes! you created new theme which you can edit it from 0% to 100% by only some clicks, any time, any where, and for any reason, add any number of custom template to your theme too.
Author: Mohammad Ali Aghababaei Amir
Text Domain: wp-template-on-the-fly
Version: 1.0
Author URI: http://alibabaei.com/
License: GPL2
*/

add_action('admin_menu', 'wp_template_on_the_fly_setting_menu');
add_action("template_redirect",'wp_template_on_the_fly');
add_action("add_meta_boxes",'wp_template_on_the_fly_meta_box_add');
add_action('plugins_loaded', 'wp_template_on_the_fly_init');
register_activation_hook(__FILE__,'wp_template_on_the_fly_db_create');
register_deactivation_hook(__FILE__,'wp_template_on_the_fly_db_drop');

function wp_template_on_the_fly_init(){
	load_plugin_textdomain('wp-template-on-the-fly', false, basename(dirname(__FILE__)).'/languages/');
}
function wp_template_on_the_fly_option_init(){
	$option = get_option('totf_sidebars',false);
	if(!$option){
		add_option('totf_sidebars',array(),'','yes');
		return;
	}else{
		foreach($option as $s)
			register_sidebar(array('name'=>$s['name'],'id'=>$s['id'],'description'=>$s['description'],'class'=>'','before_widget'=>'','after_widget'=>'','before_title'=>'','after_title'=>''));
	}
}
add_action('widgets_init','wp_template_on_the_fly_option_init');
function wp_template_on_the_fly_meta_box_add(){
	add_meta_box('wp_template_on_the_fly_meta_box','Template On The Fly','wp_template_on_the_fly_meta_box','page','side','low');
}
function wp_template_on_the_fly_meta_box(){
	global $wpdb;
	$t = $wpdb->prefix."template_on_the_fly_template";
	$templates = $wpdb->get_results("SELECT * FROM $t WHERE `status`=1 AND `template` NOT LIKE '%$%'");
	$options = '';
	foreach($templates as $a)
		$options .= '<option value="'.$a->template.'">'.substr($a->template,0,-4).'</option>';
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			if($('select#page_template').length>0){
				$('select#page_template').append('<?php echo $options;?>');
			}
		});
	</script>
	<?php
}
function wp_template_on_the_fly_setting_menu(){
	$parent_slug = 'options-general.php';
	$page_title = __('WP Template On The Fly','wp-template-on-the-fly');
	$menu_title = __('Template On The Fly','wp-template-on-the-fly');
	$capability = 'manage_options';
	$menu_slug = 'template-on-the-fly';
	$function = 'wp_template_on_the_fly_setting_page_content';
	add_submenu_page($parent_slug,$page_title,$menu_title,$capability,$menu_slug,$function);
}
function wp_template_on_the_fly_setting_page_content(){
	global $wpdb;
	$path = '/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
	$pathcss = $path.'/css/style.css';
	$pathcsscolorpicker = $path.'/css/colorpicker.css';
	$pathjs = $path.'/js/script.js';
	$pathjscolorpicker = $path.'/js/colorpicker.js';
	$t = $wpdb->prefix."template_on_the_fly_template";
	$templates = $wpdb->get_results("SELECT * FROM $t WHERE `template` LIKE '%$%' ORDER BY `parent`");
	$templatestotalpages = $wpdb->get_var("SELECT COUNT(*) FROM $t");
	$setting = get_option('template_on_the_fly_setting');
	wp_enqueue_style('m_e_the_wp_css',$pathcss);
	wp_enqueue_script('m_e_the_wp_js',$pathjs);
	wp_enqueue_style('m_e_the_wp_css_color_picker',$pathcsscolorpicker);
	wp_enqueue_script('m_e_the_wp_js_color_picker',$pathjscolorpicker);
	global $wp_rewrite;
?>
	<script type="text/javascript">
		var _path = '<?php echo $path;?>';
		var error = '<?php _e('error','wp-template-on-the-fly');?>';
		var SaveChangesToSidebars = '<?php _e('Save Changes To Sidebars','wp-template-on-the-fly');?>';
		var active = '<?php _e('active','wp-template-on-the-fly');?>';
		var wpaction = 'wp_template_on_the_fly_ajax';
	</script>
	<div class="t_o_t_f" style="direction:<?php _e('ltr','wp-template-on-the-fly'); ?>">
		<h2 class="plugin_title"><?php _e('WP Template On The Fly','wp-template-on-the-fly'); ?></h2>
		<div class="plugin_support_div">
			<a class="button plugin_support" href="http://alibabaei.com/wp-plugins/wp-template-on-the-fly" target="_blank">
				<?php _e('Get Full Support {Help,Tutorial,Bug Report/Fix,Improvements,...}','wp-template-on-the-fly'); ?>
			</a>
		</div>
		<div class="plugin_explain"></div>
		<div class="messagebox">
			<div id="loading" class="vishidden"><?php _e('Loading...','wp-template-on-the-fly'); ?></div>
			<div id="t_o_t_f_message" class="message"></div>
		</div>
		<div class="wrapper">
			<ul class="category-tabs">
				<li class="tabs"><?php _e('Templates','wp-template-on-the-fly'); ?></li>
				<li class="tabs"><?php _e('Sidebars','wp-template-on-the-fly'); ?></li>
				<li class="tabs"><?php _e('Template-Sidebar','wp-template-on-the-fly'); ?></li>
			</ul>
			<div class="container">
				<ul class="category-tabs">
					<li class="tabs"><?php _e('All Templates','wp-template-on-the-fly'); ?></li>
					<li class="tabs"><?php _e('Add New','wp-template-on-the-fly'); ?></li>
				</ul>
				<div class="container">
					<form method="POST" action="" id="all_templates_list">
						<input type="hidden" name="parent" id="parent" value=''/>
						<input type="hidden" name="actions" id="action" value=''/>
						<div class="actions">
							<span><?php _e('Apply an action to selected Templates:','wp-template-on-the-fly'); ?></span>
							<img class="active" src="<?php echo $path;?>/img/status_1.png" onclick="$('#all_templates_list #action').val($(this).attr('class')).parent().submit();"/>
							<img class="inactive" src="<?php echo $path;?>/img/status_0.png" onclick="$('#all_templates_list #action').val($(this).attr('class')).parent().submit();"/>
							<img class="remove" src="<?php echo $path;?>/img/status_2.png" onclick="$('#all_templates_list #action').val($(this).attr('class')).parent().submit();"/>
						</div>
						<div class="templatesfiltering">
							<span><?php _e('Filters:','wp-template-on-the-fly'); ?></span>
							<div class="delimiterdivright">&nbsp;</div>
							<input type="checkbox" name="t_filter_show_primary"<?php echo $setting['t_filter_show_primary']==1?' checked="checked"':'';?>/>
							<span><?php _e('Show Primary','wp-template-on-the-fly'); ?></span>
							<div class="delimiterdivright">&nbsp;</div>
							<input type="checkbox" name="t_filter_show_active"<?php echo $setting['t_filter_show_active']==1?' checked="checked"':'';?>/>
							<span><?php _e('Show Active','wp-template-on-the-fly'); ?></span>
							<div class="delimiterdivright">&nbsp;</div>
							<input type="checkbox" name="t_filter_show_inactive"<?php echo $setting['t_filter_show_inactive']==1?' checked="checked"':'';?>/>
							<span><?php _e('Show Inactive','wp-template-on-the-fly'); ?></span>
							<div class="delimiterdivright">&nbsp;</div>
							<input type="checkbox" name="t_filter_show_deleted"<?php echo $setting['t_filter_show_deleted']==1?' checked="checked"':'';?>/>
							<span><?php _e('Show Deleted','wp-template-on-the-fly'); ?></span>
						</div>
						<div class="templatessorting">
						<?php for($j=0;$j<count(explode(",",$setting['t_sort_bases']));$j++){?>
							<div class="sort_tools_container">
								<span><?php _e('Sort('.($j+1).') Base:','wp-template-on-the-fly'); ?></span>
								<select name="t_sort_base_<?php echo $j;?>" id="t_sort_base_<?php echo $j;?>">
									<option value="-">-----</option>
								<?php foreach(explode(",",$setting['t_sort_bases']) as $sb){?>
									<option value="<?php echo $sb;?>"<?php echo isset($setting['t_sort_base_'.$j]) && $setting['t_sort_base_'.$j]==$sb?' selected="selected"':'';?>><?php echo ucfirst($sb);?></option>
								<?php } ?><?php /*echo isset($setting['t_sort_base_'.$j]) && $setting['t_sort_base_'.$j]==$sb?' selected="selected"':'';*/ ?>
								</select>
								<div class="delimiterdivright">&nbsp;</div>
								<span><?php _e('Sort('.($j+1).') Direction:','wp-template-on-the-fly'); ?></span>
								<select name="t_sort_dir_<?php echo $j;?>" id="t_sort_dir_<?php echo $j;?>">
									<option value="asc"<?php echo !isset($setting['t_sort_dir_'.$j]) || (isset($setting['t_sort_dir_'.$j]) && $setting['t_sort_dir_'.$j]=="asc")?' selected="selected"':'';?>><?php _e('Ascending','wp-template-on-the-fly'); ?></option>
									<option value="desc"<?php echo isset($setting['t_sort_dir_'.$j]) && $setting['t_sort_dir_'.$j]=="desc"?' selected="selected"':'';?>><?php _e('Descending','wp-template-on-the-fly'); ?></option>
								</select>
							</div>
						<?php } ?>
						</div>
						<div class="templatespaging">
							<span><?php _e('Pagination:','wp-template-on-the-fly'); ?></span>
							<div class="delimiterdivright">&nbsp;</div>
							<span><?php _e('Page Number:','wp-template-on-the-fly'); ?></span>
							<input type="text" name="templatespagenumber" class="templatespagenumber" value="<?php echo $setting['templatespagenumber'];?>" size="1"/>
							<div class="delimiterdivright">&nbsp;</div>
							<span><?php _e('From:','wp-template-on-the-fly'); ?></span>
							<input type="button" class="button templatestotalpages" value="<?php echo ceil($templatestotalpages/$setting['templatesperpage']);?>"/>
							<div class="delimiterdivright">&nbsp;</div>
							<span><?php _e('Counts Per Page:','wp-template-on-the-fly'); ?></span>
							<input type="text" name="templatesperpage" class="templatesperpage" value="<?php echo $setting['templatesperpage'];?>" size="1"/>
							<div class="delimiterdivright">&nbsp;</div>
							<div class="delimiterdivright">&nbsp;</div>
							<input type="submit" name="submit" class="button ok" value="<?php _e('submit','wp-template-on-the-fly'); ?>"/>
						</div>
					</form>
				</div>
				<div class="container">
					<form  method="POST" action="" id="add_template_req">
						<span><?php _e('Select Template Type:','wp-template-on-the-fly'); ?></span>
						<select name="add_template_req_sel" onchange="$(this).parent().submit();">
							<option value="-">----</option>
							<?php foreach($templates as $tpl){ ?>
							<option value="<?php echo $tpl->template_id;?>"><?php echo $tpl->template;?></option>							
							<?php } ?>
						</select>
					</form>
				</div>
			</div>
			<div class="container">
				<form action="" method="POST" id="sidebar_create_form">
					<input type="hidden" name="sidebar_create"/>
					<span><?php _e('Name of Sidebar:','wp-template-on-the-fly'); ?></span>
					<input type="text" name="name"/>
					<div class="delimiterdivright"></div>
					<span><?php _e('Description of Sidebar:','wp-template-on-the-fly'); ?></span>
					<input type="text" name="description"/>
					<input type="submit" class="button" value="<?php _e('Create Sidebar','wp-template-on-the-fly'); ?>"/>
				</form>
				<div class="sidebar_create_form_message hide message error"><?php _e('Name of Sidebar must be UNIQUE','wp-template-on-the-fly'); ?></div>
				<div class="delimiterdivbottom"></div>
			</div>
			<div class="container">
				<div class="template_sidebar_forms">
					<form action="" method="POST" id="template_sidebar_tpl_form">
						<div>
							<span><?php _e('Select a Template to modify:','wp-template-on-the-fly'); ?></span>
							<select name="template_sidebar_tpl" onchange="$(this).parent().submit();">
								<option value="-">----</option>
							<?php
								$templates = $wpdb->get_results("SELECT * FROM $t WHERE `template` NOT LIKE '%$%'");
								foreach($templates as $tpl){ ?>
									<option value="<?php echo $tpl->template_id;?>"><?php echo $tpl->template;?></option>
							<?php } ?>
							</select>
						</div>
					</form>
				</div>
				<div class="template_sidebar_css_pad">
					<form action="" method="POST" id="template_sidebar_css_pad">
						<div class="header">
							<center><h3><?php _e('Style Pad','wp-template-on-the-fly'); ?></h3></center>
							<span><?php _e('Target:','wp-template-on-the-fly'); ?></span>
							<input type="text" name="template_sidebar_css_pad_target_name" id="template_sidebar_css_pad_target_name"/>
							<input type="hidden" name="template_sidebar_css_pad_target_id" id="template_sidebar_css_pad_target_id"/>
							<input type="hidden" name="template_sidebar_css_pad_target_type" id="template_sidebar_css_pad_target_type"/>
							<input type="submit" name="submit" class="button" value="<?php _e('Save Style','wp-template-on-the-fly'); ?>"/>
						</div>
						<div class="css_pad_background_attachment">
							<span>Background Attachment:</span>
							<select name="csstag-background-attachment">
								<option value="-">----</option>
								<option value="scroll">scroll</option>
								<option value="fixed">fixed</option>
								<option value="local">local</option>
							</select>
						</div>
						<div class="css_pad_background_clip">
							<span>Background Clip:</span>
							<select name="csstag-background-clip">
								<option value="-">----</option>
								<option value="border-box">Border-Box</option>
								<option value="padding-box">Padding-Box</option>
								<option value="content-box">Content-Box</option>
							</select>
						</div>
						<div class="css_pad_background_color">
							<span>Background Color:</span>
							<select name="csstag-background-color" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="color">color</option>
								<option value="transparent">Transparent</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="background_color_color" class="hide color" id="background_color_color" size="3"/>
						</div>
						<div class="css_pad_background_image">
							<span>Background Image:</span>
							<select name="csstag-background-image" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="url">URL</option>
								<option value="none">None</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="background_image_url" class="hide url" size="10"/>
						</div>
						<div class="css_pad_background_origin">
							<span>Background Origin:</span>
							<select name="csstag-background-origin">
								<option value="-">----</option>
								<option value="border-box">Border-Box</option>
								<option value="padding-box">Padding-Box</option>
								<option value="content-box">Content-Box</option>
							</select>
						</div>
						<div class="css_pad_background_position">
							<span>Background Position:</span>
							<select name="csstag-background-position" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">xpos ypos</option>
								<option value="percentage">x% y%</option>
								<option value="left top">left top</option>
								<option value="left center">left center</option>
								<option value="left bottom">left bottom</option>
								<option value="right top">right top</option>
								<option value="right center">right center</option>
								<option value="right bottom">right bottom</option>
								<option value="center top">center top</option>
								<option value="center center">center center</option>
								<option value="center bottom">center bottom</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="background_position_percentage" class="hide percentage" size="3"/>
							<input type="text" name="background_position_length" class="hide length" size="3"/>
						</div>
						<div class="css_pad_background_repeat">
							<span>Background Repeat:</span>
							<select name="csstag-background-repeat">
								<option value="-">----</option>
								<option value="repeat">Repeat</option>
								<option value="repeat-x">Repeat-X</option>
								<option value="repeat-y">Repeat-Y</option>
								<option value="no-repeat">No-Repeat</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_background_size">
							<span>Background Size:</span>
							<select name="csstag-background-size" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="cover">Cover</option>
								<option value="contain">Contain</option>
							</select>
							<input type="text" name="background_size_percentage" class="hide percentage" size="1"/>
							<input type="text" name="background_size_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_bottom_color">
							<span>Border Bottom Color:</span>
							<select name="csstag-border-bottom-color" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="color">color</option>
								<option value="transparent">Transparent</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_bottom_color_color" class="hide color" id="border_bottom_color_color" size="3"/>
						</div>
						<div class="css_pad_border_bottom_left_radius">
							<span>Border Bottom Left Radius:</span>
							<select name="csstag-border-bottom-left-radius" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
							</select>
							<input type="text" name="border_bottom_left_radius_percentage" class="hide percentage" size="1"/>
							<input type="text" name="border_bottom_left_radius_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_bottom_right_radius">
							<span>Border Bottom Right Radius:</span>
							<select name="csstag-border-bottom-right-radius" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
							</select>
							<input type="text" name="border_bottom_right_radius_percentage" class="hide percentage" size="1"/>
							<input type="text" name="border_bottom_right_radius_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_bottom_style">
							<span>Border Bottom Style:</span>
							<select name="csstag-border-bottom-style">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="hidden">Hidden</option>
								<option value="dotted">Dotted</option>
								<option value="dashed">Dashed</option>
								<option value="solid">Solid</option>
								<option value="double">Double</option>
								<option value="groove">Groove</option>
								<option value="ridge">Ridge</option>
								<option value="inset">Inset</option>
								<option value="outset">Outset</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_border_bottom_width">
							<span>Border Bottom Width:</span>
							<select name="csstag-border-bottom-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="thin">Thin</option>
								<option value="medium">Medium</option>
								<option value="thick">Thick</option>
								<option value="length">Length</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_bottom_width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_collapse">
							<span>Border Collapse:</span>
							<select name="csstag-border-collapse">
								<option value="-">----</option>
								<option value="collapse">Collapse</option>
								<option value="separate">Separate</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_border_left_color">
							<span>Border Left Color:</span>
							<select name="csstag-border-left-color" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="color">color</option>
								<option value="transparent">Transparent</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_left_color_color" class="hide color" id="border_left_color_color" size="3"/>
						</div>
						<div class="css_pad_border_left_style">
							<span>Border Left Style:</span>
							<select name="csstag-border-left-style">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="hidden">Hidden</option>
								<option value="dotted">Dotted</option>
								<option value="dashed">Dashed</option>
								<option value="solid">Solid</option>
								<option value="double">Double</option>
								<option value="groove">Groove</option>
								<option value="ridge">Ridge</option>
								<option value="inset">Inset</option>
								<option value="outset">Outset</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_border_left_width">
							<span>Border Left Width:</span>
							<select name="csstag-border-left-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="thin">Thin</option>
								<option value="medium">Medium</option>
								<option value="thick">Thick</option>
								<option value="length">Length</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_left_width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_right_color">
							<span>Border Right Color:</span>
							<select name="csstag-border-right-color" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="color">color</option>
								<option value="transparent">Transparent</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_right_color_color" class="hide color" id="border_right_color_color" size="3"/>
						</div>
						<div class="css_pad_border_right_style">
							<span>Border Right Style:</span>
							<select name="csstag-border-right-style">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="hidden">Hidden</option>
								<option value="dotted">Dotted</option>
								<option value="dashed">Dashed</option>
								<option value="solid">Solid</option>
								<option value="double">Double</option>
								<option value="groove">Groove</option>
								<option value="ridge">Ridge</option>
								<option value="inset">Inset</option>
								<option value="outset">Outset</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_border_right_width">
							<span>Border Right Width:</span>
							<select name="csstag-border-right-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="thin">Thin</option>
								<option value="medium">Medium</option>
								<option value="thick">Thick</option>
								<option value="length">Length</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_right_width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_spacing">
							<span>Border Spacing:</span>
							<select name="csstag-border-spacing" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length Length</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_spacing_length" class="hide length" size="3"/>
						</div>
						<div class="css_pad_border_top_color">
							<span>Border Top Color:</span>
							<select name="csstag-border-top-color" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="color">color</option>
								<option value="transparent">Transparent</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_top_color_color" class="hide color" id="border_top_color_color" size="3"/>
						</div>
						<div class="css_pad_border_top_left_radius">
							<span>Border Top Left Radius:</span>
							<select name="csstag-border-top-left-radius" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
							</select>
							<input type="text" name="border_top_left_radius_percentage" class="hide percentage" size="1"/>
							<input type="text" name="border_top_left_radius_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_top_right_radius">
							<span>Border Top Right Radius:</span>
							<select name="csstag-border-top-right-radius" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
							</select>
							<input type="text" name="border_top_right_radius_percentage" class="hide percentage" size="1"/>
							<input type="text" name="border_top_right_radius_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_border_top_style">
							<span>Border Top Style:</span>
							<select name="csstag-border-top-style">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="hidden">Hidden</option>
								<option value="dotted">Dotted</option>
								<option value="dashed">Dashed</option>
								<option value="solid">Solid</option>
								<option value="double">Double</option>
								<option value="groove">Groove</option>
								<option value="ridge">Ridge</option>
								<option value="inset">Inset</option>
								<option value="outset">Outset</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_border_top_width">
							<span>Border Top Width:</span>
							<select name="csstag-border-top-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="thin">Thin</option>
								<option value="medium">Medium</option>
								<option value="thick">Thick</option>
								<option value="length">Length</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="border_top_width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_bottom">
							<span>Bottom:</span>
							<select name="csstag-bottom" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="bottom_percentage" class="hide percentage" size="1"/>
							<input type="text" name="bottom_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_color">
							<span>Color:</span>
							<select name="csstag-color" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="color">color</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="color_color" class="hide color" id="color_color" size="3"/>
						</div>
						<div class="css_pad_display">
							<span>Display:</span>
							<select name="csstag-display">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="inherit">Inherit</option>
								<option value="block">Block</option>
								<option value="inline">Inline</option>
								<option value="inline-block">Inline-Block</option>
								<option value="inline-table">Inline-Table</option>
								<option value="list-item">List-Item</option>
								<option value="run-in">Run-In</option>
								<option value="table">Table</option>
								<option value="table-caption">Table-Caption</option>
								<option value="table-column-group">Table-Column-Group</option>
								<option value="table-header-group">Table-Header-Group</option>
								<option value="table-footer-group">Table-Footer-Group</option>
								<option value="table-row-group">Table-Row-Group</option>
								<option value="table-cell">Table-Cell</option>
								<option value="table-column">Table-Column</option>
								<option value="table-row">Table-Row</option>
							</select>
						</div>
						<div class="css_pad_float">
							<span>Float:</span>
							<select name="csstag-float">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="left">Left</option>
								<option value="right">Right</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_font_family">
							<span>Font Family:</span>
							<select name="csstag-font-family" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="inherit">Inherit</option>
								<option value="fontname">Font Name</option>
							</select>
							<input type="text" name="font_family_fontname" class="hide fontname" size="2"/>
						</div>
						<div class="css_pad_font_size">
							<span>Font Size:</span>
							<select name="csstag-font-size" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
								<option value="xx-small">XX-Small</option>
								<option value="x-small">X-Small</option>
								<option value="small">Small</option>
								<option value="medium">Medium</option>
								<option value="large">Large</option>
								<option value="x-large">X-Large</option>
								<option value="xx-large">XX-Large</option>
								<option value="smaller">Smaller</option>
								<option value="larger">Larger</option>
							</select>
							<input type="text" name="font_size_percentage" class="hide percentage" size="1"/>
							<input type="text" name="font_size_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_font_stretch">
							<span>Font Stretch:</span>
							<select name="csstag-font-stretch">
								<option value="-">----</option>
								<option value="normal">Normal</option>
								<option value="wider">Wider</option>
								<option value="narrower">Narrower</option>
								<option value="ultra-condensed">Ultra-Condensed</option>
								<option value="extra-condensed">Extra-Condensed</option>
								<option value="condensed">Condensed</option>
								<option value="semi-condensed">Semi-Condensed</option>
								<option value="semi-expanded">Semi-Expanded</option>
								<option value="expanded">Expanded</option>
								<option value="extra-expanded">Extra-Expanded</option>
								<option value="ultra-expanded">Ultra-Expanded</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_font_style">
							<span>Font Style:</span>
							<select name="csstag-font-style">
								<option value="-">----</option>
								<option value="normal">Normal</option>
								<option value="italic">Italic</option>
								<option value="oblique">Oblique</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_font_variant">
							<span>Font Variant:</span>
							<select name="csstag-font-variant">
								<option value="-">----</option>
								<option value="normal">Normal</option>
								<option value="small-caps">Small-Caps</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_font_weight">
							<span>Font Weight:</span>
							<select name="csstag-font-weight">
								<option value="-">----</option>
								<option value="normal">Normal</option>
								<option value="bold">bold</option>
								<option value="bolder">bolder</option>
								<option value="lighter">lighter</option>
								<option value="100">100</option>
								<option value="200">200</option>
								<option value="300">300</option>
								<option value="400">400</option>
								<option value="500">500</option>
								<option value="600">600</option>
								<option value="700">700</option>
								<option value="800">800</option>
								<option value="900">900</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_height">
							<span>Height:</span>
							<select name="csstag-height" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="height_percentage" class="hide percentage" size="1"/>
							<input type="text" name="height_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_left">
							<span>Left:</span>
							<select name="csstag-left" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="left_percentage" class="hide percentage" size="1"/>
							<input type="text" name="left_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_line_height">
							<span>Line Height:</span>
							<select name="csstag-line-height" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="normal">Normal</option>
								<option value="number">Number</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="line_height_percentage" class="hide percentage" size="1"/>
							<input type="text" name="line_height_length" class="hide length" size="1"/>
							<input type="text" name="line_height_number" class="hide number" size="1"/>
						</div>
						<div class="css_pad_margin_bottom">
							<span>Margin Bottom:</span>
							<select name="csstag-margin-bottom" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="margin_bottom_percentage" class="hide percentage" size="1"/>
							<input type="text" name="margin_bottom_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_margin_left">
							<span>Margin Left:</span>
							<select name="csstag-margin-left" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="margin_left_percentage" class="hide percentage" size="1"/>
							<input type="text" name="margin_left_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_margin_right">
							<span>Margin Right:</span>
							<select name="csstag-margin-right" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="margin_right_percentage" class="hide percentage" size="1"/>
							<input type="text" name="margin_right_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_margin_top">
							<span>Margin Top:</span>
							<select name="csstag-margin-top" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="margin_top_percentage" class="hide percentage" size="1"/>
							<input type="text" name="margin_top_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_max_height">
							<span>Max Height:</span>
							<select name="csstag-max-height" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="max_height_percentage" class="hide percentage" size="1"/>
							<input type="text" name="max_height_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_max_width">
							<span>Max Width:</span>
							<select name="csstag-max-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="none">None</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="max_width_percentage" class="hide percentage" size="1"/>
							<input type="text" name="max_width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_min_height">
							<span>Min Height:</span>
							<select name="csstag-min-height" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="min_height_percentage" class="hide percentage" size="1"/>
							<input type="text" name="min_height_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_min_width">
							<span>Min Width:</span>
							<select name="csstag-min-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="min_width_percentage" class="hide percentage" size="1"/>
							<input type="text" name="min_width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_overflow_x">
							<span>Overflow X:</span>
							<select name="csstag-overflo-x">
								<option value="-">----</option>
								<option value="visible">Visible</option>
								<option value="hidden">Hidden</option>
								<option value="scroll">Scroll</option>
								<option value="auto">Auto</option>
								<option value="no-display">No-Display</option>
								<option value="no-content">No-Content</option>
							</select>
						</div>
						<div class="css_pad_overflow_y">
							<span>Overflow Y:</span>
							<select name="csstag-overflo-y">
								<option value="-">----</option>
								<option value="visible">Visible</option>
								<option value="hidden">Hidden</option>
								<option value="scroll">Scroll</option>
								<option value="auto">Auto</option>
								<option value="no-display">No-Display</option>
								<option value="no-content">No-Content</option>
							</select>
						</div>
						<div class="css_pad_padding_bottom">
							<span>Padding Bottom:</span>
							<select name="csstag-padding-bottom" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="padding_bottom_percentage" class="hide percentage" size="1"/>
							<input type="text" name="padding_bottom_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_padding_left">
							<span>Padding Left:</span>
							<select name="csstag-padding-left" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="padding_left_percentage" class="hide percentage" size="1"/>
							<input type="text" name="padding_left_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_padding_right">
							<span>Padding Right:</span>
							<select name="csstag-padding-right" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="padding_right_percentage" class="hide percentage" size="1"/>
							<input type="text" name="padding_right_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_padding_top">
							<span>Padding Top:</span>
							<select name="csstag-padding-top" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="padding_top_percentage" class="hide percentage" size="1"/>
							<input type="text" name="padding_top_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_position">
							<span>Position:</span>
							<select name="csstag-position">
								<option value="-">----</option>
								<option value="static">Static</option>
								<option value="relative">Relative</option>
								<option value="absolute">Absolute</option>
								<option value="fixed">Fixed</option>
							</select>
						</div>
						<div class="css_pad_right">
							<span>Right:</span>
							<select name="csstag-right" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="right_percentage" class="hide percentage" size="1"/>
							<input type="text" name="right_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_text_align">
							<span>Text Align:</span>
							<select name="csstag-text-align">
								<option value="-">----</option>
								<option value="center">Center</option>
								<option value="left">Left</option>
								<option value="right">Right</option>
								<option value="justify">Justify</option>
								<option value="inherit">Inherit</option>
							</select>
						</div>
						<div class="css_pad_top">
							<span>Top:</span>
							<select name="csstag-top" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="top_percentage" class="hide percentage" size="1"/>
							<input type="text" name="top_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_vertical_align">
							<span>Vertical Align:</span>
							<select name="csstag-vertical-align" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="baseline">Baseline</option>
								<option value="sub">Sub</option>
								<option value="super">Super</option>
								<option value="top">Top</option>
								<option value="text-top">Text-Top</option>
								<option value="middle">Middle</option>
								<option value="bottom">Bottom</option>
								<option value="text-bottom">Text-Bottom</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="vertical_align_percentage" class="hide percentage" size="1"/>
							<input type="text" name="vertical_align_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_width">
							<span>Width:</span>
							<select name="csstag-width" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="length">Length</option>
								<option value="percentage">%</option>
								<option value="auto">Auto</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="width_percentage" class="hide percentage" size="1"/>
							<input type="text" name="width_length" class="hide length" size="1"/>
						</div>
						<div class="css_pad_z_index">
							<span>Z Index:</span>
							<select name="csstag-z-index" onchange="css_pad_input_show($(this));">
								<option value="-">----</option>
								<option value="auto">Auto</option>
								<option value="number">Number</option>
								<option value="inherit">Inherit</option>
							</select>
							<input type="text" name="line_height_number" class="hide number" size="1"/>
						</div>
					</form>
				</div>
				<div class="delimiterdivbottom"></div>
			</div>
		</div>
	</div>
<?php
}
function wp_template_on_the_fly(){
	global $wp_query;
	global $wpdb;
	$t = $wpdb->prefix."template_on_the_fly_template"; 
	$templates_ = $wpdb->get_results("SELECT * FROM $t WHERE `status`=1 AND template NOT LIKE '%$%'");
	$templates = array();
	foreach($templates_ as $item)
		$templates[$item->template] = array($item->template_id,$item->style);
	if(is_robots())	return;
	if(is_feed()) return;
	if(is_trackback()) return;
	if(defined('WP_USE_THEMES') && WP_USE_THEMES){
		if(is_404()){
			if(isset($templates['404.php'])){
				echo_template_and_sidebars("404.php",$templates);
				exit();
			}
		}elseif(is_search()){
			if(isset($templates['search.php'])){
				echo_template_and_sidebars("search.php",$templates);
				exit();
			}
		}elseif(is_archive()){
			if(is_tax()){
				$taxonomy = $wp_query->queried_object->taxonomy;
				$term = $wp_query->queried_object->slug;
				if(isset($templates["taxonomy-$taxonomy-$term.php"])){
					echo_template_and_sidebars("taxonomy-$taxonomy-$term.php",$templates);
					exit();
				}
				if(isset($templates["taxonomy-$taxonomy.php"])){
					echo_template_and_sidebars("taxonomy-$taxonomy.php",$templates);
					exit();
				}
				if(isset($templates["taxonomy.php"])){
					echo_template_and_sidebars("taxonomy.php",$templates);
					exit();
				}
			}elseif(is_category()){
				$slug = $wp_query->queried_object->slug;
				$id = $wp_query->queried_object->term_id;
				if(isset($templates["category-$slug.php"])){
					echo_template_and_sidebars("category-$slug.php",$templates);
					exit();
				}
				if(isset($templates["category-$id.php"])){
					echo_template_and_sidebars("category-$id.php",$templates);
					exit();
				}
				if(isset($templates["category.php"])){
					echo_template_and_sidebars("category.php",$templates);
					exit();
				}
			}elseif(is_tag()){
				$slug = $wp_query->queried_object->slug;
				$id = $wp_query->queried_object->term_id;
				if(isset($templates["tag-$slug.php"])){
					echo_template_and_sidebars("tag-$slug.php",$templates);
					exit();
				}
				if(isset($templates["tag-$id.php"])){
					echo_template_and_sidebars("tag-$id.php",$templates);
					exit();
				}
				if(isset($templates["tag.php"])){
					echo_template_and_sidebars("tag.php",$templates);
					exit();
				}
			}elseif(is_author()){
				$id = $wp_query->query_vars['author'];
				$author = get_the_author_meta($id);
				$nicename = $author->user_nicename;
				if(isset($templates["author-$nicename.php"])){
					echo_template_and_sidebars("author-$nicename.php",$templates);
					exit();
				}
				if(isset($templates["author-$id.php"])){
					echo_template_and_sidebars("author-$id.php",$templates);
					exit();
				}
				if(isset($templates["author.php"])){
					echo_template_and_sidebars("author.php",$templates);
					exit();
				}
			}elseif(is_date()){
				if(is_day()){
					$day = $wp_query->query['day'];
					if(isset($templates["day-$day.php"])){
						echo_template_and_sidebars("day-$day.php",$templates);
						exit();
					}
					if(isset($templates["date-day.php"])){
						echo_template_and_sidebars("date-day.php",$templates);
						exit();
					}
				}elseif(is_month()){
					$month = $wp_query->query['monthnum'];
					if(isset($templates["month-$month.php"])){
						echo_template_and_sidebars("month-$month.php",$templates);
						exit();
					}
					if(isset($templates["date-month.php"])){
						echo_template_and_sidebars("date-month.php",$templates);
						exit();
					}
				}elseif(is_year()){
					$year = $wp_query->query['year'];
					if(isset($templates["year-$year.php"])){
						echo_template_and_sidebars("year-$year.php",$templates);
						exit();
					}
					if(isset($templates["date-year.php"])){
						echo_template_and_sidebars("date-year.php",$templates);
						exit();
					}
				}
				if(isset($templates["date.php"])){
					echo_template_and_sidebars("date.php",$templates);
					exit();
				}
			}elseif(is_post_type_archive()){
				$posttype = $wp_query->query_vars['post_type'];
				if(isset($templates["archive-$posttype.php"])){
					echo_template_and_sidebars("archive-$posttype.php",$templates);
					exit();
				}
			}
			if(isset($templates["archive.php"])){
				echo_template_and_sidebars("archive.php",$templates);
				exit();
			}
		}elseif(is_singular()){
			if(is_single()){
				if(is_attachment()){
					$mimetype = explode("/",get_post_mime_type());
					$subtype = $mimetype[1];
					$mimetype = $mimetype[0];
					if(isset($templates["$mimetype.php"])){
						echo_template_and_sidebars("$mimetype.php",$templates);
						exit();
					}
					if(isset($templates["$subtype.php"])){
						echo_template_and_sidebars("$subtype.php",$templates);
						exit();
					}
					if(isset($templates["$mimetype_$subtype.php"])){
						echo_template_and_sidebars("$mimetype_$subtype.php",$templates);
						exit();
					}
					if(isset($templates["attachment.php"])){
						echo_template_and_sidebars("attachment.php",$templates);
						exit();
					}
				}else{
					$posttype = get_post_type();
					if(isset($templates["single-$posttype.php"])){
						echo_template_and_sidebars("single-$posttype.php",$templates);
						exit();
					}
				}
				if(isset($templates["single.php"])){
					echo_template_and_sidebars("single.php",$templates);
					exit();
				}
			}elseif(is_page()){
				$page = $wp_query->queried_object;
				$custom = get_post_meta($page->ID,'_wp_page_template');
				$custom = $custom[0];
				if($custom!='default'){
					if(isset($templates[$custom])){
						echo_template_and_sidebars($custom,$templates);
						exit();
					}
				}
				$slug = $page->post_name;
				if(isset($templates["page-$slug.php"])){
					echo_template_and_sidebars("page-$slug.php",$templates);
					exit();
				}
				$id = $page->ID;
				if(isset($templates["page-$id.php"])){
					echo_template_and_sidebars("page-$id.php",$templates);
					exit();
				}
				if(isset($templates['page.php'])){
					echo_template_and_sidebars("page.php",$templates);
					exit();
				}
			}
		}elseif(is_front_page()){
			if(isset($templates['front-page.php'])){
				echo_template_and_sidebars("front-page.php",$templates);
				exit();
			}
			$front = get_option('show_on_front');
			if($front=="posts"){
				if(isset($templates['home.php'])){
					echo_template_and_sidebars("home.php",$templates);
					exit();
				}
			}
			if($front=="page"){
				$page = get_option('page_on_front');
				if($page>0){
					$custom = get_option('_wp_page_template');
					if($custom!='default'){
						if(isset($templates["$custom.php"])){
							echo_template_and_sidebars("$custom.php",$templates);
							exit();
						}
					}
					$page = get_post($page);
					$slug = $page->post_name;
					if(isset($templates["page-$slug.php"])){
						echo_template_and_sidebars("page-$slug.php",$templates);
						exit();
					}
					$id = $page->ID;
					if(isset($templates["page-$id.php"])){
						echo_template_and_sidebars("page-$id.php",$templates);
						exit();
					}
					if(isset($templates['page.php'])){
						echo_template_and_sidebars("page.php",$templates);
						exit();
					}
				}
			}
		}elseif(is_home()){
			if(isset($templates['home.php'])){
				echo_template_and_sidebars("home.php",$templates);
				exit();
			}
		}elseif(is_comments_popup()){
			if(isset($templates['comments-popup.php'])){
				echo_template_and_sidebars("comments-popup.php",$templates);
				exit();
			}
		}
		if(isset($templates['index.php'])){
			echo_template_and_sidebars("index.php",$templates);
			exit();
		}
	}
	return;
}
function echo_template_and_sidebars($template,$templates){
	global $wpdb;
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar"; 
	$tid = $templates[$template][0];
	$sidebars = $wpdb->get_results("SELECT * FROM $ts WHERE template_id=$tid ORDER BY `sort` ASC");
	?>
	<div id="<?php echo $template;?>" class="template_container" style="<?php echo $templates[$template][1];?>">
	<?php foreach($sidebars as $s){ ?>
		<div id="<?php echo $s->sidebar.'-'.$s->sort;?>" class="template_sidebar" style="<?php echo $s->style;?>">
		<?php dynamic_sidebar($s->sidebar); ?>
		</div>
	<?php } ?>
	</div>
	<?php
}
function wp_template_on_the_fly_db_drop(){
	global $wpdb;
	$t_s = $wpdb->prefix."template_on_the_fly_template_sidebar"; 
	$t = $wpdb->prefix."template_on_the_fly_template"; 
	$res = $wpdb->query("DROP TABLE $t_s,$t"); 
	delete_option('template_on_the_fly_setting');
}
function wp_template_on_the_fly_db_create(){
	$template_on_the_fly_setting = array(
										'parent'=> '',
										'templatespagenumber'=> 1,
										'templatesperpage'=> 50,
										't_filter_show_active'=> 1,
										't_filter_show_deleted'=> 1,
										't_filter_show_inactive'=> 1,
										't_filter_show_primary'=> 1,
										't_sort_bases'=> 'template,status,parent',
										't_sort_base_0'=> '-',
										't_sort_base_1'=> '-',
										't_sort_base_2'=> '-',
										't_sort_dir_0'=> 'asc',
										't_sort_dir_1'=> 'asc',
										't_sort_dir_2'=> 'asc'
										);
	add_option('template_on_the_fly_setting',$template_on_the_fly_setting,'','yes');
	global $wpdb;
	$t_s = $wpdb->prefix."template_on_the_fly_template_sidebar"; 
	$t = $wpdb->prefix."template_on_the_fly_template"; 
	$sql_t_s = "CREATE TABLE IF NOT EXISTS `$t_s` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `template_id` int(11) NOT NULL,
				  `sidebar` varchar(255) NOT NULL,
				  `sort` int(11) NOT NULL,
				  `style` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$sql_t = "CREATE TABLE IF NOT EXISTS `$t` (
				  `template_id` int(11) NOT NULL AUTO_INCREMENT,
				  `template` varchar(255) NOT NULL,
				  `status` tinyint(2) NOT NULL DEFAULT '0',
				  `isprimary` tinyint(1) NOT NULL DEFAULT '0',
				  `parent` varchar(255) NOT NULL,
				  `style` text NOT NULL,
				  `description` text NOT NULL,
				  PRIMARY KEY (`template_id`),
				  UNIQUE KEY `template` (`template`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	$sql_t_i = "INSERT IGNORE INTO `$t` (`template_id`, `template`, `status`, `isprimary`, `parent`, `style`, `description`) VALUES ".
				'(1, "index.php", 0, 1, "0", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(2, "archive.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(3, "404.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(4, "search.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(5, "single.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(6, "page.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(7, "home.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(8, "front-page.php", 0, 1, "0,1,7", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(9, "comments-popup.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(10, "date.php", 0, 1, "0,1,2", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(11, "attachment.php", 0, 1, "0,1,5", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(12, "paged.php", 0, 1, "0,1", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(13, "single-post.php", 0, 1, "0,1,5", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(14, "taxonomy.php", 0, 1, "0,1,2", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(15, "category.php", 0, 1, "0,1,2", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(16, "tag.php", 0, 1, "0,1,2", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(17, "author.php", 0, 1, "0,1,2", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(18, "year.php", 0, 1, "0,1,2,10", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(19, "month.php", 0, 1, "0,1,2,10", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(20, "day.php", 0, 1, "0,1,2,10", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(21, "week.php", 0, 1, "0,1,2,10", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(22, "year-$year.php", 0, 1, "0,1,2,10,18", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(23, "month-$month.php", 0, 1, "0,1,2,10,19", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(24, "day-$day.php", 0, 1, "0,1,2,10,20", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(25, "week-$week.php", 0, 1, "0,1,2,10,21", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(26, "taxonomy-$taxonomy.php", 0, 1, "0,1,2,14", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(27, "taxonomy-$taxonomy-$term.php", 0, 1, "0,1,2,14,26", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(28, "category-$slug.php", 0, 1, "0,1,2,15,29", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(29, "category-$id.php", 0, 1, "0,1,2,15", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(30, "tag-$slug.php", 0, 1, "0,1,2,16,31", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(31, "tag-$id.php", 0, 1, "0,1,2,16", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(32, "author-$nicename.php", 0, 1, "0,1,2,17,33", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(33, "author-$id.php", 0, 1, "0,1,2,17", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(34, "archive-$posttype.php", 0, 1, "0,1,2", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(35, "$mimetype.php", 0, 1, "0,1,5,11,37,36", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(36, "$subtype.php", 0, 1, "0,1,5,11,37", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(37, "$mimetype_$subtype.php", 0, 1, "0,1,5,11", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(38, "single-$posttype.php", 0, 1, "0,1,5", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(39, "page-$slug.php", 0, 1, "0,1,6,40", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(40, "page-$id.php", 0, 1, "0,1,6", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", ""),
				(41, "$custom.php", 0, 1, "0,1,6,40,39", "max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;", "");';
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta($sql_t_s);
	dbDelta($sql_t);
	$res = $wpdb->query($sql_t_i);
}
function t_o_t_f_find_parent($parents){
	global $wpdb;
	$t = $wpdb->prefix."template_on_the_fly_template";
	$templates = $wpdb->get_results("SELECT `template`,`template_id` FROM $t WHERE template_id IN ($parents)");
	for($i=0;$i<count($templates);$i++)
		$templates[$i] = array('template'=>$templates[$i]->template,'id'=>$templates[$i]->template_id);
	return $templates;
}
function archive_posttype($input=''){
	if($input==''){
		$post_types = get_post_types(); ?>
		<div class="archive_posttype">
			<span><?php _e('Select a Post Type:','wp-template-on-the-fly'); ?></span>
			<select name="archive_posttype">
				<option value="-">----</option>
			<?php
			foreach($post_types as $pt){ ?>
				<option value="<?php echo urlencode($pt);?>"><?php echo $pt;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['archive_posttype'],'archive-$posttype.php','archive-'.$input['archive_posttype'],'select a Post Type');
	return;
}
function year_year($input=''){
	if($input==''){ ?>
		<div class="year_year">
			<span><?php _e('Input year number in 4 digits format:','wp-template-on-the-fly'); ?></span>
			<input type="text" name="year_year"/>
		</div>
		<?php
	}else return insert_template_to_db('','year-$year.php','year-'.$input['year_year'],'');
	return;
}
function month_month($input=''){
	if($input==''){ ?>
		<div class="month_month">
			<span><?php _e('Input month number in 2 digits format:','wp-template-on-the-fly'); ?></span>
			<input type="text" name="month_month"/>
		</div>
		<?php
	}else return insert_template_to_db('','month-$month.php','month-'.$input['month_month'],'');
	return;
}
function day_day($input=''){
	if($input==''){ ?>
		<div class="day_day">
			<span><?php _e('Input day number of the month:','wp-template-on-the-fly'); ?></span>
			<input type="text" name="day_day"/>
		</div>
		<?php
	}else return insert_template_to_db('','day-$day.php','day-'.$input['day_day'],'');
	return;
}
function week_week($input=''){
	if($input==''){ ?>
		<div class="week_week">
			<span><?php _e('Input week number of the year:','wp-template-on-the-fly'); ?></span>
			<input type="text" name="week_week"/>
		</div>
		<?php
	}else return insert_template_to_db('','week-$week.php','week-'.$input['week_week'],'');
	return;
}
function taxonomy_taxonomy($input=''){
	if($input==''){
		$taxs = get_taxonomies(); ?>
		<div class="taxonomy_taxonomy">
			<span><?php _e('Select a Taxonomy:','wp-template-on-the-fly'); ?></span>
			<select name="taxonomy_taxonomy">
				<option value="-">----</option>
			<?php
			foreach($taxs as $tx){ ?>
				<option value="<?php echo urlencode($tx);?>"><?php echo $tx;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['taxonomy_taxonomy'],'taxonomy-$taxonomy.php','taxonomy-'.$input['taxonomy_taxonomy'],'select a Taxonomy');
	return;
}
function taxonomy_taxonomy_term($input=''){
	if($input==''){
		$taxs = get_taxonomies(); ?>
		<div class="taxonomy_taxonomy_term">
			<span><?php _e('Select a Taxonomy:','wp-template-on-the-fly'); ?></span>
			<select name="taxonomy_taxonomy" onchange="$('.taxonomy_taxonomy_term .taxonomy_term').attr({'disabled':'disabled'}).addClass('hide');$('.taxonomy_taxonomy_term .taxonomy_term.'+$(this).val()).removeAttr('disabled').removeClass('hide');">
				<option value="-">----</option>
			<?php
			foreach($taxs as $tx){ ?>
				<option value="<?php echo urlencode($tx);?>"><?php echo $tx;?></option>
			<?php } ?>
			</select>
			<?php
			foreach($taxs as $tx){
				$terms = get_terms($tx); ?>
				<div class="taxonomy_term hide <?php echo $tx;?>">
					<span><?php _e('Select a Term:','wp-template-on-the-fly'); ?></span>
					<select name="taxonomy_taxonomy_term" class="taxonomy_term hide <?php echo $tx;?>">
						<option value="-">----</option>
					<?php foreach($terms as $tr){ ?>
							<option value="<?php echo $tr->slug;?>"><?php echo $tr->name;?></option>
					<?php } ?>
					</select>
				</div>
			<?php } ?>
		</div>
		<?php
	}else{
		if($input['taxonomy_taxonomy']!='-')
			return insert_template_to_db($input['taxonomy_taxonomy_term'],'taxonomy-$taxonomy-$term.php','taxonomy-'.$input['taxonomy_taxonomy'].'-'.$input['taxonomy_taxonomy_term'],'select a Term');
		else return "Please select a Taxonomy.";
	}
	return;
}
function category_id($input=''){
	if($input==''){
		$cats = get_categories(); ?>
		<div class="category_id">
			<span><?php _e('Select a Category:','wp-template-on-the-fly'); ?></span>
			<select name="category_id">
				<option value="-">----</option>
			<?php
			foreach($cats as $ct){ ?>
				<option value="<?php echo $ct->term_id;?>"><?php echo $ct->name;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['category_id'],'category-$id.php','category-'.$input['category_id'],'select a Category');
	return;
}
function category_slug($input=''){
	if($input==''){
		$cats = get_categories(); ?>
		<div class="category_slug">
			<span><?php _e('Select a Category:','wp-template-on-the-fly'); ?></span>
			<select name="category_slug">
				<option value="-">----</option>
			<?php
			foreach($cats as $ct){ ?>
				<option value="<?php echo $ct->slug;?>"><?php echo $ct->name;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['category_slug'],'category-$slug.php','category-'.$input['category_slug'],'select a Category');
	return;
}
function tag_id($input=''){
	if($input==''){
		$tags = get_terms('post_tag'); ?>
		<div class="tag_id">
			<span><?php _e('Select a Tag:','wp-template-on-the-fly'); ?></span>
			<select name="tag_id">
				<option value="-">----</option>
			<?php
			foreach($tags as $tg){ ?>
				<option value="<?php echo $tg->term_id;?>"><?php echo $tg->name;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['tag_id'],'tag-$id.php','tag-'.$input['tag_id'],'select a Tag');
	return;
}
function tag_slug($input=''){
	if($input==''){
		$tags = get_terms('post_tag'); ?>
		<div class="tag_slug">
			<span><?php _e('Select a Tag:','wp-template-on-the-fly'); ?></span>
			<select name="tag_slug">
				<option value="-">----</option>
			<?php
			foreach($tags as $tg){ ?>
				<option value="<?php echo $tg->slug;?>"><?php echo $tg->name;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['tag_slug'],'tag-$slug.php','tag-'.$input['tag_slug'],'select a Tag');
	return;
}
function author_id($input=''){
	if($input==''){
		$users = get_users(); ?>
		<div class="author_id">
			<span><?php _e('Select an Author:','wp-template-on-the-fly'); ?></span>
			<select name="author_id">
				<option value="-">----</option>
			<?php
			foreach($users as $us){ ?>
				<option value="<?php echo $us->ID;?>"><?php echo $us->user_nicename;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['author_id'],'author-$id.php','author-'.$input['author_id'],'select a Author');
	return;
}
function author_nicename($input=''){
	if($input==''){
		$users = get_users(); ?>
		<div class="author_nicename">
			<span><?php _e('Select an Author:','wp-template-on-the-fly'); ?></span>
			<select name="author_nicename">
				<option value="-">----</option>
			<?php
			foreach($users as $us){ ?>
				<option value="<?php echo $us->user_nicename;?>"><?php echo $us->user_nicename;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['author_nicename'],'author-$nicename.php','author-'.$input['author_nicename'],'select a Author');
	return;
}
function single_posttype($input=''){
	if($input==''){
		$post_types = get_post_types(); ?>
		<div class="single_posttype">
			<span><?php _e('Select a Post Type:','wp-template-on-the-fly'); ?></span>
			<select name="single_posttype">
				<option value="-">----</option>
			<?php
			foreach($post_types as $pt){ ?>
				<option value="<?php echo $pt;?>"><?php echo $pt;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['single_posttype'],'single-$posttype.php','single-'.$input['single_posttype'],'select a Post Type');
	return;
}
function mimetype_subtype($input=''){
	if($input==''){
		$mimetype_subtype = array();
		$mime = wp_get_mime_types();
		foreach($mime as $k=>$m){
			$s = substr($m,0,strpos($m,"/"));
			$d = explode('|',$k);
			foreach($d as $t)
				if(!in_array($s.'_'.$t,$mimetype_subtype)) $mimetype_subtype[] = $s.'_'.$t;
		} ?>
		<div class="mimetype_subtype">
			<span><?php _e('Select a Mimetype_Subtype:','wp-template-on-the-fly'); ?></span>
			<select name="mimetype_subtype">
				<option value="-">----</option>
			<?php
			foreach($mimetype_subtype as $ms){ ?>
				<option value="<?php echo $ms;?>"><?php echo $ms;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['mimetype_subtype'],'$mimetype_$subtype.php',$input['mimetype_subtype'],'select a Mimetype_Subtype');
	return;
}
function subtype($input=''){
	if($input==''){
		$subtype = array();
		$mime = wp_get_mime_types();
		foreach($mime as $k=>$m){
			$d = explode('|',$k);
			foreach($d as $t)
				if(!in_array($t,$subtype)) $subtype[] = $t;
		} ?>
		<div class="subtype">
			<span><?php _e('Select a Subtype:','wp-template-on-the-fly'); ?></span>
			<select name="subtype">
				<option value="-">----</option>
			<?php
			foreach($subtype as $s){ ?>
				<option value="<?php echo $s;?>"><?php echo $s;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['subtype'],'$subtype.php',$input['subtype'],'select a Subtype');
	return;
}
function mimetype($input=''){
	if($input==''){
		$mimetype = array();
		$mime = wp_get_mime_types();
		foreach($mime as $k=>$m){
			$s = substr($m,0,strpos($m,"/"));
			if(!in_array($s,$mimetype)) $mimetype[] = $s;
		} ?>
		<div class="mimetype">
			<span><?php _e('Select a Mimetype:','wp-template-on-the-fly'); ?></span>
			<select name="mimetype">
				<option value="-">----</option>
			<?php
			foreach($mimetype as $s){ ?>
				<option value="<?php echo $s;?>"><?php echo $s;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['mimetype'],'$mimetype.php',$input['mimetype'],'select a Mimetype');
	return;
}
function page_id($input=''){
	if($input==''){
		$pages = get_pages(); ?>
		<div class="page_id">
			<span><?php _e('Select a Page:','wp-template-on-the-fly'); ?></span>
			<select name="page_id">
				<option value="-">----</option>
			<?php
			foreach($pages as $pg){ ?>
				<option value="<?php echo $pg->ID;?>"><?php echo $pg->post_title;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['page_id'],'page-$id.php','page-'.$input['page_id'],'select a Page');
	return;
}
function page_slug($input=''){
	if($input==''){
		$pages = get_pages(); ?>
		<div class="page_slug">
			<span><?php _e('Select a Page:','wp-template-on-the-fly'); ?></span>
			<select name="page_slug">
				<option value="-">----</option>
			<?php
			foreach($pages as $pg){ ?>
				<option value="<?php echo $pg->post_name;?>"><?php echo $pg->post_title;?></option>
			<?php } ?>
			</select>
		</div>
		<?php
	}else return insert_template_to_db($input['page_slug'],'page-$slug.php','page-'.$input['page_slug'],'select a Page');
	return;
}
function custom($input=''){
	if($input==''){ ?>
		<div class="custom">
			<span><?php _e('Insert a Custom name for your Template:','wp-template-on-the-fly'); ?></span>
			<input type="text" name="custom"/>
		</div>
		<?php
	}else return insert_template_to_db($input['custom'],'$custom.php',urlencode($input['custom']),'enter a correct name');
	return;
}
function insert_template_to_db($input,$parent,$template,$message){
	if($input!='-'){
		global $wpdb;
		$t = $wpdb->prefix."template_on_the_fly_template";
		$template = $template.'.php';
		if($wpdb->get_var("SELECT COUNT(*) FROM $t WHERE template='$template'")==0){
			$parent = $wpdb->get_row("SELECT * FROM $t WHERE template='$parent'");
			$parent = $parent->parent.",".$parent->template_id;
			$style = 'max-width:960px;min-height:100px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;background-color:#d2d2d2;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;';
			if($wpdb->query("INSERT INTO $t (`template`,`parent`,`style`) VALUES ('$template','$parent','$style')")!==false)
				return "Your Template: $template , has successfully created.";
			else return "Error in creating your template: $template.";
		}else return "A Template with name: $template , already exists.";
	}else return "Please $message.";
}
function add_sidebar_template($sdb,$tid){
	global $wpdb;
	$wpdb->show_errors();
	$t = $wpdb->prefix."template_on_the_fly_template";
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar";
	$o = $wpdb->get_results("SELECT * FROM $ts WHERE template_id=$tid ORDER BY `sort` DESC");
	if(count($o)>0) $o = intval($o[0]->sort) + 1;
	else $o = 1;
	$style = 'width:auto;height:100px;background-color:#9AA8A2;border-top-width:1px;border-top-style:solid;border-top-color:#ff5c00;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#ff5c00;border-right-width:1px;border-right-style:solid;border-right-color:#ff5c00;border-left-width:1px;border-left-style:solid;border-left-color:#ff5c00;display:block;';
	$a = $wpdb->query("INSERT INTO $ts(`template_id`,`sidebar`,`sort`,`style`) VALUES ($tid,'$sdb',$o,'$style')");
	return $a;
}
function produce_template_theme($tid){
	global $wpdb;
	$wpdb->show_errors();
	$t = $wpdb->prefix."template_on_the_fly_template";
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar";
	$template = $wpdb->get_results("SELECT * FROM $t WHERE template_id=$tid");
	$template = $template[0];
	$sidebars = $wpdb->get_results("SELECT * FROM $ts WHERE template_id=$tid ORDER BY `sort` ASC");
	?>
	<div>
		<form action="" method="POST" id="template_sidebar_sdb_form">
			<input type="hidden" name="template_sidebar_sdb_tpl" id="template_sidebar_sdb_tpl"/>
			<div>
				<span><?php _e('Select a Sidebar to Add:','wp-template-on-the-fly'); ?></span>
				<select name="template_sidebar_sdb" onchange="$(this).parent().submit();">
					<option value="-">----</option>
				<?php
					global $wp_registered_sidebars;
					foreach($wp_registered_sidebars as $sdb){ ?>
						<option value="<?php echo $sdb['id'];?>"><?php echo $sdb['name']." (".$sdb['id'].")";?></option>
				<?php } ?>
				</select>
			</div>
		</form>
		<div class="template_theme_container" id="<?php echo $template->template;?>" tplid="<?php echo $template->template_id;?>" style="<?php echo $template->style;?>">
			<?php foreach($sidebars as $s){ ?>
			<div class="template_theme_sidebar" sidebarname="<?php echo $s->sidebar;?>" sidebartpl="<?php echo $s->template_id;?>" sidebarsort="<?php echo $s->sort;?>" style="<?php echo $s->style;?>">
				<div class="template_theme_sidebar_sort"><?php _e('Sort:','wp-template-on-the-fly'); ?> <input type="text" class="template_theme_sidebar_sort_val" value="<?php echo $s->sort;?>" size="1"/></div>
				<div class="template_theme_sidebar_del"><?php _e('Delete:','wp-template-on-the-fly'); ?> <input type="checkbox" class="template_theme_sidebar_del_val"/></div>
				<div class="template_theme_sidebar_name"><?php _e('Name:','wp-template-on-the-fly'); ?> <?php echo $s->sidebar;?></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php
	return;
}
function save_css_pad($post){
	global $wpdb;
	$wpdb->show_errors();
	$t = $wpdb->prefix."template_on_the_fly_template";
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar";
	$style = '';
	$type = $post['template_sidebar_css_pad_target_type'];
	$id = $post['template_sidebar_css_pad_target_id'];
	foreach($post as $k=>$v)
		if(strpos($k,"csstag-")!==false && $v!='-'){
			$tag = str_replace('csstag-','',$k);
			if($v=='fontname' || $v=='length' || $v=='number') $value = $post[str_replace('-','_',$tag).'_'.$v];
			elseif($v=='color') $value = '#'.$post[str_replace('-','_',$tag).'_'.$v];
			elseif($v=='url') $value = "url('".$post[str_replace('-','_',$tag).'_'.$v]."')";
			elseif($v=='percentage') $value = $post[str_replace('-','_',$tag).'_'.$v].'%';
			else $value = $v;
			$style .= trim($tag).':'.trim($value).';';
		}
	$style = addslashes($style);
	if($type=='template'){
		$res = $wpdb->query("UPDATE $t SET `style`='$style' WHERE template_id=$id");
		if($res) return produce_template_theme($id);
		else return 'error';
	}else{
		$template_id = substr($id,0,strpos($id,'_'));
		$id = substr($id,strpos($id,'_')+1);
		$sidebar = substr($id,0,strrpos($id,'_'));
		$sort = substr($id,strrpos($id,'_')+1);
		$res = $wpdb->query("UPDATE $ts SET `style`='$style' WHERE template_id=$template_id AND sidebar='$sidebar' AND `sort`=$sort");
		if($res) return produce_template_theme($template_id);
		else return 'error';
	}
}
function template_theme_save($sort,$del){
	global $wpdb;
	$wpdb->show_errors();
	$t = $wpdb->prefix."template_on_the_fly_template";
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar";
	if(is_array($del) && count($del)>0){
		$template_id = $del[0][0];
		$qr = array();
		foreach($del as $d) $qr[] = $d[1];
		$qr = implode(",",$qr);
		$res = $wpdb->query("DELETE FROM $ts WHERE template_id=$template_id AND `sort` IN ($qr)");
		if($res===false) return 'error';
	}
	if(is_array($sort) && count($sort)>0){
		$template_id = $sort[0][0];
		$qr = array();
		foreach($sort as $p) $qr[] = "WHEN $p[1] THEN -$p[2]";
		$qr = implode(" ",$qr);
		$res = $wpdb->query("UPDATE $ts SET `sort`=(CASE `sort` $qr END) WHERE template_id=$template_id");
		if($res){
			$res = $wpdb->query("UPDATE $ts SET `sort`=ABS(`sort`) WHERE template_id=$template_id");
			return produce_template_theme($template_id);
		}else return 'error';
	}
}
function produce_sidebar_list(){
	global $wpdb;
	global $wp_registered_sidebars;
	$wpdb->show_errors();
	$t = $wpdb->prefix."template_on_the_fly_template";
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar"; ?>
	<table class="sidebar_list">
			<tbody>
			<tr class="header">
				<th class="select"><?php _e('Remove','wp-template-on-the-fly'); ?></th>
				<th class="name"><?php _e('Name','wp-template-on-the-fly'); ?></th>
				<th class="id"><?php _e('Id','wp-template-on-the-fly'); ?></th>
				<th class="desc"><?php _e('Description','wp-template-on-the-fly'); ?></th>
			</tr>
			<?php $i=0;foreach($wp_registered_sidebars as $s){$i++; ?>
			<tr class="item <?php echo $i%2==0?'even':'odd';?>">
				<td class="select"><?php if(strpos($s['id'],'totfs-')!==false){ ?><input type="checkbox" class="sidebars_list_item_del" sidebarid="<?php echo $s['id'];?>"><?php } ?></td>
				<td class="name"><?php echo $s['name'];?></td>
				<td class="id"><?php echo $s['id'];?></td>
				<td class="desc"><?php echo $s['description'];?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php
	return;
}
function add_sidebar($post){
	global $wp_registered_sidebars;
	$sidebars_widgets = get_option('sidebars_widgets');
	$totf_sidebars = get_option('totf_sidebars');
	$name = $post['name'];
	$desc = $post['desc'];
	$id = "totfs-".sanitize_html_class(sanitize_title_with_dashes($post['name']));
	if(isset($wp_registered_sidebars[$id]) || isset($sidebars_widgets[$id]) || isset($totf_sidebars[$id])) return 'exists';
	$totf_sidebars[$id] = $wp_registered_sidebars[$id] = array('name'=>$name,'id'=>$id,'description'=>$desc,'class'=>'','before_widget'=>'','after_widget'=>'','before_title'=>'','after_title'=>'');
	$sidebars_widgets[$id] = array();
	$sidebars_names = array();
	$new_sidebars = array();
	foreach($wp_registered_sidebars as $a) $sidebars_names[strpos($a['id'],'totfs-')===false?$a['name']:'0'.$a['name']] = $a['id'];
	ksort($sidebars_names,SORT_NATURAL | SORT_FLAG_CASE);
	foreach($sidebars_names as $b) $new_sidebars[$b] = $wp_registered_sidebars[$b];
	$wp_registered_sidebars = $new_sidebars;
	update_option('totf_sidebars',$totf_sidebars);
	update_option('sidebars_widgets',$sidebars_widgets);
	return;
}
function remove_sidebar($post){
	global $wp_registered_sidebars;
	global $wpdb;
	$wpdb->show_errors();
	$ts = $wpdb->prefix."template_on_the_fly_template_sidebar";
	$sidebars_widgets = get_option('sidebars_widgets');
	$totf_sidebars = get_option('totf_sidebars');
	foreach($post as $s)
		if(strpos($s,'totfs-')!==false){
			unregister_sidebar($s);
			$res = $wpdb->query("DELETE FROM $ts WHERE sidebar='$s'");
			unset($sidebars_widgets[$s]);
			unset($totf_sidebars[$s]);
			unset($wp_registered_sidebars[$s]);
		}
	update_option('sidebars_widgets',$sidebars_widgets);
	update_option('totf_sidebars',$totf_sidebars);
	return;
}
add_action('wp_ajax_wp_template_on_the_fly_ajax', 'wp_template_on_the_fly_ajax_handle');
function wp_template_on_the_fly_ajax_handle(){
	if(isset($_POST['postdata'])){
		$postdata = array();
		foreach($_POST['postdata'] as $data)
			if($data['name']!='checkbox[]') $postdata[$data['name']] = $data['value'];
			else $postdata['checkbox'][] = $data['value'];
	}
	if(isset($postdata["parent"])){
		global $wpdb;
		$wpdb->show_errors();
		$t = $wpdb->prefix."template_on_the_fly_template";
		if($postdata['actions']!='' && count($postdata['checkbox'])>0){
			$ids = implode(",",$postdata['checkbox']);
			$action = $postdata['actions']=='remove'?2:($postdata['actions']=='active'?1:0);
			$result = $wpdb->query("UPDATE $t SET `status`=$action WHERE template_id IN ($ids)");
		}
		$parent = $postdata['parent'];
		$templatespagenumber = intval($postdata['templatespagenumber']);
		$templatesperpage = intval($postdata['templatesperpage']);
		$t_filter_show_primary = isset($postdata['t_filter_show_primary'])?1:0;
		$t_filter_show_active = isset($postdata['t_filter_show_active'])?1:0;
		$t_filter_show_inactive = isset($postdata['t_filter_show_inactive'])?1:0;
		$t_filter_show_deleted = isset($postdata['t_filter_show_deleted'])?1:0;
		$orderby = array();
		$t_sort_q = array();
		foreach($postdata as $k=>$a)
			if(strpos($k,"t_sort_base_")!==false){
				$index = intval(str_replace("t_sort_base_","",$k));
				$dir = $postdata['t_sort_dir_'.$index];
				if($a!='-') $orderby[] = "$a $dir";
				$t_sort_q["t_sort_base_$index"] = "$a";
				$t_sort_q["t_sort_dir_$index"] = "$dir";
			}
		$template_on_the_fly_setting = array(
											'parent'=>"$parent",
											'templatespagenumber'=>$templatespagenumber,
											'templatesperpage'=>$templatesperpage,
											't_filter_show_active'=>$t_filter_show_active,
											't_filter_show_deleted'=>$t_filter_show_deleted,
											't_filter_show_inactive'=>$t_filter_show_inactive,
											't_filter_show_primary'=>$t_filter_show_primary,
											't_sort_bases'=> 'template,status,parent'
											);
		foreach($t_sort_q as $k=>$v) $template_on_the_fly_setting[$k] = $v;
		update_option('template_on_the_fly_setting',$template_on_the_fly_setting);
		$orderby = count($orderby)>0?"ORDER BY ".implode(",",$orderby):'';
		$limit = "LIMIT ".(($templatespagenumber-1)*$templatesperpage).",".$templatesperpage;
		$where = '';?>
		<div id="all_templates_list_container">
			<div class="template_list_breadcrumb">
				<span><?php _e('Breadcrumb:','wp-template-on-the-fly'); ?></span>
				<span class="breadvalue">
					<input type="button" class="button" value="<?php _e('main','wp-template-on-the-fly'); ?>" onclick="$('#all_templates_list #parent').val('').parent().submit();"/>
					<?php 
					if($parent!=''){
						$parents = $wpdb->get_results("SELECT * FROM $t WHERE FIND_IN_SET(template_id,(SELECT `parent` FROM $t WHERE template_id=$parent)) OR template_id=$parent");
						foreach($parents as $p) 
							if($p->template_id==$parent)
								{$pp = explode(",",$p->parent);$pp[] = $p->template_id;}
						foreach($pp as $p)
							foreach($parents as $a)
								if($a->template_id==$p){?>
									/<input type="button" class="button" value="<?php echo $a->template;?>" templateid="<?php echo $a->template_id;?>" onclick="$('#all_templates_list #parent').val($(this).attr('templateid')).parent().submit();"/>
						<?php 	}
						$where = "WHERE `parent` LIKE CONCAT((SELECT `parent` FROM $t WHERE template_id=$parent),',$parent','%')";
					} ?>
				</span>
			</div>
	<?php
		$where .= $where==''?'WHERE ':' AND ';
		$where .= "`isprimary` IN (0".($t_filter_show_primary==1?',1':'').") AND `status` IN (10".($t_filter_show_active==1?',1':'').($t_filter_show_inactive==1?',0':'').($t_filter_show_deleted==1?',2':'').")";
		$templates = $wpdb->get_results("SELECT * FROM $t $where $orderby $limit");
		$path = '/wp-content/plugins/'.str_replace("ajax","",dirname(plugin_basename(__FILE__)));
		$i=0;?>
			<table class="template_list">
				<tbody>
					<tr class="header">
						<th class="select deselected"><?php _e('Select','wp-template-on-the-fly'); ?></th>
						<th class="template"><?php _e('Template','wp-template-on-the-fly'); ?></th>
						<th class="primary"><?php _e('Primary','wp-template-on-the-fly'); ?></th>
						<th class="status"><?php _e('Statu','wp-template-on-the-fly'); ?>s</th>
						<th class="parent"><?php _e('Parent','wp-template-on-the-fly'); ?></th>
						<th class="action"><?php _e('Action','wp-template-on-the-fly'); ?></th>
					</tr>
		<?php foreach($templates as $template)
				if(strpos($template->template,"$")===false){
					$i++;?>
					<tr class="item<?php echo $i%2==0?' even':' odd';?>">
						<td class="select">
							<input type="checkbox" name="checkbox[]" class="checkbox" value="<?php echo $template->template_id;?>"/>
						</td>
						<td class="template">
							<input type="button" class="button" value="<?php echo $template->template;?>" templateid="<?php echo $template->template_id;?>" onclick="$('#all_templates_list #parent').val($(this).attr('templateid')).parent().submit();"/>
						</td>
						<td class="primary">
							<?php if($template->isprimary){?><img src="<?php echo $path;?>/img/status_1.png"/><?php } ?>
						</td>
						<td class="status">
							<img src="<?php echo $path;?>/img/status_<?php echo $template->status;?>.png"/>
						</td>
						<td class="parent">
						<?php
							$parents = t_o_t_f_find_parent($template->parent);
							foreach($parents as $par){
								?>
									/<input type="button" class="button" value="<?php echo $par['template'];?>" templateid="<?php echo $par['id'];?>" onclick="$('#all_templates_list #parent').val($(this).attr('templateid')).parent().submit();"/>
							<?php
							}?>
						</td>
						<td class="action">
							<img class="active" src="<?php echo $path;?>/img/status_1.png" onclick="$(this).parents('tr.item').find('td.select input').prop('checked',true);$('#all_templates_list #action').val($(this).attr('class')).parent().submit();"/>
							<img class="inactive" src="<?php echo $path;?>/img/status_0.png" onclick="$(this).parents('tr.item').find('td.select input').prop('checked',true);$('#all_templates_list #action').val($(this).attr('class')).parent().submit();"/>
							<img class="remove" src="<?php echo $path;?>/img/status_2.png" onclick="$(this).parents('tr.item').find('td.select input').prop('checked',true);$('#all_templates_list #action').val($(this).attr('class')).parent().submit();"/>
						</td>
					</tr>
		<?php } ?>
				</tbody>
			</table>
		</div>
	<?php
	}elseif(isset($postdata["add_template_req_sel"]) && $postdata["add_template_req_sel"]!='-'){
		global $wpdb;
		$wpdb->show_errors();
		$t = $wpdb->prefix."template_on_the_fly_template";
		$tid = intval($postdata["add_template_req_sel"]);
		$template = $wpdb->get_results("SELECT * FROM $t WHERE template_id=$tid");
		$template = $template[0];
		$funcName = str_replace("-","_",str_replace("$","",str_replace(".php","",$template->template))); ?>
		<form action="" method="POST" id="add_template_form" >
			<input type="hidden" name="parenttpl" value="<?php echo $template->template;?>"/>
			<input type="hidden" name="funcName" value="<?php echo $funcName;?>"/>
			<div class="add_template_desc"><?php echo $template->description;?></div>
			<?php call_user_func($funcName); ?>
			<input type="submit" class="button" value="<?php _e('Create Template','wp-template-on-the-fly'); ?>"/>
		</form>
	<?php
	}elseif(isset($postdata["funcName"]) && $postdata["funcName"]!=''){
		echo call_user_func($postdata["funcName"],$postdata);
	}elseif(isset($postdata["template_sidebar_tpl"]) && $postdata["template_sidebar_tpl"]!='-'){
		return produce_template_theme($postdata["template_sidebar_tpl"]);
	}elseif(isset($postdata["template_sidebar_sdb"]) && $postdata["template_sidebar_sdb"]!='-'){
		$a = add_sidebar_template($postdata["template_sidebar_sdb"],$postdata["template_sidebar_sdb_tpl"]);
		if($a) return produce_template_theme($postdata["template_sidebar_sdb_tpl"]);
		else echo 'Sidebar Insert Problem';
	}elseif(isset($postdata['template_sidebar_css_pad_target_id'])){
		echo save_css_pad($postdata);
	}elseif((isset($_POST['template_theme_save']) && count($_POST['template_theme_save'])>0) 
			|| (isset($_POST['template_theme_save_del']) && count($_POST['template_theme_save_del'])>0)){
		echo template_theme_save($_POST['template_theme_save'],$_POST['template_theme_save_del']);
	}elseif(isset($_POST["sidebar_list_init"])){
		produce_sidebar_list();
	}elseif(isset($_POST['sidebars_list_del'])){
		remove_sidebar($_POST['sidebars_list_del']);
		produce_sidebar_list();
	}elseif(isset($postdata['sidebar_create'])){
		add_sidebar($postdata);
		produce_sidebar_list();
	}
	die();
}
?>
