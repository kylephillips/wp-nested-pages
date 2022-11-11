var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var minifycss = require('gulp-minify-css');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

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
	'assets/js/lib/nestedpages.trash-with-children.js',
	'assets/js/lib/nestedpages.wpml.js'
];
var js_source_settings = [
	'assets/js/lib/nestedpages.settings-reset.js',
	'assets/js/lib/nestedpages.userprefs-reset.js',
	'assets/js/lib/nestedpages.settings-admin-customization.js',
	'assets/js/lib/nestedpages.settings.js'
];
var js_compiled = 'assets/js/';

/**
* Minify the admin styles and output
*/
var styles = function(){
	return gulp.src(scss)
		.pipe(sass({sourceComments: 'map', sourceMap: 'sass', style: 'compact'}))
		.pipe(autoprefix('last 5 version'))
		.pipe(minifycss({keepBreaks: false}))
		.pipe(gulp.dest(css))
		.pipe(livereload())
		.pipe(notify('Nested Pages styles compiled & compressed.'));
}

/**
* Concatenate and minify scripts
*/
var scripts = function(){
	return gulp.src(js_source)
		.pipe(concat('nestedpages.min.js'))
		// .pipe(uglify())
		.pipe(gulp.dest(js_compiled));
};

/**
* Output unminified JS for dev environment
*/
var scripts_dev = function(){
	return gulp.src(js_source)
		.pipe(concat('nestedpages.js'))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled));
};

/**
* Output the settings JS and output
*/
var scripts_settings = function(){
	return gulp.src(js_source_settings)
		.pipe(concat('nestedpages.settings.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled));
};

/**
* Watch Task
*/
gulp.task('watch', function(){
	livereload.listen();
	gulp.watch(scss, gulp.series(styles));
	gulp.watch(js_source, gulp.series(scripts, scripts_dev));
	gulp.watch(js_source_settings, gulp.series(scripts_settings));
});

/**
* Default
*/
gulp.task('default', gulp.series(styles, scripts, scripts_dev, scripts_settings, 'watch'));