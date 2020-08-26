<template>
	<div>
	</div>
</template>

<script>
export default {
	created() {
		sessionStorage.clear();
		this.$router.push({ name: "Login" });
		location.reload();
	},
};
</script>

<style>
</style>