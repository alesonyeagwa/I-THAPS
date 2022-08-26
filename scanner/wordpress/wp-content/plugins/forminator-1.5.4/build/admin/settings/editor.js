!function(t){formintorjs.define(["admin/settings/text"],function(n){return n.extend({events:{"click .wpmudev-insert-content":"insert_content","click .fui-editor-options button":"toggle_menu"},className:"sui-form-field",get_field_html:function(){var t={cols:"40",rows:"5",class:"forminator-field-singular wpmudev-textarea",id:this.get_field_id(),name:this.get_name()};this.options.placeholder&&(t.placeholder=this.options.placeholder);var n=!_.isUndefined(this.options.enableFormData)&&this.options.enableFormData?this.get_form_data():"",o=!_.isUndefined(this.options.hideEditorOptions)&&this.options.hideEditorOptions,e='<div class="fui-editor-options"><button class="sui-tooltip" data-tooltip="'+Forminator.l10n.options.form_based_data+'"><i class="sui-icon-layout" aria-hidden="true"></i></button><ul>'+n+this.get_utilities()+"</ul></div>";return o&&(e=""),'<div class="fui-editor">'+e+"<textarea "+this.get_field_attr_html(t)+">"+this.get_saved_value()+"</textarea></div>"},toggle_dropdown:function(){this.$el.find(".fui-editor-options button").toggleClass("current"),this.$el.find(".fui-editor-options ul").toggleClass("current")},toggle_menu:function(t){t.preventDefault(),this.toggle_dropdown()},get_form_data:function(){var t=["captcha","product","hidden","pagination","postdata","total","upload"];!_.isUndefined(this.options.enablePostData)&&this.options.enablePostData&&!_.isUndefined(this.options.enableUpload)&&this.options.enableUpload&&(t=["captcha","product","hidden","pagination","total"]);var n=Forminator.Utils.get_fields(this.model.get("wrappers"),t),o="",e="",i="";return _.each(n,function(t,n){"true"===t.required?e+='<li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{'+t.element_id+'}">'+t.label+"</a></li>":i+='<li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{'+t.element_id+'}">'+t.label+"</a></li>"}),!_.isUndefined(this.options.enableAllFormFields)&&this.options.enableAllFormFields&&(o+='<li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{all_fields}">'+Forminator.l10n.options.all_fields+"</a></li>"),o+='<li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{form_name}">'+Forminator.l10n.options.form_name+"</a></li>",""!==e&&(o+='<li class="wpmudev-dropdown--option"><strong>'+Forminator.l10n.options.required_form_fields+"</strong></li>",o+=e),""!==i&&(o+='<li class="wpmudev-dropdown--option"><strong>'+Forminator.l10n.options.optional_form_fields+"</strong></li>",o+=i),o},get_utilities:function(){return'<li class="wpmudev-dropdown--option"><strong>'+Forminator.l10n.options.misc_data+'</strong></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{user_ip}">'+Forminator.l10n.options.user_ip_address+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{date_mdy}">'+Forminator.l10n.options.date+' (mm/dd/yyyy)</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{date_dmy}">'+Forminator.l10n.options.date+' (dd/mm/yyyy)</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{embed_id}">'+Forminator.l10n.options.embed_id+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{embed_title}">'+Forminator.l10n.options.embed_title+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{embed_url}">'+Forminator.l10n.options.embed_url+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{user_agent}">'+Forminator.l10n.options.user_agent+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{http_refer}">'+Forminator.l10n.options.refer_url+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{user_name}">'+Forminator.l10n.options.display_name+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{user_email}">'+Forminator.l10n.options.user_email+'</a></li><li class="wpmudev-dropdown--option"><a class="wpmudev-insert-content" data-content="{user_login}">'+Forminator.l10n.options.user_login+"</a></li>"},insert_content:function(n){if(n.preventDefault(),!_.isUndefined(tinymce)){var o=t(n.target).data("content");tinymce.get(this.get_field_id()).insertContent(o),this.toggle_dropdown()}},on_render:function(){this.$el.attr("id","wrapper-"+this.get_field_id()),this.initialize_editor(),this.append_controls()},initialize_editor:function(){var n=this;_.isUndefined(window.wp.editor)||_.isUndefined(tinymce)?setTimeout(function(){n.initialize_editor()},100):setTimeout(function(){window.wp.editor.remove(n.get_field_id()),window.wp.editor.initialize(n.get_field_id(),{tinymce:!0,quicktags:!0});var o=tinymce.get(n.get_field_id());_.isNull(o)||o.on("change",function(t){n.save_value(o.getContent()),n.trigger("changed",o.getContent())}),t("#"+n.get_field_id()).on("change",function(e){o.setContent(t(this).val()),n.trigger("changed",o.getContent())})},100)},append_controls:function(){}})})}(jQuery);