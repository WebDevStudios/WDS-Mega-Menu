module.exports = function(grunt) {

	// Load all grunt tasks in package.json matching the `grunt-*` pattern.
	require('load-grunt-tasks')(grunt);

	var pkg = grunt.file.readJSON( 'package.json' );

	grunt.initConfig({
		pkg: pkg,

		/**
		 * Minify SVGs using SVGO.
		 *
		 * @link https://github.com/sindresorhus/grunt-svgmin
		 */
		svgmin: {
			options: {
				plugins: [
					{ removeComments: true },
					{ removeUselessStrokeAndFill: true },
					{ removeEmptyAttrs: true }
				]
			},
			dist: {
				files: [{
					expand: true,
					cwd: 'assets/svg/',
					src: ['*.svg'],
					dest: 'assets/svg/'
				}]
			}
		},

		/**
		 * Merge SVGs into a single SVG.
		 *
		 * @link https://github.com/FWeinb/grunt-svgstore
		 */
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
					'assets/svg-defs.svg': 'assets/svg-icons/*.svg',
				}
			}
		},

		/**
		 * Compile Sass into CSS using node-sass.
		 *
		 * @link https://github.com/sindresorhus/grunt-sass
		 */
		sass: {
			options: {
				outputStyle: 'expanded',
				sourceComments: true,
				sourceMap: false,
			},
			dist: {
				files: {
					'assets/css/admin.css': 'assets/scss/admin.scss'
				}
			}
		},

		/**
		 * Apply several post-processors to CSS using PostCSS.
		 *
		 * @link https://github.com/nDmitry/grunt-postcss
		 */
		postcss: {
			options: {
				map: false,
				processors: [
					require('autoprefixer')({ browsers: 'last 2 versions' }),
					require('css-mqpacker')({ sort: true }),
				]
			},
			dist: {
				src: ['assets/css/admin.css', '!*.min.js']
			}
		},

		/**
		 * A modular minifier, built on top of the PostCSS ecosystem.
		 *
		 * @link https://github.com/ben-eb/cssnano
		 */
		cssnano: {
			options: {
				autoprefixer: false,
				safe: true,
			},
			dist: {
				files: {
					'assets/css/admin.min.css': 'assets/css/admin.css'
				}
			}
		},

		/**
		 * Minify files with UglifyJS.
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-uglify
		 */
		uglify: {
			build: {
				options: {
					sourceMap: false,
					mangle: false
				},
				files: [{
					expand: true,
					cwd: 'assets/js/',
					src: ['**/*.js', '!**/*.min.js'],
					dest: 'assets/js/',
					ext: '.min.js'
				}]
			}
		},

		/**
		 * Run tasks whenever watched files change.
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-watch
		 */
		watch: {

			scripts: {
				files: ['assets/js/**/*.js'],
				tasks: ['javascript'],
				options: {
					spawn: false,
					livereload: true,
				},
			},

			css: {
				files: ['assets/scss/**/*.scss'],
				tasks: ['styles'],
				options: {
					spawn: false,
					livereload: true,
				},
			},

			svg: {
				files: ['assets/svg/*.svg'],
				tasks: ['svgstore'],
				options: {
					spawn: false,
					livereload: true,
				},
			},
		},

		/**
		 * Clear files and folders.
		 *
		 * @link https://github.com/gruntjs/grunt-contrib-clean
		 */
		clean: {
			js: ['assets/js/project*', 'assets/js/**/*.min.js']
		},

		/**
		 * Internationalize WordPress themes and plugins.
		 *
		 * @link https://github.com/claudiosmweb/grunt-wp-i18n
		 */
		makepot: {
			theme: {
				options: {
					cwd: '',
					domainPath: 'languages/',
					potFilename: pkg.name + '.pot',
					type: 'wp-plugin'
				}
			}
		},

		/**
		 * Add Text Domain
		 *
		 * https://github.com/grappler/makepot/blob/master/add-textdomain.php
		 */
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

		/**
		 * Automatic Notifications when Grunt tasks fail.
		 *
		 * @link https://github.com/dylang/grunt-notify
		 */
		notify_hooks: {
			options: {
				enabled: true,
				max_jshint_notifications: 5,
				title: "WDS Mega Menus",
				success: false,
				duration: 2,
			}
		},
	});

	// Register Grunt tasks.
	grunt.registerTask('styles', ['sass', 'postcss', 'cssnano']);
	grunt.registerTask( 'php', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask('javascript', ['uglify']);
	grunt.registerTask('icons', ['svgmin', 'svgstore']);
	grunt.registerTask('i18n', ['makepot']);
	grunt.registerTask('default', ['styles', 'javascript', 'icons', 'i18n']);

	// grunt-notify shows native notifications on errors.
	grunt.loadNpmTasks('grunt-notify');
	grunt.task.run('notify_hooks');
};