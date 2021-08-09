jQuery(document).ready(function($) {

    function createChart(element, chartData, color, bgcolor, chartfill, points, currencySymbol) {
        var ctx = element.get(0).getContext("2d"),
            color1 = color;
        /* if ("#ff0000" == color1) var color2 = "#f5c992";
        else if ("#006400" == color1) var color2 = "#5cdfbb"; */
        if (chartData.length > 1) {
            color2 = chartData[0] > chartData[chartData.length - 1] ? "#ff0000" : "#5cdfbb";
        } //else var color2 = color;

        var bgcolor1 = bgcolor;
        if ("#ff9999" == bgcolor1) var bgcolor2 = "#f7e0c3";
        else if ("#90EE90" == bgcolor1) var bgcolor2 = "#c1ffee";
        else var bgcolor2 = bgcolor;
        var gradientStroke = ctx.createLinearGradient(500, 0, 200, 0);
        gradientStroke.addColorStop(0, color1), gradientStroke.addColorStop(1, color2);
        var gradientFill = ctx.createLinearGradient(500, 0, 200, 0);
        gradientFill.addColorStop(0, bgcolor1), gradientFill.addColorStop(1, bgcolor2);
        var data = {
                labels: chartData,
                datasets: [{
                    fill: chartfill,
                    lineTension: .25,
                    pointRadius: points,
                    data: chartData,
                    backgroundColor: gradientFill,
                    borderColor: gradientStroke,
                    pointBorderColor: gradientStroke
                }]
            },
            maxval = Math.max.apply(Math, chartData) + 1 * Math.max.apply(Math, chartData) / 100,
            minval = Math.min.apply(Math, chartData) - 1 * Math.min.apply(Math, chartData) / 100,
            settings, chart = new Chart(element, {
                type: "line",
                data: data,
                options: {
                    hover: {
                        mode: "nearest",
                        intersect: !0
                    },
                    maintainAspectRatio: !1,
                    scales: {
                        xAxes: [{
                            display: !1
                        }],
                        yAxes: [{
                            display: !1,
                            ticks: {
                                min: minval,
                                max: maxval
                            }
                        }]
                    },
                    animation: {
                        duration: 400
                    },
                    legend: {
                        display: !1
                    },
                    tooltips: {
                        mode: "index",
                        intersect: !1,
                        displayColors: !1,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return formatTipPrice = formatPrice(parseFloat(tooltipItem.xLabel)), currencySymbol + " " + formatTipPrice
                            },
                            title: function(tooltipItem, data) {
                                return !1
                            }
                        }
                    }
                }
            })
    }

    function formatPrice(num) {
        return isNaN(num) ? "-" : (num = (num + "").split("."))[0].replace(/(\d{1,3})(?=(?:\d{3})+(?!\d))/g, "$1,") + (num.length > 1 ? "." + num[1] : "")
    }
    $.fn.generateSmallChart = function() {
        var thisEle = $(this),
            coin_id = $(this).data("coin-id"),
            period = $(this).data("period"),
            cache = $(this).data("cache"),
            color = $(this).data("color"),
            bgcolor = $(this).data("bg-color"),
            chartfill = $(this).data("chart-fill"),
            pointsSettings = $(this).data("points"),
            currencyPrice = $(this).data("currency-price"),
            currencySymbol = $(this).data("currency-symbol"),
            points = 0;
        1 == pointsSettings && (points = 2);
        var sparklineCon = $(this);
        if (1 == cache) {
            var historicalData = $(this).data("content");
            "undefined" !== historicalData ? (historicalData = historicalData.map(function(value) {
                convertedPrice = parseFloat(value) * currencyPrice;
                var decimalPosition = convertedPrice >= 1 ? 2 : convertedPrice < 1e-6 ? 8 : 6;
                return convertedPrice.toFixed(decimalPosition)
            }), createChart(thisEle, historicalData, color, bgcolor, chartfill, points, currencySymbol)) : thisEle.before('<span class="no-graphical-data">' + thisEle.data("msz") + "</span>")
        } else {

            // get data from local cache
            var cacheIndex = coin_id + '-' + period;
            var coinCacheData = lscache.get(cacheIndex);
            if (coinCacheData) {
                historicalData = coinCacheData;
                createChart(thisEle, historicalData, color, bgcolor, chartfill, points, currencySymbol);
            } else {

                var request_data = {
                    action: "ccpw_small_charts",
                    coinid: coin_id,
                    period: period
                };

                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: ajax_object.ajax_url,
                    data: request_data,
                    success: function(response) {
                        if (response != undefined && "success" == response.type) {
                            if (response.data) {
                                var historicalData = response.data;
                                historicalData = historicalData.map(function(value) {
                                    convertedPrice = parseFloat(value) * currencyPrice;
                                    var decimalPosition = convertedPrice >= 1 ? 2 : convertedPrice < 1e-6 ? 8 : 6;
                                    return convertedPrice.toFixed(decimalPosition)
                                })
                            }
                            //set local cache
                            lscache.set(coin_id + '-' + period, historicalData, 60);
                            createChart(thisEle, historicalData, color, bgcolor, chartfill, points, currencySymbol)

                        } else {
                            if (thisEle.prev('.no-graphical-data').length == 0)
                                thisEle.before('<span class="no-graphical-data">' + thisEle.data("msz") + "</span>")
                        }
                    }
                })
            }
        }
    }
    $(".ccpw-sparkline-charts").each(function(index) {
        $(this).generateSmallChart()
    })

    if ($(".slider-widget").hasClass('style-3')) {
        $(".slider-row").on("afterChange", function(slick, currentSlide, nextSlide) {
            var next_div, prev_div = nextSlide - 2;
            $("div[data-slick-index=" + (nextSlide + 2) + "] .ccpw-sparkline-charts").generateSmallChart(),
                $("div[data-slick-index=" + prev_div + "] .ccpw-sparkline-charts").generateSmallChart()
        });
    }

});