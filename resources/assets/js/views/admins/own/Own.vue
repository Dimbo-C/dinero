<template>
    <div>
        <page-header icon="fa-users" title="Администраторы системы"></page-header>

        <loading :show="loading"></loading>

        <div v-if="admins" class="container-fluid">
            <div class="m-b-lg">
                <div class="btn-group p-b-xs">
                    <button class="btn btn-success" @click="showModal('modal-add-own-admin')">
                        <i class="fa fa-btn fa-fw fa-user"></i>Добавить администратора
                    </button>
                </div>
                <div class="btn-group p-b-xs">
                    <button class="btn btn-success" @click="showModal('modal-add-co-worker')">
                        <i class="fa fa-btn fa-fw fa-user-plus"></i>Добавить сотрудника
                    </button>
                </div>
                <div class="btn-group p-b-xs">
                    <router-link to="/admins/own/metrics" class="btn btn-primary">
                        <i class="fa fa-btn fa-fw fa-bar-chart"></i>Посмотреть статистику
                    </router-link>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Логин</th>
                        <th>Права</th>
                        <th>Валовый оборот</th>
                        <th>Персонал</th>
                        <th>Ботов</th>
                        <th>Клиентов</th>
                        <th>Статус</th>
                        <th>Обновлено</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="admin in admins">
                        <td v-text="admin.id"></td>
                        <td v-text="admin.name"></td>
                        <td>
                            <ul class="list-unstyled m-b-none">
                                <li v-for="role in admin.roles" v-text="role.name"></li>
                            </ul>
                        </td>
                        <td v-text="admin.gross_turnover"></td>
                        <td v-text="admin.staff"></td>
                        <td v-text="admin.bots"></td>
                        <td v-text="admin.clients"></td>
                        <td v-text="admin.status" class="text-success"></td>
                        <td></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button"
                                        class="btn btn-default"
                                        data-toggle="tooltip"
                                        data-placement="top" title="Изменить"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-default"
                                        data-toggle="tooltip"
                                        data-placement="top" title="Заблокировать"
                                >
                                    <i class="fa fa-ban"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-default"
                                        data-toggle="tooltip"
                                        data-placement="top" title="Удалить"
                                >
                                    <i class="fa fa-trash-o"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-default"
                                        data-toggle="tooltip"
                                        data-placement="top" title="Войти в панель"
                                >
                                    <i class="fa fa-sign-in"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <add-own-admin-modal></add-own-admin-modal>
        <add-co-worker-modal></add-co-worker-modal>
    </div>
</template>

<script>
  import AddOwnAdminModal from './AddOwnAdminModal.vue';
  import AddCoWorkerModal from './AddCoWorkerModal.vue';

  export default {
    components: {
      AddOwnAdminModal,
      AddCoWorkerModal,
    },
    data() {
      return {
        loading: false,
        admins: null,
      };
    },

    mounted() {
      this.fetchAdmins();
    },

    methods: {
      fetchAdmins() {
        this.loading = true;
        axios.get('/get-own-admins')
          .then((response) => {
            this.admins = response.data;
            this.loading = false;

            this.$nextTick(() => {
              $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
              })
            });
          })
      },
      showModal(id) {
        $(`#${id}`).modal('show');
      },
    },
  };
</script>