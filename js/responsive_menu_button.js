var menuDiv = document.getElementById("page_menu");
var menuCHeight = 0;
function menuClicked(){
if(menuDiv.offsetHeight!=menuCHeight){
  menuDiv.style.height = "47px";
  menuDiv.style.minWidth = "initial";
  menuDiv.style.width = "47px";
  menuDiv.style.overflow = "hidden";
  menuDiv.style.float = "left";
  menuCHeight = menuDiv.offsetHeight;
}
else {
  menuDiv.style.height = "auto";
  menuDiv.style.width = "calc(100% - 1.5em)";
  menuDiv.style.overflow = "auto";
  menuDiv.style.float = "none";
}
}
var menuBtnImg = null;
function menuInit() {
  var bodyWidth = document.body.offsetWidth;
  if( (bodyWidth < 550) && (menuBtnImg === null) ){
    menuBtnImg = document.createElement("img");
    menuBtnImg.src = "/1/images/menuicon.png";
    menuBtnImg.style.cursor = "pointer";
    menuBtnImg.style.marginBottom = "0.5em";
    menuBtnImg.addEventListener("click", menuClicked);
    menuDiv.insertAdjacentElement('afterbegin', menuBtnImg);
    menuClicked();
  }
  if ( (bodyWidth >= 550) && !(menuBtnImg === null) ){
     menuDiv.removeChild(menuDiv.childNodes[0]);
     menuDiv.style.width = "auto";
     menuDiv.style.height = "auto";
     menuDiv.style.overflow = "auto";
     menuDiv.style.float = "left";
     menuBtnImg = null;
  }
}
menuInit();
window.onresize = menuInit;