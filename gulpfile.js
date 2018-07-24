// Defining base pathes
var paths = {
    bootstrap: ['./node_modules/bootstrap/scss/'],
    bower: './bower_components/',
    node: './node_modules/',
    scripts: {
        src: {
            admin: './src/js/**/*-admin.js',
            public: './src/js/**/*-public.js',
            all: './src/js/**/*.js'
        },
        dest: {
            admin: './admin/js',
            public: './public/js/'
        }
    },
    styles: {
        src: {
            admin: './src/sass/app-admin.scss',
            public: './src/sass/app-public.scss',
            all: './src/sass/**/*.scss'
        },
        dest: {
            admin: './admin/css/',
            public: './public/css/'
        }
    }
};

var addsrc = require('gulp-add-src');
var babel = require('gulp-babel');
var cleanCSS = require('gulp-clean-css');
var concat = require('gulp-concat');
var del = require('del');
var gulp = require('gulp');
var imagemin = require('gulp-imagemin');
var merge = require('merge-stream');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');

gulp.task('scripts', function() {
    var adminScripts = gulp.src(paths.scripts.src.admin)
        .pipe(babel())
        .pipe(uglify())
        .pipe(concat('gforms-bootstrapper-admin.min.js'))
        .pipe(gulp.dest(paths.scripts.dest.admin));
    var publicScripts = gulp.src(paths.scripts.src.public)
        .pipe(babel())
        .pipe(uglify())
        .pipe(concat('gforms-bootstrapper-public.min.js'))
        .pipe(gulp.dest(paths.scripts.dest.public));
    return merge(adminScripts, publicScripts);
});

gulp.task('styles', function() {
    var adminStyles = gulp.src(paths.styles.src.admin)
        .pipe(plumber())
        .pipe(sass().on('error', sass.logError))
        .pipe(rename({
            basename: 'gforms-bootstrapper-admin'
        }))
        .pipe(gulp.dest(paths.styles.dest.admin))
        .pipe(cleanCSS({
            compatibility: 'ie8'
        }))
        .pipe(rename({
            basename: 'gforms-bootstrapper-admin',
            suffix: '.min'
        }))
        .pipe(gulp.dest(paths.styles.dest.admin));
    var publicStyles = gulp.src(paths.styles.src.public)
        .pipe(plumber())
        .pipe(sass().on('error', sass.logError))
        .pipe(rename({
            basename: 'gforms-bootstrapper-public'
        }))
        .pipe(gulp.dest(paths.styles.dest.public))
        .pipe(cleanCSS({
            compatibility: 'ie8'
        }))
        .pipe(rename({
            basename: 'gforms-bootstrapper-public',
            suffix: '.min'
        }))
        .pipe(gulp.dest(paths.styles.dest.public));
    return merge(adminStyles, publicStyles);
});

gulp.task('watch', function() {
    gulp.watch([paths.styles.src.all, paths.scripts.src.all], ['styles', 'scripts']);
});
