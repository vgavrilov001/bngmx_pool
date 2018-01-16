$(document).ready(function() {
    setInterval(function() { //Renew info
        $("#mapMonitor").load("index.php #mapMonitorAjax");
    },1000*2*1);


$("#mapMonitor .city").live("click",function() {
    var city_rus = $(this).attr('city_rus');
    var city_1 = $(this).attr('city_1');
    var city_2 = $(this).attr('city_2');
    var city_3 = $(this).attr('city_3');
    var city_4 = $(this).attr('city_4');
    var docWidth = $(document).width();
    $("body").css({"overflow":"hidden","maxWidth":docWidth + "px"});
    $("#pelena").css("display","inline");
    $("#brasDivMain").animate({"margin-left":"0"},300);
    $(".list").load("index.php #loadListAjax", {city_rus:city_rus, city_1:city_1, city_2:city_2, city_3:city_3, city_4:city_4}, function() {});
});


$(".closePelena").live("click",function() {
    $("#brasDivMain").animate({"margin-left":"-710px"},300);
    setTimeout(function() {
            $("#pelena").css("display","none");
            $("body").css({"overflow":"auto","maxWidth":"100%"});
    },300);

});


var critical=0;
setInterval(function() {
    critical>5 ? critical=0 : critical++;
    $("#mapMonitor .city .critical").css("box-shadow","0 0 20px "+ critical +"px #de0b0b");
},100);

/*
var warning=0;
setInterval(function() {
    warning>5 ? warning=0 : warning++;
    $("#mapMonitor .city .warning").css("box-shadow","0 0 20px "+ warning +"px #ffca0b");
},100);
*/

});
