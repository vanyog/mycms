var cbckc = "#FFFFFF"
var abckc = "#ccecff"

var last_hlayer = 1;
var last_hitem = null;

function getHTop(e){
var r = e.getBoundingClientRect();
//alert(r.top);
//return r.top;
var t = 0;
do {
  t += e.offsetTop;
//  alert(e.id + " "+e.offsetTop + " "+e.style.position );
  e = e.offsetParent;
} while (e);
return t;
}

function show_hlayer(id,e){
var l = document.getElementById("HLayer"+last_hlayer);
if (l) l.style.visibility = "hidden";
var el = document.getElementById("HLayer"+id);
if (el){
  var s = el.style;
  s.visibility = "visible";
//  s.top = (getHTop(e)+e.offsetHeight-2)+"px";//(e.offsetTop+161)+"px";
  s.top = (getHTop(e) + e.offsetHeight - 2) + "px";
//  s.top = "138px";
  var w;
  w = Math.round(e.getBoundingClientRect().left) + "px";
//  w = Math.round(e.offsetLeft) + "px";
  s.left = w;
  last_hlayer = id;
}
if (last_hitem && (last_hitem!=e)){
  if (last_hitem.className!="current"){
//      last_hitem.style.color = "inherit";
//      last_hitem.style.backgroundColor = "inherit";
  }
  else{
//      last_hitem.style.color = "#ffffff";
//      last_hitem.style.backgroundColor = cbckc;
  }
}
if (e){
//  e.style.background = "url(/3/_mod/hove.png)";
//  e.style.backgroundSize = "contain";
//  if (e.className=="current") e.style.backgroundColor = "red"; else 
//  e.style.backgroundColor = abckc;
//  e.style.color = "#000000"
  last_hitem = e;
}
}
