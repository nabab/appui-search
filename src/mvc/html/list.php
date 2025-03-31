<!-- HTML Document -->
<div class="bbn-overlay">
  <bbn-table :source="appui.plugins['appui-search'] + '/list'"
             :sortable="true"
             :pageable="true"
             :filterable="true"
             :selection="true"
             ref="table"
             :limit="100">
    <bbns-column field="value"
                 :label="_('Search expression')"
                 :min-width="200"/>
    <bbns-column field="num"
                 label="#"
                 :width="50"
                 type="number"
                 :flabel="_('Number of times searched')"/>
    <bbns-column field="results"
                 :label="_('# Results')"
                 type="number"
                 :width="100"/>
    <bbns-column field="last"
                 :label="_('Last')"
                 :width="150"
                 type="datetime"/>
    <bbns-column :label="_('Actions')"
                 :width="120"
                 :buttons="[{label: _('delete'), icon: 'nf nf-fa-times', action: removeSearch}]"/>
  </bbn-table>
</div>
