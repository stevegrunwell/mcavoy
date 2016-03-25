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
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('i18n', ['makepot']);
	grunt.registerTask('default', ['jshint', 'uglify']);
};
