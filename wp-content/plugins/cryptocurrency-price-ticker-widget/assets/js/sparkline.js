export default{
  name:'sparkline',
	    props: {
        width: {
          type: Number,
          default: 170
        },
        height: {
          type: Number,
          default: 60
        },
        preserveAspectRatio: {
          type: String,
          default: "none"
        },
        cdata: {
          type: Number,
          default: 0
        },
        limit: {
          type: Number,
          default: 20
        },
        margin: {
          type: Number,
          default: 4
        },
        smooth: {
          type: Number,
          default: 0.2
        }
    },
	  data() {
      return {
        lineData: [],
        prev: null,
        pt: [],
        boxSize:"0 0 "+ this.width+" "+this.height,
      };
    },
    watch: {
      cdata: {
        immediate: true,
        handler: function (value) {
          this.prev = null;
          const l = this.lineData.length;
          if(l === 0) {
            this.lineData = new Array(1).fill(0);
          }
          else {
            if(l === 1 && this.lineData[0] === 0) { this.lineData.pop() }
            this.lineData.push(value);
            if(l > this.limit-1) {
              this.lineData.shift()
            }
          }
          this.getDataPoints()
        }
      }
    },
    methods: {
      getMinMax(data) {
        return data.reduce((result, obj) => {
          if (obj.y < result[0]) result[0] = obj.y;
          if (obj.y > result[1]) result[1] = obj.y;
          return result;
        }, [Number.MAX_VALUE, Number.MIN_VALUE])
      },
      curve(p) {
        let res;
        if (!this.prev) {
          res = [p.x, p.y]
        } else {
          const len = (p.x - this.prev.x) * this.smooth;
          res = [ "C",
            this.prev.x + len,
            this.prev.y,
            p.x - len,
            p.y,
            p.x,
            p.y
          ];
        }
        this.prev = p;
        return res;
      },
      getDataPoints() {
        const len = this.lineData.length;
        const max = Math.max(...this.lineData);
        const min = Math.min(...this.lineData);
        const vfactor = (this.height - this.margin * 2) / (max - min || 2);
        const hfactor = (this.width - this.margin * 2) / ((this.limit || len) - (len > 1 ? 1 : 0));
        this.pt = this.lineData.map((d, i) => ({
          x: i * hfactor,
          y: max === min ? 1 : (max - d) * vfactor + this.margin
        }));
      }
    },
    computed: {
      linePoints() {
        return this.pt.map((p) => this.curve(p)).reduce((a, b) => a.concat(b));
      },
      drawLines(){
        return 'M' + (this.linePoints).join(' ');
      },
      fillPoints() {
        let allPoints = this.linePoints.concat([
          "L" + this.pt[this.pt.length - 1].x, this.height - 0,
          0, this.height - 0,
          0 , this.pt[0].y
        ]);
        return 'M'+allPoints.join(' ');
      },
      lineStyle() {
        return {
          stroke: "slategray",
          strokeWidth: 2,
          strokeLinejoin: "round",
          strokeLinecap: "round",
          fill: "none"
        };
      },
      fillStyle() {
        return {
          stroke: "none",
          strokeWidth: "0",
          fillOpacity: 0.1,
          fill: "slategray",
          pointerEvents: "auto"
        };
      }
    },
	template:`<div>
            <svg :viewBox="boxSize" :preserveAspectRatio="preserveAspectRatio">
            <defs>
                <defs>
                    <filter id="glow" x="-100%" y="-100%" width="350%" height="350%" color-interpolation-filters="sRGB">
                        <feGaussianBlur stdDeviation="1.8" result="coloredBlur" />
                        <feOffset dx="-1" dy="-1" result="offsetblur"></feOffset>
                        <feFlood id="glowAlpha" flood-color="#666" flood-opacity="0.8"></feFlood>
                        <feComposite in2="offsetblur" operator="in"></feComposite>
                        <feMerge>
                            <feMergeNode/>
                            <feMergeNode in="SourceGraphic"></feMergeNode>
                        </feMerge>
                    </filter>
                </defs>
            </defs>
              <g>
                  <path :d="fillPoints" :style="fillStyle" />
                  <path :d="drawLines" :style="lineStyle" class="sline"/>
              </g>
              <g>
                  <circle :cx="pt[pt.length - 1].x - 2"  :cy="pt[pt.length - 1].y" :r="3" style="fill:red"/>
              </g>
            </svg>
            </div>`
}