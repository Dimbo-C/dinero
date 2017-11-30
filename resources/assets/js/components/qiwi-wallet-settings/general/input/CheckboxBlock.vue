<template>
    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-8">
            <div class="checkbox">
                <label>
                    <input type="checkbox"
                           :checked="value"
                           @change="$emit('input',$event.target.checked)">
                    {{label}}
                </label>
            </div>
            <span v-if="description"
                  v-html="description"
                  class="help-block">
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                required: true
            },
            'label': String,
            'description': String
        },
    }
</script>