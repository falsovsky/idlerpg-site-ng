const { src, dest, parallel } = require("gulp");
const browserify = require('gulp-browserify');
const minifyCSS = require('gulp-csso');
const concat = require('gulp-concat');

var node_modules = "node_modules/";

var maincss = [
    node_modules + "bootswatch/dist/sketchy/bootstrap.min.css",
    "public/css/local.css"
];

async function js() {
    src(['public/js/app.js'], { sourcemaps: true })
        .pipe(browserify())
        .pipe(concat('main.min.js'))
        .pipe(dest("public/js", { sourcemaps: true }))
};

async function css() {
    src(maincss)
        .pipe(concat('main.min.css'))
        .pipe(minifyCSS())
        .pipe(dest("public/css"))
};

exports.js = js;
exports.css = css;
exports.default = parallel(css, js);
