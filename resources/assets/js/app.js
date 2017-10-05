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
            "id": "1507212997118",
            "terms": "99",
            "fields": {"account": "+380507308340"},
            "sum": {"amount": 2, "currency": "643"},
            "transaction": {"id": "11454372393", "state": {"code": "AwaitingSMSConfirmation"}},
            "comment": "",
            "source": "account_643"
        }, "status": 200
    },
    "message": null,
    "messages": null
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
