<template>
  <div class="pc-project uk-width-medium">
    <div
      v-bind:class="{'uk-card-default': project.isOpen != '1', 'uk-card-primary': project.isOpen == '1'}"
      class="uk-card uk-card-small uk-card-hover"
    >
      <div class="uk-card-body">
        <h3 class="uk-card-title">{{ project.name }}</h3>
        <template v-for="(medal, idx) in medals[project.id]">
          <MedalItem
            v-if="project.isOpen != '1'"
            :key="idx"
            :value="3 - idx"
            :teamName="getTeamName(medal.team_id)"
          />
        </template>
      </div>
      <div
        class="uk-card-footer"
        v-if="projectHasMedals() || project.isOpen == '1' || sysUser.is_admin"
      >
        <div class="uk-grid-small uk-child-width-auto" uk-grid>
          <div v-if="project.isOpen == '1' && sysUser.is_student">
            <button
              class="uk-button uk-button-primary uk-button-small"
              v-on:click="$emit('vote-clicked', project)"
            >Vote</button>
          </div>
          <div v-if="project.isOpen == '1' && sysUser.is_student">
            <button
              class="uk-button uk-button-default uk-button-small"
              v-on:click="$emit('write-ins-clicked', project)"
            >Write-ins</button>
          </div>
          <div v-if="projectHasMedals() || project.isOpen == '1'">
            <button
              class="uk-button uk-button-default uk-button-small"
              v-on:click="$emit('results-clicked', project)"
            >Results</button>
          </div>
          <div v-if="project.isOpen != '1' && sysUser.is_admin">
            <button
              class="uk-button uk-button-primary uk-button-small"
              v-on:click="setOpen(true)"
            >Open</button>
          </div>
          <div v-if="project.isOpen == '1' && sysUser.is_admin">
            <button
              class="uk-button uk-button-default uk-button-small"
              v-on:click="setOpen(false)"
            >Close</button>
          </div>
          <div v-if="sysUser.is_admin">
            <button
              class="uk-button uk-button-danger uk-button-small"
              v-on:click="deleteProject"
            >Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Inject } from 'vue-property-decorator';
import {
  Project,
  TeamList,
  UrlRoot,
  MedalList,
  User,
  SystemUser,
} from '../models/DataModels';
import MedalItem from '../components/MedalItem.vue';
import axios from 'axios';
import App from '@/App.vue';

@Component({
  components: {
    MedalItem,
  },
})
export default class ProjectCard extends Vue {
  public teams: TeamList | null = null;
  @Prop() private project!: Project;
  @Prop() private medals!: MedalList;
  @Prop() private users!: { [key: string]: User };
  @Inject() private readonly sysUser!: SystemUser;
  @Inject() private readonly removeProject!: (project: Project) => void;
  public created(): any {
    axios
      .get<TeamList>(UrlRoot + 'teams.php?project=' + this.project.id)
      .then((response) => (this.teams = response.data));
  }
  private getTeamName(teamId: string): string {
    if (!this.teams) {
      return '';
    }
    let name = '';
    this.teams[teamId].forEach((userId: string) => {
      name += `${this.users[userId].first_name} ${
        this.users[userId].last_name
      }<br>`;
    });
    return name;
  }
  private projectHasMedals(): boolean {
    return (
      this.medals &&
      this.medals.hasOwnProperty(this.project.id) &&
      this.medals[this.project.id].length > 0
    );
  }
  private setOpen(state: boolean): void {
    const openString = state ? 1 : 0;
    const params = new URLSearchParams();
    params.append('open', openString.toString());
    axios.put(UrlRoot + 'projects.php?project=' + this.project.id, params)
      .then((response) => {
        this.project.isOpen = openString;
      });
  }
  private deleteProject(): void {
    window.UIkit.modal.confirm(`Are you sure you want to delete project '${this.project.name}'?` +
      '<br>This action cannot be undone.')
      .then(() => axios.delete(UrlRoot + 'projects.php?project=' + this.project.id)
        .then((response) => this.removeProject(this.project)));
  }
}
</script>

<style>
.uk-button:not(:first-child) {
  margin-left: 8px;
}
</style>
