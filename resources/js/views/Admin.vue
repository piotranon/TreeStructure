<template>
	<div class="col-8 offset-2">

		<node-component
			v-for="(transferredNode) in nodes"
			:node="transferredNode"
			:key="transferredNode.id"
			:class="['mt-3']"
		/>

		<!-- <div
			class="row mt-3"
			v-for="(node) in nodes"
			:key="node.id"
		>
			<div class="col-8 offset-2">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="mt-auto mb-auto col-6">nagłówek {{ node.name }}</div>
							<div class="col-6 text-right">
								<a
									v-on:click="addNewGroup()"
									class
								>
									<span class="fa fa-plus"></span>
								</a>
							</div>
						</div>
					</div>
					<div class="card-body">
						{{ node.name }}
					</div>
				</div>
			</div>
		</div> -->
		<!-- <div class="row">
			<div class="col-8 offset-2 text-center">
				<button
					style="all: unset;"
					@click="xd"
				>
					<i
						class="fas fa-plus fa-3x"
						style="opacity: 0.25;"
					></i>
				</button>
			</div>
		</div> -->
		<!-- <div class="row">
			<div class="col-8 offset-2">
				<h1>This is admin page.</h1>
				<div
					v-if="message"
					id="error"
				>
					<Error :error="message" />
				</div>
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
					<input-component
						label="Parent id"
						id="parent_id"
						name="parent_id"
						type="number"
						v-model="parent_id"
						placeholder="parent_id"
						:error="errors['parent_id']"
					/>
					<input-component
						label="Order"
						id="order"
						name="order"
						type="number"
						v-model="order"
						placeholder="order"
						:error="errors['order']"
					/>
					<button
						type="submit"
						class="custom-btn w-100"
					>Create node</button>
				</form>
			</div>
		</div> -->

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
			// name: "",
			// parent_id: null,
			// order: null,
			// output: null,
			// errors: [],
			nodes: [],
		};
	},
	mounted() {
		this.$http.get("/node").then((results) => {
			this.nodes = results.data;
		});
	},
	methods: {
		xd() {},
		createNode() {
			let currentObj = this;
			currentObj.errors = {};
			currentObj.message = "";
			this.$http
				.post("/node", {
					name: this.name,
					order: this.order,
					parent_id: this.parent_id,
				})
				.then(function (response) {
					console.log(response);
				})
				.catch(function (error) {
					console.log(error);
					// console.log(error.response.data.message);
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