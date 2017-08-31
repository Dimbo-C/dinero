<template>
    <div>
        <page-header icon="fa-users" title="Администраторы системы"></page-header>

        <div class="container-fluid">
            <div class="m-b-lg">
                <div class="btn-group p-b-xs">
                    <button class="btn btn-success" @click="showModal('modal-add-rent-admin')">
                        <i class="fa fa-btn fa-fw fa-user"></i>Добавить администратора
                    </button>
                </div>
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
                        <th>ID</th>
                        <th>Логин</th>
                        <th>Аренды (руб.)</th>
                        <th>%</th>
                        <th>Персонал</th>
                        <th>Ботов</th>
                        <th>Клиентов</th>
                        <th>Аренда до</th>
                        <th>Статус</th>
                        <th>Обновлено</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="admin in admins">
                        <td>
                            <div class="checkbox m-none">
                                <label class="p-t-xs">
                                    <input type="checkbox" v-model="selected" :value="admin">
                                </label>
                            </div>
                        </td>
                        <td v-text="admin.id"></td>
                        <td v-text="admin.login"></td>
                        <td v-text="admin.rents"></td>
                        <td  v-text="admin.percent"></td>
                        <td v-text="admin.staff"></td>
                        <td v-text="admin.bots"></td>
                        <td v-text="admin.clients"></td>
                        <td v-text="admin.rent_to"></td>
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

            <div class="btn-group">
                <i class="fa fa-btn fa-hand-o-up m-l-sm hidden-xs"></i> С отмеченными:
            </div>
            <div class="btn-group p-b-xs">
                <button class="btn btn-primary"
                        @click="showModal('modal-send-message-admin')"
                        :disabled="!selected.length"
                >
                    <i class="fa fa-btn fa-fw fa-comments-o"></i>Выслать уведомление
                </button>
            </div>
        </div>

        <add-rent-admin-modal></add-rent-admin-modal>
        <send-message-modal @unselect="unselectAll"></send-message-modal>
    </div>
</template>

<script>
  import AddRentAdminModal from './AddRentAdminModal.vue';
  import SendMessageModal from './SendMessageModal.vue';
  import Table from './../../../mixins/table';

  export default {
    mixins: [Table],
    components: {
      AddRentAdminModal,
      SendMessageModal,
    },
    data() {
      return {
        admins: [
          {
            id: 2,
            login: '',
            rents: 100,
            percent: 1,
            staff: 0,
            bots: 0,
            clients: 0,
            rent_to: '',
            status: 'Активен',
            updated_at: ''
          },
        ],
      };
    },
    mounted() {
      this.initTable(this.admins);
    },
    methods: {
      showModal(id) {
        $(`#${id}`).modal('show');
      },
    },
  };
</script>