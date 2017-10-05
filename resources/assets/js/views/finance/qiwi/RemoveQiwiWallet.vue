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
                        <div class="panel-body">
                            <h3 class="body-header">Удаление QIWI кошелька</h3>
                            <template v-if="!success">
                                <div class="body-content">
                                    <div class="alert-notification">
                                        <b>Внимание!</b> Вы собираетесь удалить QIWI кошелек из системы
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-4 col-sm-8">
                                            <input type="checkbox"
                                                   id="confirm"
                                                   name="confirm"
                                                   v-model="confirm">
                                            <label for="confirm">
                                                Я подтверждаю, что хочу удалить кошелек {{login}}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-4 col-sm-8">
                                            <button class="btn btn-primary"
                                                    @click="proceed"
                                                    :disabled="!confirm">
                                                <i class="fa fa-times"></i>
                                                Удалить кошелек
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-if="success">
                                <div class="body-content">
                                    <div class="success-notification">
                                        <b>Отлично!</b> Qiwi кошелек успешно удален из системы
                                    </div>
                                    <div>
                                        Вы можете перейти к
                                        <router-link to="/finance/qiwi/dashboard">
                                            <a>списку</a>
                                        </router-link>
                                        кошельков или
                                        <router-link to="/finance/qiwi/add-wallet">
                                            <a>загеристрировать новый</a>
                                        </router-link>
                                        кошелек
                                    </div>
                                </div>
                            </template>
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
                success: false,
                confirm: true,
                login: this.$route.params.wallet

            }
        },
        mounted() {
            this.prepareComponent();
        },

        methods: {
            prepareComponent() {

                this.$nextTick(() => {
                    $('.tooltip').removeClass('in');
                });
            },
            proceed() {
                Dinero.post("/api/qiwi-wallets/remove/" + this.login, new Form())
                    .then((data) => {
                        console.log(data);
                        this.success = true;
                    });
            },
        },

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

    .alert-notification {
        padding: 12px;
        margin-bottom: 15px;
        color: #8a6d3b;
        font-size: 14px;
        background-color: #fcf8e3;
        border-color: #d6e9c6;
        border-radius: 3px;
    }

    .success-notification {
        padding: 12px;
        margin-bottom: 15px;
        color: #fdeada;
        font-size: 14px;
        background-color: #dff0d8;
        border-color: #d6e9c6;
        border-radius: 3px;
    }
</style>
