import Api from './api.js';

import store from './store.js'
const wsApi = new Api();

const subscribeSymbol = function(symbol) {
  wsApi.onTicker(symbol,(ticker) => {
    const tick = {
      price: parseFloat(ticker.c),
      vol: parseFloat(ticker.q).toFixed(2),
      percent: parseFloat(ticker.P).toFixed(2),
      chg: ticker.p,
      high: ticker.h,
      low: ticker.l,
      open: ticker.o,
      time:ticker.E,
      symbol: symbol
    };
   
    store.commit('UPDATE_TICKER', tick)
  })
};

const combinedStream=function(symbolArr,displayRs){
 
 
  wsApi.onCombinedStream(symbolArr,(ticker) => {
     var response = {}; 
  const tick = {
      price: parseFloat(ticker.data.c),
      vol: parseFloat(ticker.data.q).toFixed(2),
      percent: parseFloat(ticker.data.P).toFixed(2),
      chg: ticker.data.p,
      high: ticker.data.h,
      low: ticker.data.l,
      open: ticker.data.o,
      time:ticker.data.E,
      symbol:ticker.data.s
    }; 
   response[ticker.data.s]=tick;
    displayRs(response);
  

  })
}
const unSubscribeSymbol = function(symbol) {
  wsApi.closeSubscription('ticker',false, symbol)
};

const subscribeChart = function(symbol, interval) {
  wsApi.onKline(symbol, interval, () => {})
};
const unSubscribeChart = function(symbol, interval) {
  wsApi.closeSubscription('kline',false, symbol, interval)
}
export {subscribeSymbol, unSubscribeSymbol, subscribeChart, unSubscribeChart,combinedStream}