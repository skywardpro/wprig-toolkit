/**
* File forms.ts
*
* Contains functions, inits, and configurations for all sort of forms.
*/

const FORMCARRY_ENDPOINT_FORM = 'https://formcarry.com/s/XXXXXXXXXXXX';

const MAX_ATTACHMENT_SIZE = 5 * 1024 * 1024; // 5 MB

// Phone masking interfaces and variables
interface Country {
	name: string;
	code: string;
	iso: string;
	flag: string;
	mask: string;
}

interface IpData {
	country_code: string;
	[key: string]: any;
}

const phoneMasks = new Map<HTMLInputElement, any>(); // Map to store phone masks for each input
let phoneMaskingInitialized = false; // Flag to track if phone masking has been initialized
let countrySelectorInitialized = false; // Flag to track if country selector events have been initialized
let countrySelectorHandler: ((e: Event) => void) | null = null; // Store the handler function

// Function to initialize phone masking for specific forms (e.g., in modal)
export async function initPhoneMaskingForForms(forms?: NodeListOf<HTMLFormElement> | HTMLFormElement[]): Promise<void> {
	if (typeof (window as any).IMask === 'undefined') {
		console.warn('[Forms] IMask not available, phone masking will not work');
		return;
	}

	try {
		const country = await getCountryFromClientIp();
		if (country) {
			// Get forms to initialize - if not provided, use all forms
			const targetForms = forms ? Array.from(forms) : Array.from(document.querySelectorAll('form'));
			
			targetForms.forEach((form) => {
				// Skip if form doesn't have .form__countries selector
				if (!form.querySelector('.form__countries')) {
					return;
				}
				const phoneInputs = form.querySelectorAll('input[type="tel"]') as NodeListOf<HTMLInputElement>;
				phoneInputs.forEach((phoneInput) => {
					// Skip if already initialized
					if (phoneMasks.has(phoneInput)) {
						return;
					}

					// Update flag and country code in selector for this form
					const formCountries = form.querySelectorAll('.form__countries__current__flag img');
					formCountries.forEach((flag) => {
						flag.setAttribute('src', country.flag);
						flag.setAttribute('data-src', country.flag);
					});
					
					const formCodes = form.querySelectorAll('.form__countries__current__code');
					formCodes.forEach((codeElement) => {
						codeElement.textContent = country.code;
					});
					
					// Create mask
					const mask = (window as any).IMask(phoneInput, {
						mask: country.mask,
						lazy: true,
						overwrite: true,
						autofix: true,
					});
					phoneMasks.set(phoneInput, mask);
					
					// Set placeholder
					phoneInput.setAttribute('placeholder', country.mask.replaceAll('0', '0'));
					phoneInput.setAttribute('data-initialized', 'true');
				});
			});
			
			// Create countries list if it doesn't exist
			const existingList = document.querySelector('.form__countries__list');
			if (!existingList || existingList.children.length === 0) {
				createCountriesList(country);
			}
			
			// Initialize country selector handler if not already done
			if (!countrySelectorInitialized && !countrySelectorHandler) {
				countrySelectorHandler = (e: Event) => {
					const target = e.target as HTMLElement;
					if (
						target.className === "form__countries__current" ||
						target.className === "form__countries__current__flag" ||
						target.className === "form__countries__current__arrow" ||
						target.className === "form__countries__current__code"
					) {
						const holder = target.closest('.form__countries')?.querySelector('.form__countries__list-holder') as HTMLElement;
						if (holder) {
							if (holder.classList.contains('show')) {
								holder.classList.remove('show', 'show--top', 'show--bottom');
								holder.style.display = 'none';
							} else {
								holder.style.display = 'block';
								const holderRect = holder.getBoundingClientRect();
								const windowHeight = window.innerHeight;
								const bottomSpace = windowHeight - holderRect.bottom;

								holder.classList.remove('show--top', 'show--bottom');
								holder.classList.add('show');
								if (bottomSpace < 100) {
									holder.classList.add('show--top');
								} else {
									holder.classList.add('show--bottom');
								}
							}
						}
					}
				};
				
				document.querySelector('body')?.addEventListener("click", countrySelectorHandler);
				countrySelectorInitialized = true;
			}
		}
	} catch (error) {
		console.error('[Forms] Error initializing phone masking for forms:', error);
	}
}

// Declare IMask global
declare const IMask: any;

type Errors = Record<string, string[]> | undefined;
type Constraints = Record<string, any>;
type SubmitButton = HTMLButtonElement | (HTMLInputElement & { type: 'submit' }) | HTMLSelectElement;
type FormattedFormData = Record<string, any>;

// Assume that validate is a globally available function (e.g. from validate.js)
interface ValidateFunction {
	(values: Record<string, unknown>, constraints: Record<string, unknown>): Record<string, string[]> | undefined;
	validators: {
		file?: (value: File | undefined, options: { extensions?: string[]; maxSize?: number }) => string | undefined;
		[key: string]: ((value: any, options?: any, key?: string, attributes?: any) => string | undefined) | undefined;
	};
}

declare const validate: ValidateFunction;

// Global form constraints configuration
// Can be overridden from window object: (window as any).formConstraints = { ... }
const defaultFormConstraints: Record<string, Constraints> = {
	'example-form': {
		name: {
			presence: {
				allowEmpty: false,
			},
			length: {
				minimum: 2,
				maximum: 30,
			},
		},
		email: {
			presence: {
				allowEmpty: false,
			},
			email: true,
			length: {
				minimum: 2,
				maximum: 50,
			},
		},
		phone: {
			presence: {
				allowEmpty: false,
				message: "Phone number is required"
			},
			length: {
				minimum: 7,
				maximum: 20,
				tooShort: "Phone number must be at least %{count} characters",
				tooLong: "Phone number must be no more than %{count} characters"
			},
			format: {
				pattern: /^\+?[\d\s\-()]+$/,
				message: "Phone number format is invalid"
			}
		},
	},
	'default': {
		email: {
			presence: {
				allowEmpty: false,
			},
			email: true,
			length: {
				minimum: 2,
				maximum: 50,
			},
		},
	}
};

// Get form constraints from global variable or use defaults
function getFormConstraints(formId: string | null): Constraints {
	const globalConstraints = (window as any).formConstraints as Record<string, Constraints> | undefined;
	const constraintsMap = globalConstraints || defaultFormConstraints;
	
	if (formId && constraintsMap[formId]) {
		return constraintsMap[formId];
	}
	
	return constraintsMap['default'] || defaultFormConstraints['default'];
}

// Function to update validation classes on submit
function updateValidationWarnings(errors: Errors, form: HTMLFormElement): void {


	// Remove 'not-valid' class from all inputs
	form.querySelectorAll('input').forEach((input) => {
		input.classList.remove('not-valid');
	});

	// Remove error messages
	form.querySelectorAll('.error-message').forEach((el) => {
		el.remove();
	});


	// Add 'not-valid' class to inputs with errors
	let errorCounter = 0;
	if (errors) {
		Object.keys(errors).forEach((key) => {
			errorCounter++;
			const field = form.querySelector<HTMLInputElement | HTMLSelectElement>(`[name="${key}"]`);
			if (field) {
				field.classList.add('not-valid');
				// display error message under the input
				const errorMsg = document.createElement('span');
				errorMsg.classList.add('error-message', 'color--red')
				errorMsg.innerText = errors[key][0];
				field.after(errorMsg);
			}
		});
		if (errorCounter > 1) {
			showNotification(form, 'Fill all required fields.', 'fail');
		} else {
			showNotification(form, 'Fill the required field.', 'fail');
		}
	}
}

// Function to update validation on input change
function updateValidationInputClass(errors: Errors, input: HTMLInputElement | HTMLSelectElement): void {
	// Add 'not-valid' class to inputs with errors
	if (errors) {
		input.classList.add('not-valid');
	} else {
		// Remove 'not-valid' class from input
		input.classList.remove('not-valid');
	}
}

// Validate a field on change
function validateField(event: Event, constraints: Constraints): void {
	const target = event.target as HTMLInputElement;
	const fieldName = target.name;
	const value: Record<string, unknown> = {};
	
	// For phone inputs, use unmasked value for validation
	if (target.type === 'tel' && phoneMasks.has(target)) {
		const mask = phoneMasks.get(target);
		const unmaskedValue = mask.unmaskedValue || '';
		// Clean unmasked value to get only digits (including +)
		let cleanValue = unmaskedValue.replace(/[^\d+]/g, '');
		// Remove leading + if present for length validation (we count only digits)
		if (cleanValue.startsWith('+')) {
			cleanValue = cleanValue.substring(1);
		}
		value[fieldName] = cleanValue || '';
	} else {
		value[fieldName] = target.value;
	}

	const fieldConstraints: Constraints = {};
	fieldConstraints[fieldName] = constraints[fieldName];

	const errors = validate(value, fieldConstraints);
	updateValidationInputClass(errors, target);

	// Log validation errors for debugging
	if (errors && errors[fieldName]) {
		console.error('[Forms] Validation errors for', fieldName, ':', errors[fieldName]);
		if (target.type === 'tel') {
			console.log('[Forms] Phone unmasked value length:', value[fieldName] ? String(value[fieldName]).length : 0);
		}
	}
}

// Function to correct email domain typos
function correctEmailDomain(email: string): string {
	// Define a map of typos and their corrections
	const domainCorrections: Record<string, string> = {
		// Common typos for .com
		".con": ".com",
		".cob": ".com",
		".co,": ".com",
		".cok": ".com",
		".coj": ".com",
		".vom": ".com",
		".xom": ".com",
		".cpm": ".com",
		".cim": ".com",

		// Common typos for .net
		".ner": ".net",
		".nrt": ".net",
		".neq": ".net",
		".met": ".net",
		".nte": ".net",
		".bet": ".net",

		// Common typos for .org
		".irg": ".org",
		".prg": ".org",
		".ogr": ".org",
		".ofg": ".org",
		".og": ".org",
		".0rg": ".org",

		// Common typos for .edu
		".edj": ".edu",
		".edk": ".edu",
		".edl": ".edu",
		".rdu": ".edu",
		".esu": ".edu",

		// Common typos for .gov
		".gob": ".gov",
		".gof": ".gov",
		".g0v": ".gov",
		".hov": ".gov",
		".govb": ".gov",

		// Common typos for .co (e.g., .co domains like example.co)
		".cp": ".co",
		".cp,": ".co",
		".coo": ".co",
		".ci": ".co",
		".xo": ".co",

		// Common typos for .io (e.g., example.io)
		".lo": ".io",
		".i0": ".io",
		".po": ".io",
		".ik": ".io",
		".jo": ".io",

		// Common typos for .biz
		".buz": ".biz",
		".viz": ".biz",
		".bix": ".biz",
		".biv": ".biz",

		// Common typos for .info
		".imfo": ".info",
		".inro": ".info",
		".ibfo": ".info",
		".infp": ".info",
		".9nfo": ".info",
	};

	// Extract the domain part of the email
	const atIndex = email.lastIndexOf('@');
	if (atIndex === -1) return email; // Invalid email format, return as is

	const localPart = email.substring(0, atIndex);
	const domainPart = email.substring(atIndex);

	// Check and correct the domain part
	for (const [typo, correction] of Object.entries(domainCorrections)) {
		if (domainPart.endsWith(typo)) {
			return localPart + domainPart.slice(0, -typo.length) + correction;
		}
	}

	return email; // Return the email unmodified if no typos are found
}

// Submit forms
function submitFormViaFormCarry(form: HTMLFormElement): void {
	console.log('[Forms] submitFormViaFormCarry called for form:', form.id);
	
	// Check if form is already submitting
	if ((form as any)._isSubmitting) {
		console.warn('[Forms] Form is already submitting, ignoring duplicate submission');
		return;
	}
	
	// Mark form as submitting
	(form as any)._isSubmitting = true;
	
	// Disable submit button to prevent multiple submissions
	const submitButton = form.querySelector<SubmitButton>('button[type="submit"], input[type="submit"]');
	if (submitButton) {
		submitButton.disabled = true;
		console.log('[Forms] Submit button disabled');
	}

	const formAction = getFormActionHref(form);
	if (!formAction) {
		console.error('[Forms] Form action is not defined for form:', form.id);
		(form as any)._isSubmitting = false;
		if (submitButton) {
			submitButton.disabled = false;
		}
		return;
	}
	
	console.log('[Forms] Form action:', formAction);

	// Update phone with country code before submission
	updatePhoneWithCountryCode(form);

	const formattedFormData = getFormattedFormData(form);
	console.log('[Forms] Formatted form data:', formattedFormData);

	// Save data to local storage (for debugging or persistence)
	localStorage.setItem('formData', JSON.stringify(formattedFormData));

	sendFormData(form, formattedFormData, submitButton);
}

function resetFormFileInputs(form: HTMLFormElement) {
	const fileInputs = form.querySelectorAll<HTMLInputElement>('input[type="file"]');

	fileInputs.forEach((input) => {
		const parentForm = input.closest('form');
		const spanLabel = parentForm?.querySelector('.form-file-label') as HTMLElement | null;
		const spanRemoveFile = parentForm?.querySelector('.form-file-label__remove-file-btn__text') as HTMLElement | null;

		input.classList.remove('has-file');
		if (spanLabel) spanLabel.classList.remove('has-file');
		if (spanRemoveFile) spanRemoveFile.textContent = '';
		input.value = '';
	});
}

async function sendFormData(form: HTMLFormElement, data: FormattedFormData, submitButton: SubmitButton | null) {
	const formAction = getFormActionHref(form);
	console.log('[Forms] Sending form data to:', formAction);
	console.log('[Forms] Request payload:', data);

	try {
		const headers = {
			'Content-Type': 'application/json',
			'Accept': 'application/json'
		};

		const response = await fetch(formAction, {
				method: 'POST',
				headers,
				body: JSON.stringify(data)
			});

		console.log('[Forms] Response status:', response.status);
		console.log('[Forms] Response ok:', response.ok);

		if (!response.ok) {
			if (response.status === 429) {
				console.error('[Forms] 429 Too Many Requests error');
				showNotification(form, "Too many requests. Try again in 5 minutes.", "fail");
			} else {
				const errorResponse = await response.json().catch(() => ({}));
				console.error('[Forms] Error response:', errorResponse);
				if (errorResponse.code === 422) {
					showNotification(form, "Error processing your request. Try again.", "fail");
				} else {
					showNotification(form, "Error processing your request. Invalid data. Try again.", "fail");
				}
			}
		} else {
			const responseData = await response.json();
			console.log('[Forms] Success response:', responseData);
			if (responseData.status === "success") {
				showNotification(form, "Your request is successfully received!", "success");
				form.reset();
				resetFormFileInputs(form);

				// Dispatch form_submit_success event for GA tracking (with bubbles for event delegation)
				form.dispatchEvent(new Event('form_submit_success', { bubbles: true }));
			}
		}
	} catch (error) {
		console.error('[Forms] Error sending form data:', error);
		showNotification(form, "Error processing your request. Try again.", "fail");
	} finally {
		// Reset submitting flag
		(form as any)._isSubmitting = false;
		if (submitButton) {
			submitButton.disabled = false; // Re-enable the submit button
			console.log('[Forms] Submit button re-enabled');
		}
	}
}

/**
 * Converts FormData into a structured object, handling special cases:
 * - Checkboxes: Values are grouped into arrays
 * - Select elements: Captures displayed text (not just values)
 * - Email fields: Applies domain correction
 * @param formData Raw form data from HTMLFormElement
 * @param form The source HTML form (for DOM element lookup)
 * @returns Structured object with field names as keys
 */
function getFormattedFormData(form: HTMLFormElement): FormattedFormData {

	const formData = new FormData(form);
	const formattedData: FormattedFormData = {};

	formData.forEach((inputValue, inputName) => {
		// Skip hidden fields with _complete suffix - we'll handle them separately
		if (inputName.endsWith('_complete')) {
			return;
		}
		
		const inputEl = form.querySelector(`[name="${inputName}"]`) as HTMLInputElement | HTMLSelectElement | null;

		// Handle multiple checkboxes
		if (inputEl && inputEl instanceof HTMLInputElement && inputEl.type === "checkbox") {
			if (!formattedData[inputName]) {
				formattedData[inputName] = [];
			}
			if (inputEl.checked) {
				formattedData[inputName].push(inputValue);
			}
		}
		// Handle select elements
		else if (inputEl && inputEl.tagName === "SELECT") {
			const selectElement = inputEl as HTMLSelectElement;
			formattedData[inputName] = selectElement.options[selectElement.selectedIndex].text;
		}
		// Handle phone fields - use complete number if available
		else if (inputEl && inputEl instanceof HTMLInputElement && inputEl.type === "tel") {
			// Check if there's a hidden field with complete number
			const completeInput = form.querySelector(`input[name="${inputName}_complete"]`) as HTMLInputElement;
			if (completeInput && completeInput.value) {
				formattedData[inputName] = completeInput.value;
			} else {
				formattedData[inputName] = inputValue;
			}
		}
		// Handle other form fields
		else {
			// Correct the email domain if the field is "email"
			if (inputName === "email" && typeof inputValue === "string") {
				inputValue = correctEmailDomain(inputValue);
			}
			formattedData[inputName] = inputValue;
		}
	});

	return formattedData;
}

// Helper function to check if form is a CF7 form
function isCF7Form(form: HTMLFormElement): boolean {
	return form.classList.contains('wpcf7-form') || 
		   form.querySelector('.wpcf7-form-control-wrap') !== null ||
		   form.hasAttribute('data-status') ||
		   form.closest('.wpcf7') !== null;
}

// Attach input event listeners to all input fields
export function attachFormValidation(): void {
	// Регистрируем кастомный валидатор для файлов один раз
	if (!validate.validators.file) {
		validate.validators.file = function(value, options) {
			if (!value) {
				return; // presence проверяется отдельно
			}
			
			// value будет объектом File
			const file = value;
			
			// Проверка расширения
			if (options.extensions && file.name) {
				const ext = file.name.split('.').pop()?.toLowerCase();
				if (!ext || !options.extensions.includes(ext)) {
					return `resume must be one of the following extensions: ${options.extensions.join(', ')}`;
				}
			}
			
			// Проверка размера (в байтах)
			if (options.maxSize && file.size > options.maxSize) {
				return `resume size must be less than ${Math.round(options.maxSize / 1024 / 1024)} MB`;
			}
			
			return undefined; // no errors
		};
	}

	document.querySelectorAll<HTMLFormElement>('form').forEach((form) => {
		// Skip CF7 forms - они обрабатываются самим плагином
		if (isCF7Form(form)) {
			console.log('[Forms] Skipping CF7 form:', form.id || 'unnamed');
			return;
		}
		
		// Get constraints from global variables
		const formId = form.getAttribute('id');
		const constraints = getFormConstraints(formId);

		// Capture the current constraints in a constant (for closure clarity)
		const currentConstraints: Constraints = constraints;
		// console.log('Get attachFormValidation for', form, 'with', currentConstraints)

		// Listener to validate on input change and blur
		form.querySelectorAll<HTMLInputElement>('input').forEach((input) => {
			// Validate on change (when user leaves the field)
			input.addEventListener('change', (event) => validateField(event, constraints as Constraints));
			// Also validate on blur for better UX (especially for phone numbers)
			input.addEventListener('blur', (event) => validateField(event, constraints as Constraints));
		});

		form.querySelectorAll<HTMLTextAreaElement>('textarea').forEach((textarea) => {
			textarea.addEventListener('change', (event) => validateField(event, constraints as Constraints));
			textarea.addEventListener('blur', (event) => validateField(event, constraints as Constraints));
		});

		// Validate select elements
		form.querySelectorAll<HTMLSelectElement>('select').forEach((select) => {
			select.addEventListener('change', (event) => {
				const target = event.target as HTMLSelectElement;
				const fieldName = target.name;
				const value: Record<string, unknown> = {};
				value[fieldName] = target.value;

				const fieldConstraints: Constraints = {};
				fieldConstraints[fieldName] = constraints[fieldName];

				const errors = validate(value, fieldConstraints);
				updateValidationInputClass(errors, target);
			});
		});

		function restrictToLetters(event: Event): void {
			const target = event.target as HTMLInputElement;
			const regex = /^[\p{L}\s\-,.—']*$/u;

			// Restrict input based on regex
			if (!regex.test(target.value)) {
				target.value = target.value.replace(/[^\p{L}\s\-,.—']/gu, '');
			}

			// Restrict input to max 30 characters
			if (target.value.length > 30) {
				target.value = target.value.slice(0, 30);
			}
		}
		

		// Add listeners for name inputs
		const nameInputs = document.querySelectorAll<HTMLInputElement>('input[name="name"]');

		nameInputs.forEach(input => {
			input.addEventListener('input', restrictToLetters);
		});


		function restrictLargeEmail(event: Event): void {
			const target = event.target as HTMLInputElement;
			if (target.value.length > 50) {
				target.value = target.value.slice(0, 50);
			}
			// Also restrict spaces in email input
			target.value = target.value.replace(/\s+/g, '');
		}

		const emailInputs = document.querySelectorAll<HTMLInputElement>('input[type="email"]');
		emailInputs.forEach(input => {
			input.addEventListener('input', restrictLargeEmail);
		});

		// Phone input restrictions are handled by IMask, no need for additional restrictions

		// Handle file inputs - add class when file is selected and update label
		const fileInputs = form.querySelectorAll<HTMLInputElement>('input[type="file"]#form-resume');
		fileInputs.forEach(input => {

			const parentForm = input.closest('form');

			if (!parentForm) return;

			const spanLabel = parentForm.querySelector('.form-file-label') as HTMLElement;

			if (!spanLabel) return;

			const spanRemoveFile = parentForm.querySelector('.form-file-label__remove-file-btn__text') as HTMLElement;

			if (!spanRemoveFile) return;

			const removeFileBtn = parentForm.querySelector('.form-file-label__remove-file-btn') as HTMLButtonElement;

			// Function to update file state
			const updateFileState = (files: FileList | null) => {
				if (files && files.length > 0) {
					input.classList.add('has-file');
					spanLabel.classList.add('has-file');
					let fileName = files[0].name;
					if (fileName.length > 15) {
						const positionDot = fileName.lastIndexOf('.');
						if (positionDot !== -1) {
							fileName = fileName.slice(0, 15) + '... ' + fileName.slice(positionDot);
						} else {
							fileName = fileName.slice(0, 15) + '...';
						}
					}
					spanRemoveFile.textContent = fileName;
				} else {
					input.classList.remove('has-file');
					spanLabel.classList.remove('has-file');
					spanRemoveFile.textContent = '';
					// Reset input value
					input.value = '';
				}
			};

			// Check initial state
			if (input.files && input.files.length > 0) {
				updateFileState(input.files);
			}
			
			// Handle file input change
			input.addEventListener('change', function() {
				updateFileState(this.files);
			});

			// Handle remove file button
			if (removeFileBtn) {
				removeFileBtn.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					updateFileState(null);
				});
			}

			// Drag and drop handlers
			const container = spanLabel.closest('.is-relative') as HTMLElement;
			if (container) {
				// Prevent default drag behaviors
				['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
					container.addEventListener(eventName, function(e) {
						e.preventDefault();
						e.stopPropagation();
					});
				});

				// Highlight drop area when item is dragged over it
				container.addEventListener('dragenter', function() {
					container.classList.add('is-dragover');
				});

				container.addEventListener('dragleave', function(e) {
					// Only remove highlight if we're leaving the container itself
					if (!container.contains(e.relatedTarget as Node)) {
						container.classList.remove('is-dragover');
					}
				});

				container.addEventListener('drop', function(e) {
					container.classList.remove('is-dragover');
					
					const dt = e.dataTransfer;
					if (dt && dt.files && dt.files.length > 0) {
						// Create a new FileList-like object and assign to input
						const dataTransfer = new DataTransfer();
						dataTransfer.items.add(dt.files[0]);
						input.files = dataTransfer.files;
						
						// Trigger change event to update UI
						const changeEvent = new Event('change', { bubbles: true });
						input.dispatchEvent(changeEvent);
					}
				});
			}
		});

		// console.log('Ready for submit form', form)

		// Check if form already has submit handler to prevent duplicate handlers
		if ((form as any)._formHandlerAttached) {
			console.log('[Forms] Form', form.id, 'already has submit handler, skipping');
			return;
		}
		
		// Mark form as having handler attached
		(form as any)._formHandlerAttached = true;

		// Handle form submission
		form.addEventListener('submit', function (event: Event): void {
			console.log('[Forms] Submit event triggered for form:', form.id);
			event.preventDefault();
			event.stopImmediatePropagation(); // Prevent other handlers

			// Check if already submitting
			if ((form as any)._isSubmitting) {
				console.warn('[Forms] Form already submitting, preventing duplicate');
				return;
			}

			const values: Record<string, any> = {};

			form.querySelectorAll<HTMLInputElement | HTMLTextAreaElement>('input, textarea').forEach((input) => {
				// Обработка файлов
				if (input instanceof HTMLInputElement && input.type === 'file') {
					if (input.files && input.files.length > 0) {
						values[input.name] = input.files[0]; // Передаем File объект для валидации
					} else {
						values[input.name] = undefined; // Для presence валидации
					}
					return;
				}

				if (
					!(input instanceof HTMLTextAreaElement)
					&& (input.type === 'radio' || input.type === 'checkbox')
				) {
					if (input.checked) {
						if (!values[input.name]) {
							values[input.name] = [];
						}
						values[input.name].push(input.value);
					}
				} else {
					// For phone inputs, get unmasked value if IMask is available
					if (input instanceof HTMLInputElement && input.type === 'tel') {
						// Try to get unmasked value from IMask
						if (phoneMasks.has(input)) {
							const mask = phoneMasks.get(input);
							const unmaskedValue = mask.unmaskedValue || '';
							// Clean unmasked value to get only digits for validation
							let cleanValue = unmaskedValue.replace(/[^\d+]/g, '');
							// Remove leading + if present for length validation (we count only digits)
							if (cleanValue.startsWith('+')) {
								cleanValue = cleanValue.substring(1);
							}
							values[input.name] = cleanValue || '';
						} else {
							// Remove mask characters manually if no mask instance
							let cleanValue = input.value.replace(/[^\d+]/g, '');
							if (cleanValue.startsWith('+')) {
								cleanValue = cleanValue.substring(1);
							}
							values[input.name] = cleanValue || '';
						}
					} else {
						values[input.name] = input.value;
					}
				}
			});

			// Convert single checkboxes and radios to single value instead of array
			for (const key in values) {
				if (Array.isArray(values[key]) && values[key].length === 1) {
					values[key] = values[key][0];
				}
			}

			console.log('[Forms] Form values for validation:', values);

			// Validate the form
			const errors = validate(values, currentConstraints);

			// Update validation warning messages and hilights
			updateValidationWarnings(errors, form);

			if (errors) {
				console.error('[Forms] Validation errors:', errors);
			} else {
				console.log('[Forms] Form validation passed, submitting...');
				submitFormViaFormCarry(form);
			}
		}, { once: false }); // Allow multiple submissions but prevent duplicates with flag
	});
}

/*
 * Custom notification for forms
 */
function showNotification(form: HTMLFormElement, message: string, type: 'fail' | 'success'): void {
	const el = document.createElement('div');
	el.classList.add('form-notification', 'typo--body-big__desktop', 'typo--medium');
	el.textContent = message;
	if (type === 'fail') {
		el.classList.add('form-notification--failed');
	} else {
		el.classList.add('form-notification--success');
	}
	document.body.appendChild(el);
	setTimeout(() => {
		el.remove();
	}, 5500);
}

// Function to set the form action based on the form's ID
function getFormActionHref(form: HTMLFormElement): string {
	const formActions: Record<string, string> = {
		'example-form': FORMCARRY_ENDPOINT_FORM
	};

	const formId = form.getAttribute('id') ?? '';
	if (formActions[formId]) {
		return formActions[formId];
	}
	return FORMCARRY_ENDPOINT_FORM;
}

// Phone masking functions
async function getCountryFromClientIp(): Promise<Country | null> {
	try {
		// Check session storage first
		const storedIpRegion = sessionStorage.getItem('ipRegion');
		if (storedIpRegion) {
			const ipData: IpData = JSON.parse(storedIpRegion);
			if (ipData && ipData.country_code) {
				const countries = await fetchCountries();
				const country = findCountryByIso(countries, ipData.country_code);
				if (country) {
					return country;
				}
			}
		}

		// If not in session storage or data is invalid, fetch from API
		const countryCode = await getClientIp(); // check client ip and return country code from country object
		const countries = await fetchCountries(); // fetch all countries provided in /assets/data/countries.json
		const country = findCountryByIso(countries, countryCode); // simple map function that return country object
		if (country) {
			return country;
		}
		return null;
	} catch (error) {
		console.error('Error in processing:', error);
		throw error;
	}
}

function getClientIp(): Promise<string> {
	const myUrl = 'https://api.ipstack.com/check?access_key=d8bace1287ee24f129651eeb25a270e1';
	return fetch(myUrl)
		.then((response) => response.json())
		.then((data: IpData) => {
			sessionStorage.setItem('ipRegion', JSON.stringify(data));
			return data.country_code;
		})
		.catch((error) => {
			console.error('Error:', error);
			throw error;
		});
}

function fetchCountries(): Promise<Country[]> {
	return fetch((window as any).themeUrl?.baseUrl + '/assets/data/countries.json')
		.then((response) => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.catch((error) => {
			console.error('There has been a problem with your fetch operation:', error);
			throw error;
		});
}

function findCountryByIso(countries: Country[], isoCode: string): Country | undefined {
	return countries.find((country) => country.iso === isoCode);
}

function resetPhoneField(phoneInput: HTMLInputElement): void {
	const countryCodeElement = phoneInput.closest('.form__field--tel')?.querySelector('.form__countries__current__code');
	const countryCode = countryCodeElement?.textContent || '';
	
	if (countryCode && phoneInput.value) {
		// Remove all occurrences of the country code from the beginning
		let cleanValue = phoneInput.value;
		const countryCodePattern = new RegExp(`^${countryCode.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\s*`, 'g');
		cleanValue = cleanValue.replace(countryCodePattern, '');
		
		// Remove any extra spaces at the beginning
		cleanValue = cleanValue.trim();
		
		// Only update if we actually cleaned something
		if (cleanValue !== phoneInput.value) {
			phoneInput.value = cleanValue;
		}
	}
}

function getCompletePhoneNumber(phoneInput: HTMLInputElement): string {
	const countryCodeElement = phoneInput.closest('.form__field--tel')?.querySelector('.form__countries__current__code');
	const countryCode = countryCodeElement?.textContent || '';
	let phoneNumber = phoneInput.value || '';
	
	if (phoneNumber && countryCode) {
		// Clean the phone number by removing any existing country codes
		let cleanPhoneNumber = phoneNumber;
		
		// Remove the country code if it's already at the beginning
		if (cleanPhoneNumber.startsWith(countryCode + ' ')) {
			cleanPhoneNumber = cleanPhoneNumber.substring((countryCode + ' ').length);
		}
		
		// Also check for multiple occurrences and clean them up
		const countryCodePattern = new RegExp(`^${countryCode.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\s*`, 'g');
		cleanPhoneNumber = cleanPhoneNumber.replace(countryCodePattern, '');
		
		// Remove any extra spaces at the beginning
		cleanPhoneNumber = cleanPhoneNumber.trim();
		
		if (cleanPhoneNumber) {
			// Clean the phone number to keep only digits and +
			const cleanDigits = cleanPhoneNumber.replace(/[^\d+]/g, '');
			const completeNumber = countryCode.replace(/[^\d+]/g, '') + cleanDigits;
			
			return completeNumber;
		} else {
			// If no phone number left after cleaning, just return the country code
			const cleanCountryCode = countryCode.replace(/[^\d+]/g, '');
			return cleanCountryCode;
		}
	}
	
	return phoneNumber;
}

function setNewCountry(el: HTMLElement): void {
	document.querySelectorAll('.form__countries__list-holder').forEach((phoneList) => {
		phoneList.classList.remove('show');
		(phoneList as HTMLElement).style.display = 'none';
	});

	document.querySelectorAll('.form__countries__list__item').forEach((countryListItem) => {
		countryListItem.classList.remove('active');
		(countryListItem as HTMLElement).style.display = 'flex';
	});

	const code = el.getAttribute('code');
	const mask = el.getAttribute('mask');
	const image = el.getAttribute('image-url');
	
	if (!code || !mask || !image) return;

	// Update flag and country code in selector
	document.querySelectorAll('.form__countries__current__flag img').forEach(el => {
		el.setAttribute('src', image);
		el.setAttribute('data-src', image);
	});
	
	// Update country code in selector
	document.querySelectorAll('.form__countries__current__code').forEach((codeElement) => {
		codeElement.textContent = code;
	});

	const phoneInputs = document.querySelectorAll('input[type="tel"]') as NodeListOf<HTMLInputElement>;
	phoneInputs.forEach((phoneInput) => {
		if (phoneMasks.has(phoneInput)) {
			phoneMasks.get(phoneInput).destroy();
		}

		// Clean the phone field before applying new mask
		resetPhoneField(phoneInput);

		// Use only the mask part (without country code) for the input
		if (typeof (window as any).IMask !== 'undefined') {
			const newMask = (window as any).IMask(phoneInput, {
				mask: mask,
				lazy: true,
				overwrite: true,
				autofix: true,
			});
			phoneMasks.set(phoneInput, newMask);
		}

		phoneInput.value = ''; // Clear the input value
		phoneInput.setAttribute('placeholder', mask.replaceAll('0', '0'));
	});

	document.querySelectorAll('.form__countries__list__item[code="' + code + '"]').forEach((countryListItemCurrent) => {
		countryListItemCurrent.classList.add('active');
	});
}

function createCountriesList(currentCountry: Country | null): void {
	fetchCountries()
		.then((countries) => {
			let listHtml = '';

			countries.forEach((country) => {
				const activeClass = currentCountry && country.iso === currentCountry.iso ? 'active' : '';
				const countryCode = country.code.replaceAll('\\', '');
				listHtml += `
					<div class="form__countries__list__item ${activeClass}" image-url="${country.flag}" code="${countryCode}" mask="${country.mask}">
						<span class="countries-list__name">${country.name}</span>
						<span class="countries-list__code">${countryCode}</span>
					</div>`;
			});

			const phoneLists = document.querySelectorAll('.form__countries__list');
			phoneLists.forEach((phoneList) => {
				phoneList.innerHTML = listHtml;
			});

			document.querySelectorAll('.form__countries__list__item').forEach((countryElement) => {
				countryElement.addEventListener('click', function () {
					setNewCountry(this as HTMLElement);
				});
			});

			const searchInputs = document.querySelectorAll('#countrySearchInput') as NodeListOf<HTMLInputElement>;
			searchInputs.forEach((searchInput) => {
				searchInput.addEventListener('input', function () {
					const searchValue = this.value.toLowerCase();
					document.querySelectorAll('.form__countries__list__item').forEach((countryElement) => {
						const countryName = countryElement.querySelector('.countries-list__name')?.textContent?.toLowerCase() || '';
						(countryElement as HTMLElement).style.display = countryName.includes(searchValue) ? '' : 'none';
					});
				});
			});
		})
		.catch((error) => {
			console.error('Error:', error);
		});
}

export async function initPhoneMasking(): Promise<void> {
	// Skip if already initialized
	if (phoneMaskingInitialized) {
		return;
	}

	if ((window as any).formsScriptLoaded) {
		return;
	}

	// Check if there are any forms with .form__countries selector
	const formsWithCountries = document.querySelectorAll('form .form__countries');
	if (formsWithCountries.length === 0) {
		console.log('[Forms] No forms with .form__countries found, skipping phone masking initialization');
		return;
	}

	(window as any).formsScriptLoaded = true;
	
	// Set flag immediately to prevent multiple calls
	phoneMaskingInitialized = true;
	
	try {
		const country = await getCountryFromClientIp();
		if (country) {
			// Update flag and country code in selector
			document.querySelectorAll('.form__countries__current__flag img').forEach((flag) => {
				flag.setAttribute('src', country.flag);
				flag.setAttribute('data-src', country.flag);
			});
			
			// Update country code in selector
			document.querySelectorAll('.form__countries__current__code').forEach((codeElement) => {
				codeElement.textContent = country.code;
			});

			// Create mask without the country code (only the number part)
			// Only initialize phone inputs that are inside forms with .form__countries
			const phoneInputs = document.querySelectorAll('input[type="tel"]') as NodeListOf<HTMLInputElement>;
			phoneInputs.forEach((phoneInput) => {
				// Check if this phone input is inside a form with .form__countries
				const form = phoneInput.closest('form');
				if (!form || !form.querySelector('.form__countries')) {
					return;
				}

				if (phoneInput.getAttribute('data-initialized') === 'true') {
					return;
				}
				// Skip if already initialized
				if (phoneMasks.has(phoneInput)) {
					return;
				}
				
				// Use only the mask part (without country code) for the input
				if (typeof (window as any).IMask !== 'undefined') {
					const mask = (window as any).IMask(phoneInput, {
						mask: country.mask,
						lazy: true,
					});
					phoneMasks.set(phoneInput, mask);
				}

				// Set placeholder without country code
				phoneInput.setAttribute('placeholder', country.mask.replaceAll('0', '0'));
				phoneInput.setAttribute('data-initialized', 'true');
			});

			// Only create countries list if it doesn't exist yet
			const existingList = document.querySelector('.form__countries__list');
			if (!existingList || existingList.children.length === 0) {
				createCountriesList(country);
			}

			// Triggers to show countries list (only add once)
			if (!countrySelectorInitialized && !countrySelectorHandler) {
				countrySelectorHandler = (e: Event) => {
					const target = e.target as HTMLElement;
					if (
						target.className === "form__countries__current" ||
						target.className === "form__countries__current__flag" ||
						target.className === "form__countries__current__arrow" ||
						target.className === "form__countries__current__code"
					) {
						const holder = target.closest('.form__countries')?.querySelector('.form__countries__list-holder') as HTMLElement;
						if (holder) {
							if (holder.classList.contains('show')) {
								holder.classList.remove('show', 'show--top', 'show--bottom');
								holder.style.display = 'none';
                            } else {
                                // Show temporarily to measure
                                holder.style.display = 'block';
                                const holderRect = holder.getBoundingClientRect();
                                const windowHeight = window.innerHeight;
                                const bottomSpace = windowHeight - holderRect.bottom;

                                holder.classList.remove('show--top', 'show--bottom');
                                holder.classList.add('show');
                                if (bottomSpace < 100) {
                                    holder.classList.add('show--top');
                                } else {
                                    holder.classList.add('show--bottom');
                                }
                            }
                        }
                    }
                };
                
                document.querySelector('body')?.addEventListener("click", countrySelectorHandler);
                countrySelectorInitialized = true;
            }
        } else {
			// Only create countries list if it doesn't exist yet
			const existingList = document.querySelector('.form__countries__list');
			if (!existingList || existingList.children.length === 0) {
				createCountriesList(null);
			}
		}
	} catch (error) {
		console.error('Error:', error);
		// Reset flag on error to allow retry
		phoneMaskingInitialized = false;
	}
}

function updatePhoneWithCountryCode(form: HTMLFormElement): void {
	// Check if we've already updated this form in this submission cycle
	if ((form as any)._phoneUpdated) {
			return;
		}

	const phoneInputs = form.querySelectorAll('input[type="tel"]') as NodeListOf<HTMLInputElement>;
	
	phoneInputs.forEach((phoneInput) => {
		// Get the complete phone number with country code
		const completeNumber = getCompletePhoneNumber(phoneInput);
		
		if (completeNumber) {
			// Create or update hidden input with complete number
			let hiddenInput = form.querySelector(`input[name="${phoneInput.name}_complete"]`) as HTMLInputElement;
			if (!hiddenInput) {
				hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = phoneInput.name + '_complete';
				form.appendChild(hiddenInput);
			}
			hiddenInput.value = completeNumber;
			
			// Keep the visible input unchanged - user should not see country code added
			// The complete number will be sent via hidden field
		}
	});
	
	// Mark this form as updated to prevent multiple calls
	(form as any)._phoneUpdated = true;
	
	// Reset the flag after a short delay to allow for future submissions
    setTimeout(() => {
		(form as any)._phoneUpdated = false;
	}, 1000);
}

// Custom select initialization
function initCustomSelects(): void {
	const selects = document.querySelectorAll<HTMLSelectElement>('form select:not(.wpcf7-select)');
	
	selects.forEach((select) => {
		// Skip if already initialized
		if (select.hasAttribute('data-custom-select-initialized')) {
			return;
		}
		
		// Skip CF7 selects
		if (select.closest('.wpcf7-form')) {
			return;
		}
		
		// Create custom select wrapper
		const wrapper = document.createElement('div');
		wrapper.className = 'form__select-custom';
		
		// Create custom select button
		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'form__select-custom__current';
		button.setAttribute('aria-haspopup', 'listbox');
		button.setAttribute('aria-expanded', 'false');
		
		// Create custom select text span
		const textSpan = document.createElement('span');
		textSpan.className = 'form__select-custom__current__text';
		
		// Set initial text
		const selectedOption = select.options[select.selectedIndex];
		if (selectedOption && selectedOption.value) {
			textSpan.textContent = selectedOption.textContent || selectedOption.value;
		} else {
			// Use placeholder if available
			const placeholder = select.getAttribute('data-placeholder') || select.getAttribute('placeholder') || 'Select an option...';
			textSpan.textContent = placeholder;
			textSpan.classList.add('placeholder');
		}
		
		button.appendChild(textSpan);
		
		// Create arrow
		const arrow = document.createElement('span');
		arrow.className = 'form__select-custom__current__arrow';
		button.appendChild(arrow);
		
		// Create dropdown list
		const listHolder = document.createElement('div');
		listHolder.className = 'form__select-custom__list-holder';
		listHolder.style.display = 'none';
		
		const list = document.createElement('ul');
		list.className = 'form__select-custom__list';
		list.setAttribute('role', 'listbox');
		
		// Create options
		Array.from(select.options).forEach((option, index) => {
			if (option.value === '' && option.textContent === '') {
				return; // Skip empty placeholder options
			}
			
			const listItem = document.createElement('li');
			listItem.className = 'form__select-custom__list__item';
			listItem.setAttribute('role', 'option');
			listItem.setAttribute('data-value', option.value);
			listItem.textContent = option.textContent || option.value;
			
			if (select.selectedIndex === index && option.value) {
				listItem.classList.add('active');
				textSpan.textContent = option.textContent || option.value;
				textSpan.classList.remove('placeholder');
				// Set initial has-value class for floating labels
				select.classList.add('has-value');
			}
			
			listItem.addEventListener('click', () => {
				// Update native select
				select.selectedIndex = index;
				
				// Update has-value class for floating labels
				if (select.value && select.value !== '') {
					select.classList.add('has-value');
				} else {
					select.classList.remove('has-value');
				}
				
				select.dispatchEvent(new Event('change', { bubbles: true }));
				
				// Update custom select display
				textSpan.textContent = option.textContent || option.value;
				textSpan.classList.remove('placeholder');
				
				// Update active state
				list.querySelectorAll('.form__select-custom__list__item').forEach(item => {
					item.classList.remove('active');
				});
				listItem.classList.add('active');
				
				// Close dropdown
				listHolder.style.display = 'none';
				listHolder.classList.remove('show', 'show--top', 'show--bottom');
				button.setAttribute('aria-expanded', 'false');
			});
			
			list.appendChild(listItem);
		});
		
		listHolder.appendChild(list);
		
		// Toggle dropdown
		button.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			
			const isOpen = listHolder.classList.contains('show');
			
			// Close all other selects
			document.querySelectorAll('.form__select-custom__list-holder.show').forEach((otherHolder) => {
				if (otherHolder !== listHolder) {
					(otherHolder as HTMLElement).style.display = 'none';
					otherHolder.classList.remove('show', 'show--top', 'show--bottom');
					const otherButton = otherHolder.closest('.form__select-custom')?.querySelector('.form__select-custom__current') as HTMLElement;
					if (otherButton) {
						otherButton.setAttribute('aria-expanded', 'false');
					}
				}
			});
			
			if (isOpen) {
				listHolder.style.display = 'none';
				listHolder.classList.remove('show', 'show--top', 'show--bottom');
				button.setAttribute('aria-expanded', 'false');
				// Update floating label state when closing
				if (select.value && select.value !== '') {
					select.classList.add('has-value');
				} else {
					select.classList.remove('has-value');
				}
			} else {
				listHolder.style.display = 'block';
				const holderRect = listHolder.getBoundingClientRect();
				const windowHeight = window.innerHeight;
				const bottomSpace = windowHeight - holderRect.bottom;
				
				listHolder.classList.remove('show--top', 'show--bottom');
				listHolder.classList.add('show');
				if (bottomSpace < 100) {
					listHolder.classList.add('show--top');
				} else {
					listHolder.classList.add('show--bottom');
				}
				button.setAttribute('aria-expanded', 'true');
				// Add has-value class when opening dropdown for floating labels
				select.classList.add('has-value');
			}
		});
		
		// Close dropdown when clicking outside
		document.addEventListener('click', (e) => {
			if (!wrapper.contains(e.target as Node)) {
				listHolder.style.display = 'none';
				listHolder.classList.remove('show', 'show--top', 'show--bottom');
				button.setAttribute('aria-expanded', 'false');
				// Update floating label state when closing
				if (select.value && select.value !== '') {
					select.classList.add('has-value');
				} else {
					select.classList.remove('has-value');
				}
			}
		});
		
		// Save parent and next sibling before moving select
		const parent = select.parentNode;
		const nextSibling = select.nextSibling;
		
		// Hide native select
		select.style.position = 'absolute';
		select.style.opacity = '0';
		select.style.pointerEvents = 'none';
		select.style.width = '1px';
		select.style.height = '1px';
		select.setAttribute('aria-hidden', 'true');
		select.setAttribute('tabindex', '-1');
		select.setAttribute('data-custom-select-initialized', 'true');
		
		// Add all elements to wrapper
		wrapper.appendChild(select);
		wrapper.appendChild(button);
		wrapper.appendChild(listHolder);
		
		// Replace select with wrapper in DOM
		if (parent) {
			if (nextSibling) {
				parent.insertBefore(wrapper, nextSibling);
			} else {
				parent.appendChild(wrapper);
			}
		}
	});
}

// Initialize floating labels
function initFloatingLabels(): void {
	const floatingLabelForms = document.querySelectorAll<HTMLFormElement>('form.floating-label');
	
	floatingLabelForms.forEach((form) => {
		// Handle input and textarea fields
		const inputs = form.querySelectorAll<HTMLInputElement | HTMLTextAreaElement>('input:not([type="submit"]):not([type="button"]):not([type="file"]), textarea');
		
		inputs.forEach((input) => {
			const field = input.closest('.form__field');
			
			// Check initial state
			const updateFloatingLabel = () => {
				if (input.value && input.value.trim() !== '') {
					input.classList.add('has-value');
					if (field) {
						field.classList.add('has-value');
					}
				} else {
					input.classList.remove('has-value');
					if (field) {
						field.classList.remove('has-value');
					}
				}
			};
			
			// Set initial state
			updateFloatingLabel();
			
			// Update on input
			input.addEventListener('input', updateFloatingLabel);
			input.addEventListener('change', updateFloatingLabel);
			
			// Handle focus/blur for field state
			input.addEventListener('focus', () => {
				if (field) {
					field.classList.add('is-focused');
				}
			});
			input.addEventListener('blur', () => {
				if (field) {
					field.classList.remove('is-focused');
				}
				updateFloatingLabel();
			});
		});
		
		// Handle select fields
		const selects = form.querySelectorAll<HTMLSelectElement>('select');
		selects.forEach((select) => {
			const field = select.closest('.form__field');
			const updateSelectLabel = () => {
				if (select.value && select.value !== '') {
					select.classList.add('has-value');
					if (field) {
						field.classList.add('has-value');
					}
				} else {
					select.classList.remove('has-value');
					if (field) {
						field.classList.remove('has-value');
					}
				}
			};
			
			// Set initial state
			updateSelectLabel();
			
			// Update on change
			select.addEventListener('change', updateSelectLabel);
			
			// Also update when custom select button is focused
			const customSelectButton = select.closest('.form__select-custom')?.querySelector('.form__select-custom__current');
			if (customSelectButton) {
				customSelectButton.addEventListener('focus', () => {
					select.classList.add('has-value');
					if (field) {
						field.classList.add('is-focused', 'has-value');
					}
				});
				customSelectButton.addEventListener('blur', () => {
					if (field) {
						field.classList.remove('is-focused');
					}
					// Only remove has-value if no value selected
					if (!select.value || select.value === '') {
						select.classList.remove('has-value');
						if (field) {
							field.classList.remove('has-value');
						}
					}
				});
			}
		});
	});
}

// Attach form validation
document.addEventListener("DOMContentLoaded", () => {
	console.log('[Forms] Initializing forms...');
	attachFormValidation();
	
	// Initialize custom selects first (so floating labels can work with them)
	initCustomSelects();
	
	// Initialize floating labels after custom selects are ready
	// Use setTimeout to ensure DOM is fully updated
	setTimeout(() => {
		initFloatingLabels();
	}, 0);
	
	// Initialize phone masking only if IMask is available and forms with .form__countries exist
	if (typeof (window as any).IMask !== 'undefined') {
		// Check if there are forms with .form__countries before initializing
		const formsWithCountries = document.querySelectorAll('form .form__countries');
		if (formsWithCountries.length > 0) {
			initPhoneMasking();
		} else {
			console.log('[Forms] No forms with .form__countries found, skipping phone masking');
		}
	} else {
		console.warn('[Forms] IMask not available, phone masking will not work');
	}
});
