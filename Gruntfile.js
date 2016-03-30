module.exports = function(grunt) {

	grunt.initConfig({

		jshint: {
			all: ['assets/js/src/*.js']
		},

		uglify: {
			options: {
        sourceMap: true
      },
			admin: {
				files: {
					'assets/js/admin.min.js': ['assets/js/src/admin.js']
				}
			}
		},

		sass: {
			options: {
				sourceMap: true
			},
			dist: {
				files: {
					'assets/css/admin.min.css': 'assets/css/src/admin.scss'
				}
			}
		},

		cssmin: {
			options: {
				sourceMap: true
			},
			target: {
				files: [{
					expand: true,
					cwd: 'assets/css',
					src: ['*.css'],
					dest: 'assets/css',
				}]
			}
		},

		copy: {
			main: {
				src: [
					'assets/**',
					'!assets/*/src/**',
					'!assets/*/src',
					'inc/**',
					'languages/*',
					'CHANGELOG.md',
					'mcavoy.php',
					'LICENSE.md',
					'readme.txt',
					'uninstall.php'
				],
				dest: 'dist/'
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: 'languages/',
					mainFile: 'mcavoy.php',
					type: 'wp-plugin',
					updateTimestamp: false,
					updatePoFiles: true
				}
			}
	}
	});

	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('i18n', ['makepot']);
	grunt.registerTask('scripts', ['jshint', 'uglify']);
	grunt.registerTask('styles', ['sass', 'cssmin']);
	grunt.registerTask('build', ['scripts', 'styles', 'i18n', 'copy']);
	grunt.registerTask('default', ['scripts', 'styles']);
};
