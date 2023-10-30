(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-search'] + '/',
        searchOn: false,
        cfg: {
          sourceValue: 'value',
          sourceText: 'text',
          minLength: 2,
          placeholder: bbn._('Search for...'),
          value: ''
        }
      };
    },
    methods: {
      show() {
        this.searchOn = true;
      },
      hide() {
        this.searchOn = false;
      },
      toggle() {
        this.searchOn = !this.searchOn;
      },
      registerSearch() {
        appui.register('appui-search-big-search', this);
        this.getRef('search').registerFunction((...args) => {
          return appui.getRef('router').searchForString(...args);
        });
      }
    },
    watch: {
      searchOn(v) {
        this.$emit(v ? 'open' : 'close');
        this.getRef('search').isOpened = v;
      }
    },
  }
})();