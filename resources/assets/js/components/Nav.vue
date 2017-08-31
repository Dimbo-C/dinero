<template>
    <ul class="nav" :class="navClass">
        <li class="disabled">
            <a href="#" class="text-uppercase"><span>Управление</span></a>
        </li>
        <router-link tag="li" to="/dashboard" exact="">
            <a><i class="fa fa-home fa-btn fa-fw"></i><span>Инфо. панель</span></a>
        </router-link>

        <li>
            <a href="#sidebar-admins"
               class="collapsed"
               data-toggle="collapse"
            >
                <i class="fa fa-users fa-btn fa-fw"></i><span>Администраторы</span>
            </a>
            <ul class="nav nav-pills nav-stacked collapse"
                data-collapse="true"
                id="sidebar-admins"
            >
                <router-link tag="li" to="/admins/rent">
                    <a><span class="fa-btn fa-fw hidden-xs">R</span><span>Rent</span></a>
                </router-link>

                <router-link tag="li" to="/admins/own">
                    <a><span class="fa-btn fa-fw hidden-xs">O</span><span>Own</span></a>
                </router-link>
            </ul>
        </li>

        <router-link tag="li" to="/proxies">
            <a><i class="fa fa-shield fa-btn fa-fw"></i><span>Прокси</span></a>
        </router-link>

        <li>
            <a href="#sidebar-finance"
               class="collapsed"
               data-toggle="collapse"
            >
                <i class="fa fa-money fa-btn fa-fw"></i><span>Финансы</span>
            </a>
            <ul class="nav nav-pills nav-stacked collapse"
                data-collapse="true"
                id="sidebar-finance"
            >
                <router-link tag="li" to="/finance/rent">
                    <a><span class="fa-btn fa-fw hidden-xs">С</span><span>Счета аренды</span></a>
                </router-link>

                <router-link tag="li" to="/finance/bitcoin">
                    <a><span class="fa-btn fa-fw hidden-xs">B</span><span>Bitcoin</span></a>
                </router-link>

                <router-link tag="li" to="/finance/qiwi">
                    <a><span class="fa-btn fa-fw hidden-xs">Q</span><span>Qiwi Visa Wallet</span></a>
                </router-link>
            </ul>
        </li>

        <router-link tag="li" to="/settings" exact="">
            <a><i class="fa fa-cogs fa-btn fa-fw"></i><span>Настройки системы</span></a>
        </router-link>
    </ul>
</template>

<script>
  export default {
    props: ['nav-class'],
    mounted() {
      this.initCollapsed();
    },
    methods: {
      initCollapsed() {
        this.$nextTick(() => {
          const collapse = $('.sidebar-menu [data-collapse="true"]');

          // Чтобы не мигал бэкграунд при переключении
          collapse.on('show.bs.collapse', function () {
//              $(this).parent('li').find('a').addClass('active');
          });

          if ($(collapse).find('li.active').length) {
            $(collapse).addClass('in').parent('li').find('a').removeClass('collapsed').addClass('active')
          } else {
            $(collapse).parent('li').find('a').removeClass('collapsed').removeClass('active')
          }
        });
      }
    },
  }
</script>
