!function(e){formintorjs.define(["text!tpl/dashboard.html"],function(t){return Backbone.View.extend({className:"wpmudev-section--popup",popupTpl:Forminator.Utils.template(e(t).find("#forminator-delete-popup-tpl").html()),initialize:function(e){this.nonce=e.nonce,this.id=e.id,this.referrer=e.referrer},render:function(){this.$el.html(this.popupTpl({nonce:this.nonce,id:this.id,referrer:this.referrer}))}})})}(jQuery);