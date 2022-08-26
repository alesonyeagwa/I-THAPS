!function(t){formintorjs.define(["admin/builder/appearance/appearance-settings","admin/builder/appearance/behaviour-settings","admin/builder/appearance/email-settings","admin/builder/appearance/advanced-settings","admin/builder/appearance/pagination-settings","admin/builder/appearance/integrations-settings","admin/popup/ajax","text!admin/templates/appearance.html"],function(a,i,e,n,r,o,s,l){return Backbone.View.extend({mainTpl:Forminator.Utils.template(t(l).find("#appearance-main-tpl").html()),buttonsTpl:Forminator.Utils.template(t(l).find("#appearance-buttons-tpl").html()),events:{"click .forminator-save-changes":"save_changes","click .sui-vertical-tab a":"go_to_tab","change .sui-sidenav-hide-lg select":"go_to_tab","click .forminator-save-layout":"save_layout","click .wpmudev-button-back":"back_to_builder","click #forminator-appearance-next":"next_tab","click #forminator-appearance-back":"prev_tab","click .fui-button-preview":"open_preview","change .wpmudev-input,.forminator-field-singular":"set_dirty"},has_pagination:!1,dirty:!1,currentTab:"forminator-wizard-appearance",tab_steps:["forminator-wizard-appearance","forminator-wizard-behaviour","forminator-wizard-emails","forminator-wizard-integrations"],initialize:function(t){return this.render()},render:function(){this.$el.closest(".sui-wrap").removeClass("fui-builder-page");var n=this.model.get("wrappers");this.has_pagination=Forminator.Utils.has_field_type(n,"pagination"),Forminator.Events.trigger("forminator:sidebar:close:settings"),this.$el.html(this.mainTpl({has_pagination:this.has_pagination,addons_enabled:Forminator.Data.addons_enabled})),this.$el.append(this.buttonsTpl()),t("#wpmudev-header h1").html(Forminator.l10n.appearance.settings_title),t("#wpmudev-header .wpmudev-button").hide();var s={appearance:a,behaviour:i,emails:e};this.init_tabs(),this.has_pagination&&(this.tab_steps.splice(1,0,"forminator-wizard-pagination"),s.pagination=r),Forminator.Data.addons_enabled&&(s.integrations=o),this.append_settings(s);var l=this;t(window).off("beforeunload.forminator-leave-wizard-confirm"),t(window).on("beforeunload.forminator-leave-wizard-confirm",function(t){if(l.dirty)return Forminator.l10n.popup.save_alert}),Forminator.Utils.sui_delegate_events(),SUI.suiSelect(this.$el.find(".sui-sidenav-hide-lg select"))},back_to_builder:function(t){Forminator.navigate("builder",{trigger:!0})},cancel_builder:function(t){t.preventDefault(),window.location.href=Forminator.Data.modules.custom_form.form_list_url},open_preview:function(a){a.preventDefault();var i=t(a.target);i.hasClass("fui-button-preview")||(i=i.closest(".fui-button-preview"));var e=i.data("modal"),n=i.data("nonce"),r=i.data("form-id");this.open_preview_popup(e,n,r,Forminator.l10n.popup.preview_cforms+" - "+this.model.get("formName"),this.model.toJSON())},open_preview_popup:function(a,i,e,n,r){_.isUndefined(n)&&(n=Forminator.l10n.appearance.preview_form);var o=new s({action:a,nonce:i,data:r,id:e});Forminator.Popup.open(function(){t(this).append(o.el),t(".forminator-design--material").each(function(){var a=t(this),i=a.find(".forminator-input--wrap"),e=a.find(".forminator-textarea--wrap"),n=(a.find(".forminator-product"),a.find(".forminator-date")),r=a.find(".forminator-pagination--nav"),o=r.find("li");t('<span class="forminator-nav-border"></span>').insertAfter(o),i.prev(".forminator-field--label").addClass("forminator-floating--input"),e.prev(".forminator-field--label").addClass("forminator-floating--textarea"),n.hasClass("forminator-has_icon")?n.prev(".forminator-field--label").addClass("forminator-floating--date"):n.prev(".forminator-field--label").addClass("forminator-floating--input")})},{title:n,has_footer:!1,has_custom_box:!0})},save_changes:function(a){a.preventDefault(),this.validate()?(this.save(!1,!0),t(".fui-button-preview").show()):t("html, body").animate({scrollTop:0},500)},save_layout:function(a){a.preventDefault(),this.validate()?this.save(!0,!0):t("html, body").animate({scrollTop:0},500)},save:function(a,i){var e=!1;if(i&&this.$el.find(".forminator-save").addClass("sui-button-onload"),a){this.model.get("is_new")||!1||(this.model.set("is_new",!0),e=!0)}var n=this,r=Forminator.Utils.model_to_json(this.model),o=this.model.get("formName")||Forminator.Data.currentForm.formName||"",s=Forminator.Data.currentForm.form_id||-1;nonce=Forminator.Data.formNonce,r.wrappers=[],t.post({url:Forminator.Data.ajaxUrl,data:{action:"forminator_save_builder_settings",_wpnonce:nonce,formName:o,form_id:s,data:r}}).success(function(t){if(n.dirty=!1,-1===s&&(Forminator.Data.currentForm.form_id=t.data),i&&(setTimeout(function(){n.$el.find(".forminator-save").removeClass("sui-button-onload")},500),a||Forminator.Notification.open("success",o+" "+Forminator.l10n.options.been_saved,4e3)),a){var r;e?(r=Forminator.Data.modules.custom_form.form_list_url,window.location.href=r+"&new=true&title="+o.replace(/ /g,"-")):(r=Forminator.Data.modules.custom_form.form_list_url,window.location.href=r+"&notification=true&title="+o.replace(/ /g,"-"))}}).error(function(){Forminator.Notification.open("error",Forminator.l10n.options.error_saving,5e3)})},validate:function(){var t=!1;return _.isEmpty(this.model.get("formName"))?(this.$el.find("#appearance-form-name").trigger("change"),t=!0):this.$el.find("#appearance-form-name").trigger("change"),!t},append_settings:function(t){var a=this;_.each(t,function(t,i){var e=new t({model:a.model});a.$el.find("#forminator-wizard-"+i).append(e.el)})},go_to_tab:function(a){a.preventDefault();var i=t(a.target),e=i.attr("href"),n="";if(_.isUndefined(e)){n=i.val()}else n=e.replace("#","",e);_.isEmpty(n)||(this.currentTab=n),this.update_tab(),this.update_buttons(),a.stopPropagation()},init_tabs:function(){this.update_tab(),this.update_buttons()},update_tab_select:function(){this.$el.find(".sui-sidenav-hide-lg select").val(this.currentTab),this.$el.find(".sui-sidenav-hide-lg select").trigger("sui:change")},update_tab:function(){this.clear_tabs(),this.$el.find("[data-tab-id="+this.currentTab+"]").addClass("current"),this.$el.find(".wpmudev-settings--box#"+this.currentTab).show()},clear_tabs:function(){this.$el.find(".sui-vertical-tab ").removeClass("current"),this.$el.find(".wpmudev-settings--box").hide()},is_first_tab:function(){return _.first(this.tab_steps)===this.currentTab},is_last_tab:function(){return _.last(this.tab_steps)===this.currentTab},next_tab:function(){if(this.validate()){this.save(!1,!0);var a=_.indexOf(this.tab_steps,this.currentTab);a++,_.isUndefined(this.tab_steps[a])||(this.currentTab=this.tab_steps[a]),this.update_tab(),this.update_buttons(),this.update_tab_select()}t("html, body").animate({scrollTop:0},500)},prev_tab:function(){var a=_.indexOf(this.tab_steps,this.currentTab);a--,_.isUndefined(this.tab_steps[a])||(this.currentTab=this.tab_steps[a]),this.update_tab(),this.update_buttons(),this.update_tab_select(),t("html, body").animate({scrollTop:0},500)},update_buttons:function(){this.is_first_tab()?(this.$el.find("#forminator-cform-builder").css("display","inline-flex"),this.$el.find("#forminator-appearance-save").css("display","inline-flex"),this.$el.find("#forminator-appearance-next").css("display","inline-flex"),this.$el.find("#forminator-appearance-publish").hide(),this.$el.find("#forminator-appearance-back").hide()):this.is_last_tab()?(this.$el.find("#forminator-appearance-back").css("display","inline-flex"),this.$el.find("#forminator-appearance-publish").css("display","inline-flex"),this.$el.find("#forminator-appearance-next").hide(),this.$el.find("#forminator-cform-builder").hide(),this.$el.find("#forminator-appearance-save").hide()):(this.$el.find("#forminator-appearance-back").css("display","inline-flex"),this.$el.find("#forminator-appearance-save").css("display","inline-flex"),this.$el.find("#forminator-appearance-next").css("display","inline-flex"),this.$el.find("#forminator-cform-builder").hide(),this.$el.find("#forminator-appearance-publish").hide())},set_dirty:function(){this.dirty=!0}})})}(jQuery);