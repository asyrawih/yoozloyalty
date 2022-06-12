const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    // App
    .js('resources/js/app/app.js', 'public/js/app.js').vue()
    .sass('resources/sass/app/app.scss', 'public/css/app.css')

    // Campaign
    .js('resources/js/campaign/app.js', 'public/js/campaign.js').vue()
    .sass('resources/sass/campaign/app.scss', 'public/css/campaign.css')

    // Staff
    .js('resources/js/staff/app.js', 'public/js/staff.js').vue()
    .sass('resources/sass/staff/app.scss', 'public/css/staff.css')

    // Website
    .js('resources/js/site/app.js', 'public/js/site.js').vue()
    .sass('resources/sass/site/app.scss', 'public/css/site.css')

    .extract([
        '@ckeditor/ckeditor5-build-classic',
        '@ckeditor/ckeditor5-vue2',
        '@mdi/font',
        '@vue/composition-api',
        '@websanova/vue-auth',
        'axios',
        'babel-polyfill',
        'core-js',
        'es6-promise',
        'js-cookie',
        'lodash',
        'moment',
        'nprogress',
        'pusher-js',
        'vee-validate',
        'vue',
        'vue-analytics',
        'vue-axios',
        'vue-csv-import',
        'vue-gallery',
        'vue-i18n',
        'vue-qrcode-component',
        'vue-router',
        'vue-tel-input-vuetify',
        'vue-the-mask',
        'vue2-animate',
        'vuetify',
        'vuex'
        // 'babel-polyfill',
        // 'vue',
        // 'lodash',
        // 'vuetify',
        // 'pusher-js',
        // 'js-cookie',
        // 'moment',
        // 'es6-promise',
        // 'nprogress',
        // 'vee-validate',
        // 'vue-axios',
        // 'vue-i18n',
        // 'vue-qrcode-component',
        // 'vue-router',
        // 'vuex',
        // 'vue-template-compiler',
        // 'vue-analytics',
        // 'vue-the-mask'
    ])
    .sourceMaps()
    .version();
