<template>
<div class="large-12 medium-12 small-12 column">
	<div class="secondary callout"><h3>Laravel Foundation Vue.js Axios and Toastr</h3></div>
	<div class="secondary callout"><a data-open="create-item" class="success button">Create Item</a></div>
	<table class="hover">
		<thead>
			<tr>
				<th>Title</th>
				<th>Description</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="item in items">
				<td>{{ item.title }}</td>
				<td>{{ item.description }}</td>
				<td><a data-open="edit-item" class="button" @click.prevent="editItem(item)">Edit</a><div role="button" class="alert button" v-on:click.prevent="deleteItem(item)">Delete</div></td>
			</tr>
		</tbody>
	</table>
	<!-- Pagination -->
	<ul class="pagination text-center" role="navigation" aria-label="Pagination">
		<li><a href="#" @click.prevent="changePage(pagination.current_page - 1)" v-bind:class="[pagination.current_page > 1 ? '': 'disabled']">Previous</a></li>
		<li v-for="page in pagesNumber" v-bind:class="[page == isActived ? 'current' : '']">
			<a href="#" v-on:click.prevent="changePage(page)">{{ page }}</a>
		</li>
		<li><a href="#" aria-label="Next page" @click.prevent="changePage(pagination.current_page + 1)" v-bind:class="[pagination.current_page < pagination.last_page ? '': 'disabled']">Next</a></li>
	</ul>
	<!-- Create Item Modal -->
	<div class="reveal" id="create-item" data-reveal>
		<form method="POST" enctype="multipart/form-data" @submit.prevent="createItem">
			<!-- Item -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Title*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="title" placeholder="Title" v-model="newItem.title" />
					</div>
				</div>
				<span v-for="error in formErrors['title']" v-if="formErrors['title']" class="error">{{ error }}</span>
			</div>
			<!-- Description -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Description*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="description" placeholder="Description" v-model="newItem.description" />
					</div>
				</div>
				<span v-for="error in formErrors['description']" v-if="formErrors['description']" class="error">{{ error }}</span>
			</div>
			<input type="submit" value="Create" class="expanded button" />
		</form>
		<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<!-- Edit Item Modal -->
	<div class="reveal" id="edit-item" data-reveal>
		<form method="POST" enctype="multipart/form-data" @submit.prevent="updateItem(fillItem.id)">
			<input name="_method" type="hidden" value="PUT">
			<!-- Item -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Title*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="title" v-model="fillItem.title" />
					</div>
				</div>
				<span v-for="error in formErrorsUpdate['title']" v-if="formErrorsUpdate['title']" class="error">{{ error }}</span>
			</div>
			<!-- Description -->
			<div class="large-12 medium-12 small-12 columns">
				<div class="row collapse prefix-radius">
					<div class="medium-3 columns">
						<span class="prefix"><strong>Description*</strong></span>
					</div>
					<div class="medium-9 column">
						<input type="text" name="description" placeholder="Description" v-model="fillItem.description" />
					</div>
				</div>
				<span v-for="error in formErrorsUpdate['description']" v-if="formErrorsUpdate['description']" class="error">{{ error }}</span>
			</div>
			<input type="submit" value="Update" class="expanded button" />
		</form>
		<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
</template>

<script>
	export default {
		data: () => ({
			items: [],
			pagination: {
				total: 0, 
				per_page: 2,
				from: 1, 
				to: 0,
				current_page: 1
			},
			offset: 4,
			formErrors:{},
			formErrorsUpdate:{},
			newItem : {'title':'','description':''},
			fillItem : {'title':'','description':'','id':''}
		}),

		computed: {
			isActived: function () {
				return this.pagination.current_page;
			},
			pagesNumber: function () {
				if (!this.pagination.to) {
					return [];
				}
				var from = this.pagination.current_page - this.offset;
				if (from < 1) {
					from = 1;
				}
				var to = from + (this.offset * 2);
				if (to >= this.pagination.last_page) {
					to = this.pagination.last_page;
				}
				var pagesArray = [];
				while (from <= to) {
					pagesArray.push(from);
					from++;
				}
				return pagesArray;
			}
		},

		mounted : function(){
			this.getVueItems(this.pagination.current_page);
		},

		methods : {

			getVueItems: function(page){
				axios.get('/laravel-vue-foundation-simple-CRUD/public/vueitems?page='+page).then(response => {
					this.items = response.data.data.data,
					this.pagination = response.data.pagination
				});
			},

			createItem: function(){
				var input = this.newItem;
				axios.post('/laravel-vue-foundation-simple-CRUD/public/vueitems',input).then((response) => {
					this.changePage(this.pagination.current_page);
					this.newItem = {'title':'','description':''};
					$('#create-item').foundation('close');
					toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
				}).catch((error) => {
					this.formErrors = error.response.data;
				});
			},

			deleteItem: function(item){
				axios.delete('/laravel-vue-foundation-simple-CRUD/public/vueitems/'+item.id).then((response) => {
					this.changePage(this.pagination.current_page);
					toastr.error('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
				});
			},

			editItem: function(item){
				this.fillItem.title = item.title;
				this.fillItem.id = item.id;
				this.fillItem.description = item.description;
				$('#edit-item').foundation('open');
			},

			updateItem: function(id){
				var input = this.fillItem;
				axios.put('/laravel-vue-foundation-simple-CRUD/public/vueitems/'+id,input).then((response) => {
					this.changePage(this.pagination.current_page);
					this.fillItem = {'title':'','description':'','id':''};
					$("#edit-item").foundation('close');
					toastr.info('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
				}, (error) => {
					this.formErrorsUpdate = error.response.data;
				});
			},

			changePage: function (page) {
				this.pagination.current_page = page;
				this.getVueItems(page);
			}
		}
	}
</script>
