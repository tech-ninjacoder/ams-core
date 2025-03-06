const mix = require("laravel-mix");

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

mix.setPublicPath("./")
.setResourceRoot("../") // Turns assets paths in css relative to css file
    // .options({
    //     processCssUrls: false,
    // })
    .sass("src/resources/sass/payslip.scss", "css/payslip.css")
    .sass("src/resources/sass/core/core.scss", "css/core.css")
    .sass("node_modules/dropzone/src/dropzone.scss", "css/dropzone.css")
    .sass("src/resources/sass/_global.scss", "css/fontawesome.css")
    .js("src/resources/js/mainApp.js", "js/core.js")
.extract([
    // Extract packages from node_modules to vendor.js
    "jquery",
    "bootstrap",
    "popper.js",
    "axios",
    "sweetalert2",
    "lodash"
])

if (mix.inProduction()) {
    mix.version().options({
        // Optimize JS minification process
        terser: {
        cache: true,
        parallel: true,
        sourceMap: true
    }
});}
else {
    // Uses inline source-maps on development
    mix.webpackConfig({
    devtool: "inline-source-map",
    });
}