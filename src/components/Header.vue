<template>
  <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky">
    <div class="uk-navbar-container" uk-navbar>
      <div class="uk-navbar-left">
        <div class="uk-navbar-item uk-logo">
          People's Choice Awards (Now in&nbsp;
          <em style="color: red;">HD</em>!)
        </div>
      </div>
      <div class="uk-navbar-center">
        <ul class="uk-navbar-nav">
          <li>
            <a href="#">Section 1</a>
            <div class="uk-navbar-dropdown">
              <ul class="uk-nav uk-navbar-dropdown-nav">
                <li class="uk-active">
                  <a href="#">Section 1</a>
                </li>
                <li>
                  <a href="#">Section 2</a>
                </li>
                <li>
                  <a href="#">Section 3</a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
      <div class="uk-navbar-right">
        <ul class="uk-navbar-nav">
          <li>
            <a href="#">{{ sysUser.full_name }}</a>
            <div class="uk-navbar-dropdown">
              <ul class="uk-nav uk-navbar-dropdown-nav">
                <li>
                  <a href="#" v-if="sysUser.id === null" @click="doLogin">Login</a>
                </li>
                <li>
                  <a href="#" v-if="sysUser.id !== null" @click="doLogout">Logout</a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Inject } from 'vue-property-decorator';
import { SystemUser } from '../models/DataModels';

@Component
export default class Header extends Vue {
  @Prop() private title!: string;
  @Inject() private readonly sysUser!: SystemUser;
  @Inject() private doLogin!: () => void;
  @Inject() private doLogout!: () => void;
}
</script>

<style scoped>
</style>
