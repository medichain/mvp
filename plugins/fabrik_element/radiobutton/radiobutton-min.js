/*! Fabrik */

define(["jquery","fab/elementlist"],function(a,b){return window.FbRadio=new Class({Extends:b,options:{btnGroup:!0},type:"radio",initialize:function(a,b){this.setPlugin("fabrikradiobutton"),this.parent(a,b),this.btnGroup()},btnGroup:function(){if(this.options.btnGroup){this.btnGroupRelay();var a=this.getContainer();a&&(a.getElements(".radio.btn-group label").addClass("btn"),a.getElements(".btn-group input[checked]").each(function(a){var b,c=a.getParent("label");"null"===typeOf(c)&&(c=a.getNext()),b=a.get("value"),""===b?c.addClass("active btn-primary"):"0"===b?c.addClass("active btn-danger"):c.addClass("active btn-success")}))}},btnGroupRelay:function(){var a=this.getContainer();a&&(a.getElements(".radio.btn-group label").addClass("btn"),a.addEvent("click:relay(.btn-group label)",function(a,b){var c,d=b.get("for");""!==d&&(c=document.id(d)),"null"===typeOf(c)&&(c=b.getElement("input")),this.setButtonGroupCSS(c)}.bind(this)))},setButtonGroupCSS:function(a){var b;""!==a.id&&(b=document.getElement("label[for="+a.id+"]")),"null"===typeOf(b)&&(b=a.getParent("label.btn"));var c=a.get("value"),d=parseInt(a.get("fabchecked"),10);a.get("checked")&&1!==d||(b&&(b.getParent(".btn-group").getElements("label").removeClass("active").removeClass("btn-success").removeClass("btn-danger").removeClass("btn-primary"),""===c?b.addClass("active btn-primary"):0===c.toInt()?b.addClass("active btn-danger"):b.addClass("active btn-success")),a.set("checked",!0),"null"===typeOf(d)&&a.set("fabchecked",1))},watchAddToggle:function(){var a=this.getContainer(),b=a.getElement("div.addoption"),c=a.getElement(".toggle-addoption");if(this.mySlider){var d=b.clone(),e=a.getElement(".fabrikElement");b.getParent().destroy(),e.adopt(d),b=a.getElement("div.addoption"),b.setStyle("margin",0)}this.mySlider=new Fx.Slide(b,{duration:500}),this.mySlider.hide(),c.addEvent("click",function(a){a.stop(),this.mySlider.toggle()}.bind(this))},getValue:function(){if(!this.options.editable)return this.options.value;var a="";return this._getSubElements().each(function(b){return b.checked?a=b.get("value"):null}),a},setValue:function(a){this.options.editable&&this._getSubElements().each(function(b){b.value===a?b.set("checked",!0):b.set("checked",!1)})},update:function(a){if("array"===typeOf(a)&&(a=a.shift()),this.setValue(a),!this.options.editable)return""===a?void(this.element.innerHTML=""):void(this.element.innerHTML=$H(this.options.data).get(a));if(this.options.btnGroup){this._getSubElements().each(function(b){b.value===a&&this.setButtonGroupCSS(b)}.bind(this))}},cloned:function(a){!0===this.options.allowadd&&!1!==this.options.editable&&(this.watchAddToggle(),this.watchAdd()),this._getSubElements().each(function(a,b){a.id=this.options.element+"_input_"+b;var c=a.getParent("label");c&&(c.htmlFor=a.id)}.bind(this)),this.parent(a),this.btnGroup()},getChangeEvent:function(){return this.options.changeEvent},eventDelegate:function(){var a="input[type="+this.type+"][name^="+this.options.fullName+"]";return a+=", [class*=fb_el_"+this.options.fullName+"] .fabrikElement label"}}),window.FbRadio});