<script>
import Validator from "../../validator/Validator";
import Error from "../../validator/Error";

export default {
	data() {
		return {
			languageID: 1,
			errors: new Error(),
			inputCssClass: 'form-control',
			inputErrorCssClass: 'is-invalid',
			errorCssClass: 'invalid-feedback',
		};
	},

	directives: {
		llModel(el, binding, vnode) {
			const vm = vnode.context;

			const handler = (e) => {
				vm.form.translations[vm.languageID][binding.value] =
					e.target.value;
			};

			el.value = vm.form.translations[vm.languageID][binding.value];
			el.addEventListener('input', handler);
		},
	},

	methods: {
		languageChanged(lang) {
			this.languageID = lang.id;
		},

		setTranslations(translations) {
			const trans = {};

			for (const lang of window.Laralangs.languages) {
				trans[lang.id] = { ...translations };
			}

			return trans;
		},

		laralangsValidate(validation) {
			return new Promise((resolve, reject) => {
				this.errors = new Error();
				const validator = new Validator(this, resolve, reject, validation);
			});
		}
	},

	computed: {
		translationInputClass() {
			return field => {
				return [
					this.inputCssClass,
					{
						[this.inputErrorCssClass]: this.hasTraslationError(field)
					}
				];
			}
		},

		fieldInputClass() {
			return field => {
				return [
					this.inputCssClass,
					{
						[this.inputErrorCssClass]: this.hasFieldError(field)
					}
				];
			}
		},

		hasTraslationError() {
			return field => this.errors.has(field, this.languageID);
		},

		getTraslationError() {
			return field => this.errors.get(field, this.languageID);
		},

		hasFieldError() {
			return field => this.errors.has(field);
		},

		getFieldError() {
			return field => this.errors.get(field);
		}
	}
}

</script>
