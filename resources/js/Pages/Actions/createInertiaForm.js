import {Inertia} from "@inertiajs/inertia";
import * as ziggyRoute from 'ziggy-js';

export function createInertiaForm({
    action,
    route,
    // inertiaForm,
    method = 'post',
    onSubmit,
    onSuccess,
    onBefore,
    onStart,
    onFinish,
    onError,
    headers = {},
    isJson = true,
    validation = {},
}) {
    // let inertiaForm = useForm({
    // });

    const formData = {}

    const bindData = (node, validation = {}, isValidated = true) => {

        [...node.querySelectorAll('input,select,textarea,[include]')]
            .forEach(el => {
                if(el.hasAttribute('exclude')) {
                    return
                }

                if(!!validation[el.name]) {
                    if(!validation[el.name](el)) {
                        isValidated = false
                        return
                    }
                }

                formData[el.name] = el.value
            })
    }

    return [
        formData,
        (node) => {
            if(node.tagName !== 'FORM') {
                return (node) => {};
            }

            bindData(node);

            const internalOnSubmit = (e) => {
                e.preventDefault();

                if(!!ziggyRoute) {
                    action = ziggyRoute(route)
                }

                action = action ?? node.getAttribute('action');
                method = method ?? node.getAttribute('method').toLowerCase();

                if(!action) {
                    throw new Error({error: 'Missing action attribute on form: ', form: node})
                }

                if(!method) {
                    throw new Error({error: 'Missing method attribute on form: ', form: node})
                }

                let isValidated = true;

                bindData(node, validation, isValidated)

                if(!isValidated) {
                    return false
                }

                const jsonHeaders = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }

                console.log('form internal formData: ', {formData})

                const internalHeaders = isJson ? {...headers, ...jsonHeaders} : headers

                const options = {
                    headers: internalHeaders,
                    onBefore,
                    onStart,
                    onFinish,
                    onSuccess: (res) => onSuccess(res),
                    onError: (errors) => {
                        formData.errors = errors

                        return onError(errors)
                    }
                }

                delete formData['errors']

                if (method === 'delete') {
                    Inertia.delete(action, { ...options, data: formData  })
                } else {
                    Inertia[method](action, formData, options)
                }

                // inertiaForm.submit(method, action, )
            }

            node.addEventListener('submit', internalOnSubmit)

            return {
                destroy() {
                    document.removeEventListener("addEventListener", internalOnSubmit, true);
                },
            };
        }
    ]
}
