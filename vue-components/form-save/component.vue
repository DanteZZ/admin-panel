<template>
    <div class="form-group" :class="getClass()">
      <label>{{ label }}</label>
      <input 
        type="text"
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
        console.log(lp);
        for (var ck in lp) {
          list[lp[ck]] = true;
        };
        console.log(list);
        return list;
      }
    }
  }
</script>