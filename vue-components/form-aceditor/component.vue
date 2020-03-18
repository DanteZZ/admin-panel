<template>
    <div class="form-group" :class="getClass()">
      <label>{{ label }}</label>
      <pre aceditor></pre>
    </div>
</template>

<style>
  [aceditor] {
    border-radius: .35rem!important;
    height:300px;
  }
</style>

<script>
  module.exports = {
    props: {
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
    },
    mounted: function(){
      $acvm = this;
      
      this.editor = ace.edit($($acvm.$el).find("[aceditor]")[0]);
      this.editor.setValue(this.value, -1);

      this.editor.session.setMode("ace/mode/html");
      this.editor.setTheme("ace/theme/monokai");
      this.emmet = require("ace/ext/emmet"); // important to trigger script execution
      this.editor.setOption("enableEmmet", true);

      this.editor.setOptions({fontSize: "11pt" });

      this.editor.getSession().on('change', function() {
        $acvm.updateValue($acvm.editor.getValue());
      });
    }
  }
</script>