<template>
	<div style="all: unset;">
		<button
			style="all: unset;cursor:pointer;"
			class="mx-1 my-1"
			type="button"
			data-toggle="modal"
			:data-target="'#editNode'+node.id"
		>
			<i class="fa fa-pen"></i>
		</button>
		<!-- xs -->
		<!-- <a :data-target="'#newSubNode'+node.id">
			
		</a> -->
		<!-- modal for new Node -->
		<div
			class="modal fade"
			:id="'editNode'+node.id"
			data-keyboard="false"
			data-backdrop="static"
			role="dialog"
			aria-hidden="true"
		>
			<div
				class="modal-dialog modal-dialog-centered"
				role="document"
			>
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Change name in {{node.name}}</h5>
						<button
							:id="'closeEditNodeModal'+node.id"
							type="button"
							data-dismiss="modal"
							style="all: unset;cursor:pointer;"
							aria-label="Close"
						>
							<i
								class="fas fa-times fa-2x"
								style="opacity:0.5;"
							></i>
							<!-- <span aria-hidden="true">&times;</span> -->
						</button>
					</div>
					<div class="modal-body text-left">
						<form @submit.prevent="updateNode">
							<input-component
								label="Name"
								id="name"
								name="name"
								type="text"
								v-model="name"
								:value="node.name"
								placeholder="name"
								:error="errors['name']"
							/>
							<button
								type="submit"
								class="custom-btn w-100 mt-3"
							>Update node</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import InputComponent from "../../InputComponent";
export default {
	props: ["node"],
	components: {
		InputComponent,
	},
	data() {
		return {
			name: this.node.name,
			output: null,
			errors: [],
		};
	},
	methods: {
		clearModal() {
			this.name = "";
		},
		closeEditNodeModal() {
			document
				.getElementById("closeEditNodeModal" + this.node.id)
				.click();
			this.clearModal();
		},
		updateNode() {
			console.log(this.name);
			let currentObj = this;
			currentObj.errors = {};
			this.$http
				.patch("/node", {
					name: this.name,
					node_id: this.node.id,
				})
				.then((response) => {
					console.log(response);
					//completed refresh node
					this.refreshParent();
					//hide modal
					this.closeEditNodeModal();
				})
				.catch((error) => {
					console.log(error);
					// console.log(error.response.data.message);
					currentObj.output = error;
					currentObj.errors = error.response.data
						? error.response.data.errors
						: {};
				});
		},
		refreshParent() {
			this.$emit("refresh");
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