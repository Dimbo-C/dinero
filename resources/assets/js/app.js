import Vue from 'vue';
import router from './router'
import './bootstrap';

window.Vue = Vue;

import './components/bootstrap';

$('[data-toggle="tooltip"]').tooltip({
    container: 'body',
});

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
