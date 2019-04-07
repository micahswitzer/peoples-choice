<template>
  <div class="pc-vote-dialog">
    <div ref="modal" id="modal-vote-dialog" class="uk-flex-top" uk-modal>
      <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-form-horizontal">
          <div
            class="uk-margin-small"
            v-for="(medalName, medalId) in medalTypes"
            :key="medalId"
          >
            <label class="uk-form-label" :key="medalId" :for="medalName">{{medalName}}</label>
            <div class="uk-form-controls">
              <select class="uk-select" v-model="medals[medalId]" :key="medalId">
                <option
                  v-for="(teamMembers, teamId) in teamsForMedal(teams, medals, medalId)"
                  :name="medalName"
                  :value="teamId"
                  :key="teamId"
                >{{ getTeamName(teamId) }}</option>
              </select>
            </div>
          </div>
        </div>
        <button class="uk-button uk-button-primary" @click="submitVote">Vote</button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">

import axios from 'axios';
import { Component, Prop, Vue, Watch, Inject } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, ProjectVotes, User, MedalList, UserList, SystemUser } from '../models/DataModels';
import App from '../App.vue';

@Component
export default class VoteDialog extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: UserList;
  @Inject() private readonly sysUser!: SystemUser;
  private readonly medalTypes: {[key: string]: string} = {'3': 'Gold', '2': 'Silver', '1': 'Bronze'};
  private teams?: TeamList = {};
  private voteResults?: ProjectVotes;
  private medals: {[key: string]: string | null} = {'3': null, '2': null, '1': null};
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
  @Watch('project')
  private fetchData(value: Project | null, oldValue: Project): void {
    if (typeof(value) === 'undefined' || value === null) {
      return;
    }
    if (value === oldValue) {
      return;
    }
    axios.get<TeamList>(UrlRoot + 'teams.php?project=' + value.id)
      .then((response) => this.teams = response.data);
    axios.get<{[key: string]: string | null}>(UrlRoot + 'scores.php?project=' + value.id)
      .then((response) => this.medals = response.data);
      //.then(() =>
      //  axios.get<ProjectVotes>(UrlRoot + 'vote-results.php?project=' + value.id)
      //  .then((response) => {vm.voteResults = response.data; vm.updateChart(response.data, undefined); }));
  }
  private getTeamName(teamId: string): string {
    if (!this.teams || !this.users) {
      return '';
    }
    let name: string = '';
    this.teams[teamId].forEach((userId: string) => {
      if (!this.users.hasOwnProperty(userId)) return;
      name += (`${this.users[userId].first_name} ${this.users[userId].last_name}\n`);
    });
    return name;
  }
  private teamsForMedal(teams: TeamList, medals: {[key: string]: string}, medalId: string) {
    const newTeams: TeamList = {};
    for (let teamId in teams) {
      if (!teams.hasOwnProperty(teamId)) continue;
      let cont = false;
      for (let medalIdIter in medals) {
        if (!medals.hasOwnProperty(medalIdIter)) continue;
        if (medalId === medalIdIter) continue;
        if (medals[medalIdIter] == teamId) {
          cont = true;
          break;
        }
      }
      if (cont) continue;
      if (this.sysUser &&
        this.sysUser.id !== null &&
        teams[teamId].indexOf(this.sysUser.id) != -1)
        continue;
      newTeams[teamId] = teams[teamId];
    }
    return newTeams;
  }
  private submitVote(): void {
    axios.post(UrlRoot + 'scores.php', this.medals)
      .then((response) => {
        this.$emit('update:visible', false);
        window.UIkit.notification('Vote Submitted');
      });
  }
}
</script>
