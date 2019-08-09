/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// Setting folder templates.
		dirs: {
			css: 'lib/assets/css',
			images: 'lib/assets/images',
			js: 'lib/assets/js'
		},


		// Minify .js files.
		uglify: {
			options: {
				// Preserve comments that start with a bang.
				preserveComments: /^!/
			},

			frontend: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js',
						'!*.float.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			},
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.css %>/',
				src: ['*.css'],
				dest: '<%= dirs.css %>/',
				ext: '.min.css'
			}
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: ['<%= dirs.css %>/*.css'],
				tasks: ['cssmin']
			},
			js: {
				files: [
					'<%= dirs.js %>/*js',
				],
				tasks: ['uglify']
			}
		},

		checktextdomain: {
			options:{
				text_domain: 'easy-property-listings',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,3,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					' __ngettext:1,2,3d',
					'__ngettext_noop:1,2,3d',
					'_c:1,2d',
					'_nc:1,2,4c,5d'
				]
			},
			files: {
				src: [
					'**/*.php', // Include all files
					'!node_modules/**', // Exclude node_modules/
					'!apigen/**'// Exclude apigen/
				],
				expand: true
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'languages',
				potHeaders: {
					'report-msgid-bugs-to' : 'http://wordpress.org/support/plugin/easy-property-listings',
					'last-translator' : 'Merv Barrett <support@easypropertylistings.com.au>',
					'language-team' : 'Real Estate Connected <support@realestateconnected.com.au>',
					'Plural-Forms': 'nplurals=2; plural=(n > 1);',
					'X-Poedit-SourceCharset' : 'UTF-8',
					'X-Poedit-KeywordsList' : '__;_e;_x;_ex;_n',
					'X-Poedit-Basepath' : '..',
					'X-Poedit-SearchPath-0' : '.',
					'X-Poedit-SearchPathExcluded-0' : 'node_modules',
					'X-Poedit-SearchPathExcluded-1' : 'epl-apidocs',
					'X-Poedit-SearchPathExcluded-2' : 'apigen',
					'X-Poedit-SearchPathExcluded-3' : 'Gruntfile.js',
					'X-Poedit-SearchPathExcluded-4' : 'apigen.neon',
					'X-Poedit-SearchPathExcluded-5' : 'package.json',
					'X-Poedit-SearchPathExcluded-6' : 'README.md'
				}
			},
			dist: {
				options: {
					potFilename: 'easy-property-listings.pot',
					exclude: [
						'apigen/.*',
						'tests/.*',
						'tmp/.*'
					]
				}
			}
		},

	});

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	// Register tasks
	grunt.registerTask( 'default', [
		'uglify',
		'css',
		'checktextdomain',
		'makepot'
	]);

	grunt.registerTask( 'js', [
		'uglify:frontend'
	]);

	grunt.registerTask( 'css', [
		'cssmin'
	]);

	grunt.registerTask( 'dev', [
		'default',
		'makepot'
	]);

};