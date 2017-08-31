<template>
    <div>
        <page-header icon="fa-money" title="Панель управления">
            <li>
                <a class="disabled">Финансы</a>
            </li>
            <router-link tag="li" to="/finance/qiwi">
                <a>Qiwi Visa Wallet</a>
            </router-link>
        </page-header>

        <loading :show="!walletsIsLoaded"></loading>

        <div v-if="walletsIsLoaded" class="container-fluid">

            <div class="m-b-lg">
                <router-link to="/finance/qiwi/add-wallet" class="btn btn-success">
                    <i class="fa fa-plus-square fa-btn"></i>Добавить кошелёк
                </router-link>
            </div>

            <div class="input-group m-b-lg">
                <input type="text" placeholder="" class="form-control" v-model="searchQuery">
                <span class="input-group-btn">
                <button type="button" class="btn btn-default">Поиск</button>
            </span>
            </div>

            <qiwi-type-panel v-for="type in walletsTypes"
                             :key="type.id"
                             :type="type"
                             :types="walletsTypes"
                             @moveWallets="moveWallets"
            ></qiwi-type-panel>

            <qiwi-type-panel :type="inactive"
                             :types="walletsTypes"
                             :exclude="'spent'"
                             :is-inactive="true"
                             @moveWallets="moveWallets"
            ></qiwi-type-panel>

        </div>
    </div>
</template>

<script>
  import QiwiTypePanel from './qiwi/QiwiTypePanel.vue';

  export default {
    components: { QiwiTypePanel },
    mounted() {
      this.fetchWallets();
    },
    data() {
      return {
        searchQuery: '',
        walletsIsLoaded: false,
        walletsTypes: null,
      };
    },
    watch: {
      filter () {

      }
    },
    methods: {
      fetchWallets () {
        axios.get('/api/qiwi-wallets')
          .then((response) => {
            this.walletsTypes = response.data;
            this.walletsIsLoaded = true;

            Bus.$emit('initTooltip');
          })
      },
      moveWallets (wallets, fromId, toId) {
        axios.post('/api/qiwi-wallets/move', { wallets, to: toId })
          .then(() => {
            let moveTo = this.walletsTypes.find(type => type.id === toId);

            let moveFrom = this.walletsTypes.find(type => type.id === fromId);

            moveFrom.wallets = moveFrom.wallets.filter((w) => {
              return !wallets.find(item => item.id === w.id);
            });

            wallets.forEach((w) => {
              w.is_active = 1;
              moveTo.wallets.push(w)
            });

          });
      },

      removeFromType (wallets, fromId) {

      }
    },
    computed: {
      inactive () {
        let inactive = {  name: 'Неактивные киви', wallets: [], selected: [] };

        this.walletsTypes.forEach((type) => {
          type.wallets.forEach((w) => {
            if (!w.is_active) {
              inactive.wallets.push(w);
            }
          });
        });

        return inactive;
      },

//      filteredData () {
//        let filterKey = this.searchQuery && this.searchQuery.toLowerCase();
//        let data = this.walletsTypes;
//        if (filterKey) {
//          data.forEach((t) => {
//            t.wallets = t.wallets.filter((row) => {
//              return Object.keys(row).some((key) => {
//                return String(row[key]).toLowerCase().indexOf(filterKey) > -1
//              })
//            })
//          });
//        }
//
//        return data
//      }
    },
  };
</script>