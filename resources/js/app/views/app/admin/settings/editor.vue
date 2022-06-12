<template>
    <ckeditor
        :editor="editor"
        v-model="_content"
        :config="editorConfig"
        ref="content"
        label="Content"
        class="mb-3"
        @ready="onEditorReady"
        v-bind="$attrs"
    ></ckeditor>
</template>

<script>
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
    name: "editor",
    props: {
        content: {
            type: String,
            default: "",
            required: true
        }
    },
    computed: {
        _content: {
            get() {
                return this.content
            },
            set(newValue) {
                return this.$emit('update:content', newValue)
            }
        }
    },
    data() {
        return {
            editor: ClassicEditor,
            editorConfig: {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                heading: {
                    options: [
                        {model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph'},
                        {
                            model: 'heading1',
                            view: {
                                name: 'h1', classes: 'display-2'
                            },
                            title: 'Heading 1',
                            class: 'mb-4 display-2'
                        },
                        {
                            model: 'heading2',
                            view: {
                                name: 'h2', classes: 'display-1'
                            },
                            title: 'Heading 2',
                            class: 'mb-4 display-1'
                        },
                    ]
                }
            }
        }
    },
    methods: {
        /**
         * Credit: https://stackoverflow.com/a/65683795
         */
        onEditorReady(e) {
            const myClass = 'body-1'
            const addCustomClass = () => this.$el.nextSibling
                .querySelector('.ck-content')
                .classList
                .add(myClass)

            this.$nextTick(addCustomClass)
        }
    }
}
</script>

<style scoped>

</style>
