<template>
    <div class="pc-vote-results">
        <div ref="modal" id="modal-vote-results" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                <div id="vote-results-chart" class="uk-background-cover" uk-grid>
                  <canvas ref="chart"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
declare global {
  interface Window {
    UIkit: {
      util: any;
      modal: any;
    };
  }
}
import axios from 'axios';
import { Component, Prop, Vue, Watch } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, ProjectVotes, User } from '../models/DataModels';
import { warn } from 'vue-class-component/lib/util';

@Component
export default class VoteResults extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project;
  @Prop() private users!: {[key: string]: User};
  private teams?: TeamList;
  private voteResults?: ProjectVotes;
  private chart?: Chart;
  public mounted(): any {
    const vm = this;
    window.UIkit.util.on(this.$refs.modal, 'hide', () => vm.visible = false);
    window.UIkit.util.on(this.$refs.modal, 'show', () => vm.visible = true);
    this.chart = new Chart(this.$refs.chart as HTMLCanvasElement, {
      type: 'horizontalBar',
      data: {
        labels: [],
        datasets: [{
          label: `Bronze`,
          backgroundColor: 'brown',
          data: [],
        }, {
          label: `Silver`,
          backgroundColor: 'silver',
          data: [],
        }, {
          label: `Gold`,
          backgroundColor: 'gold',
          data: [],
        }],
      },
      options: {
        scales: {
          xAxes: [{
            stacked: true,
          }],
          yAxes: [{
            stacked: true,
          }],
        },
        legend: {
          display: false,
        },
        title: {
          display: true,
          fontSize: 22,
        },
      },
    });
  }
  @Watch('visible')
  private updateVisible(value: boolean, oldValue: boolean): void {
    if (typeof(value) === 'undefined') {
      return;
    }
    if (value === oldValue) {
      return;
    }
    if (value) {
      window.UIkit.modal(this.$refs.modal).show();
    } else {
      window.UIkit.modal(this.$refs.modal).hide();
    }
  }
  @Watch('project')
  private fetchData(value: Project, oldValue: Project): void {
    console.log('project id changed to', value);
    if (typeof(value) === 'undefined') {
      return;
    }
    let vm = this;
    axios.get<TeamList>(UrlRoot + 'teams.php?project=' + value.id)
      .then((response) => vm.teams = response.data)
      .then(() =>
        axios.get<ProjectVotes>(UrlRoot + 'vote-results.php?project=' + value.id)
        .then((response) => {vm.voteResults = response.data; vm.updateChart(response.data, undefined); }));
  }
  private getTeamName(teamId: string): string[] {
    if (!this.teams) {
      return [];
    }
    let name: string[] = [];
    this.teams[teamId].forEach((userId: string) => {
      name.push(`${this.users[userId].first_name} ${this.users[userId].last_name}\n`);
    });
    return name;
  }
  @Watch('voteResults')
  private updateChart(value?: ProjectVotes, oldValue?: ProjectVotes): void {
    console.log('Updating chart', value);
    if (!this.chart ||
      !this.teams ||
      !value ||
      !this.chart.data.datasets ||
      !this.chart.config.options ||
      !this.chart.config.options.title) {
      return;
    }
    this.chart.config.options.title.text = this.project.name;
    this.chart.data.labels = [];
    this.chart.data.datasets.forEach((dataset) => {
      dataset.data = [];
    });
    const vm = this;
    for (const teamId in value) {
      if (!value.hasOwnProperty(teamId)) {
        continue;
      }
      const teamResult = value[teamId];

      this.chart.data.labels.push(this.getTeamName(teamId));
      teamResult.forEach((medal) => {
        if (!vm.chart || !vm.chart.data.datasets) {
          return;
        }
        const medalValue = parseInt(medal.points);
        const medalIdx = medalValue - 1;
        const medalCount = parseInt(medal.count);
        if (!vm.chart.data.datasets[medalIdx].data) {
          return;
        }
        (<number[]> vm.chart.data.datasets[medalIdx].data).push(medalValue * medalCount);
      });
    }
    this.chart.update();
  }
}
</script>

<style>

</style>
