<template>
  <div class="ffs-select" :class="getClass()">
    <label>{{ label }}</label>
    <input hidden>
    <div v-if="!button && multiple" class="image-list mb-3">
      <div v-if="!getList()" @click="clickButton" class="add">Добавить файлы <i class="fas fa-plus ml-1"></i></div>
      <div v-if="getList()" v-for="(file, ind) in getList()" class="file">
        <div class="wrapper">
          <i @click="remove(ind)" class="fas fa-times remove"></i>
          <div v-if="type !== 'image'" class="thumb">.{{file.ext}}</div>
          <img v-else :src="file.link" class="thumb" alt="">
          <div class="name" :title="file.name">{{file.name}}</div>
        </div>
      </div>

      <div v-if="getList()" @click="clickButton" class="file">
        <div class="wrapper wrap-btn">
          Добавить файлы <i class="fas fa-plus ml-1"></i>
        </div>
      </div>

    </div>
    
    <div v-else class="image-list mb-3">
      <div v-if="!value" @click="clickButton" class="add">Выбрать файл <i class="fas fa-plus ml-1"></i></div>
      <div v-if="value" class="file full">
        <div class="wrapper">
          <i @click="remove(0)" class="fas fa-times remove"></i>
          <div v-if="type !== 'image'" class="thumb">.{{value.split(".").pop()}}</div>
          <img v-else :src="value" class="thumb" alt="">
          <div class="name" @click="clickButton">Изменить изображение</div>
        </div>
      </div>
    </div>

    <button @click="clickButton" v-if="button" class="btn btn-primary d-block mb-3">{{buttonName()}}</button>
  </div>
</template>

<style>
  .ffs-select {
    width:100%;
  }

  .ffs-select .image-list .file {
    width:126px;
    padding:8px;
    position:relative;
  }

  .ffs-select .image-list .file.full {
    width:100%;
  }

 .ffs-select .image-list .file.full {
    padding:0px;
  }

  .ffs-select .image-list .file.full .wrapper{
    border:none;
  }

  .ffs-select .image-list .file.full .name{
    cursor:pointer;
  }

  .ffs-select .image-list .file.full .thumb {
    height:150px;
  }

  .ffs-select .image-list .file .wrapper {
    width:100%;
    height:100%;
    border-radius: .35rem;
    border: 1px solid #d1d3e2;
    padding:8px;
    text-align:center;
  }

  .ffs-select .image-list .file .wrap-btn {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    cursor:pointer;
  }

  .ffs-select .image-list .file .thumb {
    width:100%;
    height:80px;
    object-fit:contain;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.8em;
  }

  .ffs-select .image-list .file i.remove {
    cursor:pointer;
    position: absolute;
    left: 8px;
    top: 8px;
    right: 8px;
    padding: 0px 0px;
    border-radius: .35rem .35rem 0px 0px;
    background-color: #ff0a0a8c;
    color: white;
    height:0px;
    opacity:0;
    transition:.1s;
  }

   .ffs-select .image-list .file:hover i.remove {
    padding:4px 0px;
    height:auto;
    opacity:1;
   }


  .ffs-select .image-list .file .name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .ffs-select .image-list {
    width:100%;
    padding:8px;
    display:flex;
    flex-wrap:wrap;
    border-radius: .35rem;
    border: 1px solid #d1d3e2;
    min-height:96px;
  }
  .ffs-select .add {
    cursor:pointer;
    margin:auto;
    width:150px;
    height:80px;
    display:flex;
    align-items:center;
    justify-content: center;
    transition: .2s;
  }
  .ffs-select .add:hover {
    color:#4e73df;
  }
</style>

<script>
  module.exports = {
    props: {
      value: {},
      label: {
        type: String
      },
      type: {
        type: String,
        default:"*"
      },
      button: {
        type:Boolean,
        default:false
      },
      multiple: {
        type:Boolean,
        default:true
      },
      classes: {
        type: String,
        default: ""
      },
      text: {
        type: String,
        default: "Выбрать файл"
      }
    },

    methods: {
      remove:function(ind) {
        if (typeof(this.value) == "string") {
          this.value = "";
        } else {
          this.value.splice(ind,1);
        };
        this.$emit('input', this.value)
      },

      getClass: function() {
        var list = {"ffs-select":true};
        var lp = this.classes.trim().split(" ");
        for (var ck in lp) {
          list[lp[ck]] = true;
        };
        return list;
      },

      getList: function() {
        if (!this.value) {return false;};
        let res = [];
        if (typeof(this.value) == "string") {
          this.value = [this.value];
        };
        for (var k in this.value) {
          var ln = this.value[k];
          res.push({
            link:ln,
            name:decodeURI(ln.split("/").pop()),
            ext:ln.split("/").pop().split(".").pop()
          });
        };
        return res;
      },

      updateValue: function (value) {
        if (this.multiple && !this.button) {
          if (!this.value) {this.value = [];};
          console.log(this.value.concat(value));
          value = this.value.concat(value);
        };

        this.$emit('input', value)
      },

      clickButton: function () {
        
        switch (this.type) {
          case "*": var tt=2; break;
          case "image": var tt=1; break;
          case "video": var tt=3; break;
        };

        $fsvm = this;

        url = "/addict/modules/admin-panel/assets/filemanager/dialog.php?type="+tt+"&popup=1&field_id=form-file-select-"+$fsvm._uid;
        var w = 880;
        var h = 570;
        var l = Math.floor((screen.width - w) / 2);
        var t = Math.floor((screen.height - h) / 2);
        var win = window.open(url, 'Менеджер файлов', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
      },

      isJSON : function(str) {
          try {
              if (JSON.parse(str) && !!str) {
                return JSON.parse(str);
              }
          } catch (e) {
              return str;
          }
      },
      buttonName: function() {
        $fsvm = this;
        if (typeof(this.value) == "string") {
          var tt = 1;       
        } else if (typeof(this.value) == "object") {
          var tt = 2;
        } else {
          var tt = 0;
        };
        switch (tt) {
          case 0: return $fsvm.text; break;
          case 1: return decodeURI($fsvm.value.split("/").pop()); break;
          case 2: return decodeURI($fsvm.value[0].split("/").pop())+"..."; break;
        };
      }
    },

    mounted: function(){
      $fsvm = this;
      $($fsvm.$el).find("input").attr("id","form-file-select-"+$fsvm._uid);
      $($fsvm.$el).find("input").change(function(){
        var vv = $($fsvm.$el).find("input").val();
        if ($fsvm.multiple) {
          $fsvm.updateValue($fsvm.isJSON(vv));
        } else {
          $fsvm.updateValue(vv);
        };
      });
    }
  }
</script>