/**
 * Fetch SVG Defs and dump them after the <body> tag.
 */
window.wdsmm_svg_defs = {};
( function( window, $, plugin ) {

	// Private variable.
	var fooVariable = 'foo';

	// Constructor.
	plugin.init = function() {
		plugin.cache();

		if ( plugin.meetsRequirements ) {
			plugin.bindEvents();
		}
	};

	// Cache all the things.
	plugin.cache = function() {
		plugin.$c = {
			window: $(window),
			body: $( 'body' ),
		};
	};

	// Combine all events.
	plugin.bindEvents = function() {
		plugin.$c.window.on( 'load', plugin.loadSvgs );
	};

	// Do we meet the requirements?
	plugin.meetsRequirements = function() {
		return plugin.$c.body.length;
	};

	// Dump the SVGs right after the opening <body> tag.
	plugin.loadSvgs = function() {
		plugin.$c.body.prepend( svg_defs.svgs );
	};

	// Engage!
	$( plugin.init );

})( window, jQuery, window.wdsmm_svg_defs );