(() => {
  return {
    mixins: [bbn.vue.basicComponent],
    props: ['source'],
    computed: {
      cls() {
        return appui.app.get_adherent_class(this.source.statut, this.source.statut_prospect ? this.source.statut_prospect : '') || '';
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