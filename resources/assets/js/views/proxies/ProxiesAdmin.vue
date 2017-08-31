<template>
    <div>
        <page-header icon="fa-sitemap" title="PROXY на продажу">
            <router-link tag="li" to="/proxies">
                <a>Управление PROXY</a>
            </router-link>
        </page-header>

        <loading :show="loading"></loading>

        <div v-if="proxies" class="container-fluid">
            <div class="m-b-lg">
                <button class="btn btn-success" @click="showModal('modal-add-proxy-admin')">
                    <i class="fa fa-plus-square fa-btn"></i>Добавить
                </button>
            </div>

            <div class="input-group m-b-lg">
                <input type="text" class="form-control" placeholder="">
                <span class="input-group-btn">
                <button class="btn btn-default" type="button">Поиск</button>
            </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="20">
                            <div class="checkbox m-none">
                                <label class="p-t-xs">
                                    <input type="checkbox" v-model="selectAll">
                                </label>
                            </div>
                        </th>
                        <th>IP:Port - Тип PROXY</th>
                        <th>Login:Password</th>
                        <th>Страна</th>
                        <th>Статус</th>
                        <th>Обновлен статус</th>
                        <th>Добавлен</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="proxy in proxies">
                        <td>
                            <div class="checkbox m-none">
                                <label class="p-t-xs">
                                    <input type="checkbox" v-model="selected" :value="proxy">
                                </label>
                            </div>
                        </td>
                        <td>{{ proxy.host }}{{ proxy.port ? ':' + proxy.port : '' }}</td>
                        <td v-text="proxy.login ? proxy.login  + ':' + proxy.password : ''"></td>
                        <td v-text="proxy.country"></td>
                        <td>
                            <i v-if="proxy.status === 'check'" class="fa fa-circle-o-notch fa-spin"></i>
                            <i v-else-if="proxy.status === 'ok'" class="fa fa-circle text-success"></i>
                            <i class="fa fa-circle text-danger" v-else></i>
                        </td>
                        <td v-text="proxy.updated_at"></td>
                        <td v-text="proxy.created_at"></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="btn-group">
                <i class="fa fa-btn fa-hand-o-up m-l-sm hidden-xs"></i> С отмеченными:
            </div>
            <div class="btn-group">
                <a href="#" class="btn btn-primary" @click="check">Перепроверить</a>
            </div>
            <div class="btn-group">
                <a href="#" class="btn btn-danger">Удалить</a>
            </div>
        </div>

        <add-proxy-admin-modal @add-proxies="addProxies"></add-proxy-admin-modal>
    </div>
</template>

<script>
  import Table from './../../mixins/table';
  import AddProxyAdminModal from './AddProxyAdminModal.vue';

  export default {
    mixins: [Table],
    components: {
      AddProxyAdminModal,
    },
    data() {
      return {
        loading: false,
        proxies: null,
      };
    },
    watch: {
      proxies (val) {
        this.initTable(val);
      }
    },
    mounted () {
      this.fetchProxies();
    },
    methods: {
      fetchProxies () {
        this.loading = true;
        axios.get('/api/proxies', { params: { type: 'admin' } })
          .then((response) => {
            this.proxies = response.data;
            this.loading = false;
          });
      },
      showModal (id) {
        $(`#${id}`).modal('show');
      },
      addProxies (proxies) {
        proxies.forEach((p) => {
          this.proxies.push(p);

          $('#modal-add-proxy-admin').modal('hide');

          setTimeout(() => {
            Bus.$emit('showNotification', 'success', `Прокси успешно добавлен${proxies.length > 1 ? 'ы' : ''}`);
          }, 300);
        });
      },
      check() {
        this.selected.forEach((id) => {
          const proxy = this.proxies.find(proxy => proxy.id === id);

          const proxy_data = {
            host: proxy.host,
            port: proxy.port,
            auth: {
              username: proxy.login,
              password: proxy.password
            },
          };

          proxy.status = 'check';
          axios.get('http://google.com', { timeout: 5000, proxy: proxy_data })
            .then((response) => {
              proxy.status = 'ok';
            })
            .catch((error) => {
              proxy.status = 'dead';
            })
        });
      },
    },
  };
</script>
