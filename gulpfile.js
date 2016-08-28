var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var jshint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

// Paths
var scss = 'assets/scss/**/*';
var css = 'assets/css/';

var js_source = [
	'assets/js/lib/nestedpages-responsive.js',
	'assets/js/lib/nestedpages.formatter.js',
	'assets/js/lib/nestedpages.menu-toggle.js',
	'assets/js/lib/nestedpages.page-toggle.js',
	'assets/js/lib/nestedpages.nesting.js',
	'assets/js/lib/nestedpages.sync-menu-setting.js',
	'assets/js/lib/nestedpages.new-page.js',
	'assets/js/lib/nestedpages.quickedit-post.js',
	'assets/js/lib/nestedpages.quickedit-link.js',
	'assets/js/lib/nestedpages.clone.js',
	'assets/js/lib/nestedpages.tabs.js',
	'assets/js/lib/nestedpages-factory.js',
	'assets/js/lib/nestedpages.menu-links.js',
	'assets/js/lib/nestedpages.menu-search.js',
	'assets/js/lib/nestedpages.trash.js',
	'assets/js/lib/nestedpages.confirm-delete.js'
];
var js_source_settings = [
	'assets/js/lib/nestedpages.settings.js'
];
var js_compiled = 'assets/js/';

/**
* Smush the admin Styles and output
*/
gulp.task('scss', function(){
	return gulp.src(scss)
		.pipe(sass({ outputStyle: 'compressed' }))
		.pipe(autoprefix('last 15 version'))
		.pipe(gulp.dest(css))
		.pipe(livereload())
		.pipe(notify('Nested Pages styles compiled & compressed.'));
});

/**
* Smush the JS and output
*/
gulp.task('scripts', function(){
	return gulp.src(js_source)
		.pipe(concat('nestedpages.min.js'))
		.pipe(gulp.dest(js_compiled))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled))
		.pipe(notify('Nested Pages scripts compiles & compressed.'));
});

/**
* Smush the Settings JS and output
*/
gulp.task('settings-scripts', function(){
	return gulp.src(js_source_settings)
		.pipe(concat('nestedpages.settings.min.js'))
		.pipe(gulp.dest(js_compiled))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled))
		.pipe(notify('Nested Pages settings scripts compiles & compressed.'));
});


/**
* Watch Task
*/
gulp.task('watch', function(){
	livereload.listen();
	gulp.watch(scss, ['scss']);
	gulp.watch(js_source, ['scripts']);
	gulp.watch(js_source_settings, ['settings-scripts']);
});

/**
* Default
*/
gulp.task('default', ['scss', 'scripts', 'settings-scripts', 'watch']);