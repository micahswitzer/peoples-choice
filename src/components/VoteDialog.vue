<template>
    <div class="pc-vote-results">
        <div ref="modal" id="modal-vote-results" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                <div id="vote-results-chart" class="uk-background-cover" uk-grid>
                  <canvas ref="chart"/>
                </div>
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
import { warn } from 'vue-class-component/lib/util';

@Component
export default class VoteDialog extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: {[key: string]: User};
  private teams?: TeamList;
  private voteResults?: ProjectVotes;
}
</script>
