<template>
  <div id="app">
    <Header/>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line"><span>Projects</span></h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="project in projects">
            <ProjectCard
              v-bind:project="project" 
              v-bind:key="project.id"
              v-bind:medals="medals"
              v-bind:users="users"
              v-on:results-clicked="showResults"
              v-on:vote-clicked="showVote"/>
          </template>
        </div>
      </div>
    </div>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line"><span>Users</span></h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="(user, id) in users">
            <UserCard :key="id" :user="user"/>
          </template>
        </div>
      </div>
    </div>
    <VoteResults
      :visible.sync="resultsVisible"
      :project.sync="selectedProject"
      :users="users"/>
    <VoteDialog
      :visible.sync="voteVisible"
      :project.sync="voteProject"
      :users="users"/>
  </div>
</template>

<script lang="ts">
declare global {
  interface Window {
    UIkit: {
      util: any;
      modal: any;
      notification: (message: string, data?: any) => void;
    };
  }
}

import { Component, Vue, Watch, Provide } from 'vue-property-decorator';
import Header from './components/Header.vue';
import ProjectCard from './components/ProjectCard.vue';
import VoteResults from './components/VoteResults.vue';
import VoteDialog from './components/VoteDialog.vue';
import UserCard from './components/UserCard.vue';
import { Project, User, UrlRoot, MedalList, UserList, SystemUser } from './models/DataModels';
import axios from 'axios';

@Component({
  components: {
    Header,
    ProjectCard,
    VoteResults,
    VoteDialog,
    UserCard,
  },
})
export default class App extends Vue {
  public projects: Project[] = [];
  public users: UserList = {};
  public medals: MedalList = {};
  private resultsVisible: boolean = false;
  private voteVisible: boolean = false;
  private selectedProject: Project | null = null;
  private voteProject: Project | null = null;
  @Provide()
  private sysUser: SystemUser = {
    id: null, full_name: '',
    is_student: false, is_admin: false
  };

  public created(): any {
    axios.interceptors.response.use((response) => response, (error) => {
      if (error.response.status == 401) // if the error code is unauthorized
        this.updateUserInfo(); // refresh the user's credentials
      return Promise.reject(error);
    });
    axios.defaults.withCredentials = true;
    axios.get<Project[]>(UrlRoot + 'projects.php?section=1')
      .then((response) => this.projects = response.data);
    axios.get<UserList>(UrlRoot + 'users.php?section=1')
      .then((response) => this.users = response.data);
    axios.get<MedalList>(UrlRoot + 'medals.php?section=1')
      .then((response) => this.medals = response.data);
  }
  private showResults(project: Project): void {
    this.resultsVisible = true;
    this.selectedProject = project;
  }
  private showVote(project: Project): void {
    this.voteVisible = true;
    this.voteProject = project;
  }
  @Watch('resultsVisible')
  private updateSelectedProject(value: boolean, oldvalue: boolean) {
    if (value === false) {
      this.selectedProject = null;
    }
  }
  @Watch('voteVisible')
  private updateVoteProject(value: boolean, oldvalue: boolean) {
    if (value === false) {
      this.voteProject = null;
    }
  }
  private setSysUser(userId: string | null): void {
    if (userId === null) {
      this.sysUser.id = null;
      this.sysUser.full_name = 'Anonymous';
      this.sysUser.is_student = false;
      this.sysUser.is_admin = false;
    }
    else {
      this.sysUser.id = userId;
      this.sysUser.full_name =
        `${this.users[userId].first_name} ${this.users[userId].last_name}`;
      this.sysUser.is_student = this.users[userId].is_student;
      this.sysUser.is_admin = this.users[userId].is_admin;
    }
  }
  @Watch('users')
  private updateUserInfo(): void {
    axios.get<{user_id: string | null}>(UrlRoot + 'auth.php')
      .then((response) => this.setSysUser(response.data.user_id));
  }
  @Provide()
  private doLogin(): void {
    const params = new URLSearchParams();
    params.append('username', prompt('username') || '');
    params.append('password', prompt('password') || '');
    axios.post<{user_id: string | null}>(UrlRoot + 'auth.php?action=login', params)
      .then((response) => {
        this.setSysUser(response.data.user_id);
        window.UIkit.notification(`Welcome, ${this.sysUser.full_name}!`, {status: 'success'});
      });
  }
  @Provide()
  private doLogout(): void {
    axios.post(UrlRoot + 'auth.php?action=logout')
      .then((response) => this.setSysUser(null));
  }
}
</script>

<style>
</style>
