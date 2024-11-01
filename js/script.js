$=jQuery;
jQuery(document).ready(function($){
	$('.t_o_t_f .tabs').click(function(){
		$(this).parent().children('.tabs.active').removeClass('active');
		$(this).parent().parent().children('.container.active').removeClass('active');
		$(this).addClass('active');
		$(this).parent().parent().children('.container').eq($(this).parent().children('.tabs').index($(this))).addClass('active');
	});
	$('.t_o_t_f .category-tabs').each(function(){$(this).children('.tabs').eq(0).click();});
	$('.t_o_t_f .wrapper .container .template_list .header .select').live('click',function(){
		if($(this).hasClass('deselected')){
			$(this).removeClass('deselected').addClass('selected');
			$(this).parent().parent().find('.item .select .checkbox').each(function(){$(this).prop("checked",true);});
		}else{
			$(this).removeClass('selected').addClass('deselected');
			$(this).parent().parent().find('.item .select .checkbox').each(function(){$(this).prop("checked",false);});
		}
	});
	$('#all_templates_list').live('submit',function(event){event.preventDefault();all_templates_list_submit($(this));});
	$('#add_template_req').live('submit',function(event){event.preventDefault();add_template_request($(this));});
	$('#sidebar_create_form').live('submit',function(event){event.preventDefault();sidebar_create_form($(this));});
	$('#template_sidebar_tpl_form').live('submit',function(event){event.preventDefault();template_sidebar_tpl_form($(this));});
	$('#template_sidebar_css_pad').live('submit',function(event){event.preventDefault();template_sidebar_css_pad($(this));});
	$('#template_sidebar_sdb_form').live('submit',function(event){event.preventDefault();template_sidebar_sdb_form($(this));});
	$('#add_template_form').live('submit',function(event){event.preventDefault();add_template_form($(this));});
	$('#all_templates_list').submit();
	$('#background_color_color,#color_color,#border_bottom_color_color,#border_top_color_color,#border_left_color_color,#border_right_color_color').ColorPicker({
		onSubmit: function(hsb,hex,rgb,el){$(el).val(hex);$(el).ColorPickerHide();},
		onBeforeShow: function(){$(this).ColorPickerSetColor(this.value);}
	}).bind('keyup', function(){$(this).ColorPickerSetColor(this.value);});
	$('.template_theme_sidebar').live('click',function(){
		$('.template_theme_sidebar_active').remove();
		$(this).append('<div class="template_theme_sidebar_active">'+active+'</div>');
		var name = $(this).attr('sidebarname');
		var id = $(this).attr('sidebartpl')+'_'+name+'_'+$(this).attr('sidebarsort');
		var style = $(this).attr('style');
		css_pad_target_set('sidebar',name,id,style);
	});
	$('.template_theme_save_button input').live('click',function(){
		var sidebarssortarr = Array();
		var sidebarsdelarr = Array();
		$('.template_theme_container .template_theme_sidebar').each(function(){
			if($(this).find('.template_theme_sidebar_del_val').prop('checked'))
				sidebarsdelarr.push(Array($(this).attr('sidebartpl'),$(this).attr('sidebarsort')));
			else
				sidebarssortarr.push(Array($(this).attr('sidebartpl'),$(this).attr('sidebarsort'),$(this).find('.template_theme_sidebar_sort_val').val()));
		});
		template_theme_save($(this),sidebarssortarr,sidebarsdelarr);
	});
	$('.sidebars_list_save input').live('click',function(){
		var sidebarsdelarr = Array();
		$('#sidebar_create_form').parents('.container').find('.sidebars_list_item_del').each(function(){
			if($(this).prop('checked')) sidebarsdelarr.push($(this).attr('sidebarid'));
		});
		if(sidebarsdelarr.length>0) sidebars_list_save($('#sidebar_create_form').parents('.container'),sidebarsdelarr);
	});
	sidebar_list_init();
});
function all_templates_list_submit(o){
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:o.serializeArray()}).done(function(a){
		$('#all_templates_list #all_templates_list_container').remove();
		$('#all_templates_list').append($(a));
		loading();
	}).fail(function(a){loading();});
}
function add_template_request(o){
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:o.serializeArray()}).done(function(a){
		$('#add_template_form').remove();
		o.parent().append($(a));
		loading();
	}).fail(function(a){loading();});
}
function add_template_form(o){
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:o.serializeArray()}).done(function(a){
		if(a.indexOf('successfully')>=0)
			$('#t_o_t_f_message').text(a).addClass('active').addClass('updated');
		else 
			$('#t_o_t_f_message').text(a).addClass('active').addClass('error');
		loading();
	}).fail(function(a){loading();});
}
function template_theme_save(o,sr,dr){
	loading();
	$.post(ajaxurl,{action:wpaction,template_theme_save:sr,template_theme_save_del:dr}).done(function(a){
		if(a.length>0){
			if(a!='error'){
				var b = $(a).find('#template_sidebar_sdb_form');
				a = $(a).find('.template_theme_container');
				o = o.parents('.container');
				o.find('.template_theme_container,.template_theme_save_button,#template_sidebar_sdb_form').remove();
				b.find('#template_sidebar_sdb_tpl').val($(a).attr('tplid'));
				css_pad_target_set('template',$(a).attr('id'),$(a).attr('tplid'),$(a).attr('style'));
				o.append('<div class="template_theme_save_button"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
				o.append(a);
				o.find('.template_sidebar_forms').append(b);
			}else alert(error);
		}else $('.template_theme_container,.template_theme_save_button,#template_sidebar_sdb_form').remove();
		loading();
	}).fail(function(a){loading();});
}
function sidebars_list_save(o,dr){
	loading();
	$.post(ajaxurl,{action:wpaction,sidebars_list_del:dr}).done(function(a){
		if(a.length>0){
			if(a!='error'){
				o.find('.sidebar_list,.sidebars_list_save').remove();
				o.append('<div class="sidebars_list_save"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
				o.append(a);
			}else alert(error);
		}else $('.sidebar_list,.sidebars_list_save').remove();
		loading();
	}).fail(function(a){loading();});
}
function template_sidebar_tpl_form(o){
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:o.serializeArray()}).done(function(a){
		if(a.length>0){
			var b = $(a).find('#template_sidebar_sdb_form');
			a = $(a).find('.template_theme_container');
			o = o.parents('.container');
			o.find('.template_theme_container,.template_theme_save_button,#template_sidebar_sdb_form').remove();
			b.find('#template_sidebar_sdb_tpl').val($(a).attr('tplid'));
			css_pad_target_set('template',$(a).attr('id'),$(a).attr('tplid'),$(a).attr('style'));
			o.append('<div class="template_theme_save_button"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
			o.append(a);
			o.find('.template_sidebar_forms').append(b);
		}else $('.template_theme_container,.template_theme_save_button,#template_sidebar_sdb_form').remove();
		loading();
	}).fail(function(a){loading();});
}
function template_sidebar_sdb_form(o){
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:o.serializeArray()}).done(function(a){
		if(a.indexOf('Sidebar Insert Problem')>=0)
			alert(a);
		else if(a.length>0){
			var b = $(a).find('#template_sidebar_sdb_form');
			a = $(a).find('.template_theme_container');
			o = o.parents('.container');
			b.find('#template_sidebar_sdb_tpl').val($(a).attr('tplid'));
			o.find('.template_theme_container,.template_theme_save_button,#template_sidebar_sdb_form').remove();
			o.append('<div class="template_theme_save_button"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
			o.append(a);
			o.find('.template_sidebar_forms').append(b);
		}else {}
		loading();
	}).fail(function(a){loading();});
}
function css_pad_input_show(o){
	o.parent().find('input').addClass('hide');
	o.parent().find('.'+o.val()).removeClass('hide');
}
function css_pad_target_set(type,name,id,style){
	var csstags = new Array();
	$('#template_sidebar_css_pad_target_type').val(type);
	$('#template_sidebar_css_pad_target_name').val(name);
	$('#template_sidebar_css_pad_target_id').val(id);
	st = style.split(';');
	var f = $('#template_sidebar_css_pad');
	for(s in st){
		if(st[s].length>0){
			var t = st[s].split(':');
			var v = t[1];
			t = 'csstag-'+t[0];
			csstags.push(t);
			if(v.indexOf('#')==0){
				css_pad_input_show(f.find('[name="'+t+'"]').val('color'));
				f.find('[name="'+t+'"]').parent().find('input.color').val(v.replace('#',''));
			}else if(v.indexOf('url(')==0){
				css_pad_input_show(f.find('[name="'+t+'"]').val('url'));
				f.find('[name="'+t+'"]').parent().find('input.url').val(v.replace("url('",'').replace("')",''));
			}else if(t=='font-family' && v!='inherit'){
				css_pad_input_show(f.find('[name="'+t+'"]').val('fontname'));
				f.find('[name="'+t+'"]').parent().find('input.fontname').val(v);
			}else if(v.indexOf('%')>=0){
				css_pad_input_show(f.find('[name="'+t+'"]').val('percentage'));
				f.find('[name="'+t+'"]').parent().find('input.percentage').val(v.replace('%',''));
			}else if(!isNaN(v)){
				if(f.find('[name="'+t+'"] option[value="number"]').length>0){
					css_pad_input_show(f.find('[name="'+t+'"]').val('number'));
					f.find('[name="'+t+'"]').parent().find('input.number').val(v);
				}else{
					css_pad_input_show(f.find('[name="'+t+'"]').val('length'));
					f.find('[name="'+t+'"]').parent().find('input.length').val(v);
				}
			}else if(!isNaN(v.slice(0,-2))){
				css_pad_input_show(f.find('[name="'+t+'"]').val('length'));
				f.find('[name="'+t+'"]').parent().find('input.length').val(v);
			}else f.find('[name="'+t+'"]').val(v).parent().find('input').addClass('hide');
		}
	}
	f.find('select[name^="csstag-"]').each(function(){if($.inArray($(this).attr('name'),csstags)==-1) $(this).val('-').parent().find('input').addClass('hide');});
}
function template_sidebar_css_pad(o){
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:o.serializeArray()}).done(function(a){
		if(a!='error'){
			var b = $(a).find('#template_sidebar_sdb_form');
			a = $(a).find('.template_theme_container');
			o.parents('.container').find('.template_theme_container,.template_theme_save_button,#template_sidebar_sdb_form').remove();
			b.find('#template_sidebar_sdb_tpl').val($(a).attr('tplid'));
			css_pad_target_set('template',$(a).attr('id'),$(a).attr('tplid'),$(a).attr('style'));
			o.parents('.container').append('<div class="template_theme_save_button"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
			o.parents('.container').append(a);
			o.parents('.container').find('.template_sidebar_forms').append(b);
		}else alert(error);
		loading();
	}).fail(function(a){loading();});
}
function sidebar_list_init(){
	loading();
	$.post(ajaxurl,{action:wpaction,sidebar_list_init:'true'}).done(function(a){
		$('#sidebar_create_form').parents('.container').find('.sidebars_list,.sidebars_list_save').remove();
		$('#sidebar_create_form').parents('.container').append('<div class="sidebars_list_save"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
		$('#sidebar_create_form').parents('.container').append(a);
		loading();
	}).fail(function(a){loading();});
}
function sidebar_create_form(o){
	var data = o.serializeArray();
	var nameindex,descindex;
	for(x in data){
		if(data[x]['name']=='name') nameindex=x;
		else if(data[x]['name']=='description') descindex=x;
	}
	if(data[nameindex]['value']=='' || data[descindex]['value']==''){
		o.find('input').removeClass('error');
		if(data[nameindex]['value']=='') o.find('input[name="name"]').addClass('error');
		if(data[descindex]['value']=='') o.find('input[name="description"]').addClass('error');
		return;
	}
	o.find('input').removeClass('error');
	loading();
	$.post(ajaxurl,{action:wpaction,postdata:data}).done(function(a){
		if(a.length>0){
			if(a!='error'){
				o.parents('.container').find('.sidebar_list,.sidebars_list_save').remove();
				o.parents('.container').append('<div class="sidebars_list_save"><input type="button" class="button" value="'+SaveChangesToSidebars+'"/></div>');
				o.parents('.container').append(a);
			}else alert(error);
		}else $('.sidebar_list,.sidebars_list_save').remove();
		loading();
	}).fail(function(a){loading();});
}
function loading(){
	var a = $('#loading');
	if(a.hasClass('vishidden')) a.removeClass('vishidden');
	else a.addClass('vishidden');
}
function plugin_support(){

}