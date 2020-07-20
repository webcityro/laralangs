<template>
	<ul class="nav nav-tabs" role="tablist">
		<li v-for="l in languages" :key="l.id" class="nav-item">
			<a
				href="#"
				:class="tabLinkCssClass(l)"
				:id="l.id+'-tab'"
				@click.prevent="change(l)"
			>
				<img :src="'/vendor/Webcityro/Laralangs/images/flags/'+l.image" :alt="l.name" />
				{{ l.name }}
				<span v-if="hasErrors(l)">
					({{ getLanguageErrorsCount(l) }})
				</span>
			</a>
		</li>
	</ul>
</template>

<script>
import Error from "../validator/Error";

export default {
	props: {
		currentID: { type: Number, required: false, default: window.Laralangs.defaultLanguage.id },
		eventBus: { type: [Object, Boolean], required: false, default: false },
		eventListenMethod: { type: String, required: false },
		eventFireMethod: { type: String, required: false },
		eventListen: { type: String, required: false },
		eventFire: { type: String, required: false },
		errors: { type: Object, required: false, default: () => new Error() },
	},

	data() {
		return {
			languages: window.Laralangs.languages,
			currentLanguageID: this.currentID
		};
	},

	mounted() {
		this.change(this.getLanguage(this.currentLanguageID));

		if (this.eventBus !== false) {
			this.eventBus[this.eventListenMethod](this.eventListen, language => {
				this.change(this.getLanguage(language));
			});
		}
	},

	methods: {
		getLanguage(language) {
			return typeof language === 'object' ? language : this.languages.filter(l => l.id == language)[0];
		},

		change(language) {
			this.currentLanguageID = language.id;

			if (this.eventBus !== false) {
				this.eventBus[this.eventFireMethod](this.eventFire, language);
			}
			this.$emit('change', language);
		}
	},

	computed: {
		hasErrors() {
			return language => Object.keys(this.errors.errors.translations[language.id]).length > 0;
		},

		getLanguageErrorsCount() {
			return language => this.hasErrors(language) ? Object.keys(this.errors.errors.translations[language.id]).length : '';
		},

		tabLinkCssClass() {
			return tab => [
				'nav-link',
				{
					active: tab.id == this.currentLanguageID,
					'text-danger': this.hasErrors(tab)
				}
			];
		}
	},

	watch: {
		currentID(id) {
			this.currentLanguageID = id;
		}
	}
};
</script>

<style>
</style>
