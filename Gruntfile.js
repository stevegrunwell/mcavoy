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
					exclude: [
						'bin/*',
						'dist/*',
						'features/*',
						'node_modules/*',
						'plugin-repo-assets/*',
						'tests/*',
						'vendor/*'
					],
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
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('i18n', ['makepot']);
	grunt.registerTask('scripts', ['jshint', 'uglify']);
	grunt.registerTask('build', ['scripts', 'i18n', 'copy']);
	grunt.registerTask('default', ['scripts']);
};
