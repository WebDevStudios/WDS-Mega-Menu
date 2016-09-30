// Require our dependencies
const plumber = require( 'gulp-plumber' );
const cheerio = require( 'gulp-cheerio' );
var autoprefixer = require('autoprefixer');
var cssnano = require('gulp-cssnano');
var del = require('del');
var gulp = require('gulp');
var gutil = require('gulp-util');
var notify = require('gulp-notify');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var sassLint = require('gulp-sass-lint');
var sort = require('gulp-sort');
var sourcemaps = require('gulp-sourcemaps');
var svgmin = require('gulp-svgmin');
var svgstore = require('gulp-svgstore');
var uglify = require('gulp-uglify');
var wpPot = require('gulp-wp-pot');

// Set assets paths.
var paths = {
	css: ['assets/css/*.css', '!*.min.css'],
	icons: 'assets/svg/*.svg',
	php: ['./*.php', './**/*.php'],
	sass: 'assets/scss/*.scss',
	scripts: ['assets/js/*.js', '!assets/js/*.min.js'],
};

/**
 * Handle errors and alert the user.
 */
function handleErrors () {
	var args = Array.prototype.slice.call(arguments);

	notify.onError({
		title: 'Task Failed [<%= error.message %>',
		message: 'See console.',
		sound: 'Sosumi' // See: https://github.com/mikaelbr/node-notifier#all-notification-options-with-their-defaults
	}).apply(this, args);

	gutil.beep(); // Beep 'sosumi' again

	// Prevent the 'watch' task from stopping
	this.emit('end');
}

/**
 * Delete style.css and style.min.css before we minify and optimize
 */
gulp.task('clean:styles', function() {
	return del(['admin.css', 'admin.min.css'])
});

/**
 * Compile Sass and run stylesheet through PostCSS.
 *
 * https://www.npmjs.com/package/gulp-sass
 * https://www.npmjs.com/package/gulp-postcss
 * https://www.npmjs.com/package/gulp-autoprefixer
 */
gulp.task('postcss', ['clean:styles'], function() {
	return gulp.src('assets/scss/*.scss', paths.css)

	// Deal with errors.
	.pipe(plumber({ errorHandler: handleErrors }))

	// Wrap tasks in a sourcemap.
	.pipe(sourcemaps.init())

		// Compile Sass using LibSass.
		.pipe(sass({
			errLogToConsole: true,
			outputStyle: 'expanded' // Options: nested, expanded, compact, compressed
		}))

		// Parse with PostCSS plugins.
		.pipe(postcss([
			autoprefixer({
				browsers: ['last 2 version']
			}),
		]))

	// Create sourcemap.
	.pipe(sourcemaps.write())

	// Create style.css.
	.pipe(gulp.dest('assets/css'))
});

/**
 * Minify and optimize style.css.
 *
 * https://www.npmjs.com/package/gulp-cssnano
 */
gulp.task('cssnano', ['postcss'], function() {
	return gulp.src('assets/css/admin.css')
	.pipe(plumber({ errorHandler: handleErrors }))
	.pipe(cssnano({
		safe: true // Use safe optimizations
	}))
	.pipe(rename('admin.min.css'))
	.pipe(gulp.dest('assets/css'))
});

/**
 * Sass linting.
 *
 * https://www.npmjs.com/package/sass-lint
 */
gulp.task('sass:lint', ['cssnano'], function() {
	gulp.src([
		'assets/scss/*.scss',
	])
	.pipe(sassLint())
	.pipe(sassLint.format())
	.pipe(sassLint.failOnError());
});

/**
 * Delete the svg-defs.svg before we minify, concat.
 */
gulp.task('clean:icons', function() {
	return del(['assets/svg-defs.svg']);
});

/**
 * Minify, concatenate, and clean SVG icons.
 *
 * https://www.npmjs.com/package/gulp-svgmin
 * https://www.npmjs.com/package/gulp-svgstore
 * https://www.npmjs.com/package/gulp-cheerio
 */
gulp.task('svg', ['clean:icons'], function() {
	return gulp.src(paths.icons)
	.pipe(plumber({ errorHandler: handleErrors }))
	.pipe(svgmin())
	.pipe(rename({ prefix: 'icon-' }))
	.pipe(svgstore({ inlineSvg: true }))
	.pipe(cheerio({
		run: function($, file) {
			$('svg').attr('style', 'display:none');
			$('[fill]').removeAttr('fill');
			$('path').removeAttr('class');
		},
		parserOptions: { xmlMode: true }
	}))
	.pipe(rename( 'svg-defs.svg' ))
	.pipe(gulp.dest('assets/'))
});

 /**
  * Minify compiled javascript after concatenated.
  * https://www.npmjs.com/package/gulp-uglify
  */
gulp.task('uglify', function() {
	return gulp.src(paths.scripts)
	.pipe(rename({suffix: '.min'}))
	.pipe(uglify({
		mangle: false
	}))
	.pipe(gulp.dest('assets/js'));
});

/**
 * Delete the theme's .pot before we create a new one.
 */
gulp.task('clean:pot', function() {
	return del(['languages/precept.pot']);
});

/**
 * Scan the theme and create a POT file.
 *
 * https://www.npmjs.com/package/gulp-wp-pot
 */
gulp.task('wp-pot', ['clean:pot'], function() {
	return gulp.src(paths.php)
	.pipe(plumber({ errorHandler: handleErrors }))
	.pipe(sort())
	.pipe(wpPot({
		destFile:'wds-mega-menus.pot',
		package: 'wds-mega-menus'
	}))
	.pipe(gulp.dest('languages/'));
});

/**
 * Process tasks and reload browsers on file changes.
 *
 * https://www.npmjs.com/package/browser-sync
 */
gulp.task('watch', function() {

	// Run tasks when files change.
	gulp.watch(paths.icons, ['icons']);
	gulp.watch(paths.sass, ['styles']);
	gulp.watch(paths.scripts, ['scripts']);
});

/**
 * Create individual tasks.
 */
gulp.task('i18n', ['wp-pot']);
gulp.task('icons', ['svg']);
gulp.task('scripts', ['uglify']);
gulp.task('styles', ['cssnano']);
gulp.task('default', [ 'i18n', 'icons', 'styles', 'scripts']);