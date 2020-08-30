<template>
	<div
		class="alert alert-danger alert-dismissible fade show"
		id="error"
		role="alert"
	>
		{{error}}
		<button
			type="button"
			class="close"
			data-dismiss="alert"
			aria-label="Close"
		>
			<span aria-hidden="true"><i
					class="fas fa-times"
					style="opacity:0.5;"
				></i></span>
		</button>
	</div>
</template>

<script>
export default {
	data() {
		return {};
	},
	name: "Error",
	props: ["error"],
	created() {
		console.log(this.error);
	},
};
</script>

<style>
p {
	margin: 0;
}
</style>