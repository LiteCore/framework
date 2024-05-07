module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

    concat: {
      backend: {
        //options: {
        //  stripBanners: true,
        //  banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
        //    '<%= grunt.template.today("yyyy-mm-dd") %> */',
        //},
        files: {
          'public_html/backend/template/js/app.js': ['public_html/backend/template/js/components/*.js']
        },
      },
      frontend: {
        files: {
          //'public_html/frontend/templates/default/js/app.js': ['public_html/frontend/templates/default/js/components/*.js']
        },
      }
    },

		less: {
			backend_variables: {
				options: {
					compress: false,
					sourceMap: false,
					relativeUrls: true
				},
				files: {
					'public_html/backend/template/css/variables.css' : 'public_html/backend/template/less/variables.less',
				}
			},

			backend_minified: {
				options: {
					compress: true,
					sourceMap: true,
					sourceMapBasepath: 'public_html/backend/template/less/',
					sourceMapRootpath: '../less/',
					sourceMapURL: function(path) { return path.replace(/.*\//, '') + '.map'; },
					relativeUrls: true
				},
				files: {
					'public_html/backend/template/css/app.min.css'			 : 'public_html/backend/template/less/app.less',
					'public_html/backend/template/css/framework.min.css' : 'public_html/backend/template/less/framework.less',
					'public_html/backend/template/css/printable.min.css' : 'public_html/backend/template/less/printable.less'
				}
			},

			frontend: {
				options: {
					compress: false,
					sourceMap: false,
					relativeUrls: true
				},
				files: {
					'public_html/frontend/template/css/app.css'       : 'public_html/frontend/template/less/app.less',
					'public_html/frontend/template/css/email.css'     : 'public_html/frontend/template/less/email.less',
					'public_html/frontend/template/css/framework.css' : 'public_html/frontend/template/less/framework.less',
					'public_html/frontend/template/css/printable.css' : 'public_html/frontend/template/less/printable.less',
					'public_html/frontend/template/css/variables.css' : 'public_html/frontend/template/less/variables.less'
				}
			},

			frontend_minified: {
				options: {
					compress: true,
					sourceMap: true,
					relativeUrls: true
				},
				files: {
					'public_html/frontend/template/css/app.min.css'       : 'public_html/frontend/template/less/app.less',
					'public_html/frontend/template/css/email.min.css'     : 'public_html/frontend/template/less/email.less',
					'public_html/frontend/template/css/framework.min.css' : 'public_html/frontend/template/less/framework.less',
					'public_html/frontend/template/css/printable.min.css' : 'public_html/frontend/template/less/printable.less'
				}
			}
		},

		'dart-sass': {
			trumbowyg_minified: {
				options: {
					sourceMap: false,
					outputStyle: 'compressed',
					compass: false
				},
				files: {
					'public_html/assets/trumbowyg/ui/trumbowyg.min.css': 'public_html/assets/trumbowyg/ui/trumbowyg.scss'
				}
			}
		},

		phplint: {
			options: {
				//phpCmd: 'C:/xampp/php82/php.exe', // Defaults to php
				limit: 10,
				stdout: false
			},
			files: 'public_html/**/*.php'
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

		watch: {
			replace: {
				files: [
					'package.json',
				],
				tasks: ['replace']
			},

			less: {
				files: [
					'public_html/assets/featherlight/featherlight.less',
					'public_html/backend/template/**/*.less',
					'public_html/frontend/template/**/*.less',
				],
				tasks: ['less']
			},

			javascripts: {
				files: [
					'public_html/assets/featherlight/featherlight.js',
					'public_html/backend/template/**/js/*.js',
					'public_html/frontend/template/**/js/*.js',
					'!public_html/frontend/templat/**/js/*.min.js',
				],
				tasks: ['uglify']
			},

			sass: {
				files: [
					'public_html/assets/trumbowyg/ui/trumbowyg.scss',
				],
				tasks: ['dart-sass']
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
	grunt.registerTask('build', ['replace', 'less', 'dart-sass', 'concat', 'uglify']);

	require('phplint').gruntPlugin(grunt);
	grunt.registerTask('test', ['phplint']);
};
