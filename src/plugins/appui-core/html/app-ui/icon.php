<div class="bbn-appui-search bbn-vmiddle bbn-h-100 bbn-hpadding">
   <div class="bbn-block bbn-p"
      @mouseup="onMouseUp"
      @mousedown="onMouseDown"
      tabindex="0">
      <i ref="icon"
         :class="'bbn-xxl nf nf-' + (isMicrophone ? 'md-microphone' : (isLoading ? 'fa-warning' : 'oct-search'))"/>
   </div>
   <audio ref="audioPlayer"
          bbn-if="aiPlugin"/>
</div>
