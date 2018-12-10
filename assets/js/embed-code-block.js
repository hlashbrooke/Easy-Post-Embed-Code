( function( blocks, i18n, element ) {
    var el = element.createElement;
    var __ = i18n.__;

    blocks.registerBlockType( 'easy-post-embed-code/embed-code-block', {
        title: __( 'Embed Code', 'easy-post-embed-code' ),
        icon: 'media-code',
        category: 'widgets',

        edit: function() {
            return (
                el(
                    'div', { className: 'components-placeholder' },
                    el( 'span', { className: 'dashicons dashicons-media-code' } ),
                    el( 'h2', {}, __( 'Embed code for this post', 'easy-post-embed-code' ) ),
                    el( 'em', {}, __( 'Includes width/height selection and button to copy the code', 'easy-post-embed-code' ) )
                )
            );
        },
        save: function() {
            return null;
        },
    } );
}(
    window.wp.blocks,
    window.wp.i18n,
    window.wp.element
) );
