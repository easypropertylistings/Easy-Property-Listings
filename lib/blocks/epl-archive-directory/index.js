wp.blocks.registerBlockType('epl/archive-directory', {
    edit: function Edit() {
        var useBlockProps = wp.blockEditor.useBlockProps;
        var ServerSideRender = wp.serverSideRender;

        return wp.element.createElement(
            'div',
            useBlockProps(),
            wp.element.createElement(ServerSideRender, {
                block: 'epl/archive-directory'
            })
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});
