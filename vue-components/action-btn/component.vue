<template>
  <button class="btn" :class="getClass()" @click="btnClick">{{getText()}} <i v-if="state > 1" :class="{'fa-circle-notch':(state == 2), 'fa-spin':(state ==2), 'fa-check':(state==3),'fa-times':(state==4)}" class="fas"></i></button>
</template>

<script>
  module.exports = {
    props: {
    	label:{default:"Кнопка"},
    	action:{},
    	classes:{default:"btn-primary"}
    },
    data: function(){
    	return {state:1};
    },
    methods: {
    	getText: function() {
    		switch (this.state){
    			case 1: return this.label; break; //Simple
    			case 2: return "Обработка"; break; //Loading
    			case 3: return "Готово"; break; //Loaded
    			case 4: return "Ошибка"; break; //Loaded
    		};
    	},
	    getClass: function() {
	        var list = {};
	        var lp = this.classes.trim().split(" ");
	        for (var ck in lp) {
	          list[lp[ck]] = true;
	        };
	        return list;
	    },
    	btnClick: function() {
    		if (this.action[0] == undefined) {
    			this.action = [this.action];
    		};
    		res = {};
    		var stopper = false;
    		for (var ak in this.action) {
    			
    			if (stopper) {break;};

    			var act = this.action[ak];
	    		switch (act.type) {
					case "href": //Ссылка
						$vm = this;
						href = act.href;
						var vrs = href.match(/\{(.*?)\}/g);
						for (var vk in vrs) {
							val = vrs[vk].replace("{","").replace("}","");
							if (val[0] == '#') {
								valse = val.substr(1);
								val = res;

								valse = valse.split(".");
								
								for (var valk in valse) {
									if(valse[valk] == "") {continue;};
									val = val[valse[valk]];
								};
								href = href.replace(vrs[vk],val);
							} else {
								vmres = $vm.$root;

								val = val.split(".");

								for (var valk in val) {
									if(val[valk] == "") {continue;};
									vmres = vmres[val[valk]];
								};

								href = href.replace(vrs[vk],vmres);
							};
							
						};
						location.href = href;
					break;

					case "handler": //Выполнить какой-либо хендлер
						this.state = 2;
						var $bvm = this;
						for (var kk in act.data) {
							var kval = act.data[kk];
							if (kval[0] == "$") {
								kdat = this.$parent;
								link = kval.slice(1).split(".");
								for (var k in link) {
									kdat = kdat[link[k]];
								};
								act.data[kk] = kdat;
							};
						};
						res = sendToHandler(act.handler,act.data);
						if (res.error == undefined) {
							res = res.result;
							setTimeout(function(){
								$bvm.state = 3;
							},100);
							setTimeout(function(){
								$bvm.state = 1;
							},2400);
						} else {
							setTimeout(function(){
								$bvm.state = 4;
							},400);
							setTimeout(function(){
								$bvm.state = 1;
							},2400);
							console.log("Произошла ошибка при отправке: "+res.error);
							var stopper = true;
							res = res.result;
						};
					break;
				};
			};
    	}
    }
  }
</script>