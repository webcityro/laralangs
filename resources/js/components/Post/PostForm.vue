<template>
	<div class="card">
		<div class="card-header">Add a post</div>

		<div class="card-body">
			<laralangs-form-language-tabs :errors="errors" @change="languageChanged"></laralangs-form-language-tabs>
			<div class="form-group">
				<label for="title">Title*</label>
				<input type="text" id="title" :class="translationInputClass('title')" v-ll-model="'title'" />
				<span
					v-if="hasTraslationError('title')"
					:class="errorCssClass"
				>{{ getTraslationError('title') }}</span>
			</div>
			<div class="form-group">
				<label for="body">Body*</label>
				<textarea id="body" :class="translationInputClass('body')" v-ll-model="'body'"></textarea>
				<span v-if="hasTraslationError('body')" :class="errorCssClass">{{ getTraslationError('body') }}</span>
			</div>
			<div class="form-group">
				<label for="sortOrder">Sort order*</label>
				<input
					type="number"
					id="sortOrder"
					:class="fieldInputClass('sortOrder')"
					v-model="form.fields.sortOrder"
				/>
				<span v-if="hasFieldError('sortOrder')" :class="errorCssClass">{{ getFieldError('sortOrder') }}</span>
			</div>
			<div class="form-group">
				<label for="active">Status*</label>
				<select id="active" :class="fieldInputClass('active')" v-model="form.fields.active">
					<option value>Select an status</option>
					<option value="1">Active</option>
					<option value="0">Inactive</option>
				</select>
				<span v-if="hasFieldError('active')" :class="errorCssClass">{{ getFieldError('active') }}</span>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary" @click.prevent="submit">Save post</button>
			</div>
		</div>
	</div>
</template>

<script>
import FormMixin from '../mixins/FormMixin';

export default {
	mixins: [FormMixin],

	components: {},

	props: {
		endpoint: { type: String, required: true },
		post: { type: [Object, Boolean], required: false, default: false }
	},

	data() {
		return {
			form: {
				fields: {
					active: '',
					sortOrder: 1,
				},
				translations: {},
			},
		};
	},

	created() {
		this.form.translations = this.setTranslations({
			title: '',
			body: '',
		});
	},

	mounted() {
		if (this.post !== false) {
			this.populateForm();
		}
	},

	methods: {
		populateForm() {
			for (const key in this.form.fields) {
				this.form.fields[key] = this.post.fields[key];
			}

			for (const languageID in this.form.translations) {
				this.form.translations[languageID] = {
					...this.form.translations[languageID],
					...this.post.translations[languageID]
				};
			}
		},

		submit() {
			this.validate()
				.then(this.save)
				.catch((errors) => {
					console.log('validation failed', errors);
				});
		},

		save() {
			axios
				[this.post === false ? 'post' : 'patch'](this.endpoint, this.form)
				.then(({ data }) => {
					window.location.href = this.endpoint+(this.post === false ? '/'+data.fields.id : '');
				})
				.catch(({response}) => {
					if (response.status == 422) {
						this.errors.setBackendErrors(response.data.errors);
					}
				});
		},

		validate() {
			return this.laralangsValidate({
				fields: {
					sortOrder: (value) => {
						if (value.length === 0) {
							return 'The sort order is required.';
						} else if (isNaN(value)) {
							return 'The title must be an number.';
						}
					},
					active: (value) => {
						if (value.length === 0) {
							return 'The status is required.';
						}
					},
				},
				translations: {
					title: (value) => {
						if (value.length === 0) {
							return 'The title is required.';
						} else if (value.length < 5 || value.length > 50) {
							return 'The title must be between 5 and 50 characters.';
						}
					},
					body: (value) => {
						if (value.length === 0) {
							return 'The body is required.';
						} else if (value.length < 50) {
							return 'The body must be at lest 50 characters.';
						}
					},
				},
			});
		},
	},

	computed: {},

	watch: {}
};
</script>

<style>
</style>
