function formula_params(){
var u = "http://<!--$$_VARIABLE_SERVER['HTTP_HOST']_$$--><!--$$_VARIABLE_pth_$$-->mod/formula/ajax_fcount.php?i=" + 
                      Math.floor(Math.random() * 1000);
ajaxO.open("GET", u, false);
ajaxO.send(null);
var fid = ajaxO.responseText;
return "_" + fid;
}