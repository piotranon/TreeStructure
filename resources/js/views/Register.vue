<template>
	<div class="container">
		<div class="row h-100 align-items-center">
			<div class="align-middle offset-lg-3 offset-md-2 offset-sm-0 col-lg-6 col-md-8 col-sm-12">
				<div class="card card-block">
					<div class="row no-gutters">
						<div class="col-md-12">
							<div class="card-body">
								<h5 class="card-title">Sign up</h5>
								<div
									v-if="message"
									id="error"
								>
									<Error :error="message" />
								</div>
								<form @submit.prevent="register">
									<h4>Creating user.</h4>
									<input-component
										label="Name"
										id="name"
										name="name"
										type="text"
										v-model="name"
										placeholder="John Snow"
										:error="errors['name']"
									/>
									<input-component
										label="Email"
										id="email"
										name="email"
										v-model="email"
										type="text"
										placeholder="example@ex.com"
										:error="errors['email']"
									/>
									<input-component
										label="Password"
										id="password"
										name="password"
										type="password"
										v-model="password"
										placeholder="eg. Min 8 charas with specials."
										:error="errors['password']"
									/>
									<input-component
										label="Re-password"
										id="re_password"
										name="re_password"
										type="password"
										v-model="re_password"
										placeholder="Retype same password from above."
										:error="errors['re-password']"
									/>
									<div class="form-check">
										<input
											class="form-check-input"
											type="checkbox"
											name="policy"
											id="policy"
										/>
										<label
											class="form-check-label"
											for="policy"
										>
											Agreement of
											<a href>policy</a>.
										</label>
									</div>
									<div class="form-group mt-3">
										<button
											class="custom-btn"
											type="submit"
										>Register</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Error from "../components/Error";
import InputComponent from "../components/InputComponent";
export default {
	components: {
		Error,
		InputComponent,
	},
	data() {
		return {
			name: "",
			email: "",
			password: "",
			re_password: "",
			agreement: "",
			output: null,
			errors: [],
		};
	},
	created() {
		if (sessionStorage.getItem("user"))
			this.$router.push({ name: "Admin" });
	},
	methods: {
		register() {
			let currentObj = this;
			currentObj.errors = {};
			currentObj.message = "";

			this.$http
				.post("/register", {
					email: this.email,
					password: this.password,
					re_password: this.re_password,
					name: this.name,
				})
				.then(() => {
					this.$router.push({ name: "Login" });
				})
				.catch(function (error) {
					currentObj.output = error;
					currentObj.errors = error.response.data
						? error.response.data.errors
						: {};
					currentObj.message = error.response.data.message;
				});
		},
	},
};
</script>

<style lang="scss" scoped>
.custom-btn {
	border: none;
	width: 100%;
	padding: 1vh;
	color: white;
	background: linear-gradient(to left, #845ec2, #ff6f91);
	border-radius: 5px;
	box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.4);
}
</style>