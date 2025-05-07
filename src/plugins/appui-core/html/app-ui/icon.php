<div class="bbn-appui-search bbn-hpadding">
   <div class="bbn-block bbn-p"
      @mouseup="onMouseUp"
      @mousedown="onMouseDown"
      tabindex="0">
      <i ref="icon"
         :class="(isMobile ? 'bbn-lg' : 'bbn-xxl') + ' nf nf-' + (isMicrophone ? 'md-microphone' : (isLoading ? 'fa-warning' : 'oct-search'))"/>
   </div>
   <audio ref="audioPlayer"
          bbn-if="aiPlugin"/>
</div>
