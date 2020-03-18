<template>
	<div class="div">
		<div class="table-container">
			<div class="row">
				<div class="col-md-9 d-flex align-items-end">
					<div class="form-group mr-3">
					    <small>Отображать по</small>
					    <select v-model="pagination.perpage" class="form-control form-control-sm">
					      <option>5</option>
					      <option>10</option>
					      <option>25</option>
					      <option>50</option>
					      <option>100</option>
					    </select>
					</div>
					<div class="input-group w-auto mb-3">
						<input type="text" v-model="search" @input="pagination.current=1" class="form-control form-control-sm" placeholder="Поиск по таблице">
						<div class="input-group-append">
							<button class="btn btn-outline-secondary btn-sm" type="button"><i class="fa fa-search"></i></button>
						</div>
					</div>
				</div>
				<div class="col-md-3 d-flex justify-content-end pb-3">
					<a v-if="create_link" :href="clickCreate(create_link)" class="btn btn-primary btn-sm mt-4 mr-2" style="color:white; cursor:pointer">
					    Создать
					</a>
					<div v-if="!disableremove" class="btn-group pt-4">
					  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    Действия
					  </button>
					  <div class="dropdown-menu">
					    <a class="dropdown-item" href="javascript:void(0)" @click="removeSelected">Удалить</a>
					  </div>
					</div>
				</div>
			</div>
			<div class="table-over" style="width:100%; overflow:auto;">
				<table class="table table-bordered">
					<tr>
						<th class="w-min">
							<div class="custom-control custom-checkbox">
							    <input type="checkbox" class="custom-control-input" @change="selectAll" id="row-select-all">
							    <label class="custom-control-label" for="row-select-all"></label>
						    </div>
						</th>
						<th v-for="column in columns" @click="setFilter(column.key,column.nofilter)" style="cursor:pointer;">
							{{column.name}}
							<span v-if="!column.nofilter">
								<i v-if="(column.key == filtercolumn) && (!filterinverse)" class="fas fa-sort-desc"></i>
								<i v-else-if="(column.key == filtercolumn) && (filterinverse)" class="fas fa-sort-asc"></i>
								<i v-else class="fas fa-sort" style="opacity:.1;"></i>
							</span>
						</th>
						<th v-if="buttons"></th>
					</tr>
					<tr v-if="data.length > 0" v-for="row in filteredList">
						<td class="w-min">
							<div class="custom-control custom-checkbox">
							    <input v-model="selected" :value="row.id" type="checkbox" class="custom-control-input" :id="'row-select-'+row.id">
							    <label class="custom-control-label" :for="'row-select-'+row.id"></label>
						    </div>
						</td>
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
			</div>
			<div v-if="!data" class="alert alert-light text-center" role="alert">
			  Нет данных
			</div>
			<div class="row">
				<div class="col-md-6">Показано {{pagination.showed}} из {{data.length}} элементов.</div>
				<div class="col-md-6 d-flex justify-content-end">
					<nav aria-label="Page navigation example">
					  <ul class="pagination">
					    <li class="page-item" :class="{disabled:isPage('first')}" @click="changePage('-')"><a class="page-link" href="javascript:void(0)">Назад</a></li>
					    <li v-for="page in pagesList" :key="page.num" :class="{active:page.current}" class="page-item"><a class="page-link" href="javascript:void(0)" @click="pagination.current = page.num">{{page.num}}</a></li>
					    <li class="page-item" :class="{disabled:isPage('last')}" @click="changePage('+')"><a class="page-link" href="javascript:void(0)">Далее</a></li>
					  </ul>
					</nav>
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
		props: {
			data:{},
			columns:{},
			buttons:{},
			create_link:{},
			disableremove:{default:false}
		},

		data: function(){
			return {
				search:"",
				selected:[],
				changables:[],
				pagination: {perpage:25,current:1,showed:0},
				filtercolumn: "id",
				filterinverse:false
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
			clickCreate:function(href) {
				var vrs = href.match(/\{(.*?)\}/g);
				for (var vk in vrs) {
					val = vrs[vk].replace("{","").replace("}","");
					vmres = this.$root;
					val = val.split(".");
					for (var valk in val) {
						if(val[valk] == "") {continue;};
						vmres = vmres[val[valk]];
					};
					href = href.replace(vrs[vk],vmres);
				};
				return href;
			},
			buttonClick:function(button,row) {
				switch (button.action) {
					case "href": //Ссылка
						href = button.href;

						var vrs = href.match(/\{(.*?)\}/g);
						for (var vk in vrs) {
							val = vrs[vk].replace("{","").replace("}","");
							if (val[0] == '$') {
								val = val.substr(1);
								vmres = this.$root;
								val = val.split(".");

								for (var valk in val) {
									if(val[valk] == "") {continue;};
									vmres = vmres[val[valk]];
								};
								href = href.replace(vrs[vk],vmres);
							} else {
								href = href.replace(vrs[vk],row[val]);
							};
						};

						location.href = href;
					break;

					case "remove": //Удалить набор данных
						if (confirm("Вы действительно хотите удалить?")) {
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
						};
					break;

					case "remove_usr": //Удалить набор данных
						if (confirm("Вы действительно хотите удалить?")) {
							res = sendToHandler("usr-remove",row);
							if (res.result) {
								var rend = Array();
								for (var dk in this.data) {
									if (this.data[dk].id !== row.id) {
										rend.push(this.data[dk]);
									};
								};
								this.data = rend;
							} else {
								alert("Произошла ошибка при удалении");
								console.log("Ошибка: ",res.error);
							};
						};
					break;
					case "remove_priority": //Удалить набор данных
						if (confirm("Вы действительно хотите удалить?")) {
							console.log(row);
							
							res = sendToHandler("priority-remove",row);
							if (res.result) {
								var rend = Array();
								for (var dk in this.data) {
									if (this.data[dk].id !== row.id) {
										rend.push(this.data[dk]);
									};
								};
								this.data = rend;
							} else {
								alert("Произошла ошибка при удалении");
								console.log("Ошибка: ",res.error);
							};
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
		    		if (confirm("Вы действительно хотите удалить?")) {
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
				};
		    },
		    setFilter: function(key,no) {
		    	if (!no) {
		    		if (this.filtercolumn == key) {
			    		this.filterinverse = !this.filterinverse;
			    	} else {
			    		this.filtercolumn = key;
			    		this.filterinverse =false;
			    	}
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
	    		var $dtb = this;
	    		endlist.sort(function(a,b) {

	    			let pl = $dtb.filtercolumn.split(".");
		    		let cA = a;
		    		let cB = b;
		    		for (var l in pl) {
		    			if (!cA[pl[l]]) {cA = false; break;};
		    			cA = cA[pl[l]];
		    			if (cA.constructor === Array) {if (!cA.length) {cA = false; break;}}
		    		};

		    		for (var l in pl) {
		    			if (!cB[pl[l]]) {cB = false; break;};
		    			cB = cB[pl[l]];
		    			if (cB.constructor === Array) {if (!cB.length) {cB = false; break;}}
		    		};

		    		if (typeof(cA) == "string") {
		    			cA = cA.toLowerCase();
		    			cB = cB.toLowerCase();

		    			if (cA < cB) {return -1;};
						if (cA > cB) {return 1;};
		    		} else if ((typeof(cA) == "integer") || (typeof(cA) == "float")) {
		    			return cA - cB;
		    		};
	    			
					return 0
	    		});

	    		if (this.filterinverse) {
	    			endlist.reverse();
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