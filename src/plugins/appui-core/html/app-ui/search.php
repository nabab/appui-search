<div bbn-show="searchOn"
     class="bbn-overlay bbn-secondary"
     @keydown.escape="searchOn = false"
     style="z-index: 13; background-color: transparent !important;">
   <div bbn-if="searchOn"
        class="bbn-overlay bbn-secondary"
        style="opacity: 0.9"> </div>
   <bbn-stream-search :placeholder="cfg.placeholder"
                     :select-url="root + 'select'"
                     ref="search"
                     :start-url="root + 'start'"
                     :reset-url="root + 'update'"
                     :stop-url="root + 'stop'"
                     :select-url="root + 'select'"
                     bbn-model="cfg.value"
                     :suggest="true"
                     @hook:mounted="registerSearch"
                     :source-value="cfg.sourceValue"
                     :source-text="cfg.sourceText"
                     :min-length="cfg.minLength"
                     @select.prevent="$nextTick(() => searchOn = false)"
                     @close="hide"
                     :limit="50"
                     :pageable="true"
                     class="bbn-no"/>
   <div class="bbn-top-right bbn-p bbn-spadding bbn-xl"
         @click="hide"
         tabindex="0">
      <i class="nf nf-fa-times"/>
   </div>
</div>
