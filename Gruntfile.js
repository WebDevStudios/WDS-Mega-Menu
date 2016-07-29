module.exports = function( grunt ) {
	require('load-grunt-tasks')(grunt);
	var pkg = grunt.file.readJSON( 'package.json' );
	grunt.initConfig( {
		pkg: pkg,
		svgmin: {
			options: {
				plugins: [ // https://github.com/svg/svgo/tree/master/plugins
					{ removeComments: true },
					{ removeTitle: true },
					{ removeEmptyAttrs: true }
				]
			},
			dist: {
				files: [{
					expand: true,
					cwd: '',
					src: ['assets/svg/*.svg'],
					dest: ''
				}]
			}
		},
		svgstore: {
			options: {
				prefix: 'icon-',
				cleanup: ['fill', 'style'],
				svg: {
					style: 'display: none;'
				}
			},
			default: {
				files: {
					'assets/svg-defs.svg': 'assets/svg/*.svg',
				}
			}
		},
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
		},
		svg: {
			files: ['assets/svg/*.svg'],
			tasks: ['svgstore'],
			options: {
				spawn: false,
				livereload: true,
			},
		},
	} );

	// Default task.
	grunt.registerTask( 'php', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask('icons', ['svgmin', 'svgstore']);
	grunt.registerTask( 'default', ['php'] );
	grunt.util.linefeed = '\n';
};
