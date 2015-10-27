var last_hlayer = 1;
var last_hitem = null;

function getHTop(e){
<<<<<<< HEAD
var r = e.getBoundingClientRect();
//alert(r.top);
//return r.top;
var t = 0;
do {
  t += e.offsetTop;
//  alert(e.id + " "+e.offsetTop + " "+e.style.position );
=======
var t = 0;
do {
  t += e.offsetTop;
>>>>>>> a4315946ef3842c73660e8cee97aa86450811558
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
<<<<<<< HEAD
//  s.top = (getHTop(e)+e.offsetHeight-2)+"px";//(e.offsetTop+161)+"px";
  s.top = (getHTop(e) + e.offsetHeight - 2) + "px";
//  s.top = "138px";
  var w;
  w = Math.round(e.getBoundingClientRect().left) + "px";
//  w = Math.round(e.offsetLeft) + "px";
  s.left = w;
=======
  s.top = (getHTop(e) + e.offsetHeight - 2) + "px";
  s.left = Math.round(e.getBoundingClientRect().left) + "px";
>>>>>>> a4315946ef3842c73660e8cee97aa86450811558
  last_hlayer = id;
}
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
