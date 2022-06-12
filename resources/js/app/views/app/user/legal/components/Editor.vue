<template>
    <ckeditor
        ref="content"
        label="Content"
        class="mb-3"
        :editor="state.editor"
        :config="state.editorConfig"
        v-model="_content"
        v-bind="$attrs"
    >

    </ckeditor>
</template>

<script>
import { computed, defineComponent, reactive } from '@vue/composition-api';
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default defineComponent({
    name: "editor-component",
    props: {
        content: {
            type: String,
            default: "",
            required: true,
        }
    },
    setup(props, { root, emit }) {
        const state = reactive({
            editor: ClassicEditor,
            editorConfig: {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                heading: {
                    options: [
                        {
                            model: 'paragraph',
                            title: 'Paragraph',
                            class: 'ck-heading_paragraph'
                        },
                        {
                            model: 'heading1',
                            view: {
                                name: 'h1',
                                classes: 'display-2'
                            },
                            title: 'Heading 1',
                            class: 'mb-4 display-2'
                        },
                        {
                            model: 'heading2',
                            view: {
                                name: 'h2',
                                classes: 'display-1'
                            },
                            title: 'Heading 2',
                            class: 'mb-4 display-1'
                        },
                    ]
                }
            },
        });

        const _content = computed({
            get: () => props.content,
            set: value => {
                emit('update:content', value);
            }
        });


        return {
            state,
            _content,
        };
    },
});
</script>
