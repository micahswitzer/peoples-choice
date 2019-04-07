<template>
  <div id="app">
    <Header/>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line">
          <span>Projects</span>
        </h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="project in projects">
            <ProjectCard
              v-bind:project="project"
              v-bind:key="project.id"
              v-bind:medals="medals"
              v-bind:users="users"
              v-on:results-clicked="showResults"
              v-on:vote-clicked="showVote"
              v-on:write-ins-clicked="showWriteins"
            />
          </template>
          <div v-if="sysUser.is_admin == 1">
            <button class="uk-button uk-button-primary" @click="createProject">New Project</button>
          </div>
        </div>
      </div>
    </div>
    <div class="uk-section uk-section-small uk-section-default">
      <div class="uk-container uk-container-expand">
        <h2 class="uk-heading-line">
          <span>Users</span>
        </h2>
        <div class="uk-grid-small" uk-grid>
          <template v-for="(user, id) in users">
            <UserCard :key="id" :user="user" :userId="id"/>
          </template>
          <div class="uk-width-medium">
            <div
              class="uk-card uk-card-default uk-card-small uk-width-medium"
              v-if="sysUser.is_admin == 1"
            >
              <div class="uk-card-header">
                <input
                  class="uk-input uk-form-blank uk-form-large uk-width-1-2"
                  type="text"
                  placeholder="First"
                  v-model="newUser.first_name"
                >
                <input
                  class="uk-input uk-form-blank uk-form-large uk-width-1-2"
                  type="text"
                  placeholder="Last"
                  v-model="newUser.last_name"
                >
              </div>
              <div class="uk-card-body">
                <input
                  class="uk-input uk-width-1-1"
                  type="text"
                  placeholder="Linux Username"
                  v-model="newUser.linux_name"
                >
                <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                  <label>
                    <input class="uk-checkbox" type="checkbox" v-model="newUser.is_admin"/> Is Admin?
                  </label>
                </div>
              </div>
              <div class="uk-card-footer">
                <button class="uk-button uk-button-primary uk-button-small" @click="createUser">Create</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <VoteResults :visible.sync="resultsVisible" :project.sync="selectedProject" :users="users"/>
    <VoteDialog :visible.sync="voteVisible" :project.sync="voteProject" :users="users"/>
    <WriteInDialog :visible.sync="writeinsVisible" :project.sync="writeinsProject" :users="users"/>
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
import WriteInDialog from './components/WriteInDialog.vue';
import {
  Project,
  User,
  UrlRoot,
  MedalList,
  UserList,
  SystemUser,
  ProjectVotes
} from './models/DataModels';
import axios from 'axios';

@Component({
  components: {
    Header,
    ProjectCard,
    VoteResults,
    VoteDialog,
    UserCard,
    WriteInDialog,
  }
})
export default class App extends Vue {
  public projects: Project[] = [];
  public users: UserList = {};
  public medals: MedalList = {};
  private resultsVisible: boolean = false;
  private voteVisible: boolean = false;
  private writeinsVisible: boolean = false;
  private selectedProject: Project | null = null;
  private voteProject: Project | null = null;
  private writeinsProject: Project | null = null;
  @Provide()
  private sysUser: SystemUser = {
    id: null,
    full_name: '',
    is_student: false,
    is_admin: false
  };
  private newUser: User = {
    first_name: '',
    last_name: '',
    linux_name: '',
    is_student: true,
    is_admin: false
  }

  public created(): any {
    axios.interceptors.response.use(
      response => response,
      error => {
        if (error.response.status == 401)
          // if the error code is unauthorized
          this.updateUserInfo(); // refresh the user's credentials
        return Promise.reject(error);
      }
    );
    axios.defaults.withCredentials = true;
    axios
      .get<Project[]>(UrlRoot + 'projects.php?section=1')
      .then(response => (this.projects = response.data));
    axios
      .get<UserList>(UrlRoot + 'users.php?section=1')
      .then(response => (this.users = response.data));
    axios
      .get<MedalList>(UrlRoot + 'medals.php?section=1')
      .then(response => (this.medals = response.data));
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
    } else {
      this.sysUser.id = userId;
      this.sysUser.full_name = `${this.users[userId].first_name} ${
        this.users[userId].last_name
      }`;
      this.sysUser.is_student = this.users[userId].is_student;
      this.sysUser.is_admin = this.users[userId].is_admin;
    }
  }
  @Watch('users')
  private updateUserInfo(): void {
    axios
      .get<{ user_id: string | null }>(UrlRoot + 'auth.php')
      .then(response => this.setSysUser(response.data.user_id));
  }
  @Provide()
  private doLogin(): void {
    const params = new URLSearchParams();
    params.append('username', prompt('username') || '');
    params.append('password', prompt('password') || '');
    axios
      .post<{ user_id: string | null }>(
        UrlRoot + 'auth.php?action=login',
        params
      )
      .then(response => {
        this.setSysUser(response.data.user_id);
        window.UIkit.notification(`Welcome, ${this.sysUser.full_name}!`, {
          status: 'success'
        });
      });
  }
  @Provide()
  private doLogout(): void {
    axios
      .post(UrlRoot + 'auth.php?action=logout')
      .then(response => this.setSysUser(null));
  }
  @Provide()
  private removeProject(project: Project): void {
    this.projects.splice(this.projects.indexOf(project), 1);
  }
  @Provide()
  private removeUser(userId: string): void {
    delete this.users[userId];
    this.$forceUpdate();
  }
  private createProject(): void {
    window.UIkit.modal.prompt('New project name:', '').then((name: string) => {
      const params = new URLSearchParams();
      params.append('name', name);
      axios
        .post<{ project_id: number }>(
          UrlRoot + 'projects.php?section=1',
          params
        )
        .then(response =>
          this.projects.push({
            id: response.data.project_id,
            name: name,
            isOpen: 0
          })
        );
    });
  }
  private createUser(): void {
      const params = new URLSearchParams();
      params.append('section_id', '1');
      params.append('first_name', this.newUser.first_name);
      params.append('last_name', this.newUser.last_name);
      params.append('linux_name', this.newUser.linux_name);
      params.append('student', this.newUser.is_student ? '1' : '0');
      params.append('admin', this.newUser.is_admin ? '1' : '0');
      axios.post<{user_id: number}>(UrlRoot + 'users.php', params)
        .then((response) => {
          this.$set(this.users, response.data.user_id, {
            first_name: this.newUser.first_name,
            last_name: this.newUser.last_name,
            linux_name: this.newUser.linux_name,
            is_student: this.newUser.is_student,
            is_admin: this.newUser.is_admin,
          });
          this.newUser.first_name = '';
          this.newUser.last_name = '';
          this.newUser.linux_name = '';
          this.newUser.is_admin = false;
        });
  }
  @Watch('writeinsVisible')
  private updateWritinsVisible(value: boolean, oldvalue: boolean): void {
    if (value === false) {
      this.writeinsProject = null;
    }
  }
  private showWriteins(project: Project): void {
    this.writeinsProject = project;
    this.writeinsVisible = true;
  }
}
</script>

<style>
</style>
