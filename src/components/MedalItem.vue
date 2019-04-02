<template>
<div class="pc-medal">
	<span uk-icon="icon: star"
    v-bind:class="{'medal-gold': medal.value == 3, 'medal-silver': medal.value == 2, 'medal-bronze': medal.value == 1}">
  </span>&nbsp;
  <span>{{ personName }}</span>
</div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { Medal } from '../models/DataModels';

@Component
export default class MedalItem extends Vue {
  @Prop() private medal!: Medal;
  @Prop() private personName!: string;
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
