!function(t){formintorjs.define(["admin/settings/text"],function(t){return t.extend({className:"sui-form-field",get_field_html:function(){var t={type:"text",id:this.get_field_id(),name:this.get_name(),value:this.get_saved_value(),"data-default-color":this.options.default_color?this.options.default_color:"","data-alpha":!0};return this.options.tooltip&&("wpmudev-tip",'data-tip="'+this.options.tooltip+'"'),"<input "+this.get_field_attr_html(t)+" /></div>"},on_render:function(){var t=this;this.get_field().wpColorPicker({change:function(e,i){t.trigger("changed",i.color.toCSS())},clear:function(e,i){t.trigger("changed",t.options.default_color?t.options.default_color:"")}})}})})}(jQuery);