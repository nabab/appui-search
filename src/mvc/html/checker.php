<!-- HTML Document -->

<div class="bbn-w-100">
  <div class="bbn-padding bbn-centered-block" style="max-width: 100%; box-sizing: border-box">
    <div class="bbn-card bbn-vlmargin">
      <h1 class="bbn-c"><?= _("Search checker") ?></h1>
      <div class="bbn-section bbn-flex-height">
        <legend><?= _("What it does") ?></legend>
        <p class="bbn-w-100">
          <?= _("First insert the value to search for becaue it will condition the searches that will be launched") ?><br><br>
          <?= _("Then pick the search that you to be performed.") ?><br>
          <?= _("Once picked you will see its configuraion in the JSON editor") ?><br>
          <?= _("Below two buttons will appear:") ?>
        </p>
        <ul class="bbn-w-100">
          <li><?= _("one to display the database query") ?></li>
          <li><?= _("the other will perform the search and show you the results") ?></li>
        </ul>
      </div>
      <div class="bbn-grid-fields bbn-padding">
        <label><?= _("Value to search for") ?></label>
        <bbn-input bbn-model="currentValue"
                   class="bbn-widest"
                   :nullable="true"
                   @input="changeValue"/>

        <div class="bbn-label"><?= _("Search type") ?></div>
        <bbn-dropdown source-value="signature"
                      source-text="text"
                      source-url=""
                      bbn-model="currentSearch"
                      :disabled="!currentValue.length"
                      :nullable="true"
                      class="bbn-widest"
                      placeholder="<?= _("Choose a search") ?>"
                      :data="{value: currentValue}"
                      :source="root + 'searches'"
                      ref="dd"
                      @dataloaded="onSignaturesLoaded"/>

        <label><?= _("Search by signature") ?></label>
        <bbn-input bbn-model="currentSignature"
                   class="bbn-widest"
                   @input="changeSignature"/>

        <div class="bbn-label"><?= _("Configuration") ?></div>
        <div>
          <bbn-json-editor bbn-model="currentCfg"
                           class="bbn-widest"
                           readonly
                           style="min-height: 6rem"/>
        </div>

        <div class="bbn-label"> </div>
        <div>
          <bbn-button @click="send"
                      :disabled="(currentSearch === null) || !currentValue">
            <?= _("Check results") ?>
          </bbn-button>
          <bbn-button @click="sendAll"
                      :disabled="!isLoaded || !currentValue.length"
                      bbn-if="!isLoading">
            <?= _("Test all searches") ?>
          </bbn-button>
          <bbn-button @click="clear"
                      :disabled="!isLoaded || !currentValue.length"
                      bbn-elseif="!isLoading">
            <?= _("Clear") ?>
          </bbn-button>
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
        <template bbn-else
                  bbn-for="res in allResults">
          <div class="bbn-label"><?= _("Search name") ?></div>
          <div bbn-text="res.name"/>

          <div class="bbn-label"><?= _("Number of results") ?></div>
          <div :class="{
            'bbn-green': res.num > 0,
            'bbn-red': res.time && !res.num
          }"
               bbn-text="res.time ? res.num : _('Unknown')"/>

          <div class="bbn-label"><?= _("Query time") ?></div>
          <div bbn-text="res.time || _('Running')"/>

          <hr>
        </template>
      </div>
    </div>
  </div>
</div>
