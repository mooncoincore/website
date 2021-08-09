jQuery(document).ready(function($){
    $(".ccpw-ticker-cont ul").each(function(index){
            var speed=$(this).data("speed");
            var eleWidth  =  $(this).find("li:first-child").width();
            $(this).bxSlider({
                ticker:true,
                minSlides:1,
                maxSlides:12,
                slideWidth:"auto",
                tickerHover:true,
                wrapperClass:"tickercontainer",
                speed:speed*4000,
            });
         });

    $(".ccpw-tooltip").not(".tooltipstered").tooltipster({
        animation: "fade",
        contentCloning: true,
        contentAsHTML: true,
        interactive: true,
        delayTouch:[200,200]
    }); 

});