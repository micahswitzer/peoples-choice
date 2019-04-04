<template>
    <div class="pc-vote-results">
        <div id="modal-vote-results" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                <div id="vote-results-chart" class="uk-background-cover" uk-grid/>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { } from '../models/DataModels';

@Component
export default class VoteResults extends Vue {
  @Prop() private project_id!: string;
}
</script>

<style>
#vote-results-chart {
    width: 100%;
    height: 100%;
    background-color: lightblue;
}
</style>
