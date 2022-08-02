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

        <div class="container-fluid" style="margin-top: 5px;">
				<div class="row justify-content-center">
					<div class="col-md-10">
						<div class="form-group">
							<label for="employee_id" class="font-weight-bold">Employee ID</label>
							<input type="text" class="form-control" v-model="employee.employee_id" placeholder="Employee ID">
						</div>
					</div>

					<div class="col-md-10" style="margin-top: 25px;">
						<div class="form-group">
							<label for="employee_name" class="font-weight-bold">Employee Name</label>
							<input type="text" class="form-control" v-model="employee.employee_name" placeholder="Employee ID">
						</div>
					</div>

					<div class="col-md-10" style="margin-top: 25px;">
						<div class="form-group">
							<label for="employee_email" class="font-weight-bold">Employee Email</label>
							<input type="text" class="form-control" v-model="employee.employee_email" placeholder="Employee ID">
						</div>
					</div>

					<div class="col-md-10" style="margin-top: 25px;">
						<div class="form-group">
							<label for="employee_nrc_number" class="font-weight-bold">Employee NRC</label>
							<input type="text" class="form-control" v-model="employee.employee_nrc_number" placeholder="Employee ID">
						</div>
					</div>

					<div class="col-md-10" style="margin-top: 25px;">
						<div class="form-group">
							<button class="btn btn-primary" @click="updateData">Update</button>
						</div>
					</div>
				</div>
		</div>
    </div>
</template>

<script>
    export default {

      data() {
        return {
          employee: {},
		  params: null,
		  user: null,

        }
      },
      created() {
		this.user = this.$userName;
		const url = window.location.href;
		const lastParam = url.split("/").slice(-1)[0];
		this.params=lastParam;
		console.log(lastParam);
		console.log("success");
		this.editData();
      },
	  methods: {
	  	editData() {
				 axios
                    .get('/api/update/'+this.params)
                    .then( response => {
						this.employee=response.data
						console.log('this is data');
						console.log(response.data);
					})
                    .catch(err => {
						console.log(err);
					})
        	},
			updateData() {
				let formData = new FormData();
				 formData.append('employee_id',this.employee.employee_id);
				 formData.append('employee_name',this.employee.employee_name);
				 formData.append('employee_email',this.employee.employee_email);
				 formData.append('employee_nrc_number',this.employee.employee_nrc_number);
				 
				 axios
                    .post('/api/update/'+this.params,formData)
                    .then( response => {
						console.log('update data');
						console.log(response.data);
						if(response.data.code == "success"){
                        this.showAlert(response.data.message,"success");
                        }
						else{
                            this.showAlert(response.data.message,"error");
                        }
						window.location.href= '/home';
					})
                    .catch(err => {
						console.log(err);
						this.showAlert("Update Fail!","error");
					})
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