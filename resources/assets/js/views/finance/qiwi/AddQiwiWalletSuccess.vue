<template>
    <div>
        <page-header icon="fa-money" title="Добавление кошелька">
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
                        <div class="panel-heading">Регистрация QIWI кошелька завершена</div>
                        <div class="panel-body">
                            <h3 class="body-header">Регистрация QIWI кошелька</h3>
                            <div class="body-content">
                                <div class="success-notification">
                                    <b>Отлично!</b> QIWI кошелек успешно зарегистрирован в Dinero
                                </div>
                                <div class="wallet-info">
                                    <p>Ваш кошелек <span v-text="this.login"></span>
                                        успешно зарегистрирован в системе Dinero.</p>
                                    <p>Вы можете перейти к
                                        <router-link to="/finance/qiwi/dashboard">
                                            <a>списку</a>
                                        </router-link>
                                        кошельков или
                                        <router-link to="/finance/qiwi/add-wallet">
                                            <a>зарегистрировать</a>
                                        </router-link>
                                        новый кошелек.
                                    </p>
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
                useProxy: true,
                proxyServer: '',
                proxyAuth: '',
                form: new Form({
                    login: '',
                    password: '',
                    name: '',
                    proxy: {
                        host: '',
                        port: '',
                        login: '',
                        password: ''
                    },
                    type: 'receive',
                    register_new: false,
                    is_active: true,
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
                this.form.use_proxy = this.useProxy;
                Dinero.post('/api/qiwi-wallets', this.form).then(this.processResult)
            },
            processResult(result){
                console.log(result);

                var messageType = result.status === "success" ? "success" : "warning";
                Bus.$emit('showNotification', messageType, result.message);
//                this.$route.router.go("/finance/qiwi/add-wallet-success");
            }

        },
        computed: {
            walletTypeDescription() {
                return this.walletTypes.find(t => t.value === this.form.type).description;
            },
            login () {
                return this.$route.params.wallet;
            },
        }
    }
</script>

<style scoped>
    .body-header {
        padding: 15px 0;
        margin-top: 0;
        font-weight: bold;
        font-size: 16px;
        border-bottom: 1px solid;
    }

    .body-content {
        padding: 15px;
    }

    .success-notification {
        padding: 12px;
        margin-bottom: 15px;
        color: #3c763d;
        font-size: 14px;
        background-color: #dff0d8;
        border-color: #d6e9c6;
        border-radius: 3px;
    }

    .wallet-info {

    p {
        margin-bottom: 0;
    }

    }
</style>
