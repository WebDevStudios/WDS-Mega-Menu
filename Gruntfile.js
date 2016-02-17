module.exports = function( grunt ) {
	require('load-grunt-tasks')(grunt);
	var pkg = grunt.file.readJSON( 'package.json' );
	grunt.initConfig( {
		pkg: pkg,
		watch: {
			php: {
				files: ['**/*.php', '!vendor/**.*.php'],
				tasks: ['php'],
				options: {
					spawn: false,
					debounceDelay: 500
				}
			}
		},
		makepot: {
			dist: {
				options: {
					domainPath: '/languages/',
					potFilename: pkg.name + '.pot',
					type: 'wp-plugin'
				}
			}
		},
		addtextdomain: {
			dist: {
				options: {
					textdomain: pkg.name
				},
				target: {
					files: {
						src: ['**/*.php']
					}
				}
			}
		}
	} );

	// Default task.
	grunt.registerTask( 'php', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask( 'default', ['php'] );
	grunt.util.linefeed = '\n';
};
