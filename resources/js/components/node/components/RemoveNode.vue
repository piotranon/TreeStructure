<template>
	<div style="all: unset;">

		<button
			style="all: unset;cursor:pointer;"
			class="mx-1 my-1"
			type="button"
			data-toggle="modal"
			:data-target="'#removeNode'+node.id"
		>
			<i class="fa fa-minus"></i>
		</button>
		<!-- xs -->
		<!-- <a :data-target="'#newSubNode'+node.id">
			
		</a> -->
		<!-- modal for new Node -->
		<div
			class="modal fade"
			:id="'removeNode'+node.id"
			data-keyboard="false"
			data-backdrop="static"
			role="dialog"
			aria-hidden="true"
		>
			<div
				class="modal-dialog modal-dialog-centered modal-notify modal-danger"
				role="document"
			>
				<!--Content-->
				<div class="modal-content text-center">
					<!--Header-->
					<div class="modal-header">
						<h5 class="modal-title">Deleting node named {{node.name}}</h5>
						<button
							:id="'closeRemoveModal'+node.id"
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

					<!--Body-->
					<div class="modal-body">
						<div
							v-if="message"
							id="error"
						>
							<Error :error="message" />
						</div>
						Are you sure that you want to delete {{node.name}} ?
						<br><br>
						Also all childs of {{node.name}} will be deleted with all of their childs e.t.c.
						<br><br>
						This process cannot be undone.

					</div>
					<!--Footer-->
					<div class="modal-footer">
						<div class="row w-100">
							<div class="col">
								<button
									@click="closeRemoveModal"
									class="btn  btn-outline-danger w-50"
								>Cancel</button>
							</div>
							<div class="col">
								<button
									@click="deleteNode"
									class="btn  btn-danger waves-effect w-50"
								>Delete</button>
							</div>
						</div>

					</div>
				</div>
				<!--/.Content-->
			</div>
		</div>
	</div>
</template>

<script>
import Error from "../../Error";
export default {
	props: ["node"],
	components: {
		Error,
	},
	data() {
		return {
			output: null,
			message: "",
			errors: [],
		};
	},
	methods: {
		closeRemoveModal() {
			document.getElementById("closeRemoveModal" + this.node.id).click();
		},
		deleteNode() {
			let currentObj = this;
			currentObj.errors = {};
			currentObj.message = "";

			this.$http
				.delete("/node/" + this.node.id)
				.then((response) => {
					console.log(response);
					//completed refresh node
					this.refreshParent();
					//hide modal
					this.closeRemoveModal();
				})
				.catch((error) => {
					console.log(error);

					currentObj.output = error;
					currentObj.errors = error.response.data
						? error.response.data.errors
						: {};
					currentObj.message = error.response.data.message;
				});
		},
		refreshParent() {
			this.$emit("refresh");
		},
	},
};
</script>

<style>
</style>