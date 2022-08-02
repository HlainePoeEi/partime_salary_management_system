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

		<!-- to show loading after submit button -->
		<Loading :active.sync="isLoading" :is-full-page="true"/>
		<!-- to show loading after submit button -->

		<div class="container-fluid" style="margin-top: 5px;">
			<div class="row justify-content-center">
				<div class="col-md-10"> <!-- {{selected}} -->
					<div id="inputFile" class="row">
							<input ref="file" type="file" @change="onFilePicked" hidden>
							<button @click="selectFile()" id="fileSelectBtn">Choose File</button>&nbsp; {{ filename }}
					</div>
					<div class="row">
							<button id="uploadExcel" @click="uplodFile" :disabled="image == null">
							<font-awesome :icon="['fas','upload']"/>&nbsp;
							 Upload Excel
							</button>
					</div>
					<div class="table-responsive">
						<table style="margin-top:20px;" class="table table-bordered text-center">
						<thead>
							<tr>
							<th></th>
							<th>No</th>
							<th>ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Action</th>
							</tr>
						</thead>
						<tbody v-if="users && users.data.length > 0">
							<tr v-for="(user, index) in users.data" :key="index" :id="user.employee_name" >
							<td><input type="checkbox" v-model="selected" :value="user.id"></td>
							<td>{{ index+num+1 }}</td>
							<td>{{ user.employee_id }}</td>
							<td id="emp_name">{{ user.employee_name }}</td>
							<td id="emp_email">{{ user.employee_email }}</td>
							<td>
								<a :href="'/pdf_preview/' + user.id" class="btn btn-success btn-sm">Preview</a>
								<a :href="'/edit/' + user.id" class="btn btn-warning btn-sm">Update</a>
							</td>
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
					<a id="deleteData" class="btn" @click="deleteData" style="margin-bottom: -5px;">Delete Data</a>	
					<a id="sendEmail" class="btn" @click="sendEmail" style="margin-bottom: -5px;">Send Email</a>			
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import pagination from 'laravel-vue-pagination';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
		
		 components: {
			    "font-awesome": FontAwesomeIcon,
          	    pagination,		
				Loading,  
    },
  data() {
    return {
		filename:"no file choosen",
		image:null,
		selected: [],
		selectAll: false,
		userEmails: [],
		users: null,
		isLoading: false,
		user:null,
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
				.get(`/api/employeeapi?page=${page}`)
				.then(({ data }) => {
				this.num = (page-1)*10;
				this.users = data;
				})
				.catch(({ response }) => {
				console.error(response);
				});
			},
			 sendEmail() {
				let formData = new FormData();
				 formData.append('selectedItems',this.selected);
				 this.selected = [];
				 this.isLoading = true;
				 axios
                    .post('/api/employeeapi',formData)
                    .then( response => {
						console.log(response);
						this.list();
						this.isLoading = false;
						// this.showAlert(response.data,"success");
						if(response.data.code == "error"){
                        this.showAlert(response.data.message,"error");
                        }
						else{
                            this.showAlert(response.data.message,"success");
                        }
					})
                    .catch(err => {
						console.log(err);
						this.list();
						this.isLoading = false;
						this.showAlert("Sent Email Fail!","error");
					})
                    .finally(() => this.loading = false)
        	},
			deleteData() {
				let formData = new FormData();
				 formData.append('selectedItems',this.selected);
				 this.selected = [];
				 axios
                    .post('/api/deleteapi',formData)
                    .then( response => {
						console.log(response);
						this.list();
						if(response.data.code == "error"){
                        this.showAlert(response.data.message,"error");
                        }
						else{
                            this.showAlert(response.data.message,"success");
                        }
					})
                    .catch(err => {
						console.log(err);
						this.list();
						this.showAlert("Delete Fail!","error");
					})
        	},
			logout(){
               axios.post('logout').then(response => {
					if (response.status === 302 || 401) {
							console.log('logout');
						}
						}).catch(error => {

					});
				},
			uplodFile(){
				if(this.image){
					console.log("File Exit");
					let formData = new FormData();
					formData.append('file',this.image);
					this.isLoading = true;
					axios
						.post('/api/employeeapi',formData)
						.then( response => {
							this.image = null;
							this.list();
							this.filename = "no file choosen"
							this.isLoading = false;
							this.showAlert("File Uploaded Successfully!","success");
							console.log(response);
						})
						.catch(err => {
							console.log(err);
							this.isLoading = false;
							this.showAlert("File Upload Fail!","error");
							this.filename = "no file choosen";
							})
						.finally(() => this.loading = false)
				}
			},
			selectFile(){
				let fileInputElement = this.$refs.file;
				fileInputElement.click();
			},
			onFilePicked (event) {
				const files = event.target.files
				this.filename = files[0].name
				const fileReader = new FileReader()
				fileReader.addEventListener('load', () => {
					this.imageUrl = fileReader.result
				})
				fileReader.readAsDataURL(files[0])
				this.image = files[0]
			},
			showAlert(msg,status){
			const Toast = this.$swal.mixin({
            toast: true,
            position: 'top-end',
			showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            	}
            	})
            Toast.fire({
                icon: status,
                title: msg,
            	})
        	},
		}
    }
</script>
<style>
#inputFile{
	padding-left: 5px;
}
.container{
	background-color: #f5f5f0;
}
#uploadExcel {
	text-decoration: none;
	color: black;
	background-color: white;
	padding: 2px;
	margin-left: 10px;
	margin-top: 10px;
	width: auto;
}

#deleteData {
	text-decoration: none;
	float: right;
	color: white;
	background-color: rgb(240, 35, 7);
	padding: 5px;
	margin-left: 5px;
	margin-bottom: 10px;
}

#sendEmail {
	text-decoration: none;
	float: right;
	color: white;
	background-color: rgb(240, 197, 7);
	padding: 5px;
	margin-bottom: 10px;
}

#fileSelectBtn{
    width: 120px;
	margin-left: 5px;
}

tr:nth-child(even) {

  background-color: rgb(245, 245, 245);

}

#emp_name,#emp_email{

    text-align: left;

}
</style>
