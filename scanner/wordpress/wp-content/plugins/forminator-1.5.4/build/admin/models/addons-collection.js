!function(e){formintorjs.define(["admin/models/addon-model"],function(e){return Backbone.Collection.extend({model:e,get_by_slug:function(e){e=e.toLowerCase();var n=!1;return this.each(function(t){t.get("slug").toLowerCase()==e&&(n=t)}),n},model_index:function(e){return this.indexOf(e)},get_by_index:function(e){return this.at(e)}})})}(jQuery);