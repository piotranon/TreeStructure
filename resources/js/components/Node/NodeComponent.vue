<template>
	<div>
		<!-- {{node}} -->
		<div class="row">
			<div class="card w-100 mx-2">
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
					<node-component
						v-for="(transferredNode) in node.childs"
						:node="transferredNode"
						:key="transferredNode.id"
						:class="['mt-1']"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: "node-component",
	props: ["node"],
};
</script>

<style>
</style>