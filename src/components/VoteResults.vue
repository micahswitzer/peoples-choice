<template>
    <div class="pc-vote-results">
        <div ref="modal" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                <div uk-height-viewport uk-grid>
                  <div class="uk-width-3-4">
                    <canvas ref="chart"/>
                  </div>
                  <div class="uk-width-1-4">
                    <h3>Write-ins</h3>
                    <ul class="uk-list uk-list-striped uk-margin-large-right">
                      <li v-for="writeIn in writeIns" :key="writeIn.id">
                        <div uk-grid>
                          <div class="uk-width-auto uk-text-middle">
                            <span v-html="getTeamNameNewline(writeIn.team_id)"></span>
                          </div>
                          <div class="uk-width-expand">
                            <span><em>{{ writeIn.message }}</em></span>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">

import axios from 'axios';
import { Component, Prop, Vue, Watch } from 'vue-property-decorator';
import { Chart } from 'chart.js';
import { Project, TeamList, UrlRoot, ProjectVotes, User, WriteIn } from '../models/DataModels';

@Component
export default class VoteResults extends Vue {
  @Prop() private visible: boolean = false;
  @Prop() private project!: Project | null;
  @Prop() private users!: {[key: string]: User};
  private teams?: TeamList;
  private voteResults?: ProjectVotes;
  private chart?: Chart;
  private writeIns: WriteIn[] = [];
  private intervalToken?: number;
  public mounted(): any {
    const vm = this;
    window.UIkit.util.on(this.$refs.modal, 'hide', () => this.$emit('update:visible', false));
    window.UIkit.util.on(this.$refs.modal, 'show', () => this.$emit('update:visible', true));
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
        maintainAspectRatio: false,
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
      if (this.project && this.project.isOpen === 1) {
        this.intervalToken = window.setInterval(() => {
          this.fetchData(this.project, null);
        }, 5000);
      }
    } else {
      window.UIkit.modal(this.$refs.modal).hide();
      if (this.intervalToken) {
        window.clearInterval(this.intervalToken);
        this.intervalToken = undefined;
      }
    }
  }
  @Watch('project')
  private fetchData(value: Project | null, oldValue: Project | null): void {
    if (typeof(value) === 'undefined' || value === null) {
      return;
    }
    axios.get<TeamList>(UrlRoot + 'teams.php?project=' + value.id)
      .then((response) => this.teams = response.data)
      .then(() =>
        axios.get<ProjectVotes>(UrlRoot + 'vote-results.php?project=' + value.id)
        .then((response) => {this.voteResults = response.data; this.updateChart(response.data, undefined); }));
    axios.get<WriteIn[]>(UrlRoot + 'write-in.php?project=' + value.id)
      .then((response) => this.writeIns = response.data);
  }
  private getTeamName(teamId: string): string[] {
    if (!this.teams) {
      return [];
    }
    const name: string[] = [];
    this.teams[teamId].forEach((userId: string) => {
      name.push(`${this.users[userId].first_name} ${this.users[userId].last_name}`);
    });
    return name;
  }
  private getTeamNameNewline(teamId: string): string {
    if (!this.teams) {
      return '';
    }
    let name: string = '';
    this.teams[teamId].forEach((userId: string) => {
      name += (`${this.users[userId].first_name} ${this.users[userId].last_name}<br>`);
    });
    return name;
  }
  @Watch('voteResults')
  private updateChart(value?: ProjectVotes, oldValue?: ProjectVotes): void {
    if (!this.chart ||
      !this.teams ||
      !value ||
      !this.chart.data.datasets ||
      !this.chart.config.options ||
      !this.chart.config.options.title ||
      !this.project) {
      return;
    }
    this.chart.config.options.title.text = this.project.name;
    this.chart.data.labels = [];
    this.chart.data.datasets.forEach((dataset) => {
      dataset.data = [];
    });
    for (const teamId in value) {
      if (!value.hasOwnProperty(teamId)) {
        continue;
      }
      const teamResult = value[teamId];
      this.chart.data.labels.push(this.getTeamName(teamId));
      for (let i = 0; i < 3; i++) {
        const medalValue = i + 1;
        const medal = teamResult.find((val) => val.points === medalValue.toString());
        let medalCount: number = 0;
        if (typeof(medal) !== 'undefined') {
          medalCount = parseInt(medal.count, 10);
        }
        if (!this.chart.data.datasets[i].data) {
          return;
        }
        (this.chart.data.datasets[i].data as number[]).push(medalValue * medalCount);
      }
    }
    this.chart.update();
  }
}
</script>

<style>

</style>
