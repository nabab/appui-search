<!-- HTML Document -->

<div class="bbn-w-100">
  <div class="bbn-padding bbn-centered-block" style="max-width: 100%; box-sizing: border-box">
    <div class="bbn-card bbn-vlmargin">
      <h1 class="bbn-c"><?= _("Search checker") ?></h1>
      <div class="bbn-grid-fields bbn-padding">
        <label><?= _("Value to search for") ?></label>
        <bbn-input bbn-model="currentValue" class="bbn-widest"/>

        <div class="bbn-label"><?= _("Search type") ?></div>
        <bbn-dropdown source-value="num"
                      source-text="name"
                      source-url=""
                      bbn-model="currentSearch"
                      :nullable="true"
                      class="bbn-widest"
                      placeholder="<?= _("Choose a search") ?>"
                      :source="source.fns"/>

        <div class="bbn-label"><?= _("COnfiguration") ?></div>
        <div>
          <bbn-json-editor bbn-model="currentCfg"
                           class="bbn-widest"
                           readonly
                           style="min-height: 6rem"/>
        </div>

        <div class="bbn-label"> </div>
        <div>
          <bbn-button @click="send"
                      :disabled="(currentSearch === null) || !currentValue"><?= _("Check results for the selected search") ?></bbn-button>
        </div>

        <h3 class="bbn-grid-full bbn-c">
          <span bbn-if="currentResults"><?= _("Current results") ?></span>
          <span bbn-else><?= _("Select a search, set something to search for and see the results here") ?></span>
        </h3>

        <template bbn-if="currentResults">
          <div class="bbn-label"><?= _("Selected search") ?></div>
          <div bbn-text="currentResults?.cfg?.name"/>

          <div class="bbn-label"><?= _("Number of results") ?></div>
          <div bbn-text="currentResults?.data?.length"/>
          
          <div class="bbn-label">
            <bbn-button @click="showSQL = !showSQL"><?= _("Show the SQL query") ?></bbn-button>
          </div>
          <div>
            <pre bbn-if="showSQL"
                 bbn-text="currentResults.sql"
                 class="bbn-no-margin bbn-no-padding bbn-widest"
                 style="white-space: break-spaces"/>
          </div>
          
          <div class="bbn-label">
            <bbn-button @click="showResults = !showResults"><?= _("Show the results") ?></bbn-button>
          </div>
          <div>
            <pre bbn-if="showResults"
                 bbn-text="JSON.stringify(currentResults.data, null, 2)"
                 class="bbn-no-margin bbn-no-padding bbn-widest"
                 style="white-space: break-spaces"/>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>
