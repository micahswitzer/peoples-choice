<template>
    <div class="pc-vote-results">
        <div ref="modal" id="modal-vote-results" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                <div id="vote-results-chart" class="uk-background-cover" uk-grid/>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
declare global {
  interface Window {
    UIkit: {
      util: any;
      modal: any;
    };
  }
}
import { Component, Prop, Vue, Watch } from 'vue-property-decorator';
import { } from '../models/DataModels';

@Component
export default class VoteResults extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private projectId!: string;

  public mounted(): any {
    const vm = this;
    window.UIkit.util.on(this.$refs.modal, 'hide', () => vm.visible = false);
    window.UIkit.util.on(this.$refs.modal, 'show', () => vm.visible = true);
  }
  @Watch('visible')
  private updateVisible(value: boolean, oldValue: boolean) {
    if (typeof(value) === 'undefined') {
      return;
    }
    if (value === oldValue) {
      return;
    }
    if (value) {
      window.UIkit.modal(this.$refs.modal).show();
    } else {
      window.UIkit.modal(this.$refs.modal).hide();
    }
  }
}
</script>

<style>
#vote-results-chart {
  width: 100%;
  height: 100%;
  background-color: lightblue;
}
</style>
