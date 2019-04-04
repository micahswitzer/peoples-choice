<template>
  <div id="app">
    <Header/>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line"><span>Projects</span></h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="project in projects">
            <ProjectCard v-bind:project="project" v-bind:key="project.id"/>
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
import { Project, User } from './models/DataModels';
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
  private urlRoot: string = 'http://judah.cedarville.edu/~switzer/projects/5/';

  public $mount(): any {
    axios.get<Project[]>(this.urlRoot + 'projects.php?section=1')
      .then((response) => this.projects = response.data);
    return super.$mount();
  }
}
</script>

<style>
</style>
