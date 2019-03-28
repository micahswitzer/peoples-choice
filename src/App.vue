<template>
  <div id="app">
    <Header/>
    <ProjectCard title="Project 1"/>
    <ProjectCard title="Project 2"/>
    <ProjectCard title="Project 3"/>
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
