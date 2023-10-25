(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-search'] + '/',
        searchOn: false,
        isRegistered: false,
      };
    },
    methods: {
      show() {
        this.searchOn = true;
      }
    },
    watch: {
      searchOn(val) {
        const search = appui.getRegistered('appui-search-big-search');
        if (val && search) {
          if (!this.isRegistered) {
            search.$on('close', () => {
              bbn.fn.log("BIG SEARVH CLOSING");
              this.searchOn = false;
            }, false, this);
            this.isRegistered = true;
          }

          search.show();
        }
      }
    }
  }
})();