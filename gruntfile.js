module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		concat: {
			application: {
				//options: {
				//	stripBanners: true,
				//	banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
				//		'<%= grunt.template.today("yyyy-mm-dd") %> */',
				//},
				files: {
					'src/backend/template/js/app.js': ['src/backend/template/js/components/*.js'],
					'src/frontend/template/js/app.js': ['src/frontend/template/js/components/*.js']
				},
			},
		},

		'scss': {
			trumbowyg: {
				options: {
					sourceMap: false,
					outputStyle: 'compressed',
					compass: false
				},
				files: {
					'src/assets/trumbowyg/ui/trumbowyg.min.css': 'src/assets/trumbowyg/ui/trumbowyg.scss'
				}
			},

			framework: {
				options: {
					sourceMap: false,
					compass: false
				},
				files: {
					'src/assets/litecore/css/framework.css': 'src/assets/litecore/scss/framework/main.scss',
					'src/assets/litecore/css/email.css': 'src/assets/litecore/scss/email.scss',
					'src/assets/litecore/css/printable.css': 'src/assets/litecore/scss/printable.scss'
				}
			},

			framework_minified: {
				options: {
					sourceMap: false,
					outputStyle: 'compressed',
					compass: false
				},
				files: {
					'src/assets/litecore/css/framework.min.css': 'src/assets/litecore/scss/framework/main.scss',
					'src/assets/litecore/css/email.min.css': 'src/assets/litecore/scss/email.scss',
					'src/assets/litecore/css/printable.min.css': 'src/assets/litecore/scss/printable.scss'
				}
			},

			variables: {
				options: {
					compress: false,
					sourceMap: false,
					relativeUrls: true
				},
				files: {
					'src/backend/template/css/variables.css': 'src/backend/template/scss/variables.scss',
					'src/frontend/template/css/variables.css': 'src/frontend/template/scss/variables.scss',
				}
			},

			application: {
				options: {
					compress: true,
					sourceMap: true,
					sourceMapRootpath: '../scss/',
					sourceMapURL: function(path) { return path.replace(/.*\//, '') + '.map'; },
					relativeUrls: true
				},
				files: {
					'src/backend/template/css/app.min.css': 'src/backend/template/scss/app.scss',
					'src/backend/template/css/framework.min.css': 'src/backend/template/scss/framework.scss',
					'src/backend/template/css/printable.min.css': 'src/backend/template/scss/printable.scss',

					'src/frontend/template/css/app.min.css': 'src/frontend/template/scss/app.scss',
					'src/frontend/template/css/email.min.css': 'src/frontend/template/scss/email.scss',
					'src/frontend/template/css/framework.min.css': 'src/frontend/template/scss/framework.scss',
					'src/frontend/template/css/printable.min.css': 'src/frontend/template/scss/printable.scss'
				}
			},
		},

		phplint: {
			options: {
				//phpCmd: 'C:/Program Files/PHP/php.exe', // Defaults to php
				limit: 10,
				stdout: false
			},
			files: 'src/**/*.php'
		},

		replace: {
			app_header: {
				src: ['src/shared/app_header.inc.php'],
				overwrite: true,
				replacements: [
					{
						from: /define\('PLATFORM_VERSION', '([0-9\.]+)'\);/,
						to: 'define(\'PLATFORM_VERSION\', \'<%= pkg.version %>\');'
					}
				]
			},

			app: {
				src: [
					'src/index.php',
					'src/install/install.php',
					'src/install/upgrade.php'
				],
				overwrite: true,
				replacements: [
					{
						from: /LiteCore™ ([0-9\.]+)/,
						to: 'LiteCore™ <%= pkg.version %>'
					}
				]
			},
		},

		uglify: {
			application: {
				options: {
					sourceMap: true,
				},
				files: {
					'src/backend/template/js/app.min.js': ['src/backend/template/js/app.js'],
					'src/frontend/template/js/app.min.js': ['src/frontend/template/js/app.js'],
				}
			},
		},

		watch: {
			replace: {
				files: [
					'package.json',
				],
				tasks: ['replace']
			},

			scss: {
				files: [
					'src/assets/trumbowyg/ui/trumbowyg.scss',
					'src/backend/template/**/scss/*.scss',
					'src/frontend/template/**/scss/*.scss',
				],
				tasks: ['scss']
			},

			javascripts: {
				files: [
					'src/backend/template/**/js/components/*.js',
					'src/frontend/template/**/js/components/*.js',
					'!**/*.min.js',
				],
				tasks: ['concat', 'uglify']
			},
		}

	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-dart-sass');
	grunt.loadNpmTasks('grunt-text-replace');

	grunt.registerTask('default', ['build']);
	grunt.registerTask('build', ['replace', 'scss', 'concat', 'uglify', 'watch']);

	require('phplint').gruntPlugin(grunt);
	grunt.registerTask('test', ['phplint']);
};
