<template>
	<div class="col-md-6">
		<div class="layers bd bgc-white p-20" style="align-items: flex-start;">
			<h4 v-if="label">{{label}}</h4>
			<div class="table-container" style="width:100%">
				<table class="table table-bordered" style="width:100%">
					<tr>
						<th v-for="column in columns">
							{{column.name}}
						</th>
						<th v-if="buttons"></th>
					</tr>
					<tr v-if="data.length > 0" v-for="row in filteredList">
						<td v-for="column in columns" :class='isMinClass(column)'>
							<span v-if="!column.type || column.type == 'text'" v-bind:style="column.style">{{getColumnData(row,column.key)}}</span>
							<img v-if="column.type == 'image'" v-bind:style="column.style" v-bind:src="row[column.key]" alt=""/>
						</td>
						<td v-if="buttons" class="w-min">
							<div class="dropdown no-arrow">
							  <button type="button" class="btn btn-circle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    <i class="fa fa-ellipsis-h"></i>
							  </button>
							  <div class="dropdown-menu">
							    <a class="dropdown-item" :class="'btn-'+button.type" v-for="button in buttons" v-bind:style="button.style" @click="buttonClick(button,row)">
									<span v-if="button.view == 'text' || !button.view">{{button.title}}</span>
									<i v-if="button.view == 'fa-icon'" v-bind:class="button.class"></i>
								</a>
							  </div>
							</div>
						</td>
					</tr>
				</table>
				<div v-if="!data" class="alert alert-light text-center" role="alert">
				  Нет данных
				</div>
			</div>
		</div>
	</div>
</template>

<style>
	.dropdown-toggle.btn-circle:after {
		display:none!important;
	}
	.btn-circle {
		font-size:1.2em;
		color:grey;
		border-radius:30px;
	}
	.dropdown-menu.show {
		left: -64px!important;
	}
</style>

<script>
	module.exports = {
		props: ['data','columns','buttons','create_link','label'],

		data: function(){
			return {
				search:"",
				selected:[],
				changables:[],
				pagination: {perpage:5,current:1,showed:0}
			};
		},

		methods: {
			isMinClass:function(column) {
				cls = {};
				if (column.type == 'image') {cls["w-min"] = true;};
				return cls;
			},
			selectAll:function(event) {
				this.selected = [];
				if (event.target.checked) {
					for (var k in this.data) {
						this.selected.push(this.data[k].id);
					};
				};
			},
			getColumnData:function(data,key) {
				var alg = key.split(".");
				var res = data;
				for (var kalg in alg) {
					var res = res[alg[kalg]];
				};
				return res;
			},
			buttonClick:function(button,row) {
				switch (button.action) {
					case "href": //Ссылка
						href = button.href;
						var vrs = href.match(/\{(.*?)\}/g);
						for (var vk in vrs) {
							val = vrs[vk].replace("{","").replace("}","");
							href = href.replace(vrs[vk],row[val]);
						};
						location.href = href;
					break;

					case "remove": //Удалить набор данных
						res = sendToHandler("ds-remove",row).result;
						if (res) {
							var rend = Array();
							for (var dk in this.data) {
								if (this.data[dk].id !== row.id) {
									rend.push(this.data[dk]);
								};
							};
							this.data = rend;
						} else {
							alert("Произошла ошибка при удалении");
						};
					break;
				};
			},
			filteredData() {
				if (this.data.length == 0) { return []; };
		    	var search = this.search;
		    	var columns = this.columns;
		    	if (search == "") {
		    		return this.data;
		    	};
			    list = this.data.filter(function(row) {
			    	has = false;
			    	for (var kr in columns) {
			    		var key = columns[kr].key;
			    		var alg = key.split(".");
						var res = row;
						for (var kalg in alg) {
							var res = res[alg[kalg]];
						};
						if (typeof(res) == "string") {
			    			if (res.toLowerCase().includes(search.toLowerCase())) {
				    			has = true;
				    			break;
				    		};
			    		};
			    	};
			    	return has;
			    });
		    	return list;
		    },
		    changePage: function(num) {
		    	if (num == "+") {
		    		if (this.pagination.current < Math.ceil(this.filteredData().length/this.pagination.perpage)) {
		    			this.pagination.current++;
		    			return true;
		    		};
		    	};
		    	if (num == "-") {
		    		if (this.pagination.current > 1) {
		    			this.pagination.current--;
		    			return true;
		    		};
		    	};
		    },
		    isPage: function(type) {
		    	if (type == "first") {
		    		if (this.pagination.current == 1) {
		    			return true;
		    		} else {
		    			return false;
		    		};
		    	} else if (type == "last") {
		    		if (this.pagination.current == Math.ceil(this.filteredData().length/this.pagination.perpage)) {
		    			return true;
		    		} else {
		    			return false;
		    		};
		    	};
		    },

		    removeSelected: function() {
		    	if (this.selected.length > 0) {
		    		for (var k in this.selected) {
		    			sendToHandler("ds-remove",{id:this.selected[k]});
		    		};
		    	
					var rend = Array();
					for (var dk in this.data) {
						if (this.selected.indexOf(this.data[dk].id) < 0) {
							rend.push(this.data[dk]);
						};
					};

					this.data = rend;
				};
		    }
		},
		computed: {
			pagesList() {
				res = [];
				for (i = 1; i<= Math.ceil(this.filteredData().length/this.pagination.perpage); i++) {
					resl = {num:i};
					if (i == this.pagination.current) {resl.current = true;};
					res.push(resl);
				};
				console.log(this.data);
				return res;
			},
		    filteredList() {
		    	if (this.data.length == 0) { return []; };
		    	var search = this.search;
		    	var columns = this.columns;
		    	if (search == "") {
		    		list = this.data;
		    	} else {
				    list = this.data.filter(function(row) {
				    	has = false;
				    	for (var kr in columns) {
				    		var key = columns[kr].key;
				    		var alg = key.split(".");
							var res = row;
							for (var kalg in alg) {
								var res = res[alg[kalg]];
							};
							if (typeof(res) == "string") {
				    			if (res.toLowerCase().includes(search.toLowerCase())) {
					    			has = true;
					    			break;
					    		};
				    		};
				    	};
				    	return has;
				    });
				}; 
	    		var start = this.pagination.perpage*(this.pagination.current-1);
	    		this.pagination.showed = 0;
	    		var endlist = [];
	    		for (var key in list) {
	    			if (this.pagination.showed == this.pagination.perpage) {break;};
	    			if (key >= start) {
	    				endlist.push(list[key]);
	    				this.pagination.showed++;
	    			};
	    		};
	    		return endlist;
		    }
		}
	}
</script>

<style>
	th.w-min, td.w-min {
		width:1px;
	}
</style>