!function(e){formintorjs.define(["admin/settings/text"],function(t){return t.extend({className:"sui-form-field",get_field_html:function(){var e={type:"text",class:"sui-form-control forminator-field-singular",name:this.get_name(),value:this.get_saved_value(),placeholder:Forminator.l10n.commons.date_placeholder,title:this.label};return'<div class="sui-date"><input '+this.get_field_attr_html(e)+'><i class="sui-icon-calendar" aria-hidden="true"></i></div>'},on_render:function(){var t=this,a=this.options.dateFormat?this.options.dateFormat:"d MM yy";this.get_field().datepicker({beforeShow:function(t,a){e("#ui-datepicker-div").addClass("sui-calendar")},dateFormat:a,dayNamesMin:forminator_l10n.calendar.day_names_min,monthNames:forminator_l10n.calendar.month_names,onSelect:function(){t.trigger("changed",t.get_value())}})}})})}(jQuery);