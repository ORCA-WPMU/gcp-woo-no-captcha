var gulp = require('gulp');
var wpPot = require('gulp-wp-pot');
var potomo = require('gulp-potomo');

gulp.task('default', ['gen-pos','gen-mos']);
 
gulp.task('gen-pos', function () {
    return gulp.src('./**/*.php')
        .pipe(wpPot( {
            domain: 'gcp-woo-no-captcha',
            package: 'WooCoomerce NoCaptura for WPMU'
        } ))
        .pipe(gulp.dest('lang/gcp-woo-no-captcha-en_GB.pot'))
        .pipe(gulp.dest('lang/gcp-woo-no-captcha-en_GB.po'));
});

gulp.task('gen-mos', function () {
	var options = {                       
		poDel: true
	};

	return gulp.src('/lang/*.po')
		.pipe(potomo(options))
  		.pipe(gulp.dest('lang/'));
});