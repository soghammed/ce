<template>
	<div>
		<div class="header">
			<h1>Clients </h1>
			<button type="button" class="btn btn-small btn-dark" data-toggle="modal" data-target="#new-client" @click="clearClient">
			  New Client
			</button>
		</div>
		<nav aria-label="Page navigation example">
		  <ul class="pagination">
		    <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
		    	<a class="page-link" href="#" @click="fetchClients(pagination.prev_page_url)">Previous</a>
		    </li>

	        <li class="page-item disabled">
	        	<a class="page-link text-dark" href="#">Page {{pagination.current_page}} of {{ pagination.last_page }}</a>
	        </li>

		    <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
		    	<a class="page-link" href="#" @click="fetchClients(pagination.next_page_url)">Next</a>
		    </li>
		  </ul>
		</nav>
		<div class="data-list">
			<div class="card card-body mb-2" v-for="client in clients" v-bind:key="client.id">
				<hr/>
				<h3>{{client.first_name}} {{client.last_name}}</h3>
				<img v-bind:src="client.avatar" class="avatar"/>
				<p>{{client.email}}</p>
				<hr/>
				<div class="a-group">
					<button class="btn btn-small btn-warning" @click="editClient(client)" data-toggle="modal" data-target="#new-client">Edit</button>
					<button class="btn btn-small btn-danger" @click="deleteClient(client.id)">Delete</button>
				</div>
			</div> 
		</div>
		<div class="modal" id="new-client" tabindex="-1">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">New transaction</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
				<form @submit.prevent="addClient" class="mb-3">
					<div class="form-group">
						<input type="text" class="form-control" placeholder="First Name" v-model="client.first_name" required/>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Last Name" v-model="client.last_name" required/>
					</div>
					<div class="form-group">
						<input type="file" ref="avatarFile" class="form-control" placeholder="Avatar" v-on:change="handleAvatar"/>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Email" v-model="client.email"/>
					</div>
					<button type="submit" class="btn btn-secondary btn-block">Save</button>
			        <button type="button" id="c-modal-close" class="btn btn-light btn-block" data-dismiss="modal">Close</button>
				</form>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['colors'],
		data(){
			return {
				clients: [],
				client:{
					id:'',
					first_name:'',
					last_name:'',
					avatar:'',
					email:'',
					temp:''
				},
				client_id:'',
				pagination:{},
				edit:false
			};
		},
		created(){
			this.fetchClients();
		},
		methods:{
			handleAvatar(e){
				this.client.avatar = e.target.files[0];
			},
			clearClient(){
				this.client.first_name = ''
				this.client.last_name = ''
				this.client.email = ''
				this.client.avatar = ''
				this.$refs.avatarFile.value = '';
			},
			fetchClients(url){
				let vm = this;
				url = url || 'clients';
				fetch(url)
					.then(res => res.json())
					.then(res => {
						this.clients = res.data;
						vm.paginate(res);
					})
					.catch(err => console.log(err));

			},
			addClient(){
				//add or update;
				let formData = new FormData();
				formData.set("first_name",this.client.first_name);
				formData.set("last_name", this.client.last_name);
				formData.set("email", this.client.email);
				formData.set("client_id", this.client.client_id);
				formData.set("id", this.client.id);
				formData.set("avatar", this.client.avatar);
				let url = this.edit ? 'clients' : 'client';
				fetch(url, {
					//using post as put caused issues.
					method:'post',
					body:formData,
					headers: {
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				})
				.then( res => res.json())
				.then( res => {
					this.clearClient();
					if(this.edit){
						alert('Client updated');
						this.edit = false;
					}else{
						alert('Client added');
					}
					this.fetchClients();
					document.querySelector('#c-modal-close').click();
				})
				.catch( err => console.log(err));
			},
			editClient(client){
				this.edit = true;
				this.client.id = client.id;
				this.client.client_id = client.id;
				this.client.first_name = client.first_name;
				this.client.last_name = client.last_name;
				this.client.email = client.email;
				this.client.avatar = client.avatar;
			},
			deleteClient(id){
				if(confirm('Are you sure?')){
					fetch(`client/${id}`, {
						method: 'delete',
						headers: {
						    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					})
					.then( res => res.json())
					.then( res => {
						alert('Client deleted');
						this.fetchClients();
					})
					.catch(err => console.log(err));
				}
			},
			paginate(res){
				let pagination = {
					current_page: res.current_page,
					last_page: res.last_page,
					next_page_url: res.next_page_url,
					prev_page_url: res.prev_page_url
				}
				this.pagination = pagination;
			}
		}
	}
</script>

<style>
	.avatar{
		margin:10px auto;
		width:100px;
		height:100px;
		border-radius:50px;
	}
</style>