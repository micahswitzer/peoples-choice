<template>
  <div id="app">
    <Header/>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line"><span>Projects</span></h2>
        <div class="uk-grid-small" uk-grid>
          <ProjectCard title="Project 1"/>
          <ProjectCard title="Project 2" isOpen="true"/>
          <ProjectCard title="Project 3"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import Header from './components/Header.vue';
import ProjectCard from './components/ProjectCard.vue';

@Component({
  components: {
    Header,
    ProjectCard,
  },
})
export default class App extends Vue {

}
</script>

<style>
</style>
