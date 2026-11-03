wp.blocks.registerBlockType('epl/archive', {
    edit: function Edit(props) {
        var useBlockProps = wp.blockEditor.useBlockProps;
        var ServerSideRender = wp.serverSideRender;

        return wp.element.createElement(
            'div',
            useBlockProps(),
            wp.element.createElement(ServerSideRender, {
                block: 'epl/archive',
                attributes: props.attributes
            })
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});
