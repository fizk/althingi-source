'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var vulcanize = require('gulp-vulcanize');

gulp.task('sass', function () {
    gulp.src('./sass/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(autoprefixer())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./stylesheets'));
});

gulp.task('vulcanize', function () {
    return gulp.src('components/app.html')
        .pipe(vulcanize({
            abspath: 'resources',
            excludes: [],
            inlineScripts: true,
            inlineCss: true,
            stripExcludes: true
        }))
        .pipe(gulp.dest('.'));
});

gulp.task('watch', function () {
    gulp.watch('./sass/**/*scss', ['sass']);
    gulp.watch('./components/**/*html', ['vulcanize']);
});
