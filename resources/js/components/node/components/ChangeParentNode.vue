<template>
	<div style="all: unset;">
		<button
			style="all: unset;cursor:pointer;"
			class="mx-1 my-1"
			type="button"
			data-toggle="modal"
			:data-target="'#changeParentNode'+node.id"
		>
			<i class="fas fa-home"></i>
		</button>
		<!-- xs -->
		<!-- <a :data-target="'#newSubNode'+node.id">
			
		</a> -->
		<!-- modal for new Node -->
		<div
			class="modal fade"
			:id="'changeParentNode'+node.id"
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
						<h5 class="modal-title text-left">Change Parent Node for {{node.name}}</h5>
						<button
							:id="'closeChangeParentNodeModal'+node.id"
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
						<form @submit.prevent="updateParentNode">
							<div class="form-group mb-2">
								<label :for="name">Select new Parent Node:</label>
								<div class="input-group">
									<select
										v-model="selected"
										class="form-control"
									>
										<option
											v-for="option in options"
											v-bind:value="option.id"
											v-bind:key="option.id"
										>
											{{option.name}}
										</option>
									</select>
								</div>
							</div>
							<div class="text-center">
								Are you sure that you want to change parent for {{node.name}}?
								<br><br>
								Also all childs of {{node.name}} will be moved with all of their childs e.t.c.
								<br><br>
								This process cannot be undone.
								{{selected}}
							</div>
							<button
								type="submit"
								class="custom-btn w-100 mt-3"
							>Update parent node.</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

</template>

<script>
export default {
	props: ["node"],
	data() {
		return {
			name: this.node.name,
			output: null,
			errors: [],
			options: [],
			selected: null,
		};
	},
	mounted() {
		let nodeNames = JSON.parse(sessionStorage.getItem("nodeNames"));

		let x = [];

		for (let node of nodeNames) {
			if (node.id !== this.node.id) x.push(node);
		}

		this.options = x;
		if (this.node.parent) this.selected = this.node.parent.id;
	},
	methods: {
		clearModal() {
			this.name = "";
		},
		closeChangeParentNodeModal() {
			document
				.getElementById("closeChangeParentNodeModal" + this.node.id)
				.click();
			this.clearModal();
		},
		updateParentNode() {
			this.$http
				.patch("/node", {
					name: this.name,
					node_id: this.node.id,
					parent_id: this.selected,
				})
				.then((response) => {
					console.log(response);
					//completed refresh node
					this.refreshParent();
					//hide modal
					this.closeChangeParentNodeModal();
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