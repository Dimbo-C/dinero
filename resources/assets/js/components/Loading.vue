<template>
    <transition name="fade">
        <div v-if="show" class="loading text-primary">
            <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
        </div>
    </transition>
</template>

<script>
  export default {
    props: ['show'],
  }
</script>
