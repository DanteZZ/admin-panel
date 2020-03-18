<template>
  <div :class="getSize()">
      <div class="layers bd bgc-white p-20">
          <div class="layer w-100 mB-10">
              <h4 class="lh-1">{{label}}</h4>
          </div>
          <div class="layer w-100">
              <div class="peers ai-sb fxw-nw">
                  <div class="peer peer-greed">
                    <span class="d-ib lh-0 va-m fw-600 bdrs-10em pX-15 pY-15" :class="getClass()">{{digit+postfix}}</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
  module.exports = {
    props: {
      label: {
        type: String,
        default: "",
      },
      digit: {
        type: Number,
        default: 0
      },
      classes: {
        type: String,
        default: ""
      },
      postfix: {
        type: String,
        default: ""
      },
      size: {
        type: Number,
        default:3
      }
    },
    methods: {
      getClass: function() {
        var list = {"ffs-select":true};
        var lp = this.classes.trim().split(" ");
        for (var ck in lp) {
          list[lp[ck]] = true;
        };
        return list;
      },
      getSize: function() {
        let cl = {};
        cl["col-md-"+this.size] = true;
        return cl;
      }
    }
  }
</script>