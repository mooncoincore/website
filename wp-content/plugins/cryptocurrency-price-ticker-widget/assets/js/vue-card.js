import sparkline from './sparkline.js'
Math.convertNumber = function(num) {
    
    var si = [
        { value: 1, symbol: "" },
        { value: 1E3, symbol: "k" },
        { value: 1E6, symbol: "M" },
        { value: 1E9, symbol: "B" },
        { value: 1E12, symbol: "T" },
        { value: 1E15, symbol: "P" },
        { value: 1E18, symbol: "E" }
      ];
      var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
      var i;
      for (i = si.length - 1; i > 0; i--) {
        if (num >= si[i].value) {
          break;
        }
      }
      return (num / si[i].value).toFixed(2).replace(rx, "$1") + si[i].symbol;
}

export default{
    name:'currencycards',
	data(){
		return {
			showDropDown: false,
			iconbase: 'https://raw.githubusercontent.com/rainner/binance-watch/master/public/images/icons/' + this.info.base.toLowerCase() + '_.png',
            erorlogo:'this.src=\''+ this.info.logo+this.info.name.toLowerCase()+'.svg\'',
		}
    },
    components:{
        'sparkline':sparkline
    },
	props:['ticker','info','ischart'],
	template:`<div class="coin-box" @dblclick.stop="openDetails">
    <div class="row no-gutters coin-info">
        <div class="col-7">
            <div class="font-weight-bold coin-label">{{info.name}}</div>
            <div class="row no-gutters mt-1">
                <div class="box-icon">
                    <img :src="iconbase" :onerror="erorlogo"></span>
                </div>
                <div class="col text-left">
                    <div>{{info.base}}/{{info.quote}}</div>
                    <div class="coin-price" v-if="ticker.price">{{ticker.price || '' }}<span style="font-size: x-small; font-weight: 700; padding-left: 3px;">{{info.quote}}</span></div>
                </div>
            </div>
        </div>
        <div :class="[(ticker.percent < 0)?'ccpwp-binance-live-down':'ccpwp-binance-live-up', 'col-5','text-right']" v-if="ticker.price">
            <div class="coin-per"><span :class="[(ticker.percent < 0)?'ccpw_icon-down':'ccpw_icon-up']"></span><span>{{ ticker.percent }}%</span></div>
            <div class="coin-chg">{{parseFloat(ticker.chg).toFixed((info.quote === 'USDT') ? 3 : 8)}} </div>
            <div><span>Vol: {{ Math.convertNumber(ticker.vol) }}</span></div>
        </div>
    </div>
    <div class="sparkline-chart" v-if="ticker.price && ischart">
        <sparkline :cdata="ticker.price" :width="380" :height="90"></sparkline>
    </div>
</div>`,
}

