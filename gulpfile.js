'use strict';
var gulp         = require('gulp'),
    concat       = require('gulp-concat'),
    uglify       = require('gulp-uglify'),
    less         = require('gulp-less'),
    cleanCss     = require('gulp-clean-css'),
    del          = require('del'),
    sourcemaps   = require('gulp-sourcemaps'),
    babel        = require('gulp-babel');

gulp.task('clean', function () {
    del(['less', 'js', 'images', 'fonts']);
});

gulp.task('less', function() {
    return gulp.src([
        'web-src/less/app.less'
    ])
        .pipe(sourcemaps.init(''))
        .pipe(less())
        .pipe(cleanCss())
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('web/css/'));
});

gulp.task('pages-js', function() {
    return gulp.src([
        'web-src/js/*.js',
        'web-src/js/**/*.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(babel({compact: true}))
        .pipe(uglify())
        .pipe(sourcemaps.write("./maps"))
        .pipe(gulp.dest('web/js'));
});


gulp.task('default', ['clean'], function () {
    var tasks = [
        'less',
    ];

    tasks.forEach(function (val) {
        gulp.start(val);
    });
});

gulp.task('watch', ['less', 'pages-js'], function () {
    gulp.watch('web-src/less/*.less', ['less']);
    gulp.watch('web-src/less/**/*.less', ['less']);
    gulp.watch('web-src/js/*.js', ['pages-js']);
    gulp.watch('web-src/js/**/*.js', ['pages-js']);
});