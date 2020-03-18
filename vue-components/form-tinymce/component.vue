<template>
    <div class="form-group" :class="getClass()">
      <label>{{ label }}</label>
      <textarea
        tinymce
        class="form-control"
        :id="id"
        :value="value"
        @change="updateValue($event.target.value)"
      ></textarea>
    </div>
</template>

<style>
  .tox-tinymce {
    border-radius: .35rem!important;
  }
</style>

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
      $tvm = this;
      tinymce.init({
        target:$($tvm.$el).find("tinymce")[0],
        language: 'ru',
        selector: "textarea[tinymce]", theme: "silver",width: "100%",height: 300,
        plugins: [
             "advlist autolink link image lists charmap print preview hr anchor pagebreak",
             "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
             "table directionality emoticons paste responsivefilemanager code"
        ],
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
        image_advtab: true ,

        external_filemanager_path:"/addict/modules/admin-panel/assets/filemanager/",
        filemanager_title:"Менеджер файлов" ,
        external_plugins: { "filemanager" : "/addict/modules/admin-panel/assets/filemanager/plugin.min.js"},

        init_instance_callback: function (editor) {
          editor.on('change', function (e) {
            $tvm.updateValue(e.level.content);
          });
        }
      });
    }
  }
</script>