(function (wp, jQuery) {

    var el = wp.element.createElement;

    // source: https://stampede-design.com/blog/creating-a-custom-block-type-for-wordpress-gutenberg-editor/
    wp.blocks.registerBlockType('aa-berlin-addons/notice-block', {
        title: 'Prominent Notice',
        icon: 'lightbulb',
        category: 'common',

        attributes: {
            type: {
                type: 'string',
                default: 'info'
            },

            headline: {
                type: 'string'
            },

            content: {
                type: 'array',
                source: 'children',
                selector: 'p'
            }
        },

        edit: function (props) {
            return el(
                'section',
                {
                    className: 'aa-berlin-addons-notice-block type-' + props.attributes.type
                },
                el(
                    'select',
                    {
                        onChange: updateType,
                        value: props.attributes.type
                    },
                    el('option', {value: 'info'}, 'Info'),
                    el('option', {value: 'success'}, 'Success'),
                    el('option', {value: 'warning'}, 'Warning')
                ),
                el(
                    wp.editor.RichText,
                    {
                        tagName: 'h4',
                        placeholder: 'Enter headline here...',
                        value: props.attributes.headline,
                        onChange: updateHeadline
                    }
                ),
                el(
                    wp.editor.RichText,
                    {
                        tagName: 'p',
                        onChange: updateContent,
                        value: props.attributes.content,
                        placeholder: 'Enter message here...'
                    }
                )
            );

            function updateType(event) {
                props.setAttributes({
                    type: event.target.value
                });
            }

            function updateHeadline(headlineElements) {
                props.setAttributes({
                    headline: headlineElements
                });
            }

            function updateContent(contentElements) {
                props.setAttributes({
                    content: contentElements
                });
            }
        },

        save: function (props) {
            return el(
                'section',
                {
                    className: 'aa-berlin-addons-notice-block type-' + props.attributes.type
                },
                el(
                    wp.editor.RichText.Content,
                    {
                        tagName: 'h4',
                        value: props.attributes.headline
                    }
                ),
                el(
                    wp.editor.RichText.Content,
                    {
                        tagName: 'p',
                        value: props.attributes.content
                    }
                )
            );
        }
    });

})(wp, jQuery);
