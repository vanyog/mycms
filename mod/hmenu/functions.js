var last_hlayer = 1;
var last_hitem = null;

function getHTop(e){
var t = 0;
do {
  t += e.offsetTop;
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
  s.top = (getHTop(e) + e.offsetHeight - 2) + "px";
  s.left = Math.round(e.getBoundingClientRect().left) + "px";
  last_hlayer = id;
}
set_layer_colors(e);
}

function set_layer_colors(e){
if (last_hitem && (last_hitem!=e)){
  if (last_hitem.className!="current"){
      if (color2)  last_hitem.style.color = color2;
      if (bcolor2) last_hitem.style.backgroundColor = bcolor2;
  }
  else{
      if (color3)  last_hitem.style.color = color3;
      if (bcolor3) last_hitem.style.backgroundColor = bcolor3;
  }
}
if (e){
  if (color1)  e.style.color = color1;
  if (bcolor1) e.style.backgroundColor = bcolor1;
  last_hitem = e;
}
}

function hide_layer(i,e){
  var eh = document.getElementById("HLayer"+i);
  if(eh) eh.style.visibility = "hidden";
  set_layer_colors(e)
}
