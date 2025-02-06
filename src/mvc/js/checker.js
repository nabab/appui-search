// Javascript Document

(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    data() {
      return {
        root: appui.plugins['appui-search'] + '/',
        currentValue: '',
        currentSearch: null,
        currentResults: null,
        currentSignature: '',
        showSQL: false,
        showResults: false,
        to: 0
      }
    },
    computed: {
      currentCfg() {
        if (this.currentSearch) {
          return bbn.fn.getRow(this.getRef('dd').currentData, {'data.signature': this.currentSearch}).data;
        }
  
        return {};
      },
    },
    methods: {
      changeSignature() {
        if (this.currentSignature) {
          const row = bbn.fn.getRow(this.getRef('dd').currentData, {'data.signature': this.currentSignature});
          if (row) {
            this.currentSearch = row.data.signature;
          }
        }
      },
      changeValue() {
        this.currentSearch = null;
        clearTimeout(this.to);
        this.to = setTimeout(() => {
          this.getRef('dd').updateData()          
        }, 250)
      },
      send() {
        if (this.currentCfg && this.currentValue) {
          this.post(appui.plugins['appui-search'] + '/checker', {idx: this.currentCfg.signature, value: this.currentValue}, d => {
            if (d.sql) {
              this.currentResults = d;
            }
          });
        }
      },
      initResults() {
        this.showSQL = false;
        this.showResults = false;
        this.currentResults = null;
      }
    },
    watch: {
      currentSearch() {
        this.initResults();
      }
    }
  }
})()