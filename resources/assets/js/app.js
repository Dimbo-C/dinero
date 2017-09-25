import Vue from 'vue';
import router from './router'
import './bootstrap';

window.Vue = Vue;

import './components/bootstrap';

$('[data-toggle="tooltip"]').tooltip({
    container: 'body',
});

let test = {
    "code": {"value": "0", "_name": "NORMAL"},
    "data": {
        "body": {
            "id": "1506065852518",
            "terms": "22496",
            "fields": {
                "browser_user_agent_crc": "10891c10",
                "_meta_pay_partner": "",
                "sinap-form-version": "qw::22496, 3",
                "account": "708",
                "to_account_type": "undefind"
            },
            "sum": {"amount": 5, "currency": "643"},
            "transaction": {"id": "11371492716", "state": {"code": "Accepted"}},
            "comment": "",
            "source": "account_643"
        }, "status": 200
    },
    "message": null,
    "messages": null
};

let data = {
    "code": {"value": "0", "_name": "NORMAL"},
    "data": {
        "body": {
            "code": "QWPRC-220",
            "message": "\u041D\u0435\u0434\u043E\u0441\u0442\u0430\u0442\u043E\u0447\u043D\u043E \u0441\u0440\u0435\u0434\u0441\u0442\u0432 "
        },
        "status": 400
    },
    "message": null,
    "messages": null
};

let meme = {
    "code": {"value": "2", "_name": "ERROR"},
    "data": null,
    "message": "\u0422\u0435\u0445\u043D\u0438\u0447\u0435\u0441\u043A\u0430\u044F \u043E\u0448\u0438\u0431\u043A\u0430",
    "messages": {}
};

const app = new Vue({
    el: '#app',
    router,
    data: {
        user: Dinero.state.user,
        version: window.Dinero.version,
        updatedAt: window.Dinero.updated_at,
        showSidebar: true,
        windowWidth: 0,
    },
    created() {
        const self = this;

        Bus.$on('toggleSidebar', function () {
            self.toggleSidebar();
        });

        Bus.$on('initTooltip', function () {
            this.$nextTick(() => {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body',
                })
            });
        });


        if (localStorage.showSidebar) {
            this.showSidebar = JSON.parse(localStorage.showSidebar);
        }
    },
    mounted() {
        const self = this;

        //Init
        this.getWindowWidth();

        window.addEventListener('resize', () => {
            self.getWindowWidth()
        });
    },
    methods: {
        toggleSidebar() {
            localStorage.showSidebar = this.showSidebar = !this.showSidebar;
        },

        getWindowWidth() {
            this.windowWidth = document.documentElement.clientWidth;
        },
    }
});
