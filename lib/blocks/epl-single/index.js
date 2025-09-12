const { __ } = wp.i18n;
const { useBlockProps } = wp.blockEditor;

wp.blocks.registerBlockType('epl/single', {
    edit: function Edit() {
        return wp.element.createElement(
            'div',
            useBlockProps(),
            __('EPL Single Block – rendered on frontend only', 'easy-property-listings')
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});