<template>
  <div class="pc-vote-dialog">
    <div ref="modal" id="modal-vote-dialog" class="uk-flex-top" uk-modal>
      <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Write-ins for '{{project == null ? '' : project.name}}'</h2>
        <div class="uk-form-horizontal">
          <div class="uk-margin-small">
            <label class="uk-form-label">Team:</label>
            <div class="uk-form-controls">
              <select class="uk-select" v-model="newWritein.team_id">
                <option
                  v-for="(teamMembers, teamId) in teamsForWritein(teams)"
                  :name="teamId"
                  :value="teamId"
                  :key="teamId"
                >{{ getTeamName(teamId, ' ') }}</option>
              </select>
            </div>
          </div>
          <div class="uk-margin-small">
            <label class="uk-form-label">Message:</label>
            <div class="uk-form-controls">
              <textarea type="textarea" rows="2" class="uk-textarea" v-model="newWritein.message"></textarea>
            </div>
          </div>
          <button class="uk-button uk-button-primary" @click="submitWritein">Submit</button>
        </div>
        <ul class="uk-list uk-list-striped">
          <li v-for="(writeIn, index) in writeIns" :key="writeIn.id">
            <div uk-grid>
              <div class="uk-width-auto uk-text-middle">
                <span v-html="getTeamName(writeIn.team_id,'<br>')"></span>
              </div>
              <div class="uk-width-expand">
                <span><em>{{ writeIn.message }}</em></span>
              </div>
              <div class="uk-width-auto">
                <a href="" uk-icon="icon: trash" @click.prevent="deleteWritein(writeIn.id, index)"></a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import axios from 'axios';
import { Component, Prop, Vue, Watch, Inject } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, UserList, SystemUser, WriteIn } from '../models/DataModels';
import App from '../App.vue';

@Component
export default class WriteInDialog extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: UserList;
  @Inject() private readonly sysUser!: SystemUser;
  private teams?: TeamList = {};
  private writeIns: WriteIn[] = [];
  private newWritein: WriteIn = {
    id: '',
    team_id: '',
    message: '',
  };
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
    axios.get<WriteIn[]>(UrlRoot + 'write-in.php?project=' + value.id)
      .then((response) => this.writeIns = response.data);
    axios.get<TeamList>(UrlRoot + 'teams.php?project=' + value.id)
      .then((response) => this.teams = response.data);
  }
  private getTeamName(teamId: string, separator: string): string {
    if (!this.teams || !this.users) {
      return '';
    }
    let name: string = '';
    this.teams[teamId].forEach((userId: string) => {
      if (!this.users.hasOwnProperty(userId)) return;
      name += (`${this.users[userId].first_name} ${this.users[userId].last_name}${separator}`);
    });
    return name;
  }
  private teamsForWritein(teams: TeamList): TeamList {
    return teams;
  }
  private submitWritein(): void {
    const params = new URLSearchParams();
    params.append('team_id', this.newWritein.team_id);
    params.append('message', this.newWritein.message);
    axios.post<{writein_id: string}>(UrlRoot + 'write-in.php', params)
      .then((response) => {
        this.newWritein.id = response.data.writein_id;
        this.writeIns.push(this.newWritein);
        this.newWritein = {
          id: '',
          team_id: '',
          message: '',
        };
      });
  }
  private deleteWritein(id: string, index: number): void {
    axios.delete(UrlRoot + 'write-in.php?id=' + id)
    .then((response) => this.writeIns.splice(index, 1));
  }
}
</script>
