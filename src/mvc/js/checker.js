// Javascript Document

(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    data() {
      return {
        currentValue: '',
        currentSearch: null,
        currentResults: null,
        showSQL: false,
        showResults: false
      }
    },
    computed: {
      currentCfg() {
        if (this.currentSearch !== null) {
          return this.source.fns[this.currentSearch];
        }
  
        return {};
      }
    },
    methods: {
      send() {
        if (this.source.fns[this.currentSearch] && this.currentValue) {
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
      currentValue() {
        this.initResults();
      },
      currentSearch() {
        this.initResults();
      }
    }
  }
})()