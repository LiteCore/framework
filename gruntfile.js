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
					'public_html/backend/template/js/app.js': ['public_html/backend/template/js/components/*.js'],
					'public_html/frontend/template/js/app.js': ['public_html/frontend/template/js/components/*.js']
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
					'public_html/assets/trumbowyg/ui/trumbowyg.min.css': 'public_html/assets/trumbowyg/ui/trumbowyg.scss'
				}
			},
			
			framework: {
				options: {
					sourceMap: false,
					compass: false
				},
				files: {
					'public_html/assets/litecore/css/framework.css': 'public_html/assets/litecore/scss/framework/main.scss',
					'public_html/assets/litecore/css/email.css': 'public_html/assets/litecore/scss/email.scss',
					'public_html/assets/litecore/css/printable.css': 'public_html/assets/litecore/scss/printable.scss'
				}
			},
			
			framework_minified: {
				options: {
					sourceMap: false,
					outputStyle: 'compressed',
					compass: false
				},
				files: {
					'public_html/assets/litecore/css/framework.min.css': 'public_html/assets/litecore/scss/framework/main.scss',
					'public_html/assets/litecore/css/email.min.css': 'public_html/assets/litecore/scss/email.scss',
					'public_html/assets/litecore/css/printable.min.css': 'public_html/assets/litecore/scss/printable.scss'
				}
			},

			variables: {
				options: {
					compress: false,
					sourceMap: false,
					relativeUrls: true
				},
				files: {
					'public_html/backend/template/css/variables.css': 'public_html/backend/template/scss/variables.scss',
					'public_html/frontend/template/css/variables.css': 'public_html/frontend/template/scss/variables.scss',
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
					'public_html/backend/template/css/app.min.css': 'public_html/backend/template/scss/app.scss',
					'public_html/backend/template/css/framework.min.css': 'public_html/backend/template/scss/framework.scss',
					'public_html/backend/template/css/printable.min.css': 'public_html/backend/template/scss/printable.scss',

					'public_html/frontend/template/css/app.min.css': 'public_html/frontend/template/scss/app.scss',
					'public_html/frontend/template/css/email.min.css': 'public_html/frontend/template/scss/email.scss',
					'public_html/frontend/template/css/framework.min.css': 'public_html/frontend/template/scss/framework.scss',
					'public_html/frontend/template/css/printable.min.css': 'public_html/frontend/template/scss/printable.scss'
				}
			},
		},

		phplint: {
			options: {
				//phpCmd: 'C:/Program Files/PHP/php.exe', // Defaults to php
				limit: 10,
				stdout: false
			},
			files: 'public_html/**/*.php'
		},

		replace: {
			app_header: {
				src: ['public_html/includes/app_header.inc.php'],
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
					'public_html/index.php',
					'public_html/install/install.php',
					'public_html/install/upgrade.php'
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
					'public_html/backend/template/js/app.min.js': ['public_html/backend/template/js/app.js'],
					'public_html/frontend/template/js/app.min.js': ['public_html/frontend/template/js/app.js'],
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
					'public_html/assets/trumbowyg/ui/trumbowyg.scss',
					'public_html/backend/template/**/scss/*.scss',
					'public_html/frontend/template/**/scss/*.scss',
				],
				tasks: ['scss']
			},

			javascripts: {
				files: [
					'public_html/backend/template/**/js/components/*.js',
					'public_html/frontend/template/**/js/components/*.js',
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
