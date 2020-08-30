<template>
	<div>
		<div class="row mt-2">
			<div class="card w-100 mx-2">
				<div class="card-header">
					<div class="row">
						<div class="mt-auto mb-auto col-7 text-left">Name: <b>{{ node.name }}</b></div>
						<div class="col-5 text-right">
							<div
								style="all: unset;"
								v-if="showSettings && showBody"
							>
								<newNode
									:node="node"
									:key="'newNode'+componentKey"
									v-on:refresh="refresh"
								/>
								<removeNode
									:node="node"
									:key="'removeNode'+componentKey"
									v-on:refresh="refresh"
								/>
								<editNode
									:node="node"
									:key="'editNode'+componentKey"
									v-on:refresh="refresh"
								/>
								<moveNode
									:node="node"
									:key="'moveNode'+componentKey"
									v-on:refresh="refresh"
								/>
								<changeParentNode
									:node="node"
									:key="'changeParentNode'+componentKey"
									v-on:refresh="refreshAll"
								/>
							</div>
							<div style="all: unset;">
								<button
									style="all: unset;cursor:pointer;"
									class="mx-1 my-1"
									type="button"
									v-if="showBody"
									@click="toggleSettings"
								>
									<i class="fas fa-cog"></i>
								</button>

								<button
									style="all: unset;cursor:pointer;"
									class="mx-1 my-1"
									type="button"
									@click="toggleBody"
								>
									<i :class="{
                                        'fas fa-caret-up' : showBody,
                                        'fa fa-caret-down': !showBody
                                        }"></i>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div
					class="card-body"
					v-if="showBody && node.childs.length>0"
				>
					<node-component
						v-for="(transferredNode) in node.childs"
						:node="transferredNode"
						:key="'node'+transferredNode.id"
						:class="['mt-3']"
						v-on:refresh="refreshParent"
						v-on:refreshAll="refreshAll"
					/>

				</div>
			</div>
		</div>
	</div>
</template>

<script>
import newNode from "./components/NewNode";
import removeNode from "./components/RemoveNode";
import moveNode from "./components/MoveNode";
import editNode from "./components/EditNode";
import changeParentNode from "./components/changeParentNode";

export default {
	name: "node-component",
	components: {
		newNode,
		removeNode,
		moveNode,
		editNode,
		changeParentNode,
	},
	props: ["node"],
	data() {
		return {
			componentKey: 0,
			showSettings: false,
			showBody: true,
		};
	},
	methods: {
		toggleSettings() {
			this.showSettings = !this.showSettings;
		},
		toggleBody() {
			this.showBody = !this.showBody;
		},
		refresh() {
			this.$emit("refresh");
			this.$emit("refreshNameMapping");
		},
		refreshAll() {
			this.$emit("refreshAll");
			this.componentKey += 1;
		},
		refreshParent() {
			this.$http
				.get("/node/", {
					params: {
						node_id: this.node.id,
					},
				})
				.then((response) => {
					console.log(response);
					//completed refresh node
					this.node = response.data;
				})
				.catch((error) => {
					console.log(error);
				});
		},
	},
};
</script>

<style>
</style>