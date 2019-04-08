<template>
  <div class="pc-teams-dialog">
    <div ref="modal" class="uk-flex-top uk-modal-container" uk-modal>
      <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Assign Teams for '{{project == null ? '' : project.name}}'</h2>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import axios from 'axios';
import { Component, Prop, Vue, Watch, Inject } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, UserList, SystemUser } from '../models/DataModels';
import App from '../App.vue';

@Component
export default class WriteInDialog extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: UserList;
  @Inject() private readonly sysUser!: SystemUser;
  private teams?: TeamList = {};
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
  }
  private teamsForWritein(teams: TeamList): TeamList {
    return teams;
  }
  private saveTeams(): void {
    
  }
  private deleteTeam(id: string, index: number): void {
    axios.delete(UrlRoot + 'teams?id=' + id)
      .then((response) => {});
  }
}
</script>
