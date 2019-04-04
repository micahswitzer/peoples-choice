<template>
  <div id="app">
    <Header/>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line"><span>Projects</span></h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="project in projects">
            <ProjectCard
              v-bind:project="project" 
              v-bind:key="project.id"
              v-bind:medals="medals"
              v-bind:users="users"/>
          </template>
        </div>
      </div>
    </div>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line"><span>Users</span></h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="user in users">
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import Header from './components/Header.vue';
import ProjectCard from './components/ProjectCard.vue';
import { Project, User, UrlRoot, MedalList } from './models/DataModels';
import axios from 'axios';

@Component({
  components: {
    Header,
    ProjectCard,
  },
})
export default class App extends Vue {
  public projects: Project[] = [];
  public users: User[] = [];
  public medals: MedalList = {};

  public created(): any {
    axios.get<Project[]>(UrlRoot + 'projects.php?section=1')
      .then((response) => this.projects = response.data);
    axios.get<User[]>(UrlRoot + 'users.php?section=1')
      .then((response) => this.users = response.data);
    axios.get<MedalList>(UrlRoot + 'medals.php?section=1')
      .then((response) => this.medals = response.data);
  }
}
</script>

<style>
</style>
