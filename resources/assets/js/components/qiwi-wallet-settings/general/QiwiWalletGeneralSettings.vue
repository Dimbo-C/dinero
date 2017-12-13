<template>
    <div>
        <page-header icon="fa-money" title="Вывод средств">
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
        <div class="container-fluid"
             @keyenter="saveSettings">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-12 col-md-8">
                                    Настройки кошелька Qiwi ({{ form.login}})
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width"
                                            @click="showSetting('security')">Безопасность
                                    </button>
                                </div>
                                <div class="col-sm-12 col-md-2 text-center">
                                    <button class="btn btn-primary full-width marginless paddingless"
                                            @click="showSetting('identification')">Идентификация
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div v-if="alert.show"
                                     v-bind:class="[alert.className]"
                                     class="alert">
                                    {{alert.text}}
                                </div>

                                <input-block
                                        label='Название кошелька'
                                        v-model='form.name'
                                        placeholderText='Новый кошелек_1'>
                                </input-block>

                                <input-block
                                        label='Пароль от кошелька'
                                        v-model="form.password"
                                        placeholderText='Пароль'>
                                </input-block>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Комментарий к кошельку</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control"
                                                  v-model="form.comments"></textarea>
                                    </div>
                                </div>

                                <checkbox-block
                                        label="Использовать прокси"
                                        v-model="form.useProxy">
                                </checkbox-block>

                                <!--<div class="form-group">-->
                                <!--<div class="col-sm-offset-4 col-sm-8">-->
                                <!--<div class="checkbox">-->
                                <!--<label>-->
                                <!--<input type="checkbox" v-model="form.useProxy">-->
                                <!--Использовать прокси-->
                                <!--</label>-->
                                <!--</div>-->
                                <!--</div>-->
                                <!--</div>-->

                                <input-block
                                        v-if="form.useProxy"
                                        :label="strings.proxyServer.label"
                                        v-model="proxyServer"
                                        :placeholderText="strings.proxyServer.placeholder">
                                </input-block>


                                <input-block
                                        v-if="form.useProxy"
                                        :label="strings.proxyAuth.label"
                                        v-model="proxyAuth"
                                        :placeholderText="strings.proxyAuth.placeholder">
                                </input-block>


                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.walletActive"
                                                       checked>
                                                Кошелек активен
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       v-model="form.alwaysOnline"
                                                       checked>
                                                Режим «Всегда онлайн»
                                            </label>
                                        </div>
                                        <span class="help-block">Поставьте галочку,
                                            если хотите чтобы сессия кошелька всегда поддерживалась в режиме
                                            онлайн. Аналогично постоянному нахождению в браузере.
                                            Если галочка не стоит - сессия будет слетать и кошелек будет перелогиниваться.
                                            Не используйте для всех кошельков поголовно, только для автовыводных и приемных,
                                            где своевременное снятие денег в приоритете.
                                        </span>
                                    </div>
                                </div>

                                <input-block
                                        type="number"
                                        :label="strings.recheckTimeout.label"
                                        v-model="form.balanceRecheckTimeout"
                                        :description="strings.recheckTimeout.description">
                                </input-block>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип кошелька</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="form.walletType">
                                            <option v-for="o in form.walletTypes" :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <input-block
                                        type="number"
                                        label="Максимальный баланс"
                                        v-model="form.maximumBalance"
                                        description="Максимальный баланс кошелька, при достижении которого
                                        кошелек автоматически уходит в резервные">
                                </input-block>

                                <input-block
                                        type="number"
                                        label="Минимальный баланс"
                                        v-model="form.minimumBalance"
                                        description="Минимальный баланс кошелька.
                                            Работает на автовыводе.
                                            Оставляет сумму на балансе кошелька.
                                            Для отключения введите 0.">
                                </input-block>


                                <input-block
                                        type="number"
                                        label="Баланс для автовывода"
                                        v-model="form.autoWithdrawalMinBalance"
                                        description="Работает только если кошелек настроен на автовывод.
                                            Автовывод срабатывает если баланс кошелька больше либо равен указанной сумме.
                                            Для отключения введите 0.">
                                </input-block>

                                <input-block
                                        type="number"
                                        label="Лимит транзакций на вывод"
                                        v-model="form.autoWithdrawalLimit"
                                        description="Работает только если кошелек настроен на автовывод.
                                            За 1 раз будет выведена сумма, не превышающая данное значение.
                                            Для отключения введите 0.">
                                </input-block>


                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.autoWithdrawalActive">
                                                Автовывод включен
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Режим работы автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control"
                                                v-model="form.autoWithdrawalType">
                                            <option v-for="o in form.autoWithdrawalTypes"
                                                    :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Тип автовывода</label>
                                    <div class="col-sm-8">
                                        <select class="form-control"
                                                v-model="form.autoWithdrawalTarget">
                                            <option v-for="o in form.autoWithdrawalTargets"
                                                    :value="o.value"
                                                    v-text="o.text"></option>
                                        </select>
                                    </div>
                                </div>

                                <input-block
                                        type="number"
                                        :label="strings.autoWithdrawalTimeout.label"
                                        v-model="form.autoWithdrawalTimeout"
                                        :description='strings.autoWithdrawalTimeout.description'>
                                </input-block>

                                <input-block
                                        :label="strings.autoWithdrawalWallets.label"
                                        v-model="autoWithdrawalWallets"
                                        :description="strings.autoWithdrawalWallets.description"
                                        :placeholderText="strings.autoWithdrawalWallets.placeholder">
                                </input-block>

                                <input-block
                                        :label="strings.cardNumber.label"
                                        v-model='cardNumber'
                                        :placeholderText='strings.cardNumber.placeholder'>
                                </input-block>

                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Данные владельца карты</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderName"
                                               placeholder="Имя">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control"
                                               v-model="form.autoWithdrawalCardholderSurname"
                                               placeholder="Фамилия">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-4 col-xs-offset-1 col-lg-2 col-lg-offset-4">
                                        <button class="btn btn-primary"
                                                @click="saveSettings">Сохранить
                                        </button>
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

<script src="./QiwiWalletGeneralSettings.js"></script>
<style src="./QiwiWalletGeneralSettings.css" scoped></style>