!function(e){formintorjs.define(["admin/builder/builder/fields-panel","admin/builder/builder/shadows-panel","admin/popup/ajax","text!admin/templates/builder.html"],function(i,t,r,o){return Backbone.View.extend({mainTpl:Forminator.Utils.template(e(o).find("#builder-main-tpl").html()),buttonsTpl:Forminator.Utils.template(e(o).find("#builder-buttons-tpl").html()),noFieldsTpl:Forminator.Utils.template(e(o).find("#builder-main-no-fields-tpl").html()),events:{"click .forminator-save-changes":"save_changes","click .forminator-save-layout":"save_layout","click .wpmudev-button-cancel":"cancel_builder","click .forminator-add-new-field":"show_fields","click .wpmudev-builder-preview":"open_preview","change .wpmudev-input":"set_dirty"},$main:!1,$shadows_container:!1,fields:{},settings:{},dirty:!1,is_edit:!1,initialize:function(e){return this.wrappers=this.model.get("wrappers"),this.settings=this.model.get("settings"),this.listenTo(Forminator.Events,"dnd:reload:fields",this.render),this.listenTo(Forminator.Events,"dnd:models:updated",this.render),this.sidebar||(this.sidebar=new Forminator.Views.Builder.Sidebar({model:this.model,el:"#forminator-form-elements .fui-sidebar-wrapper"})),this.render()},render:function(){var i=Forminator.Data.currentForm.form_id||-1;this.$el.html(this.mainTpl()),this.$el.append(this.buttonsTpl()),this.$el.closest(".sui-wrap").addClass("fui-builder-page"),-1===i?(e("#wpmudev-header h1").html(Forminator.l10n.builder.builder_new_title),e("#wpmudev-header .sui-button").show()):(this.is_edit=!0,e("#wpmudev-header h1").html(Forminator.l10n.builder.builder_edit_title),e("#wpmudev-header .sui-button").show()),this.$main=this.$el.find(".fui-form-builder"),this.$shadows_container=this.$el.find(".fui-form-builder-shadow"),Forminator.Events.off("forminator:add:field:click");var t=this;e(window).off("beforeunload.forminator-leave-wizard-confirm"),e(window).on("beforeunload.forminator-leave-wizard-confirm",function(e){if(t.dirty)return Forminator.l10n.popup.save_alert}),this.render_fields(),this.render_shadows(),this.render_form_name(),setTimeout(function(){t.update_preview_button()},20),Forminator.Utils.sui_delegate_events()},update_preview_button:function(){var i=e("#wpmudev-header h1");e(".wpmudev-builder-preview"),i.width()},render_fields:function(){if(0===this.wrappers.length)return this.show_placeholder(),void this.disable_save();this.enable_save(),this.fields_panel&&(this.fields_panel.off(),this.fields_panel.remove()),this.fields_panel=new i({model:this.model}),this.$main.append(this.fields_panel.$el)},disable_save:function(){this.$el.find(".forminator-save").attr("disabled",!0)},enable_save:function(){this.$el.find(".forminator-save").attr("disabled",!1)},render_shadows:function(){this.shadows_panel&&(this.$shadows_container.html(""),this.shadows_panel.off(),this.shadows_panel.remove()),this.shadows_panel=new t({el:this.$shadows_container,model:this.model}),this.$shadows_container.append(this.shadows_panel.$el)},render_form_name:function(){var e=this,i=new Forminator.Settings.Text({model:this.model,id:"builder-form-name",name:"formName",label:Forminator.l10n.builder.form_name_field,description:Forminator.l10n.builder.form_name_field_description,on_change:function(i){this.hide_error(),_.isEmpty(e.model.get("formName"))&&this.show_error(Forminator.l10n.builder.form_name_field_validation)}});this.$el.find("#forminator-builder-form-name").append(i.el)},show_placeholder:function(){this.$main.append(this.noFieldsTpl())},save_changes:function(i){i.preventDefault(),this.validate()?this.save(!1,!0):e("html, body").animate({scrollTop:0},500)},save_layout:function(i){i.preventDefault(),this.validate()?this.save(!0,!0):e("html, body").animate({scrollTop:0},500)},save:function(i,t){var r=this,o=Forminator.Utils.model_to_json(this.model.get("wrappers")),a=this.model.get("formName")||Forminator.Data.currentForm.formName||"",n=Forminator.Data.currentForm.form_id||-1;t&&this.$el.find(".forminator-save").addClass("sui-button-onload"),e.post({url:Forminator.Data.ajaxUrl,data:{action:"forminator_save_builder_fields",_wpnonce:Forminator.Data.formNonce,formName:a,form_id:n,data:JSON.stringify(o)}}).success(function(e){if(-1===n&&(Forminator.Data.currentForm.form_id=e.data),r.is_edit&&(r.dirty=!1),t&&(setTimeout(function(){r.$el.find(".forminator-save").removeClass("sui-button-onload")},500),!i)){var o=_.template("<strong>{{ formName }}</strong> {{ Forminator.l10n.options.been_saved }}");Forminator.Notification.open("success",o({formName:a}),4e3)}i?(Forminator.Events.trigger("forminator:sidebar:close:settings"),r.is_edit?Forminator.navigate("appearance",{trigger:!0}):Forminator.navigate("appearance/"+Forminator.Data.currentForm.form_id,{trigger:!0})):r.is_edit||Forminator.navigate("builder/"+Forminator.Data.currentForm.form_id,{trigger:!1,replace:!0})}).error(function(){Forminator.Notification.open("error",Forminator.l10n.options.error_saving,5e3)})},open_preview:function(i){i.preventDefault();var t=e(i.target);t.hasClass("wpmudev-builder-preview")||(t=t.closest(".wpmudev-builder-preview"));var r=t.data("modal"),o=t.data("nonce"),a=t.data("form-id"),n=Forminator.Utils.model_to_json(this.model);this.open_preview_popup(r,o,a,Forminator.l10n.popup.preview_cforms+" - "+this.model.get("formName"),n)},open_preview_popup:function(i,t,o,a,n){_.isUndefined(a)&&(a=Forminator.l10n.appearance.preview_form);var s=new r({action:i,nonce:t,data:JSON.stringify(n),id:o});Forminator.Popup.open(function(){e(this).append(s.el),e(".forminator-design--material").each(function(){var i=e(this),t=i.find(".forminator-input--wrap"),r=i.find(".forminator-textarea--wrap"),o=(i.find(".forminator-product"),i.find(".forminator-date")),a=i.find(".forminator-pagination--nav"),n=a.find("li");e('<span class="forminator-nav-border"></span>').insertAfter(n),t.prev(".forminator-field--label").addClass("forminator-floating--input"),r.prev(".forminator-field--label").addClass("forminator-floating--textarea"),o.hasClass("forminator-has_icon")?o.prev(".forminator-field--label").addClass("forminator-floating--date"):o.prev(".forminator-field--label").addClass("forminator-floating--input")})},{title:a,has_footer:!1,has_custom_box:!0})},validate:function(){var e=!1;return _.isEmpty(this.model.get("formName"))?(this.$el.find("#forminator-validate-name").show(),e=!0):this.$el.find("#forminator-validate-name").hide(),Forminator.Utils.ensure_max_of_type_field(this.model.get("wrappers"),"captcha",1)||(e=!0),!e},cancel_builder:function(e){e.preventDefault(),window.location.href=Forminator.Data.modules.custom_form.form_list_url},show_fields:function(i){i.preventDefault(),e("body.wp-admin").addClass("wpmudev-disabled--mobile"),e("#forminator-form-elements").addClass("fui-active fui-show")},set_dirty:function(){this.dirty=!0}})})}(jQuery);