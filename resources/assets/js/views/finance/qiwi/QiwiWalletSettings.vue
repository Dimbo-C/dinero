<template>
    <div>
        <page-header icon="fa-money" title="Настройки кошелька Qiwi">
            <li>
                <a class="disabled">Финансы</a>
            </li>
            <router-link tag="li" to="/finance/qiwi">
                <a>Qiwi Visa Wallet</a>
            </router-link>
            <router-link tag="li" to="/finance/qiwi/dashboard">
                <a>Панель управления</a>
            </router-link>
        </page-header>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Настройки кошелька Qiwi</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Комментарий к кошельку</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="useProxy"> Использовать прокси
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="useProxy">
                                    <label for="" class="col-sm-4 control-label">Прокси сервер</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group" v-if="useProxy">
                                    <label for="" class="col-sm-4 control-label">Авторизация прокси</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" :disabled="!useProxy">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" checked> Кошелек активен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" checked> Режим «Всегда онлайн»
                                            </label>
                                        </div>
                                        <span class="help-block">Поставьте галочку, если хотите чтобы сессия кошелька всегда поддерживалась в режиме
                                        онлайн.</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Частота проверки баланса, мин.</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0" class="form-control">
                                        <span class="help-block">Укажите через какое количество минут система должна автоматически
                                        обновлять баланс кошелька. Чтобы отключить функцию введите 0</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип кошелька</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Максимальный баланс</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control">
                                        <span class="help-block">Максимальный баланс кошелька, при достижении которого
                                            кошелек автоматически уходит в резервные</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="autoWithdrawal"> Автовывод включен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Режим работы автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="autoWithdrawalType" :disabled="!autoWithdrawal">
                                            <option v-for="o in autoWithdrawalOptions" :value="o.value" v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" v-if="autoWithdrawalType === 'every_x_minutes'">
                                    <label for="" class="col-sm-4 control-label">Вызывать автовывод каждые X минут</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="usingVouchers"> Автовывод с помощью ваучеров
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Карта для автовывода</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label for="" class="col-sm-4 control-label">Данные владелца карты</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Имя">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Фамилия">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button class="btn btn-primary">Сохранить</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
  export default {
       /*
         * The component's data.
         */
        data() {
            return {
              useProxy: true,
              autoWithdrawal: true,
              autoWithdrawalType: 'after_each_balance_update',
              autoWithdrawalOptions: [
                { value: 'after_each_balance_update', text: 'Посе каждого обновления баланса' },
                { value: 'manually', text: 'Вручную' },
                { value: 'every_x_minutes', text: 'Каждые X минут' },
              ],
              autoWithdrawalPeriod: 1,
              usingVouchers: false,
            };
        },

        /**
         * Prepare the component.
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
              this.$nextTick(() => {
                $('.tooltip').removeClass('in');
              });
            },
        }
    }
</script>