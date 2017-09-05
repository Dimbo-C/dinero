<template>
    <div class="page-sidebar hidden-xs">
        <div class="sidebar-info">
            <ul class="list-unstyled">
                <li>Уровень доступа: {{ role }}</li>
                <li>Версия админки: {{ version }}</li>
                <li>Дата обновления: {{ updatedAt }}</li>
            </ul>
        </div>

        <div class="sidebar-menu">
            <main-nav v-if="windowWidth > 768" :nav-class="'nav-pills nav-stacked'"></main-nav>
        </div>
    </div>
</template>

<script>
  import MainNav from './../Nav.vue'

  export default {
    components: { MainNav },
    props: ['version', 'updated-at', 'user', 'window-width'],

    computed: {
      role() {
        return this.user ? this.user.roles[0].name : '';
//        return "Test role";
      }
    }
  };
</script>