            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['foreColor', 'backColor'],
                ['link'],
                ['base64'],
                ['justifyLeft', 'justifyCenter'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['fullscreen']
            ],
            btnsDef: {
                base64: {
                    ico: 'insertImage',
                    title: '@lang("Infoga bild")',
                }
            },
            lang: 'sv',
            minimalLinks: true,
            plugins: {
                colors: {
                    colorList: [
                        {!!\App\Color::list_for_trumbowyg()!!}
                    ],
                    displayAsList: true
                },
                allowTagsFromPaste: {
                    allowedTags: ['h1', 'h2', 'h3', 'h4', 'a', 'strong', 'i', 'li', 'ul', 'ol']
                }
            }
