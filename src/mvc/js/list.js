(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    methods: {
      removeSearch(row) {
        this.confirm(bbn._("Are you sure you want to remove these records from your searches?"), () => {
          bbn.fn.post(appui.plugins['appui-search'] + '/list', {id: row.id}, d => {
            if (d.success) {
              appui.success()
              this.getRef('table').updateData();
            }
            else {
              appui.error()
            }
          })
        })
      }
    }
  }
})();