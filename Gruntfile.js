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

		// JavaScript linting with JSHint.
		/*jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js',
				'!<%= dirs.js %>/*.min.js',
			]
		},*/

		// Minify .js files.
		uglify: {
			options: {
				// Preserve comments that start with a bang.
				preserveComments: /^!/
			},
			admin: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			},
			vendor: {
				files: {
					'<%= dirs.js %>/epl.min.js': ['<%= dirs.js %>/epl.js'],
					'<%= dirs.js %>/jquery-admin-scripts.min.js': ['<%= dirs.js %>/jquery-admin-scripts.js'],
					'<%= dirs.js %>/jquery-datetime-picker.min.js': ['<%= dirs.js %>/jquery-datetime-picker.js'],
					'<%= dirs.js %>/jquery-front-scripts.min.js': ['<%= dirs.js %>/jquery-front-scripts.js'],
					'<%= dirs.js %>/jquery-validationEngine-en.min.js': ['<%= dirs.js %>/jquery-validationEngine-en.js'],
					'<%= dirs.js %>/jquery-validationEngine.min.js': ['<%= dirs.js %>/jquery-validationEngine.js'],
				}
			},
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.css %>/',
				src: ['*.css'],
				dest: '<%= dirs.css %>/',
				ext: '.css'
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
				//tasks: ['jshint', 'uglify']
				tasks: ['uglify']
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'lib/languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://github.com/woothemes/woocommerce/issues',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
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

		// Check textdomain errors.
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
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php', // Include all files
					'!apigen/**', // Exclude apigen/
					'!node_modules/**', // Exclude node_modules/
					'!tests/**', // Exclude tests/
					'!tmp/**' // Exclude tmp/
				],
				expand: true
			}
		},

		// Exec shell commands.
		shell: {
			options: {
				stdout: true,
				stderr: true
			},
			apigen: {
				command: [
					'apigen generate',
					'cd apigen',
					'php hook-docs.php'
				].join( '&&' )
			}
		},

		// Clean the directory.
		clean: {
			apigen: {
				src: [ 'epl-apidocs' ]
			}
		}
	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-shell' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	//grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	//grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );

	// Register tasks
	grunt.registerTask( 'default', [
		//'jshint',
		'uglify',
		'css'
	]);

	grunt.registerTask( 'js', [
		//'jshint',
		'uglify:admin',
		'uglify:frontend'
	]);

	grunt.registerTask( 'css', [
		//'sass',
		'cssmin'
	]);

	grunt.registerTask( 'docs', [
		'clean:apigen',
		'shell:apigen'
	]);

	grunt.registerTask( 'dev', [
		'default',
		'makepot'
	]);
};
