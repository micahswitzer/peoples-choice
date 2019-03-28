<template>
<div class="pc-project uk-width-medium">
	<div class="uk-card uk-card-default uk-card-small uk-card-hover uk-card-body">
		<div v-if="isOpen" class="uk-card-badge uk-label">OPEN</div>
		<h3 class="uk-card-title">{{ title }}</h3>
	</div>
</div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';

@Component
export default class ProjectCard extends Vue {
  @Prop() private title!: string;
  @Prop() private isOpen!: boolean;
}
</script>

<style>

</style>
