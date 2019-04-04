<template>
<div class="pc-medal">
	<span uk-icon="icon: star"
    v-bind:class="{'medal-gold': value == 3, 'medal-silver': value == 2, 'medal-bronze': value == 1}">
  </span>&nbsp;
  <span v-html="teamName"></span>
</div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { Medal } from '../models/DataModels';

@Component
export default class MedalItem extends Vue {
  @Prop() private value!: number;
  @Prop() private teamName!: string;
}
</script>

<style>
.medal-gold {
  color: gold;
}
.medal-silver {
  color: silver;
}
.medal-bronze {
  color: brown;
}
</style>
