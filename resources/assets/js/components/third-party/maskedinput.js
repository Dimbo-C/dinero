require('jquery.maskedinput/src/jquery.maskedinput');
module.exports = {
    template: '<input type="text" ref="input" v-bind:value="value" v-on:blur="formatValue" >',
    props: {
        value: {
            type: String,
            default: ''
        },
        mask: ''
    },

    mounted: function () {
        this.formatValue()
    },

    methods: {
        updateValue(value) {
            this.$emit('input', value)
        },

        formatValue() {
            $(this.$el).mask(this.mask);
            this.updateValue(this.$refs.input.value);
        }
    }
};