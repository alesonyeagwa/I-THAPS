!function(e){formintorjs.define(["text!admin/templates/fields.html"],function(t){return Backbone.View.extend({events:{"click .fui-form-field":"activate_field"},mainTpl:Forminator.Utils.template(e(t).find("#builder-field-main-tpl").html()),className:"fui-form-builder--col",initialize:function(e){return this.field=this.model,this.layout=e.layout,this.field.off("forminator:field:settings:updated"),this.field.on("forminator:field:settings:updated",this.render,this),this.render()},render:function(){var e={field:this.field.toJSON(),condition:this.get_condition_markup()};this.$el.attr("id","wpmudev-field-"+this.field.cid),this.$el.html(""),this.$el.append(this.mainTpl(e)),this.$el.addClass("fui-form-builder--col-"+this.field.get("cols")),this.render_content()},get_condition_markup:function(){if(!_.isUndefined(this.field.get("conditions"))&&this.field.get("conditions").length>0){var e="show"===this.field.get("condition_action")?Forminator.l10n.sidebar.shown:Forminator.l10n.sidebar.hidden,t=Forminator.Utils.get_fields(this.layout.get("wrappers")),i=this.field.get("conditions");if(0===i.length)return!1;var n,o="",r=i.get_by_index(0),l=_.where(t,{element_id:r.get("element_id")})[0];if(void 0===l)return void i.remove(r,{silent:!0});var s=!1;if("checkbox"===l.field_type&&(s=!0),l.hasOptions&&l.values.length>0){var a=_.where(l.values,{value:r.get("value")})[0];if(a||(a=_.where(l.values,{label:r.get("value")})[0]),!a)return i.remove(r,{silent:!0}),void Forminator.Events.trigger("sidebar:settings:updated",this.field);n=a.label}else n=r.get("value");if(i.length>1){var d=Forminator.Utils.template(" + {{ total }} {{ more_label }}"),f=i.length-1;o=d({total:f,more_label:1===f?Forminator.l10n.conditions.more_condition:Forminator.l10n.conditions.more_conditions})}_.isEmpty(n)&&(n="null");var m=Forminator.Utils.template("{{ action }} {{ Forminator.l10n.sidebar.if }} <strong>{{ label }}</strong> {{ rule }} <strong>{{ valueLabel }}</strong> {{ moreConditions }}"),h=r.get("rule");if(s)switch(h){case"is":h="having";break;case"is_not":h="not_having"}return m({action:e,label:l.label,rule:Forminator.l10n.conditions[h],valueLabel:n,moreConditions:o})}},render_content:function(){var e=_.where(Forminator.Data.fields,{slug:this.field.get("type")})[0],t=_.isUndefined(e.markup)?"":e.markup;this.contentTpl=Forminator.Utils.template(t);var i=this.field.toJSON();return this.$el.find(".fui-form-field").prepend(this.contentTpl({field:i})),Forminator.Utils.sui_delegate_events(),this},activate_field:function(t){if(!(e(t.target).closest(".select2").length>0)&&"checkbox"!==e(t.target).attr("type")&&"radio"!==e(t.target).attr("type")){var i=this.$el.find(".fui-form-field");e(".fui-form-field").not(i).each(function(){e(this).removeClass("fui-active")}),i.toggleClass("fui-active"),i.hasClass("fui-active")?Forminator.Events.trigger("forminator:sidebar:open:settings",this.field):Forminator.Events.trigger("forminator:sidebar:close:settings")}}})})}(jQuery);