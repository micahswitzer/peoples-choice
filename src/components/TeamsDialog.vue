<template>
  <div class="pc-teams-dialog">
    <div ref="modal" class="uk-flex-top uk-modal-container" uk-modal>
      <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Assign Teams for '{{project == null ? '' : project.name}}'</h2>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-1-5">
            <h3>Actions</h3>
            <a href="" class="uk-button uk-button-text" @click.prevent="createSingleTeams">One User per Team</a>
            <br>
            <a href="" class="uk-button uk-button-text uk-margin-top-small" @click.prevent="addTeam">Add Team</a>
            <br>
            <a href="" class="uk-button uk-button-text uk-margin-top-small" @click.prevent="saveTeams">Save</a>
            <h3 class="uk-margin-top">Unassigned</h3>
            <ul class="uk-list uk-list-striped">
              <draggable :list="unassigned" group="users" class="uk-list uk-list-striped">
                <li v-for="userId in unassigned" :key="userId">
                  {{ users[userId].first_name }} {{ users[userId].last_name }}
                </li>
              </draggable>
            </ul>
          </div>
          <div class="uk-width-4-5">
            <div class="uk-grid-small" uk-grid>
              <div class="uk-width-1-4" v-for="(team, idx) in teams" :key="idx">
                <div class="uk-card uk-card-small uk-card-default uk-card-body">
                  <h3 class="uk-card-title">Team {{ idx }}</h3>
                  <ul class="uk-list uk-list-striped">
                    <draggable :list="team" group="users" class="uk-list uk-list-striped">
                      <li v-for="userId in team" :key="userId">
                        {{ users[userId].first_name }} {{ users[userId].last_name }}
                      </li>
                    </draggable>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import axios from 'axios';
import draggable from 'vuedraggable';
import { Component, Prop, Vue, Watch, Inject } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, UserList, SystemUser } from '../models/DataModels';
import App from '../App.vue';

@Component({
  components: {
    draggable,
  },
})
export default class TeamsDialog extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: UserList;
  @Inject() private readonly sysUser!: SystemUser;
  private teams?: TeamList = {'1': []};
  private unassigned: string[] = [];
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
      .then((response) => {
        this.teams = response.data;
        this.computeUnassigned();
      });
  }
  private createSingleTeams(): void {
    const newTeams: TeamList = {'1': []};
    let count: number = 1;
    for (const userId in this.users) {
      if (!this.users.hasOwnProperty(userId)) {
        continue;
      }
      newTeams[(count++).toString()] = [userId.toString()];
    }
    this.unassigned = [];
    this.teams = newTeams;
  }
  private saveTeams(): void {
    if (!this.project) {
      return;
    }
    axios.post(UrlRoot + 'teams.php?project=' + this.project.id, this.teams)
      .then((response) => this.$emit('update:visible', false));
  }
  private computeUnassigned(): void {
    const users: string[] = [];
    for (const userId in this.users) {
      if (!this.users.hasOwnProperty(userId)) {
        continue;
      }
      users.push(userId);
    }
    for (const teamId in this.teams) {
      if (!this.teams.hasOwnProperty(teamId)) {
        continue;
      }
      this.teams[teamId].forEach((val) => {
        const idx = users.indexOf(val.toString());
        if (idx >= 0) {
          users.splice(idx, 1);
        }
      });
    }
    this.unassigned = users;
  }
  private addTeam(): void {
    let lastIdx: number = 0;
    let hasIter: boolean = false;
    for (const teamIdx in this.teams) {
      if (!this.teams.hasOwnProperty(teamIdx)) {
        continue;
      }
      hasIter = true;
      lastIdx = parseInt(teamIdx, 10);
    }
    if (!hasIter) {
      this.teams = {'1': []};
    }
    if (!this.teams) {
      return;
    }
    this.$set(this.teams, (lastIdx + 1).toString(), [] as string[]);
  }
}
</script>
