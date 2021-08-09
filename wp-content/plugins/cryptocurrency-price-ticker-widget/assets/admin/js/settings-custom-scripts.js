jQuery(document).ready(function($){
    var widgetType='';
    var widgetType =  $(".cmb2-id-type select#type").val();
    var sField=$(".cmb2-id-display-currencies");
    var showCoinsSettings=$("#show-coins");
    switch(widgetType){
        case "table-widget":
        case "calculator":   
        case "changelly-widget":
        case "rss-feed": 
        case "technical-analysis": 
        sField.hide();
        break;
        case "chart":
            $("#show-coins option").not(":first").each(function(){
                $(this).attr("disabled",true);
            });
        break;
        default:
           // sField.show();
            $("#show-coins option").not(":first").each(function(){
                $(this).attr("disabled",false);
            });
         //   $("#display_currencies").attr("required",false);
        break;
    }
   $(".cmb2-id-type select#type").change(function(){
        var widgetType = $(this).children("option:selected").val();
        console.log(widgetType);
            if(widgetType=="chart"){
                $("#show-coins option").not(":first").each(function(){
                    $(this).attr("disabled",true);
                }); 
               
            }else{
               $("#show-coins option").not(":first").each(function(){
                    $(this).attr("disabled",false);
                }); 
            }
    });

   var showCoinsVal= $("#show-coins").val();
        if(showCoinsVal=="custom"){
           // $("#display_currencies").attr("required",true);
        }
        $("#show-coins").change(function(){
            var selVal = $(this).children("option:selected").val();
            if(selVal=="custom"){
            //    $("#display_currencies").attr("required",true)
            }else{
              //  $("#display_currencies").attr("required",false)
            }
        });
});
