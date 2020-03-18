<template>
    <div class="form-group" :class="getClass()">
      <label>{{ label }}</label>
      <select
        class="form-control"
        :id="id"
        :value="value"
        :required="required"
        v-on:change="updateValue($event.target.value)"
      >
        <option v-if="data" v-for="option in data" :value="option[data_val.value]">{{option[data_val.text]}}</option>
        <option v-else value=""></option>
      </select>
    </div>
</template>

<script>
  module.exports = {
    props: {
      data: {},
      data_val: {
        type:Object
      },
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