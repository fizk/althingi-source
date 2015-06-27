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
            abspath: '',
            excludes: [],
            stripExcludes: false
        }))
        .pipe(gulp.dest('components/index.html'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./sass/**/*scss', ['sass']);
});
