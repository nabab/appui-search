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
        allResults: [],
        currentSignature: '',
        showSQL: false,
        showResults: false,
        to: 0,
        isLoaded: false,
        isLoading: false,
        signatures: null
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
      onSignaturesLoaded() {
        this.signatures = this.getRef('dd').currentData;
        this.isLoaded = true;
      },
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
      clear() {
        this.allResults.splice(0);
      },
      async sendAll() {
        this.allResults.splice(0);
        if (this.signatures?.length) {
          this.isLoading = true;
          for (let i = 0; i < this.signatures.length; i++) {
            bbn.fn.startChrono('chrono' + i);
            const sign = this.signatures[i];
            this.allResults.push({name: sign.data.name, time: 0, num: 0});
            const d = await this.post(appui.plugins['appui-search'] + '/checker', {idx: sign.data.signature, value: this.currentValue});
            this.allResults[i].time = bbn.fn.stopChrono('chrono' + i, true);
            if (d.data?.data?.length) {
              this.allResults[i].num = d.data.data.length;
            }
          }

          this.isLoading = false;
        }
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