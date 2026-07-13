wp.blocks.registerBlockType('epl/archive', {
    edit: function Edit() {
        const { __ } = wp.i18n;
        const { useBlockProps } = wp.blockEditor;

        return wp.element.createElement(
            'div',
            useBlockProps(),
            __('EPL Archive Block – rendered on frontend only', 'easy-property-listings')
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});