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
        this.focusInput();
      },
      hide() {
        this.searchOn = false;
      },
      toggle() {
        this.searchOn = !this.searchOn;
        if (this.searchOn) {
          this.focusInput();
        }
      },
      registerSearch() {
        appui.register('appui-search-big-search', this);
        this.getRef('search').registerFunction((...args) => {
          return appui.getRef('router').searchForString(...args);
        });
      },
      focusInput(){
        this.$nextTick(() => {
          const ele = this.getRef('search').getRef('input').getRef('element');
          if (ele) {
            ele.focus();
            bbn.env.focused = ele;
            bbn.fn.selectElementText(ele);
          }
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