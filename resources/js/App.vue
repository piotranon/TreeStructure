<template>
	<div>
		<navbar />
		<div
			class="container my-3"
			style="min-height: 80vh"
		>
			<div class="row align-items-center h-100">
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>
<script>
import Navbar from "./components/NavBar";
export default {
	components: {
		Navbar,
	},
};
</script>