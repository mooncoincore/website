jQuery(document).ready(function ($) {
    var table_id = "";

    function ccpw_numeral_formating(data) {
        if (data >= 25 || data <= -1) var formatedVal = numeral(data).format("0,0.00");
        else if (data >= .5 && data < 25) var formatedVal = numeral(data).format("0,0.000");
        else if (data >= .01 && data < .5) var formatedVal = numeral(data).format("0,0.0000");
        else if (data >= 1e-4 && data < .01) var formatedVal = numeral(data).format("0,0.00000");
        else var formatedVal = numeral(data).format("0,0.00000000");
        return formatedVal
    }
    $.fn.ccpwDatatable = function () {
        table_id = $(this).attr("id");
        var $ccpw_table = $(this),
            columns = [],
            fiatSymbol = $ccpw_table.data("currency-symbol"),
            fiatCurrencyRate = $ccpw_table.data("currency-rate"),
            pagination = $ccpw_table.data("pagination"),
            fiatCurrency = $ccpw_table.data("currency-type"),
            live_changes = $ccpw_table.data("live-changes"),
            requiredCurrencies = $ccpw_table.data("required-currencies"),
            prevtext = $ccpw_table.data("prev-coins"),
            nexttext = $ccpw_table.data("next-coins"),
            zeroRecords = $ccpw_table.data("zero-records"),
            currencyLink = $ccpw_table.data("currency-slug"),
            dynamicLink = $ccpw_table.data("dynamic-link"),
            loadingLbl = $ccpw_table.data("loadinglbl"),
            numberFormat = $ccpw_table.data("number-formating");
        $ccpw_table.find("thead th").each(function (index) {
            var thisTH = $(this),
                index = thisTH.data("index"),
                classes = thisTH.data("classes");
            columns.push({
                data: index,
                name: index,
                render: function (data, type, row, meta) {
                    if (void 0 === meta.settings.json) return data;
                    switch (index) {
                        case "rank":
                            return data;
                        case "name":
                            if (void 0 !== dynamicLink && "" != dynamicLink) var coinLink = currencyLink + "/" + row.symbol + "/" + row.id,
                                html = '<div class="' + classes + '"><a class="ccpw_links" title="' + row.name + '" href="' + coinLink + '"><span class="ccpw_coin_logo">' + row.logo + '</span><span class="ccpw_coin_symbol">(' + row.symbol + ')</span><br/><span class="ccpw_coin_name ccpw-desktop">' + row.name + "</span></a></div>";
                            else var html = '<div class="' + classes + '"><span class="ccpw_coin_logo">' + row.logo + '</span><span class="ccpw_coin_symbol">(' + row.symbol + ')</span><br/><span class="ccpw_coin_name ccpw-desktop">' + data + "</span></div>";
                            return html;
                        case "price":
                            if (void 0 !== data && null != data) {
                                var formatedVal = ccpw_numeral_formating(data);

                                if("" != live_changes || null != live_changes){
                                return '<div data-val="' + row.price + '" class="' + classes + '"><div class="ccpw-formatted-price ccpw_coin_cont"  data-trading-pair="' + row.symbol + 'USDT" data-coin-price="' + row.price + '" data-coin-id="' + row.id + '" data-currency-rate="' + fiatCurrencyRate + '" data-currency-symbol="' + fiatSymbol + '" data-currency-type="' + fiatCurrency + '"><span class="live_price">' + fiatSymbol + formatedVal + "</span></span></div>";
                                 } else
                                 { return '<div data-val="' + row.price + '" class="' + classes + '"><span class="ccpw-formatted-price " data-coin-price="' + row.price + '" data-coin-symbol="' + row.symbol + '">' + fiatSymbol + formatedVal + "</span></div>";
                                }
                            }
                            return '<div class="' + classes + ">?</div>";
                        case "change_percentage_24h":
                            if (void 0 !== data && null != data) {
                                var changesCls = "up",
                                    wrpchangesCls = "ccpw-up",
                                    html;
                                if (void 0 === Math.sign && (Math.sign = function (x) {
                                        return x > 0 ? 1 : x < 0 ? -1 : x
                                    }), -1 == Math.sign(data)) var changesCls = "down",
                                    wrpchangesCls = "ccpw-down";
                                return '<div class="' + classes + " " + wrpchangesCls + '"><span class="changes ' + changesCls + ' live_c"><i class="ccpw_icon-' + changesCls + '" aria-hidden="true"></i>' + data + "%</span></div>"
                            }
                            return '<div class="' + classes + '">?</span></div>';
                        case "market_cap":
                            if (void 0 !== data && null != data) {
                                var formatedVal = ccpw_numeral_formating(data);
                                if (numberFormat) var formatedVal = numeral(data).format("(0.00 a)").toUpperCase();
                                return '<div data-val="' + row.market_cap + '" class="' + classes + '"><span class="ccpw-formatted-market-cap">' + fiatSymbol + formatedVal + "</span></div>"
                            }
                            return '<div class="' + classes + ">?</div>";
                        case "total_volume":
                            if (void 0 !== data && null != data) {
                                var formatedVal = ccpw_numeral_formating(data);
                                if (numberFormat) var formatedVal = numeral(data).format("(0.00 a)").toUpperCase();
                                return '<div data-val="' + row.total_volume + '" class="' + classes + '"><span class="ccpw-formatted-total-volume">' + fiatSymbol + formatedVal + "</span></div>"
                            }
                            return '<div class="' + classes + ">?</div>";
                        case "supply":
                            if (void 0 !== data && null != data && "N/A" != row.supply) {
                                var formatedVal = ccpw_numeral_formating(data);
                                if (numberFormat) var formatedVal = numeral(data).format("(0.00 a)").toUpperCase();
                                return '<div data-val="' + row.supply + '" class="' + classes + '"><span class="ccpw-formatted-supply">' + formatedVal + " " + row.symbol + "</span></div>"
                            }
                            return '<div class="' + classes + '">N/A</div>';
                        default:
                            return data
                    }
                },
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).attr("data-sort", cellData)
                }
            })
        }), $ccpw_table.DataTable({
            deferRender: !0,
            serverSide: !0,
            ajax: {
                url: ccpw_js_objects.ajax_url,
                type: "POST",
                dataType: "JSON",
                data: function (d) {
                    d.action = "ccpwp_get_coins_list",
                    d.currency = fiatCurrency,
                    d.nonce=ccpw_js_objects.wp_nonce,
                    d.currencyRate = fiatCurrencyRate,
                    d.requiredCurrencies = requiredCurrencies
                }
            },
            ordering: !1,
            searching: !1,
            pageLength: pagination,
            columns: columns,
            responsive: !0,
            lengthChange: !1,
            pagingType: "simple",
            processing: !0,
            dom: '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
            language: {
                processing: loadingLbl,
                loadingRecords: loadingLbl,
                paginate: {
                    next: nexttext,
                    previous: prevtext
                }
            },
            zeroRecords: zeroRecords,
            emptyTable: zeroRecords,
            renderer: {
                header: "bootstrap"
            },
            drawCallback: function (settings) {
                $ccpw_table.tableHeadFixer({
                    head: !0,
                    foot: !1,
                    left: 2,
                    right: !1,
                    "z-index": 1
                });
                if ( $(".ccpw-live").length>0) {
                      $(this).ccpWsetupWebSocket();
                }

            }
        })
    }, $(".ccpw_table_widget").each(function () {
        $(this).ccpwDatatable()
    }), new Tablesort(document.getElementById(table_id), {
        descending: !0
    })
});