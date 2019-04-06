<template>
    <div class="pc-vote-dialog">
        <div ref="modal" id="modal-vote-dialog" class="uk-flex-top" uk-modal>
            <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <p>This gonna be lit</p>
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
import axios from 'axios';
import { Component, Prop, Vue, Watch } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, ProjectVotes, User } from '../models/DataModels';

@Component
export default class VoteDialog extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: {[key: string]: User};
  private teams?: TeamList;
  private voteResults?: ProjectVotes;
  public mounted(): any {
    window.UIkit.util.on(this.$refs.modal, 'hide', () => this.$emit('update:visible', false));
    window.UIkit.util.on(this.$refs.modal, 'show', () => this.$emit('update:visible', true));
  }
  @Watch('visible')
  private updateVisible(value: boolean, oldValue: boolean): void {
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
