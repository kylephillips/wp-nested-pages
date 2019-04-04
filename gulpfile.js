var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var pump = require('pump');

// Paths
var scss = 'assets/scss/**/*';
var css = 'assets/css/';

var js_source = [
	'assets/js/lib/nestedpages.formatter.js',
	'assets/js/lib/nestedpages.dropdowns.js',
	'assets/js/lib/nestedpages.modals.js',
	'assets/js/lib/nestedpages.check-all.js',
	'assets/js/lib/nestedpages.bulk-actions.js',
	'assets/js/lib/nestedpages.hidden-item-count.js',
	'assets/js/lib/nestedpages.menu-toggle.js',
	'assets/js/lib/nestedpages.page-toggle.js',
	'assets/js/lib/nestedpages.nesting.js',
	'assets/js/lib/nestedpages.sync-menu-setting.js',
	'assets/js/lib/nestedpages.new-post.js',
	'assets/js/lib/nestedpages.quickedit-post.js',
	'assets/js/lib/nestedpages.quickedit-link.js',
	'assets/js/lib/nestedpages.clone.js',
	'assets/js/lib/nestedpages.tabs.js',
	'assets/js/lib/nestedpages-factory.js',
	'assets/js/lib/nestedpages.menu-links.js',
	'assets/js/lib/nestedpages.menu-search.js',
	'assets/js/lib/nestedpages.trash.js',
	'assets/js/lib/nestedpages.confirm-delete.js',
	'assets/js/lib/nestedpages.manual-sync.js',
	'assets/js/lib/nestedpages.post-search.js',
	'assets/js/lib/nestedpages.move-post.js',
	'assets/js/lib/nestedpages.wpml.js'
];
var js_source_settings = [
	'assets/js/lib/nestedpages.settings-reset.js',
	'assets/js/lib/nestedpages.userprefs-reset.js',
	'assets/js/lib/nestedpages.settings.js'
];
var js_compiled = 'assets/js/';

/**
* Smush the admin Styles and output
*/
gulp.task('scss', function(callback){
	pump([
		gulp.src(scss),
		sass({ outputStyle: 'compressed' }),
		autoprefix('last 15 version'),
		gulp.dest(css),
		livereload(),
		notify('Nested Pages styles compiled & compressed.')
	], callback);
});

/**
* Smush the JS and output
*/
gulp.task('scripts', function(callback){
	pump([
		gulp.src(js_source),
		concat('nestedpages.min.js'),
		gulp.dest(js_compiled),
		uglify(),
		gulp.dest(js_compiled),
		notify('Nested Pages scripts compiled & compressed.')
	], callback);
});

/**
* Output unminified JS for dev environment
*/
gulp.task('scripts-dev', function(callback){
	pump([
		gulp.src(js_source),
		concat('nestedpages.js'),
		gulp.dest(js_compiled)
	], callback);
});

/**
* Smush the Settings JS and output
*/
gulp.task('settings-scripts', function(callback){
	pump([
		gulp.src(js_source_settings),
		concat('nestedpages.settings.min.js'),
		gulp.dest(js_compiled),
		uglify(),
		gulp.dest(js_compiled),
		notify('Nested Pages settings scripts compiles & compressed.')
	], callback);
});


/**
* Watch Task
*/
gulp.task('watch', function(){
	livereload.listen();
	gulp.watch(scss, ['scss']);
	gulp.watch(js_source, ['scripts']);
	gulp.watch(js_source_settings, ['settings-scripts']);
	gulp.watch(js_source, ['scripts-dev']);
});

/**
* Default
*/
gulp.task('default', ['scss', 'scripts', 'settings-scripts', 'scripts-dev', 'watch']);