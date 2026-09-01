import { defineConfig } from 'oxlint';

export default defineConfig({
	categories: {
		correctness: 'error',
		suspicious: 'error',
		perf: 'warn',
		style: 'warn',
		restriction: 'warn',
		pedantic: 'off',
		nursery: 'warn',
	},
	rules: {
		'no-unused-vars': 'off',
		'no-explicit-any': 'off',
		'no-empty-pattern': 'off',
	},
	env: {
		browser: true,
		jquery: true,
	},
	globals: {
		waitFor: 'readonly',
		values: 'readonly',
		_env: 'readonly',
	},
	ignorePatterns: [
		'src/assets/jquery/**',
		'src/assets/trumbowyg/**',
		'src/assets/litecore/js/**/*.min.js',
		'src/assets/litecore_litecore/**',
		'src/storage/**',
		'**/node_modules/**',
	],
});