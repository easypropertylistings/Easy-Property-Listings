wp.blocks.registerBlockType('epl/single-directory', {
    edit: function Edit() {
        var useBlockProps = wp.blockEditor.useBlockProps;
        var ServerSideRender = wp.serverSideRender;

        return wp.element.createElement(
            'div',
            useBlockProps(),
            wp.element.createElement(ServerSideRender, {
                block: 'epl/single-directory'
            })
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});
