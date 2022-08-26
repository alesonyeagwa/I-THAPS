!function(t){formintorjs.define(["admin/polls/appearance-settings","admin/polls/details-settings","admin/popup/ajax","text!admin/templates/polls.html"],function(i,a,o,e){return Backbone.View.extend({currentTab:0,mainTpl:Forminator.Utils.template(t(e).find("#polls-main-tpl").html()),buttonsTpl:Forminator.Utils.template(t(e).find("#polls-buttons-tpl").html()),is_edit:!1,events:{"click .sui-vertical-tab a":"go_to_tab","click .forminator-save":"save_changes","click #forminator-polls-finish":"save_layout","click #forminator-polls-cancel":"cancel","click #forminator-polls-back":"prev_tab","click #forminator-polls-next":"next_tab","click .fui-button-preview":"open_preview","change input":"set_dirty"},initialize:function(t){return this.render()},render:function(){var o=Forminator.Data.currentForm.form_id||-1;this.is_edit=-1!==o,this.$el.html(this.mainTpl()),this.$el.append(this.buttonsTpl());var e={details:a,appearance:i},n=this;t(window).off("beforeunload.forminator-leave-wizard-confirm"),t(window).on("beforeunload.forminator-leave-wizard-confirm",function(t){if(n.dirty)return Forminator.l10n.popup.save_alert}),this.init_tabs(),this.append_settings(e),this.update_buttons(),Forminator.Utils.sui_delegate_events()},cancel:function(t){t.preventDefault(),window.location.href=Forminator.Data.modules.polls.form_list_url},save_changes:function(i){this.validate()?(this.save(!1,!0),t(".fui-button-preview").show()):t("html").animate({scrollTop:0},800)},save_layout:function(i){this.validate()?this.save(!0,!0):t("html").animate({scrollTop:0},800)},save:function(i,a){var o=this,e=Forminator.Utils.model_to_json(this.model),n=Forminator.Data.currentForm.formName||this.model.get("formName")||"",r=Forminator.Data.currentForm.form_id||-1;a&&this.$el.find(".forminator-loading").addClass("sui-button-onload"),t.post({url:Forminator.Data.ajaxUrl,data:{action:"forminator_save_poll",formName:n,form_id:r,_wpnonce:Forminator.Data.formNonce,data:e}}).success(function(t){o.dirty=!1,-1===r&&(Forminator.Data.currentForm.form_id=t.data);var e;if(i?-1===r?(e=Forminator.Data.modules.polls.form_list_url,window.location.href=e+"&new=true&title="+n.replace(/ /g,"-")):(e=Forminator.Data.modules.polls.form_list_url,window.location.href=e+"&notification=true&title="+n.replace(/ /g,"-")):o.is_edit||Forminator.navigate("poll/"+Forminator.Data.currentForm.form_id,{trigger:!1,replace:!0}),a&&(setTimeout(function(){o.$el.find(".forminator-loading").removeClass("sui-button-onload")},500),!i)){var s=_.template("<strong>{{ formName }}</strong> {{ Forminator.l10n.options.been_saved }}");Forminator.Notification.open("success",s({formName:n}),4e3)}}).error(function(){Forminator.Notification.open("error",Forminator.l10n.options.error_saving,5e3)})},append_settings:function(t){var i=this;_.each(t,function(t,a){var o=new t({model:i.model});i.$el.find("#forminator-poll-"+a).append(o.el)})},update_buttons:function(){this.is_first_tab()?(this.$el.find("#forminator-polls-cancel").css("display","inline-flex"),this.$el.find("#forminator-polls-back").hide(),this.$el.find("#forminator-polls-next").css("display","inline-flex"),this.$el.find("#forminator-polls-finish").hide()):(this.$el.find("#forminator-polls-cancel").hide(),this.$el.find("#forminator-polls-back").css("display","inline-flex"),this.$el.find("#forminator-polls-next").hide(),this.$el.find("#forminator-polls-finish").css("display","inline-flex"))},init_tabs:function(){this.update_tab()},update_tab:function(){this.clear_tabs(),this.$el.find("[data-tab-id="+this.currentTab+"]").addClass("current"),this.$el.find(".wpmudev-tab-content-"+this.currentTab).show()},clear_tabs:function(){this.$el.find(".sui-vertical-tab ").removeClass("current"),this.$el.find(".wpmudev-settings--box").hide()},is_first_tab:function(){return 0===this.currentTab},is_last_tab:function(){return this.currentTab===this.$el.find(".sui-vertical-tab").length},mark_tab:function(){this.$el.find("[data-tab-id="+this.currentTab+"]").addClass("done")},validate:function(){var t=!1;return _.isEmpty(this.model.get("formName"))&&(t=!0),_.isEmpty(this.model.get("poll-question"))&&(t=!0),0===this.model.get("answers").length?(this.$el.find(".forminator-validate-answers p").html(Forminator.l10n.polls.validate_form_answers),this.$el.find(".forminator-validate-answers").show(),t=!0):this.$el.find(".forminator-validate-answers").hide(),!t},go_to_tab:function(i){var a=t(i.target),o=a.data("tab-id");this.validate()?(this.currentTab=o,this.update_tab(),this.update_buttons()):this.$el.find("[data-tab-id="+this.currentTab+"]").removeClass("done"),i.preventDefault(),i.stopPropagation()},prev_tab:function(){this.currentTab=this.currentTab-1,this.update_tab(),this.update_buttons()},next_tab:function(){this.validate()?(this.mark_tab(),this.currentTab=this.currentTab+1,this.update_tab(),this.update_buttons(),this.save(!1,!0)):this.$el.find("[data-tab-id="+this.currentTab+"]").removeClass("done")},open_preview:function(i){i.preventDefault();var a=t(i.target);a.hasClass("fui-button-preview")||(a=a.closest(".fui-button-preview"));var o=a.data("modal"),e=a.data("nonce"),n=a.data("form-id"),r=void 0!==this.model.get("formName")?" - "+this.model.get("formName"):"";this.open_preview_popup(o,e,n,Forminator.l10n.popup.preview_polls+r,this.model.toJSON())},open_preview_popup:function(i,a,e,n,r){_.isUndefined(n)&&(n=Forminator.l10n.polls.popup_label),_.isUndefined(r.form_id)&&(r.form_id=Forminator.Data.currentForm.form_id);var s=new o({action:i,nonce:a,data:r,id:e});Forminator.Popup.open(function(){t(this).append(s.el)},{title:n,has_footer:!1,has_custom_box:!0})},set_dirty:function(){this.dirty=!0}})})}(jQuery);