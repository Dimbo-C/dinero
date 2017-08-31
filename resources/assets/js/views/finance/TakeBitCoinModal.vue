<template>
    <modal :id="'modal-take-bitcoin'" :title="'Принять BitCoin'">
        <template slot="modal-body">
            <div class="text-center">
                <img src="https://vignette1.wikia.nocookie.net/alanwake/images/a/a6/Qrcode01.png/revision/latest?cb=20120227220057" alt="">
            </div>

        </template>
    </modal>
</template>

<script>
  export default {
       /*
         * The component's data.
         */
        data() {
            return {
                //
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
                //
            },
        }
    }
</script>