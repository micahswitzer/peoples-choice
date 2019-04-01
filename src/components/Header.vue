<template>
    <div class="uk-navbar-container" uk-navbar="mode: click">
        <div class="uk-navbar-left">
            <div class="uk-navbar-item uk-logo">
                People's Choice Awards (aka&nbsp;<a href="https://google.com" target="_blank">Google</a>)
            </div>
        </div>
        <div class="uk-navbar-center">
            <ul class="uk-navbar-nav">
                <li>
                    <a href="#">Section 1</a>
                    <div class="uk-navbar-dropdown">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li class="uk-active"><a href="#">Section 1</a></li>
                            <li><a href="#">Section 2</a></li>
                            <li><a href="#">Section 3</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="uk-navbar-right">
            <ul class="uk-navbar-nav">
                <li>
                    <a href="#">Micah Switzer</a>
                    <div class="uk-navbar-dropdown">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li class="uk-active"><a href="#">Active</a></li>
                            <li><a href="#">Item</a></li>
                            <li><a href="#">Item</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';

@Component
export default class Header extends Vue {
  @Prop() private title!: string;
}
</script>

<style scoped>

</style>
