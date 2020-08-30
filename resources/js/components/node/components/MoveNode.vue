<template>
	<div style="all: unset;">
		<button
			style="all: unset;cursor:pointer;"
			class="mx-1 my-1"
			type="button"
			@click="moveUp"
		>
			<i class="fas fa-arrow-up"></i>
		</button>
		<button
			style="all: unset;cursor:pointer;"
			class="mx-1 my-1"
			type="button"
			@click="moveDown"
		>
			<i class="fas fa-arrow-down"></i>
		</button>
	</div>
</template>

<script>
export default {
	props: ["node"],
	methods: {
		moveUp() {
			this.$http
				.post("/node/changeOrder", {
					node_id: this.node.id,
					orderAddValue: -1,
				})
				.then((response) => {
					console.log(response);
					//completed refresh node

					this.refreshParent();
				})
				.catch((error) => {
					console.log(error);
				});
		},
		moveDown() {
			this.$http
				.post("/node/changeOrder", {
					node_id: this.node.id,
					orderAddValue: 1,
				})
				.then((response) => {
					console.log(response);
					//completed refresh node
					this.refreshParent();
				})
				.catch((error) => {
					console.log(error);
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