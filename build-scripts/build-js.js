'use strict';

const esbuild = require('esbuild');
const fs = require('fs');
const path = require('path');
const ROOT = path.join(__dirname, '..');

// Primary Plugin JS
const sources = [
	'assets/js/src/nestedpages.formatter.js',
	'assets/js/src/nestedpages.dropdowns.js',
	'assets/js/src/nestedpages.modals.js',
	'assets/js/src/nestedpages.check-all.js',
	'assets/js/src/nestedpages.bulk-actions.js',
	'assets/js/src/nestedpages.hidden-item-count.js',
	'assets/js/src/nestedpages.menu-toggle.js',
	'assets/js/src/nestedpages.page-toggle.js',
	'assets/js/src/nestedpages.nesting.js',
	'assets/js/src/nestedpages.sync-menu-setting.js',
	'assets/js/src/nestedpages.new-post.js',
	'assets/js/src/nestedpages.quickedit-post.js',
	'assets/js/src/nestedpages.quickedit-link.js',
	'assets/js/src/nestedpages.clone.js',
	'assets/js/src/nestedpages.tabs.js',
	'assets/js/src/nestedpages-factory.js',
	'assets/js/src/nestedpages.menu-links.js',
	'assets/js/src/nestedpages.menu-search.js',
	'assets/js/src/nestedpages.trash.js',
	'assets/js/src/nestedpages.confirm-delete.js',
	'assets/js/src/nestedpages.manual-sync.js',
	'assets/js/src/nestedpages.post-search.js',
	'assets/js/src/nestedpages.move-post.js',
	'assets/js/src/nestedpages.trash-with-children.js',
	'assets/js/src/nestedpages.wpml.js'
];

async function buildJS() {
	const combined = sources
		.map(f => fs.readFileSync(path.join(ROOT, f), 'utf8'))
		.join('\n');

	const result = await esbuild.transform(combined, {
		minify: true,
	});

	fs.writeFileSync(path.join(ROOT, 'assets/js/nestedpages.min.js'), result.code);
	console.log('Built nestedpages.min.js');
}

buildJS().catch(err => {
	console.error(err.message || err);
	process.exit(1);
});

// Unminified for dev
async function buildJSDev() {
	const combined = sources
		.map(f => fs.readFileSync(path.join(ROOT, f), 'utf8'))
		.join('\n');

	const result = await esbuild.transform(combined, {
		minify: false,
	});

	fs.writeFileSync(path.join(ROOT, 'assets/js/nestedpages.js'), result.code);
	console.log('Built nestedpages.js');
}

buildJSDev().catch(err => {
	console.error(err.message || err);
	process.exit(1);
});

// Plugin Settings
const sources_settings = [
	'assets/js/src/nestedpages.settings-reset.js',
	'assets/js/src/nestedpages.userprefs-reset.js',
	'assets/js/src/nestedpages.settings-admin-customization.js',
	'assets/js/src/nestedpages.settings.js'
];

async function buildJSSettings() {
	const combined = sources_settings
		.map(f => fs.readFileSync(path.join(ROOT, f), 'utf8'))
		.join('\n');

	const result = await esbuild.transform(combined, {
		minify: true,
	});

	fs.writeFileSync(path.join(ROOT, 'assets/js/nestedpages.settings.js'), result.code);
	console.log('Built nestedpages.settings.js');
}

buildJSSettings().catch(err => {
	console.error(err.message || err);
	process.exit(1);
});