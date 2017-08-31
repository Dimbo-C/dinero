<template>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Toggle Sidebar -->
                <div class="sidebar-toggle-wrapper hidden-xs" v-if="user">
                    <!-- Branding Image -->
                    <router-link :to="user ? '/dashboard' : '/'" class="navbar-brand">Dinero</router-link>


                    <button type="button" class="sidebar-toggle hidden-xs" @click="toggleSidebar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <!-- Branding Image -->
                <router-link :to="user ? '/dashboard' : '/'" class="navbar-brand">Dinero</router-link>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <main-nav v-if="windowWidth <= 768" :nav-class="'navbar-nav'"></main-nav>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right" v-if="user">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            1 BTC = 0 RUB
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">1 BTC = 2 775.14 USD</a>
                            </li>
                            <li>
                                <a href="#">1 USD = 59.86 RUB</a>
                            </li>
                            <li>
                                <a href="#">1 ETH = 201.5 USD</a>
                            </li>
                            <li>
                                <a href="#">1 ETH = 12 125.3 RUB</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span v-text="user.name"></span> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#"><i class="fa fa-cog fa-btn fa-fw"></i>Настройки</a>
                            </li>
                            <li>
                                <a href="/logout"><i class="fa fa-sign-out fa-btn fa-fw"></i>Выход</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
  import MainNav from './Nav.vue'

  export default {
    components: { MainNav },
      props: ['user', 'window-width'],

      methods: {
        toggleSidebar() {
          Bus.$emit('toggleSidebar');
        },
      },
    };
</script>