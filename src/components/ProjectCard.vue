<template>
	<div class="pc-project">
		<span>{{ title }}</span>
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
