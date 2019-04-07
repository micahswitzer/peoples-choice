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
    <div class="uk-card-footer" v-if="sysUser.is_admin == 1">
      <button class="uk-button uk-button-danger uk-button-small" @click="deleteUser">
        Delete
      </button>
      <button class="uk-button uk-button-primary uk-button-small" @click="resetUser">
        Reset Pass
      </button>
    </div>
  </div>
</div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Inject } from 'vue-property-decorator';
import { User, SystemUser, UrlRoot } from '../models/DataModels';
import axios from 'axios';

@Component
export default class UserCard extends Vue {
  @Prop() private userId!: string;
  @Prop() private user!: User;
  @Inject() private sysUser!: SystemUser;
  @Inject() private removeUser!: (userId: string) => void;
  private deleteUser(): void {
    window.UIkit.modal.confirm(`Are you sure you want to delete user '${this.user.first_name} ${this.user.last_name}'?<br>This action cannot be undone.`)
      .then(() => axios.delete(UrlRoot + 'users.php?id=' + this.userId).then((response) => this.removeUser(this.userId)));
  }
  private resetUser(): void  {
    const params = new URLSearchParams();
    params.append('pass', 'password');
    axios.put(UrlRoot + 'users.php?id=' + this.userId, params).then(() => alert('Password rest'));
  }
}
</script>