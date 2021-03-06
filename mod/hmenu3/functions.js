var last_hlayer = -1;
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
    var lp = Math.round(e.getBoundingClientRect().left);
    s.left = lp + "px";
    var mdiv = document.getElementById(hmid);
    var maxr = mdiv.offsetLeft + mdiv.offsetWidth;
    if( maxr < (el.offsetLeft + el.offsetWidth) ) lp = maxr - el.offsetWidth + 5;
    s.left = lp + "px";
    last_hlayer = id;
}
set_layer_colors(e);
}

function set_layer_colors(e){
if (last_hitem && (last_hitem!==e)){
  if (last_hitem.className!=="current"){
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

function hide_layer2(i,el,ev){
var l = document.getElementById("HLayer"+i);
if ( ((ev.clientY - el.offsetTop) < 0) || (!l)){
    hide_layer(i,el);
    if (color2)  last_hitem.style.color = color2;
    if (bcolor2) last_hitem.style.backgroundColor = bcolor2;
}
}

function hmenu3_hclick(i,e){
    var eh = document.getElementById("HLayer"+i);
    var h = window.getComputedStyle(eh).visibility;
    var t = getHTop(e) + e.offsetHeight - 2
//    alert(eh.offsetTop + " " + t + " - " + i + " " + h);
    if(h==="visible") hide_layer(i,e);
    if(h==="hidden")  show_hlayer(i,e);
}

function hmenu3_correct_layout(){
    var mdiv = document.getElementById(hmid);
    var a = mdiv.getElementsByTagName("a");
    var ndiv = document.createElement("div");
    var moved = 0;
    var maxi = a.length - 1;
    var mep = mdiv.offsetLeft + mdiv.offsetWidth;
    for(var i = maxi; i > 0; i--){
        var nep = a[i].offsetLeft + a[i].offsetWidth;
        if(i < maxi) nep = nep + 40;
        if   ( nep >= mep ) {
            a[i].onmouseover = "";
            a[i].onclick = "";
            ndiv.insertBefore(a[i], ndiv.firstChild);
            moved++;
        }
    }
    if(moved){
        var nlink = document.createElement("a");
        nlink.href = "#";
        nlink.innerText = ">>";
        hl = maxi + 1;
        nlink.addEventListener("mouseleave", function(){ hide_layer2(hl, this, event); } );
        nlink.addEventListener("mouseover", function(){ show_hlayer(hl,this); } );
        nlink.addEventListener("click", function(){ hmenu3_hclick(hl,this); } );
        mdiv.appendChild(nlink);
        ndiv.id = "HLayer" + hl;
        document.body.appendChild(ndiv);
    }

}

var last_res = 0;
var last_w = 0;
function hmenu3_resize_listener(e){
    var t = Date.now();
    if(t-last_res > 1000){
        var mdiv = document.getElementById(hmid);
        var w = mdiv.offsetLeft + mdiv.offsetWidth;
        last_res = t;
//        if(w !== last_w) setTimeout(function(){ location.reload(); }, 2000);
        last_w = w;
        return;
    }
}
