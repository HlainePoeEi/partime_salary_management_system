<template>
	<div>
		<!-- nav bar -->
		<nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding-left: 100px;">
			<div class="col-md-10">
				<a class="navbar-brand" :href="'/home'">Part Time Salary Management System</a>
			</div>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" :href="'/history'">History Data</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{{ user }}
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
						<a class="dropdown-item" @click="logout()" :href="'/'">Logout</a>
						</div>
					</li>
				</ul>
			</div>
		</nav>
		<!-- nav bar -->
		
		<div class="container-fluid" style="margin-top: 30px;">
			<div class="row justify-content-center">
				<div class="col-md-10"> <!-- {{selected}} -->
					<div class="table-responsive">
						<table style="margin-top:20px;" class="table table-bordered text-center">
						<thead>
							<tr>
							<th>No</th>
							<th>ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Date</th>
							</tr>
						</thead>
						<tbody v-if="users && users.data.length > 0">
							<tr v-for="(user, index) in users.data" :key="index" :id="user.employee_name" >
							<td>{{ index+num+1 }}</td>
							<td>{{ user.employee_id }}</td>
							<td id="emp_name">{{ user.employee_name }}</td>
							<td id="emp_email">{{ user.employee_email }}</td>
							<td>{{ moment(user.created_at).format("DD-MM-YYYY") }}</td>
							</tr>
						</tbody>
						<tbody v-else>
							<tr>
							<td align="center" colspan="3">No record found.</td>
							</tr>
						</tbody>	
						</table>
					</div>
					<pagination align="left" :data="users" @pagination-change-page="list">
						<template #prev-nav>
							<span>&lt;&lt; </span>
						</template>
						<template #next-nav>
							<span> &gt;&gt;</span>
						</template>
					</pagination>
					<!-- <a id="deleteData" class="btn" @click="deleteData" style="margin-bottom: -5px;">Delete All Data</a> -->
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import pagination from 'laravel-vue-pagination';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import axios from 'axios';
import moment from "moment";
    export default {
		
		 components: {
			    "font-awesome": FontAwesomeIcon,
          	    pagination,
				
    },
  data() {
    return {
		filename:"no file choosen",
		selectAll: false,
		userEmails: [],
		users: null,
		isLoading: false,
		user:null,
        moment: moment,
		num: 1,
		};
	},
		mounted() {
			this.user = this.$userName;
			this.list();
		},
		methods: {
			async list(page = 1) {
			await axios
				.get(`/api/historyapi?page=${page}`)
				.then(({ data }) => {
				this.num = (page-1)*10;
				this.users = data;
				})
				.catch(({ response }) => {
				console.error(response);
				});
			},
			// deleteData() {
			// 	let formData = new FormData();
			// 	 formData.append('selectedItems',this.selected);
			// 	 this.selected = [];
			// 	 axios
            //         .get('/api/delete_historyapi',formData)
            //         .then( response => {
			// 			console.log(response);
			// 			this.list();
			// 			if(response.data.code == "error"){
            //             this.showAlert(response.data.message,"error");
            //             }
			// 			else{
            //                 this.showAlert(response.data.message,"success");
            //             }
			// 		})
            //         .catch(err => {
			// 			console.log(err);
			// 			this.list();
			// 			this.showAlert("Delete Fail!","error");
			// 		})
        	// },
			logout(){
               axios.post('logout').then(response => {
					if (response.status === 302 || 401) {
							// this.login();
							console.log('logout');
						}
						else {
							// throw error and go to catch block
						}
						}).catch(error => {

					});
				},
		}
    }
</script>
<style>
.container{
	background-color: #f5f5f0;
}
#emp_name,#emp_email{

    text-align: left;

}
/* #deleteData {
	text-decoration: none;
	float: right;
	color: white;
	background-color: rgb(240, 35, 7);
	padding: 5px;
	margin-left: 5px;
	margin-bottom: 10px;
} */
</style>
