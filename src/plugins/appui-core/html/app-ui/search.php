<div v-show="searchOn"
     class="bbn-overlay bbn-secondary"
     @keydown.escape="searchOn = false"
     style="z-index: 13; background-color: transparent !important;">
   <div v-if="searchOn"
        class="bbn-overlay bbn-secondary"
        style="opacity: 0.9"> </div>
   <bbn-big-search :source="root + '/results'"
                    :placeholder="cfg.placeholder"
                    :select-url="root + '/select'"
                    ref="search"
                    v-model="cfg.value"
                    :suggest="true"
                    @hook:mounted="registerSearch"
                    :source-value="cfg.sourceValue"
                    :source-text="cfg.sourceText"
                    :min-length="cfg.minLength"
                    @select="$nextTick(() => searchOn = false)"
                    @close="hide"
                    :limit="50"
                    :pageable="true"
                    class="bbn-no"/>
   <div class="bbn-top-right bbn-p bbn-spadded bbn-xl"
         @click="hide"
         tabindex="0">
      <i class="nf nf-fa-times"/>
   </div>
</div>
