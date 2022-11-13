@props([
    'defaultLocale' => 'en',
    'translations',
    'locales',
    'privacyLinks' => [],
])

<div>
    <!-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci -->
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function app() {
        return {
            privacyLink: '{{ $privacyLinks[$defaultLocale] ?? '#privacy-policy' }}',
            defaultLocale: '{{ $defaultLocale }}',

            translations: {
                @foreach($translations as $key => $translation)
                "{{ $key  }}": "{{ $translation  }}",
                @endforeach
            },

            locales: [
                @foreach($locales as $locale)
                    "{{ $locale  }}",
                @endforeach
            ],

            privacyLinks: {
                @foreach($privacyLinks as $locale => $privacyLink)
                    {{ $locale }}: "{{ $privacyLink }}",
                @endforeach
            },

            init() {
                [...document.querySelectorAll('[trans]')]
                    .forEach(el => {
                        el.setAttribute('data-trans-key', el.innerText)
                    })

                this.transAll()
            },

            trans(string) {
                if(!!this.translations) {
                    return this.translations[string] ?? string
                }

                return string
            },

            transAll() {
                [...document.querySelectorAll('[trans]')]
                    .forEach(el => {
                        el.innerText = this.trans(el.getAttribute('data-trans-key'))
                    })
            },

            fetchJson(url, options) {
                options.headers = {
                    ...(options.headers ?? {}),
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }

                return fetch(url, options)
                    .then(res => res.json())
                    .catch(err => {
                        console.log('Err: ', err)
                    })
            },

            changeLanguage(locale) {
                let { origin, pathname } = new URL(document.location)

                console.log('Changing locale to: ', locale)

                // Another way: url.pathname.replace(/[^\/]+$/, '')
                pathname = pathname.split('/').map(p => !p.match(/\.\w+$/) ? p : '').join('/')
                const url = `${origin}${pathname}trans.php?lang=${locale}`

                if(Object.keys(this.privacyLinks).length > 0) {
                    this.privacyLink = this.privacyLinks[locale]
                }

                this.fetchJson(url, {
                    method: 'GET',
                })
                .then(data => {
                    console.log('Response: ', data)

                    this.translations = data

                    this.transAll()
                })
            }
        }
    }
</script>
