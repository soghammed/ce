<template>
	<div>

		<div class="header">
			<h1>Transactions </h1>
			<input type="text" class="form-control search" title="Search by First Name / Last Name / Amount / Transaction Date" placeholder="Search by First Name / Last Name / Amount / Transaction Date" v-model="transaction.q" v-on:keyup="fetchTransactions('transactions?q='+transaction.q)"/>
			<button type="button" class="btn btn-small btn-dark new-trigger" data-toggle="modal" data-target="#new-transaction" @click="clearTransaction">
			  New Transaction
			</button>
		</div>

		<div v-if="transactions.length">
			<nav aria-label="Page navigation example">
			  <ul class="pagination">
			    <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
			    	<a class="page-link" href="#" @click="fetchTransactions(pagination.prev_page_url)">Previous</a>
			    </li>

		        <li class="page-item disabled">
		        	<a class="page-link text-dark" href="#">Page {{pagination.current_page}} of {{ pagination.last_page }}</a>
		        </li>

			    <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
			    	<a class="page-link" href="#" @click="fetchTransactions(pagination.next_page_url)">Next</a>
			    </li>
			  </ul>
			</nav>
		</div>

		<div class="data-list">
			<div class="card card-body mb-2" v-for="transaction in transactions" v-bind:key="transaction.id">
				<hr/>
				<h5>
					<div v-if="transaction.client_data">{{transaction.client_data.first_name}} {{transaction.client_data.last_name}}</div>
					<div v-else>Client Removed</div>
				</h5>
				<h1><b>£{{transaction.amount}}</b></h1>
				<h5>ON</h5>
				<h4><b>{{transaction.transaction_date}}</b></h4>
				<hr/>
				<div class="a-group">
					<button class="btn btn-small btn-warning" @click="editTransaction(transaction)" data-toggle="modal" data-target="#new-transaction">Edit</button>
					<button class="btn btn-small btn-danger" @click="deleteTransaction(transaction.id)">X</button>
				</div>
			</div> 
		</div>

		<div v-if="transactions.length">
			<nav aria-label="Page navigation example">
			  <ul class="pagination">
			    <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
			    	<a class="page-link" href="#" @click="fetchTransactions(pagination.prev_page_url)">Previous</a>
			    </li>

		        <li class="page-item disabled">
		        	<a class="page-link text-dark" href="#">Page {{pagination.current_page}} of {{ pagination.last_page }}</a>
		        </li>

			    <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
			    	<a class="page-link" href="#" @click="fetchTransactions(pagination.next_page_url)">Next</a>
			    </li>
			  </ul>
			</nav>
		</div>

		<!-- new transaction -->
		<div class="modal" id="new-transaction" tabindex="-1">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">New transaction</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
				<form @submit.prevent="addTransaction" class="mb-3">
					<div class="form-group">
						<select class='form-control' @change="handleClient" id="client-list" required>
							<option value=''>Select a client</option>
							<option :value="client.id" v-for="client in clients" v-bind:key="client.id" v-model="transaction.client">{{ client.first_name }} {{ client.last_name}}</option>
						</select>
					</div>
					<div class="form-group">
						<input type="date" class="form-control" placeholder="Transaction Date" v-model="transaction.transaction_date" required/>
					</div>
					<div class="form-group">
						<input type="number" class="form-control" placeholder="Amount" v-model="transaction.amount" required/>
					</div>
					<button type="submit" class="btn btn-secondary btn-block">Save</button>
			        <button type="button" id="t-modal-close" class="btn btn-light btn-block" data-dismiss="modal">Close</button>
				</form>
		      </div>
		      <!-- <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary">Save changes</button>
		      </div>-->
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
				clients:[],
				transactions: [],
				transaction:{
					id:'',
					client:'',
					amount:'',
					transaction_date:'',
					q:''
				},
				transaction_id:'',
				pagination:{},
				edit:false
			};
		},
		created(){
			this.fetchTransactions();
			this.fetchAllClients();
		},
		methods:{
			handleClient(e){
				this.transaction.client = e.target.value;
			},
			fetchAllClients(){
				fetch('transaction/clients')
					.then(res => res.json())
					.then( res => {
						this.clients = res;
					})
					.catch(err => console.log(err));
			},
			clearTransaction(){
				this.transaction = {
					id:'',
					client:'',
					amount:'',
					transaction_date:'',
					q:''
				}
				document.querySelector('#client-list').value = '';
			},
			fetchTransactions(url){
				let vm = this;
				url = url || 'transactions';
				fetch(url)
					.then(res => res.json())
					.then(res => {
						this.transactions = res.data;
						vm.paginate(res);
					})
					.catch(err => console.log(err));

			},
			addTransaction(){
				let url = this.edit ? 'transactions' : 'transaction';
				fetch(url, {
					method:'post',
					body:JSON.stringify(this.transaction),
					headers: {
						'content-type': 'application/json',
					    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				})
				.then( res => res.json())
				.then( res => {
					this.clearTransaction();
					if(this.edit){
						alert('Transaction updated')
						this.edit = false;
					}else{
						alert('Transaction Added');
					}
					document.querySelector('#t-modal-close').click();
					this.fetchTransactions();
				})
				.catch(err => console.log(err));
			},
			editTransaction(transaction){
				this.edit = true;
				this.transaction.id = transaction.id;
				this.transaction.transaction_id = transaction.id;
				this.transaction.client = transaction.client;
				document.querySelector('#client-list').value = transaction.client;
				this.transaction.transaction_date = transaction.transaction_date;
				this.transaction.amount = transaction.amount;
			},
			deleteTransaction(id){
				if(confirm('Are you sure?')){
					fetch(`transaction/${id}`, {
						method: 'delete',
						headers: {
						    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					})
					.then( res => res.json())
					.then( res => {
						alert('Transaction deleted');
						this.fetchTransactions();
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