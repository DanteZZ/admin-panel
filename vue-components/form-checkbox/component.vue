<template>
    <div class="form-group" :class="getClass()">
       <div class="checkbox checkbox-circle checkbox-info peers ai-c mb-3">
        <input v-model="val" type="checkbox" :id="('checkbox_'+_uid)" class="peer">
        <label :for="('checkbox_'+_uid)" class="peers peer-greed js-sb ai-c">
          <span class="peer peer-greed">{{ label }}</span>
        </label>
      </div>
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
      value: {default: false}
    },

    data: function() {
      return {val:this.value};
    },

    methods: {
      getClass: function() {
        var list = {"form-group":true};
        var lp = this.classes.trim().split(" ");
        for (var ck in lp) {
          list[lp[ck]] = true;
        };
        return list;
      }
    },
    watch: {
      val:function() {
        this.$emit('input', this.val);
      },
      value:function() {
        this.val = this.value;
      }
    },
    mounted: function(){
      if (typeof(this.value) !== "boolean") {
        if (this.value !== "") {this.value = JSON.parse(this.value);};
      };
      this.$emit('input', this.value);
      console.log(this.value);
    }
  }
</script>