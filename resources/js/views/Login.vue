<template>
	<div class="align-middle offset-lg-3 offset-md-2 offset-sm-0 col-lg-6 col-md-8 col-sm-12">
		<div class="card card-block">
			<div class="row no-gutters">
				<div class="col-md-12">
					<div class="card-body">
						<h5 class="card-title">Sign in</h5>
						<div
							v-if="message"
							id="error"
						>
							<Error :error="message" />
						</div>
						<form @submit.prevent="login">
							<input-component
								label="Email"
								id="email"
								name="email"
								type="text"
								v-model="email"
								placeholder="example@gmail.com"
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
							<div class="row mt-3">
								<div class="col-6">
									<button
										type="submit"
										class="custom-btn w-100"
									>LOGIN</button>
								</div>
								<div class="col-6">
									<router-link :to="{ name: 'Register' }">
										<button class="custom-btn w-100">REGISTER</button>
									</router-link>
								</div>
							</div>
						</form>
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
			email: "",
			password: "",
			output: null,
			errors: {},
			message: "",
		};
	},
	created() {
		if (sessionStorage.getItem("user"))
			this.$router.push({ name: "About" });
	},
	methods: {
		login() {
			let currentObj = this;
			currentObj.errors = {};
			currentObj.message = "";

			this.$store
				.dispatch("login", {
					email: this.email,
					password: this.password,
				})
				.then(() => {
					this.$router.push({ name: "About" });
				})
				.catch(function (error) {
					currentObj.message = "";
					error.response.data.message.forEach(function (message) {
						currentObj.message += message;
					});
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