<template>
	<div class="form-group mb-2">
		<label :for="name">{{ label }}</label>
		<div class="input-group">
			<input
				@input="updateInput()"
				ref="input"
				class="form-control"
				:placeholder="placeholder"
				:name="name"
				:type="type"
				:id="id"
				v-bind:class="{ 'is-invalid': error }"
			/>
			<div
				v-if="error"
				class="invalid-feedback"
			>
				<p
					class="mb-2"
					v-for="(message, index) in error"
					:key="index"
				>
					{{ message }}
				</p>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: ["label", "type", "name", "id", "placeholder", "error"],

	data() {
		return {
			value: "",
		};
	},

	created() {
		this.error = null;
	},

	methods: {
		updateInput() {
			this.value = this.$refs.input.value;
			this.$emit("input", this.value);
		},
	},
};
</script>

<style></style>
