/*! Fabrik */

var ListFieldsElement=new Class({Implements:[Options,Events],addWatched:!1,options:{conn:null,highlightpk:!1,showAll:1,mode:"dropdown",defaultOpts:[],addBrackets:!1},initialize:function(a,b){this.strEl=a;var c,d;if(this.el=a,this.setOptions(b),this.options.defaultOpts.length>0){this.el=document.id(this.el),"gui"===this.options.mode?(this.select=this.el.getParent().getElement("select.elements"),d=[this.select],"null"===typeOf(document.id(this.options.conn))&&this.watchAdd()):(d=document.getElementsByName(this.el.name),this.el.empty(),document.id(this.strEl).empty());var e=this.options.defaultOpts;Array.each(d,function(a){document.id(a).empty()}),e.each(function(a){var b={value:a.value};a.value===this.options.value&&(b.selected="selected"),Array.each(d,function(d){c=a.label?a.label:a.text,new Element("option",b).set("text",c).inject(d)})}.bind(this))}else"null"===typeOf(document.id(this.options.conn))?this.cnnperiodical=this.getCnn.periodical(500,this):this.setUp()},cloned:function(a,b){this.strEl=a,this.el=document.id(a),this._cloneProp("conn",b),this._cloneProp("table",b),this.setUp()},_cloneProp:function(a,b){var c=this.options[a].split("-");c=c.splice(0,c.length-1),c.push(b),this.options[a]=c.join("-")},getCnn:function(){"null"!==typeOf(document.id(this.options.conn))&&(this.setUp(),clearInterval(this.cnnperiodical))},setUp:function(){this.el=document.id(this.el),"gui"===this.options.mode&&(this.select=this.el.getParent().getElement("select.elements")),document.id(this.options.conn).addEvent("change",function(){this.updateMe()}.bind(this)),document.id(this.options.table).addEvent("change",function(){this.updateMe()}.bind(this));var a=document.id(this.options.conn).get("value");""!==a&&-1!==a&&(this.periodical=this.updateMe.periodical(500,this)),this.watchAdd()},watchAdd:function(){if(!0!==this.addWatched){console.log("watch add",this),this.addWatched=!0;var a=this.el.getParent().getElement("button");"null"!==typeOf(a)&&(a.addEvent("mousedown",function(a){a.stop(),this.addPlaceHolder()}.bind(this)),a.addEvent("click",function(a){a.stop()}))}},updateMe:function(e){"event"===typeOf(e)&&e.stop(),document.id(this.el.id+"_loader")&&document.id(this.el.id+"_loader").show();var conn=document.id(this.options.conn);if(!conn)return void clearInterval(this.periodical);var cid=document.id(this.options.conn).get("value"),tid=document.id(this.options.table).get("value");if(tid){clearInterval(this.periodical);var url="index.php?option=com_fabrik&format=raw&task=plugin.pluginAjax&g=element&plugin=field&method=ajax_fields&showall="+this.options.showAll+"&cid="+cid+"&t="+tid,myAjax=new Request({url:url,method:"get",data:{highlightpk:this.options.highlightpk,k:2},onComplete:function(r){var els;null!==typeOf(document.id(this.strEl))&&(this.el=document.id(this.strEl)),"gui"===this.options.mode?els=[this.select]:(els=document.getElementsByName(this.el.name),this.el.empty(),document.id(this.strEl).empty());var opts=eval(r);Array.each(els,function(a){document.id(a).empty()}),opts.each(function(a){var b={value:a.value};a.value===this.options.value&&(b.selected="selected"),Array.each(els,function(c){new Element("option",b).set("text",a.label).inject(c)})}.bind(this)),document.id(this.el.id+"_loader")&&document.id(this.el.id+"_loader").hide()}.bind(this)});Fabrik.requestQueue.add(myAjax)}},addPlaceHolder:function(){var a=this.el.getParent().getElement("select"),b=a.get("value");this.options.addBrackets&&(b=b.replace(/\./,"___"),b="{"+b+"}"),this.insertTextAtCaret(this.el,b)},getInputSelection:function(a){var b,c,d,e,f,g=0,h=0;return"number"==typeof a.selectionStart&&"number"==typeof a.selectionEnd?(g=a.selectionStart,h=a.selectionEnd):(c=document.selection.createRange())&&c.parentElement()===a&&(e=a.value.length,b=a.value.replace(/\r\n/g,"\n"),d=a.createTextRange(),d.moveToBookmark(c.getBookmark()),f=a.createTextRange(),f.collapse(!1),d.compareEndPoints("StartToEnd",f)>-1?g=h=e:(g=-d.moveStart("character",-e),g+=b.slice(0,g).split("\n").length-1,d.compareEndPoints("EndToEnd",f)>-1?h=e:(h=-d.moveEnd("character",-e),h+=b.slice(0,h).split("\n").length-1))),{start:g,end:h}},offsetToRangeCharacterMove:function(a,b){return b-(a.value.slice(0,b).split("\r\n").length-1)},setSelection:function(a,b,c){if("number"==typeof a.selectionStart&&"number"==typeof a.selectionEnd)a.selectionStart=b,a.selectionEnd=c;else if(void 0!==a.createTextRange){var d=a.createTextRange(),e=this.offsetToRangeCharacterMove(a,b);d.collapse(!0),b===c?d.move("character",e):(d.moveEnd("character",this.offsetToRangeCharacterMove(a,c)),d.moveStart("character",e)),d.select()}},insertTextAtCaret:function(a,b){var c=this.getInputSelection(a).end,d=c+b.length,e=a.value;a.value=e.slice(0,c)+b+e.slice(c),this.setSelection(a,d,d)}});