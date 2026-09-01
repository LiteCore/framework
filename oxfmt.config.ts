import { defineConfig } from 'oxfmt';

export default defineConfig({
	useTabs: true,
	tabWidth: 2,
	lineWidth: 300,
	endOfLine: 'lf',
	semi: true,
	singleQuote: true,
	trailingCommas: 'all',
	arrowParens: 'always',
	bracketSpacing: true,
	overrides: [
		{
			files: ['**/*.ts', '**/*.mts', '**/*.cts'],
			options: {
				semi: false,
			},
		},
	],
	ignorePatterns: [
		'src/assets/jquery/**',
		'src/assets/trumbowyg/**',
		'src/assets/litecore/js/**/*.min.js',
		'src/assets/litecore_litecore/**',
		'src/storage/**',
		'**/node_modules/**',
	],
});
