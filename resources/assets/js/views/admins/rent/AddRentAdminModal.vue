<template>
    <!-- Add Admin Modal -->
    <div class="modal fade" id="modal-add-rent-admin" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Добавить администратора</h4>
                    <div class="form-wizard m-t-md">
                        <ul class="btn-group btn-group-justified" role="tablist">
                            <li role="presentation" class="active btn btn-default disabled">
                                <a href="#modal-add-rent-admin-credentials" class="hidden" data-toggle="tab"></a>
                                Базовая информация
                            </li>
                            <li role="presentation" class="btn btn-default disabled">
                                <a href="#settings" class="hidden" data-toggle="tab"></a>
                                Условия аренды
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="modal-add-rent-admin-credentials">
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
                                    @click="changeTabTo('settings')"
                            >Далее</button>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="settings">
                        <div class="modal-body p-b-none">

                            <div class="form-horizontal">
                                <!-- Value -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Стоимость</label>

                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="form.value" autofocus="" required>
                                    </div>
                                </div>

                                <!-- Percent -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Процент</label>

                                    <div class="col-md-8">
                                        <input type="text" class="form-control" v-model="form.percent" required>
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
                                    @click="changeTabTo('modal-add-rent-admin-credentials')"
                            >Назад</button>

                            <button type="button"
                                    class="btn btn-primary pull-right"
                            >Добавить администратора</button>
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
          loginPrefix: 'adm',
          form: new Form({
            login: '',
            password: '',
            value: 100,
            percent: 1,
            balance: 0,
          }),
        };
      },
      mounted() {
        this.init('modal-add-rent-admin');
      },
      methods: {
        reset() {
          this.form = new Form({
            login: '',
            password: '',
            value: 100,
            percent: 1,
            balance: 0,
          });
        },
      },
    });
</script>