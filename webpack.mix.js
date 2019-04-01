// Javascript dependencies are compiled with Laravel Mix https://laravel.com/docs/5.5/mix
let mix = require('laravel-mix');

mix
    .sass('resources/css/main.scss', 'themes/theme_brand_central/css')
    .options({
        processCssUrls: false
    })
    .js('resources/js/main.js', 'themes/theme_brand_central/js')
    .js('resources/js/asset-download.js', 'themes/theme_brand_central/js')
    .react('resources/react-app/asset/asset.js', 'themes/theme_brand_central/js')
    .react('resources/react-app/lightboxes/lightbox.js', 'themes/theme_brand_central/js')

// Other options:
// mix.sass, mix.js, mix.scripts, mix.stylus, mix.styles, mix.react, mix.webpackConfig, mix.copy, mix.copyDirectory,
// mix.browserSync, mix.disableNotifications
//
// Optional modifiers
// mix.js(...).version(), mix.js(...).extract(...)
//
// Accessing Info
// mix.inProduction()
