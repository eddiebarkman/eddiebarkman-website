( function( api ) {

	// Extends our custom "vw-photography" section.
	api.sectionConstructor['vw-photography'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );