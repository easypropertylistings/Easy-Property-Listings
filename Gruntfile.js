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

	});

	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	// Register tasks
	grunt.registerTask( 'default', [
		'uglify',
		'css'
	]);

	grunt.registerTask( 'js', [
		'uglify:frontend'
	]);

	grunt.registerTask( 'css', [
		'cssmin'
	]);

};