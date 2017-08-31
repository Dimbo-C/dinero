<template>
    <!-- Add Co-worker Modal -->
    <div class="modal fade" id="modal-add-co-worker" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Добавить сотрудника</h4>
                    <div class="form-wizard m-t-md">
                        <ul class="btn-group btn-group-justified" role="tablist">
                            <li role="presentation" class="active btn btn-default disabled">
                                <a href="#modal-add-co-worker-credentials" class="hidden" data-toggle="tab"></a>
                                Базовая информация
                            </li>
                            <li role="presentation" class="btn btn-default disabled">
                                <a href="#co-worker-info" class="hidden" data-toggle="tab"></a>
                                Корпоративная информация
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="modal-add-co-worker-credentials">
                        <div class="modal-body p-b-none">

                            <div class="form-horizontal">
                                <!-- Login -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Логин</label>

                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="form.login" autofocus="" required>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Пароль</label>

                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="form.password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="modal-footer border-none">
                            <button class="btn btn-default pull-left" data-dismiss="modal">Отменить</button>
                            <button type="button"
                                    class="btn btn-primary pull-right"
                                    @click="changeTabTo('co-worker-info')"
                            >Далее</button>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="co-worker-info">
                        <div class="modal-body p-b-none">

                            <div class="form-horizontal">
                                <!-- Rules -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Права</label>

                                    <div class="col-md-8">
                                        <select v-model="form.role" class="form-control">
                                            <option v-for="role in roles" :value="role.value">
                                                {{ role.text }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Rules -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label p-t-none">Второстепенные сотрудники</label>

                                    <div class="col-md-8">
                                        <select v-model="form.secondary_employees"
                                                class="form-control m-t-xs"
                                                :disabled="form.role === 'manager'"
                                        >
                                            <option v-for="item in secondary" :value="item.value">
                                                {{ item.text }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Balance -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Баланс, руб.</label>

                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="form.balance" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="modal-footer border-none">
                            <button type="button"
                                    class="btn btn-default pull-left"
                                    @click="changeTabTo('modal-add-co-worker-credentials')"
                            >Назад</button>

                            <button type="button"
                                    class="btn btn-primary pull-right"
                            >Добавить сотрудника</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Modal from './../modal';

    export default Modal.extend({
      data() {
        return {
          loginPrefix: 'coo',
          roles: [
            { text: 'Куратор', value: 'curator' },
            { text: 'Менеджер', value: 'manager' },
          ],
          secondary: [
            { text: '-', value: 0 },
            { text: 'Операторы', value: 'operators' },
          ],
          form: new Form({
            login: '',
            password: '',
            role: 'curator',
            secondary_employees: 0,
            balance: 0,
          }),
        };
      },
      watch: {
        'form.role': function (val) {
          if (val === 'manager') {
            this.form.secondary_employees = 0;
          }
        }
      },
      mounted() {
        this.init('modal-add-co-worker');
      },
      methods: {
        reset() {
          this.form = new Form({
            login: '',
            password: '',
            role: 'curator',
            secondary_employees: 0,
            balance: 0,
          });
        },
      },
    });
</script>