<template>
	<div class="col-12 col-md-8 offset-md-2">

		<node-component
			v-for="(passedNode) in nodes"
			:node="passedNode"
			:key="componentKey+'mainNode'+passedNode.id"
			:class="['mt-3']"
			v-on:refresh="refresh"
			v-on:refreshAll="refreshAll"
		/>

		<div class="row mt-3">
			<div class="col text-center">
				<button
					style="all: unset;cursor:pointer;"
					class="mx-1 my-1"
					type="button"
					data-toggle="modal"
					data-target="#newMainNode"
				>
					<i
						class="fa fa-plus fa-5x"
						style="opacity:0.25;"
					></i>
				</button>
				<div
					class="modal fade"
					id="newMainNode"
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
								<h5 class="modal-title">Create new Main Node</h5>
								<button
									id="closeNewNodeModal"
									type="button"
									data-dismiss="modal"
									style="all: unset;cursor:pointer;"
									aria-label="Close"
								>
									<i
										class="fas fa-times fa-2x"
										style="opacity:0.5;"
									></i>
								</button>
							</div>
							<div class="modal-body text-left">
								<form @submit.prevent="createNode">
									<input-component
										label="Name"
										id="name"
										name="name"
										type="text"
										v-model="name"
										placeholder="name"
										:error="errors['name']"
									/>
									<button
										type="submit"
										class="custom-btn w-100 mt-3"
									>Create node</button>
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
import NodeComponent from "../components/Node/NodeComponent";

export default {
	components: {
		Error,
		InputComponent,
		NodeComponent,
	},
	data() {
		return {
			name: "",
			// parent_id: null,
			// order: null,
			output: null,
			errors: [],
			nodes: [],
			componentKey: 0,
		};
	},
	mounted() {
		this.refreshAll();
	},
	methods: {
		refresh() {
			this.$http.get("/node/all").then((results) => {
				this.nodes = results.data;
			});
			this.refreshNameMapping();
		},
		refreshNameMapping() {
			this.$http.get("/node/nameMapping").then((results) => {
				console.log("mapping:" + results);
				var newMainNode = { id: 0, name: "New Main Node" };
				results.data[0].unshift(newMainNode);
				sessionStorage.setItem(
					"nodeNames",
					JSON.stringify(results.data[0])
				);
			});
		},
		refreshAll() {
			this.refresh();
			this.refreshNameMapping();
			this.componentKey += 1;
		},
		closeNewNodeModal() {
			document.getElementById("closeNewNodeModal").click();
		},
		createNode() {
			let currentObj = this;
			currentObj.errors = {};
			this.$http
				.post("/node", {
					name: this.name,
					parent_id: null,
					order: null,
				})
				.then((response) => {
					console.log(response);
					//completed refresh node
					this.refresh();
					//hide modal
					this.closeNewNodeModal();
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