/**
 * Gulpfile
 * @author Alexandre Simonin
 * @created 24/01/2018
 */

'use strict';

var
  // For gulp utilisation
  gulp = require('gulp'),

  // Concat sources file and rename
  concat = require('gulp-concat'),
  imagemin = require('gulp-imagemin'),

  // Minify Js
  uglify = require('gulp-uglify'),

  // CSS plugins
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  autoprefixer = require('gulp-autoprefixer');

var DIR = {
    'src': './assets',
    'dest': './public/build'
};

/**
 * @task styles
 * Compile sass/scss to unique css file
 */
gulp.task('styles', function () {
    gulp.src(DIR.src + '/scss/**/*.+(scss|sass)')
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'expanded'
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 4 versions'],
            cascade: false
        }))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(DIR.dest + '/styles/'));
});

/**
 * @task scripts
 * Compile js scripts to unique js file
 */
gulp.task('scripts', function () {
    gulp.src([
        DIR.src + '/js/externals/**/*.js',
        DIR.src + '/js/**/*.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(concat('bundle.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(DIR.dest + '/scripts/'));
});

/**
 * @task fonts
 * Copy clean fonts to destination folder
 */
gulp.task('fonts', function () {
    gulp.src(DIR.src + '/fonts/**/*.+(eot|svg|ttf|woff)')
        .pipe(gulp.dest(DIR.dest + '/fonts/'));
});

/**
 * @task images
 * Copy images to destination folder
 */
gulp.task('images', function () {
    gulp.src(DIR.src + '/images/**/*.+(jpg|jpeg|png|svg|gif)')
        .pipe(imagemin())
        .pipe(gulp.dest(DIR.dest + '/images/'));
});

/**
 * @task watch
 * Compile/watch app OTF (dev)
 */
gulp.task('watch', function () {
    gulp.watch(DIR.src + '/scss/**/*.+(scss|sass)', ['styles']);
    gulp.watch(DIR.src + '/js/**/*.js', ['scripts']);
});

/**
 * @task dist-prod
 * Compile entire dist
 */
gulp.task('dist', ['styles', 'scripts', 'fonts', 'images'], function () {
    return true;
});

/**
 * @task dist-dev
 * Compile styles and scripts
 */
gulp.task('dist-dev', ['styles', 'scripts'], function () {
    return true;
});

/**
 * @task default
 * Compile/watch app OTF (dev)
 */
gulp.task('default', ['dist'], function () {
    return true;
});
