<template>
	<div>

		<div class="header">
			<h1>Transactions </h1>
			<button type="button" class="btn btn-small btn-dark" data-toggle="modal" data-target="#new-transaction" @click="clearTransaction">
			  New Transaction
			</button>
		</div>

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

		<div class="data-list">
			<div class="card card-body mb-2" v-for="transaction in transactions" v-bind:key="transaction.id">
				<hr/>
				<h5>Client {{transaction.client}}</h5>
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
						<select class='form-control' @change="handleClient" id="client-list">
							<option value=''>Select a client</option>
							<option :value="client.id" v-for="client in clients" v-bind:key="client.id" v-model="transaction.client">{{ client.first_name }} {{ client.last_name}}</option>
						</select>
					</div>
					<div class="form-group">
						<input type="date" class="form-control" placeholder="Transaction Date" v-model="transaction.transaction_date"/>
					</div>
					<div class="form-group">
						<input type="number" class="form-control" placeholder="Amount" v-model="transaction.amount"/>
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
				colorArray: ['#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6', 
					'#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
					'#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A', 
					'#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
					'#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC', 
					'#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
					'#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680', 
					'#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
					'#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3', 
					'#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'
				],
				clients:[],
				transactions: [],
				transaction:{
					id:'',
					client:'',
					amount:'',
					transaction_date:'',
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
					transaction_date:''	
				}
				document.querySelector('#client-list').value = '';
			},
			fetchTransactions(url){
				let vm = this;
				url = url || 'transactions';
				fetch(url)
					.then(res => res.json())
					.then(res => {
						console.log(res);
						this.transactions = res.data;
						vm.paginate(res);
					})
					.catch(err => console.log(err));

			},
			addTransaction(){
				let url = this.edit ? 'transactions' : 'transaction';
				fetch(url, {
					//using post as put caused issues.
					method:'post',
					body:JSON.stringify(this.transaction),
					headers: {
						'content-type': 'application/json'
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
						method: 'delete'
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