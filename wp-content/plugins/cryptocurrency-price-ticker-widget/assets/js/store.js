import Vue from './vue.js'
import './vuex.js'

Vue.use(Vuex);
let defaultpair = [{ "symbol":"BTCUSDT","base":"BTC","quote":"USDT","name":"Bitcoin"},{"symbol":"ETHUSDT","base":"ETH","quote":"USDT","name":"Ethereum"}];

export default new Vuex.Store({
  strict: true,
  state: {
    currencies: localStorage.hasOwnProperty('vue-crypto-currencies-new')? JSON.parse(localStorage.getItem('vue-crypto-currencies-new')) : defaultpair,
    tickers: {},
    chartData: []
  },
  getters: {
    getSymbolById: state => (symbol) => {
      return state.currencies.find(s => s.symbol === symbol);
    },
    getTickerById: state => (symbol) => {
      return state.tickers[symbol]
    }
  },
  mutations: {
    UPDATE_TICKER: (state, payload) => {
      const tick = state.tickers[payload.symbol]
      payload.pchg = tick ? (payload.price > tick.price? 1 : -1 ) : 1
      Vue.set(state.tickers, payload.symbol, payload)
    },
    ADD_COIN_PAIR: (state, payload) => {
      if(!state.tickers[payload.symbol]) {
        state.currencies.push(payload);
        localStorage.setItem('vue-crypto-currencies-new', JSON.stringify(state.currencies))
      }
      Vue.set(state.tickers, payload.symbol, { pchg: 1 })
    },
    REMOVE_COIN_PAIR: (state, symbol) => {
      Vue.delete(state.tickers, symbol)
      state.currencies.splice(state.currencies.findIndex(s => s.symbol === symbol), 1);
      localStorage.setItem('vue-crypto-currencies-new', JSON.stringify(state.currencies))
    }
  }
})
