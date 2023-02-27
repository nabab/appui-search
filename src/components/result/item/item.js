(() => {
  return {
    mixins: [bbn.vue.basicComponent],
    props: {
      source: {
        type: Object,
        required: true
      /*},
      url: {
        type: String,
        required: true
      */
      },
      score: {
        type: Boolean,
        default: true
      }
    },
    methods: {
      link: bbn.fn.link
    },
    mounted() {
      this.ready = true;
    }
  }
})();