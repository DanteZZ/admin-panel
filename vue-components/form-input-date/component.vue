<template>
    <div class="form-group" :class="getClass()">
      <label>{{ label }}</label>
      <input 
        type="date"
        class="form-control"
        :id="id"
        :placeholder="placeholder"
        :value="value"
        :required="required"
        v-on:input="updateValue($event.target.value)"
      >
    </div>
</template>

<script>
  module.exports = {
    props: {
      id: {
        type: String,
        default: "",
      },
      classes: {
        type: String,
        default: ""
      },
      label: {
        type: String,
        default: ""
      },
      placeholder: {
        type: String,
        default: ""
      },
      value: {
        type: String
      },
      required: {
        type: Boolean,
        default: false
      }
    },

    methods: {
      updateValue: function (value) {
        this.$emit('input', value)
      },
      getClass: function() {
        var list = {"form-group":true};
        var lp = this.classes.trim().split(" ");
        for (var ck in lp) {
          list[lp[ck]] = true;
        };
        return list;
      }
    }
  }
</script>