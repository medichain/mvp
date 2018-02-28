/*! Fabrik */
Date.CultureInfo={name:"fr-FR",englishName:"French (France)",nativeName:"français (France)",dayNames:["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"],abbreviatedDayNames:["dim.","lun.","mar.","mer.","jeu.","ven.","sam."],shortestDayNames:["di","lu","ma","me","je","ve","sa"],firstLetterDayNames:["d","l","m","m","j","v","s"],monthNames:["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"],abbreviatedMonthNames:["janv.","févr.","mars","avr.","mai","juin","juil.","août","sept.","oct.","nov.","déc."],amDesignator:"",pmDesignator:"",firstDayOfWeek:1,twoDigitYearMax:2029,dateElementOrder:"dmy",formatPatterns:{shortDate:"dd/MM/yyyy",longDate:"dddd d MMMM yyyy",shortTime:"HH:mm",longTime:"HH:mm:ss",fullDateTime:"dddd d MMMM yyyy HH:mm:ss",sortableDateTime:"yyyy-MM-ddTHH:mm:ss",universalSortableDateTime:"yyyy-MM-dd HH:mm:ssZ",rfc1123:"ddd, dd MMM yyyy HH:mm:ss GMT",monthDay:"d MMMM",yearMonth:"MMMM yyyy"},regexPatterns:{jan:/^janv(.(ier)?)?/i,feb:/^févr(.(ier)?)?/i,mar:/^mars/i,apr:/^avr(.(il)?)?/i,may:/^mai/i,jun:/^juin/i,jul:/^juil(.(let)?)?/i,aug:/^août/i,sep:/^sept(.(embre)?)?/i,oct:/^oct(.(obre)?)?/i,nov:/^nov(.(embre)?)?/i,dec:/^déc(.(embre)?)?/i,sun:/^di(m(.(anche)?)?)?/i,mon:/^lu(n(.(di)?)?)?/i,tue:/^ma(r(.(di)?)?)?/i,wed:/^me(r(.(credi)?)?)?/i,thu:/^je(u(.(di)?)?)?/i,fri:/^ve(n(.(dredi)?)?)?/i,sat:/^sa(m(.(edi)?)?)?/i,future:/^next/i,past:/^last|past|prev(ious)?/i,add:/^(\+|aft(er)?|from|hence)/i,subtract:/^(\-|bef(ore)?|ago)/i,yesterday:/^yes(terday)?/i,today:/^t(od(ay)?)?/i,tomorrow:/^tom(orrow)?/i,now:/^n(ow)?/i,millisecond:/^ms|milli(second)?s?/i,second:/^sec(ond)?s?/i,minute:/^mn|min(ute)?s?/i,hour:/^h(our)?s?/i,week:/^w(eek)?s?/i,month:/^m(onth)?s?/i,day:/^d(ay)?s?/i,year:/^y(ear)?s?/i,shortMeridian:/^(a|p)/i,longMeridian:/^(a\.?m?\.?|p\.?m?\.?)/i,timezone:/^((e(s|d)t|c(s|d)t|m(s|d)t|p(s|d)t)|((gmt)?\s*(\+|\-)\s*\d\d\d\d?)|gmt|utc)/i,ordinalSuffix:/^\s*(st|nd|rd|th)/i,timeContext:/^\s*(\:|a(?!u|p)|p)/i},timezones:[{name:"UTC",offset:"-000"},{name:"GMT",offset:"-000"},{name:"EST",offset:"-0500"},{name:"EDT",offset:"-0400"},{name:"CST",offset:"-0600"},{name:"CDT",offset:"-0500"},{name:"MST",offset:"-0700"},{name:"MDT",offset:"-0600"},{name:"PST",offset:"-0800"},{name:"PDT",offset:"-0700"}]};