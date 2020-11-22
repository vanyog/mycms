var limit = cookie_value('limit_of_letters_to_learn', 8);
            if(limit<4) limit = 4;

if(typeof(cookie_message) == "undefined") 
   cookie_message = "На тази страница използваме бисквитки. Искате ли да ги приемете, за да функционира страницата правилно?";

var file = ['a','e','m','n','o','i','r','l', 'yg', 'u','j','ya','t',  'd', 's',
            'z','k','g','p','b','f','v','sh','gh','ch','x','ts','sht','yu','ym'];

var letter = ['А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О',
              'П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ь','Ю','Я'];

var lorder = ['А','Е','М','Н','О','И','Р','Л','Ъ','У','Й','Я','Т','Д','С',
              'З','К','Г','П','Б','Ф','В','Ш','Ж','Ч','Х','Ц','Щ','Ю','Ь']; 

var option = { upper:true, lower:true, script:true };

function rletter(i,e){
var rl = lorder[i];
var p1 = 0.5;
var p2 = 0.5;
//if(options.upper) p1 = 0.1;
if(option.lower && (Math.random()<p1)) rl = rl.toLowerCase();
if(option.script && (Math.random()<p2)){
    e.style.fontFamily = 'parvolak';
    e.style.fontSize = '108%';
//    e.style.verticalAlign = "-5px";
}
return rl;
}



