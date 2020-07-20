export default class Validator {
	constructor(wrapper, resolve, reject, { fields = {}, translations = {} }) {
		this.wrapper = wrapper;
		this.validationBag = { fields, translations };
		this.errors = {
			fields: {},
			translations: {},
		};

		Promise.all([
			...this.promises('fields'),
			...this.promises('translations'),
		])
			.then(resolve)
			.catch((error) => {
				wrapper.errors.set(this.errors.fields, 'fields');
				wrapper.errors.set(this.errors.translations, 'translations');
				reject(error);
			});
	}

	promises(key) {
		const promises = [];

		for (let field in this.validationBag[key]) {
			promises.push(
				this[key + 'Validate'](field, this.validationBag[key][field])
			);
		}

		return promises;
	}

	translationsValidate(field, callback) {
		return new Promise((resolve, reject) => {
			const noOfLangs = window.Laralangs.languages.length;
			let i = 0;

			for (const lang of window.Laralangs.languages) {
				const msg = callback(this.getValue(field, lang.id));
				i++;

				if (typeof msg === 'string') {
					this.setError(field, msg, lang.id);
					reject('Invalid data');
				} else if (noOfLangs == i) {
					resolve();
				}
			}
		});
	}

	fieldsValidate(field, callback) {
		return new Promise((resolve, reject) => {
			const msg = callback(this.getValue(field));

			if (typeof msg === 'string') {
				this.setError(field, msg);
				reject('Invalid data');
			} else {
				resolve();
			}
		});
	}

	setError(field, msg, languageID = 0) {
		if (languageID !== 0) {
			this.errors.translations[languageID] =
				this.errors.translations[languageID] || {};
			this.errors.translations[languageID][field] =
				this.errors.translations[languageID][field] || [];
			this.errors.translations[languageID][field].push(msg);
		} else {
			this.errors.fields[field] = this.errors.fields[field] || [];
			this.errors.fields[field].push(msg);
		}
	}

	getValue(field, languageID = 0) {
		return (languageID !== 0
			? this.wrapper.form.translations[languageID]
			: this.wrapper.form.fields)[field];
	}
}
