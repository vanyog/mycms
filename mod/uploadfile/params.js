if(typeof ajaxO == "undefined"){
if (window.XMLHttpRequest) ajaxO = new XMLHttpRequest();
else ajaxO = new ActiveXObject("Microsoft.XMLHTTP");
}
var upfafc = "http://<!--$$_VARIABLE_SERVER['HTTP_HOST']_$$--><!--$$_VARIABLE_pth_$$-->mod/uploadfile/ajax-fcount.php?i=" + 
                      Math.floor(Math.random() * 1000);
function uploadfile_params(){
ajaxO.open("GET", upfafc, false);
ajaxO.send(null);
var upffn = ajaxO.responseText;
var n = prompt("Name", "A" + upffn);
if(!n) return "";
var p = prompt("Page number");
var o = prompt("Option (0,1,2 or 3)");
var s = prompt("Style");
var i = confirm("Add Image");
var t = confirm("Show file time");
var z = confirm("Show file size");
var r = "";
if(n) r += "_"+n;
if(p) r += ","+ p;
if(o) r += ","+ o;
if(s) r += ",style=\""+ p +"\"";
if(i) r += ",img";
var h = "";
if(t) h += "-t";
if(z) h += "-s";
if(h) r += ",show"+h;
return r;
}