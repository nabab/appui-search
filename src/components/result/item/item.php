<div :class="[componentClass, 'bbn-w-100', 'bbn-spadded', 'bbn-p', 'bbn-default-alt-background']"
     :style="{
       color: source.fcolor || null,
       backgroundColor: source.bcolor || null
     }">
  <div class="bbn-flex-width">
    <div class="bbn-flex-fill">
      <span class="bbn-s bbn-badge bbn-bg-blue"
            v-text="source.score"/>
      <span class="bbn-lg">
        <slot name="title"/>
      </span><br>
      <span>
        <slot name="content"/>
      </span>
    </div>
    <div class="bbn-h-100 bbn-r"
         style="vertical-align: middle"
         v-html="source.match">
    </div>
  </div>
</div>
