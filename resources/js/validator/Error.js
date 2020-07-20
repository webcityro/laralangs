export default class Error {
	constructor() {
		this.clear();
	}

	set(errors, key = 'fields') {
		if (key === 'translations') {
			this.errors.translations = {
				...this.defaultTranslationsObj(),
				...errors,
			};
			return;
		}
		this.errors[key] = errors;
	}

	setBackendErrors(errors) {
		let err = {};

		for (const key in errors) {
			err = {
				...err,
				...this.recursiveSetter(key, errors[key]),
			};
		}

		this.errors = err;
	}

	recursiveSetter(path, msg, obj = this.errors) {
		const pathArr = path.split('.');
		const key = pathArr.shift();
		obj[key] =
			pathArr.length === 0
				? msg
				: this.recursiveSetter(pathArr.join('.'), msg, obj[key]);
		return obj;
	}

	all() {
		return this.errors;
	}

	clear() {
		this.errors = {
			fields: {},
			translations: this.defaultTranslationsObj(),
		};
	}

	defaultTranslationsObj() {
		const obj = {};

		for (const lang of window.Laralangs.languages) {
			obj[lang.id] = {};
		}

		return obj;
	}

	has(field, languageID = 0) {
		return this.getFieldsSet(languageID).hasOwnProperty(field);
	}

	get(field, languageID = 0) {
		if (!this.has(field, languageID)) {
			return '';
		}

		return this.getFieldsSet(languageID)[field][0];
	}

	getFieldsSet(languageID) {
		return languageID !== 0
			? this.errors.translations[languageID]
			: this.errors.fields;
	}
}
