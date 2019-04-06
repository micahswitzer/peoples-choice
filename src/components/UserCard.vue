<template>
<div class="pc-project uk-width-medium">
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-body">
      <h3 class="uk-card-title">
        <a :href="'http://judah.cedarville.edu/~' + user.linux_name + '/cs3220.html'" target="_blank">
          {{ user.first_name }}&nbsp;{{ user.last_name }}
        </a>
      </h3>
    </div>
    <div class="uk-card-footer" v-if="false">
      <button class="uk-button uk-button-primary uk-button-small">
        Vote
      </button>
      <button class="uk-button uk-button-default uk-button-small">
        Results
      </button>
    </div>
  </div>
</div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { User } from '../models/DataModels';

@Component
export default class ProjectCard extends Vue {
  @Prop() private user!: User;
}
</script>