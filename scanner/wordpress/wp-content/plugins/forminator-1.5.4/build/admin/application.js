!function(e){formintorjs.define(["admin/models","admin/views","admin/settings","text!admin/templates/builder.html","text!admin/templates/appearance.html","text!admin/templates/polls.html","text!admin/templates/quizzes.html"],function(t,i,r,o,a,n,l){return _.extend(Forminator,t),_.extend(Forminator,i),_.extend(Forminator,r),new(Backbone.Router.extend({app:!1,data:!1,layout:!1,module_id:null,routes:{"builder/:id":"run","poll/:id":"run","nowrong/:id":"run","knowledge/:id":"run",appearance:"run_appearance","appearance/:id":"run_appearance","":"run","*path":"run"},events:{},init:function(){if(!this.data)if(this.app=Forminator.Data.application||!1,this.data=e.extend(!0,{},Forminator.Data.currentForm)||{},"builder"===this.app)this.model=new Forminator.Models.Builder(this.data);else if("poll"===this.app)this.model=new Forminator.Models.Poll(this.data);else{if("knowledge"!==this.app&&"nowrong"!==this.app)return!1;this.model=new Forminator.Models.Quiz(this.data)}},run:function(e){if(this.init(),this.module_id=e,this.maybe_redirect_to_module_id(""))return!1;"builder"===this.app?this.start_builder():"poll"===this.app?this.start_poll():"knowledge"!==this.app&&"nowrong"!==this.app||this.start_quiz()},run_appearance:function(e){if(this.init(),this.app&&"builder"===this.app||this.navigate("",{trigger:!0}),this.module_id=e,this.maybe_redirect_to_module_id("/appearance"))return!1;this.start_appearance()},start_builder:function(){e(".forminator-form-wizard").empty().html(Forminator.Utils.template(e(o).find("#builder-layout-tpl").html())),this.builder&&(this.builder.fields_panel&&(this.builder.fields_panel.off(),this.builder.fields_panel.remove()),this.builder.off(),this.builder.remove()),this.builder=new Forminator.Views.Builder.Builder({model:this.model,el:"#forminator-form-builder"}),this.update_builder_width()},start_appearance:function(){e(".forminator-form-wizard").empty().html(Forminator.Utils.template(e(a).find("#appearance-layout-tpl").html())),this.appearance&&this.appearance.remove(),this.appearance=new Forminator.Views.Builder.Appearance({model:this.model,el:".forminator-appearance-content"}),this.update_builder_width()},start_poll:function(){e(".wpmudev-poll-wizard").empty().html(Forminator.Utils.template(e(n).find("#polls-layout-tpl").html())),this.poll&&this.poll.remove(),this.poll=new Forminator.Views.Polls({model:this.model,el:".forminator-polls-content"})},start_quiz:function(){e(".wpmudev-quiz-wizard").empty().html(Forminator.Utils.template(e(l).find("#quiz-layout-tpl").html())),this.quiz&&this.quiz.remove(),this.quiz=new Forminator.Views.Quizzes({model:this.model,el:".forminator-quizzes-content"})},update_builder_width:function(){var t=e(".forminator-form-wizard"),i=(e("#forminator-wizard-name"),e("#forminator-form-elements"));i.width(),t.width()},get_new_module_url:function(e){if("custom_form"===e){if(!_.isUndefined(Forminator.Data.modules.custom_form.new_form_url))return Forminator.Data.modules.custom_form.new_form_url}else if("poll"===e){if(!_.isUndefined(Forminator.Data.modules.polls.new_form_url))return Forminator.Data.modules.polls.new_form_url}else if("nowrong"===e){if(!_.isUndefined(Forminator.Data.modules.quizzes.nowrong_url))return Forminator.Data.modules.quizzes.nowrong_url}else if("knowledge"===e&&!_.isUndefined(Forminator.Data.modules.quizzes.knowledge_url))return Forminator.Data.modules.quizzes.knowledge_url;return!1},maybe_redirect_to_module_id:function(e){var t=this.app;if("builder"===t&&(t="custom_form"),null!==this.module_id&&_.isUndefined(Forminator.Data.currentForm.form_id)){var i=this.get_new_module_url(t);if(i)return i=new URL(i),i.searchParams.append("id",this.module_id),window.location.href=i.href+"#"+e,!0}return!1}}))})}(jQuery);