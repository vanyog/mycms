// Модул за българската азбука, който се използва в програми за обучение на деца


if(typeof(cookie_message) == "undefined") 
   cookie_message = "На тази страница използваме бисквитки. Искате ли да ги приемете, за да функционира страницата правилно?";

// Брой на изучаваните букви
var limit = cookie_value('limit_of_letters_to_learn', 8);
            if(limit<4) limit = 4;

// Буквите в азбучен ред
var letter = ['А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О',
              'П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ь','Ю','Я'];

// Ред, в който буквите се изучават
var lorder = ['А','Е','М','Н','О','И','Р','Л','Ъ','У','Й','Я','Т','Д','С',
              'З','К','Г','П','Б','Ф','В','Ш','Ж','Ч','Х','Ц','Щ','Ю','Ь']; 

// Транслитерация на буквите на латиница. Използва се за имена на файлове и директории, свързани с изучаването на букви.
var file = ['a','e','m','n','o','i','r','l', 'yg', 'u','j','ya','t',  'd', 's',
            'z','k','g','p','b','f','v','sh','gh','ch','x','ts','sht','yu','ym'];

// Връща истина, когато буквата е гласна
function is_vowel(i){
  switch(lorder[i]){
    case "А": case "Е": case "О": case "И": case "Ъ": case "У": case "Я": case "Ю": return true;
    default: return false;
  }
}

// Настройки, определящи какви букви се показват
var option = { 
  upper:true, // главни
  lower:true, // малки
  script:true // ръкописни
};

// Функция, която връща буква с номер i от масив lorder
// и променя стила на html елемент e, в който се показва тази буква, за да се покаже като ръкописна.

function rletter(i,e){
var rl = lorder[i];
var p1 = 0.5;
var p2 = 0.5;
if(!option.upper) p1 = 1;
if(option.lower && (Math.random()<p1)) rl = rl.toLowerCase();
if(option.script && (Math.random()<p2)){
    e.style.fontFamily = 'parvolak';
    e.style.fontSize = '108%';
//    e.style.verticalAlign = "-5px";
}
return rl;
}



