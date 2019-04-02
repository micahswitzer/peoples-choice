<template>
<div class="pc-project uk-width-medium">
  <div class="uk-card uk-card-default uk-card-small uk-card-hover uk-card-body">
    <div v-if="project.status_open == '1'" class="uk-card-badge uk-label">OPEN</div>
    <h3 class="uk-card-title">{{ project.name }}</h3>
    <MedalItem :medal="{value: 3}"/>
    <MedalItem :medal="{value: 2}"/>
    <MedalItem :medal="{value: 1}"/>
  </div>
</div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { Project } from '../models/DataModels';
import MedalItem from '../components/MedalItem.vue';

@Component({
  components: {
    MedalItem,
  },
})
export default class ProjectCard extends Vue {
  @Prop() private project!: Project;
}
</script>

<style>

</style>
