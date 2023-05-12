const mix = require('laravel-mix');
require('laravel-mix-bundle-analyzer');

// Completely override the Babel configuration to get rid of async/await transformation
mix.config.babel = () => ({
    plugins: ['@babel/plugin-proposal-object-rest-spread'],
});

mix.js('resources/js/app.js', 'resources/dist').postCss('resources/css/app.css', 'resources/dist', [
    require('postcss-easy-import')(),
    require('tailwindcss')(),
]);

if (process.argv.includes('--analyze')) {
    mix.bundleAnalyzer();
}
