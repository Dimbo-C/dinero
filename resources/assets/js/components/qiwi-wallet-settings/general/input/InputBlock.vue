<template>
    <div class="form-group">
        <label class="col-sm-4 control-label">{{label}}</label>
        <div class="col-sm-8">
            <input class="form-control"
                   :type="type"
                   :placeholder="placeholderText"
                   v-bind:value="value"
                   v-on:input="updateValue($event.target.value)">
            <span v-if="description"
                  v-html="description"
                  class="help-block">
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        name: "input-block",
        props: {
            type: {
                default: "text"
            },
            value: {
                required: true
            },
            'label': String,
            'placeholderText': String,
            'description': String
        },
        methods: {
            updateValue(val) {
                this.$emit("input", val)
            }
        },
    }
</script>