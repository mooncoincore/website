import Vue from './vue.js'
import currencycards from './vue-card.js'
import store from './store.js'
import "./vuex.js"
import {subscribeSymbol} from './binance.js'
Vue.config.productionTip = false;
jQuery(document).ready(function($){

	let vue_widgets = $('.ccpwp-binance-live-widget');

	if( vue_widgets.length > 0 ){
		vue_widgets.each(function(el){
			let element = $(this).attr('id');
			let data = window[ element ];
			let CoinData = data ;
			let vueWidget = new Vue({
				store,
				data:{
					quote: 'BNB',
					quoteOptions: ['BNB','BTC','ETH', 'USDT'],
					baseCurrency: {},
					coins: CoinData,
				},
				components:{
					'ccpw-vuewidget':currencycards
				},
				mounted(){
					if(this.coins) {
					  this.coins.forEach(coins => {
						subscribeSymbol(coins.symbol);
					  });
					}
				  },
				computed: {
					...Vuex.mapState(['tickers'])
				},
			}).$mount("#"+element);
		});
	}

});
