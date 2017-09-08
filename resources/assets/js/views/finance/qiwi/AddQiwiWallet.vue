<template>
    <div>
        <page-header icon="fa-money" title="Добавить кошелек Qiwi">
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
                        <div class="panel-heading">Добавление кошелька Qiwi</div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Номер кошелька</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" v-model="form.login">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Пароль</label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" v-model="form.password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Имя кошелька</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" v-model="form.name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.useProxy"> Использовать прокси
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.useProxy">
                                    <label class="col-sm-4 control-label">Прокси сервер</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="proxyServer"
                                               placeholder="host:port"
                                        >
                                    </div>
                                </div>

                                <div class="form-group" v-if="form.useProxy">
                                    <label class="col-sm-4 control-label">Авторизация прокси</label>
                                    <div class="col-sm-8">
                                        <input type="text"
                                               class="form-control"
                                               v-model="proxyAuth"
                                               placeholder="login:password"
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Тип кошелька</label>
                                    <div class="col-sm-8">
                                        <select name="" id="" class="form-control" v-model="form.type">
                                            <option v-for="type in walletTypes" :value="type.value"
                                                    v-text="type.text"></option>
                                        </select>
                                        <span class="help-block" v-text="walletTypeDescription"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.isActive"> Кошелек активен
                                            </label>
                                        </div>
                                        <span class="help-block">После создания кошелек может быть сразу активным (если стоит галочка)
                                            или же попасть в списо неактивных кошельков для последующей донастройки.</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" v-model="form.registerNew">
                                                Зарегистрировать новый кошелек
                                            </label>
                                        </div>
                                        <span class="help-block">Если вы хотите добавить существующий кошелек, то эта галочка должна быть снята.
                                        Если вы хотите зарегистрировать новый кошелек в системе Qiwi с текущими параметрами,
                                        то вам необходимо отметить галочку.</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button class="btn btn-primary" @click="submitForm">Добавить кошелек</button>
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
        data() {
            return {
                walletTypes: [
                    {
                        value: 'receive',
                        text: 'Прием платежей',
                        description: 'На такой кошелек можно принимать любые платежи из вне.',
                    },
                    {
                        value: 'reserve',
                        text: 'В резерве',
                        description: 'Этот кошелек никак не может быть использован. Но он готов в любой момент стать любым типом из первых двух вариантов.',
                    },
                    {
                        value: 'output',
                        text: 'Автовывод',
                        description: 'На такой кошелек будут выводиться средства с кошельков, принимающих платежи.'
                    },
                ],

                proxyServer: '',
                proxyAuth: '',
                form: new Form({
                    login: '',
                    password: '',
                    name: '',
                    useProxy: true,
                    proxy: {
                        host: '',
                        port: '',
                        login: '',
                        password: ''
                    },
                    type: 'receive',
                    registerNew: false,
                    isActive: true,
                }),
            };
        },
        watch: {
            proxyServer(val) {
                const data = val.split(':');

                this.form.proxy.host = data[0];
                this.form.proxy.port = data[1] ? data[1] : '';
            },
            proxyAuth(val) {
                const data = val.split(':');

                this.form.proxy.login = data.length ? data[0] : '';
                this.form.proxy.password = data[1] ? data[1] : '';
            },
        },
        methods: {
            submitForm() {
                Dinero.post('/api/qiwi-wallets', this.form)
                    .then(this.processResult)
            },
            processResult(result){
                console.log(result);

                var messageType = result.status === "success" ? "success" : "warning";

                if (result.status === "success") {
                    this.$router.push({path: `/finance/qiwi/add-wallet-success/${this.form.login}`});
                }else{
                    Bus.$emit('showNotification', messageType, result.message);
                }
            }
        },
        computed: {
            walletTypeDescription() {
                return this.walletTypes.find(t => t.value === this.form.type).description;
            },
        }
    }
</script>
