'use strict';

const chokidar = require('chokidar');
const { execFileSync } = require('child_process');
const path = require('path');

const ROOT = path.join(__dirname, '..');
const SCRIPTS = __dirname;

function run(script) {
	try {
		execFileSync(process.execPath, [path.join(SCRIPTS, script)], {
			cwd: ROOT,
			stdio: 'inherit',
		});
	} catch {
		// error output already printed to stderr; keep watching
	}
}

function ts() {
	return new Date().toLocaleTimeString('en-US', { hour12: false });
}

// Initial build
console.log(`[${ts()}] Building...`);
run('generate.js');
run('build-css.js');
run('build-js.js');
console.log(`[${ts()}] Ready. Watching for changes.`);

// Watch SCSS source files (excluding generated files)
chokidar.watch(path.join(ROOT, 'assets/scss/**/*.scss'), {
	ignored: [
		path.join(ROOT, 'assets/scss/_colors.scss'),
		path.join(ROOT, 'assets/scss/_editor-formats-reference.scss'),
	],
	ignoreInitial: true,
}).on('all', (event, filePath) => {
	console.log(`[${ts()}] SCSS changed: ${path.relative(ROOT, filePath)}`);
	run('build-css.js');
});

// Watch theme.json and config.json — regenerate then rebuild CSS
chokidar.watch([
	path.join(ROOT, 'theme.json'),
	path.join(ROOT, 'config.json'),
], { ignoreInitial: true }).on('all', (event, filePath) => {
	console.log(`[${ts()}] Config changed: ${path.relative(ROOT, filePath)}`);
	run('generate.js');
	run('build-css.js');
});

// Watch JS source files
chokidar.watch(path.join(ROOT, 'assets/js/src/**/*.js'), {
	ignoreInitial: true,
}).on('all', (event, filePath) => {
	console.log(`[${ts()}] JS changed: ${path.relative(ROOT, filePath)}`);
	run('build-js.js');
});
