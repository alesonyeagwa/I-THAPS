!function(e){formintorjs.define(["text!tpl/appearance.html"],function(n){return Backbone.View.extend({mainTpl:Forminator.Utils.template(e(n).find("#appearance-section-advanced-tpl").html()),className:"wpmudev-box-body",initialize:function(e){return this.render()},render:function(){this.$el.html(this.mainTpl()),this.render_fields()},render_fields:function(){var e=new Forminator.Settings.Toggle({model:this.model,id:"advanced-generate-pdf",name:"generate-pdf",hide_label:!0,values:[{value:"true",label:Forminator.l10n.appearance.generate_pdf}]}),n=new Forminator.Settings.Toggle({model:this.model,id:"advanced-integrations",name:"integrations",hide_label:!0,values:[{value:"true",label:Forminator.l10n.appearance.integrations}]});this.$el.find(".appearance-section-form-advanced").append([e.el,n.el])}})})}(jQuery);