<div :class="[componentClass, 'bbn-w-100', 'bbn-spadding', 'bbn-p']"
     :style="{
       color: source.fcolor || null,
       backgroundColor: source.bcolor || null
     }">
  <div class="bbn-flex-width">
    <div>
      <slot name="image"/>
    </div>
    <div class="bbn-flex-fill">
      <span class="bbn-s bbn-badge bbn-secondary"
            bbn-text="source.score"
            bbn-if="score"/>
      <span class="bbn-lg">
        <slot name="title"/>
      </span>
      <br>
      <span>
        <slot name="content"/>
      </span>
    </div>
    <div class="bbn-h-100 bbn-r"
         style="vertical-align: middle"
         bbn-html="source.match">
    </div>
  </div>
</div>
