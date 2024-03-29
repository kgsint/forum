<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

// props
const props = defineProps({
    form: Object
})

const markdownPreviewEnabled = ref(false)
const markdownHtml = ref('')
const loading = ref(false)

// watch changes in markdownPreviewEnabled ref
watch(markdownPreviewEnabled, (isEnabled) => {
    // if not enabled, do not make a request
    if(! isEnabled) {
        return
    }

    loading.value = true
    // if enabled, request to markdown.preview route with body
    axios.post(route('markdown.preview'), {
        body: props.form.body,
    }).then(
        res => {
            loading.value = false // reset loading
            markdownHtml.value = res.data.markdown_html // assign response data to markdownHtml ref
        }
    )
})

</script>


<template>
    <div class="fixed bottom-0 w-full shadow-sm">
        <div class="max-w-full lg:max-w-5xl mx-auto h-full bg-gray-100 border-2 border-blue-200 shadow-sm p-6 rounded-md space-y-6">
            <!-- header -->
            <div>
                <slot name="header" />
            </div>
            <!-- form -->
            <div>
                <slot name="main" :markdownPreviewEnabled="markdownPreviewEnabled" />
                <!-- markdown preview  -->
                <div
                    v-html="markdownHtml"
                    v-if="markdownPreviewEnabled && !loading"
                    class="prose max-w-none h-64 w-full bg-gray-100 border border-gray-300 rounded-md markdown p-2 overflow-auto"
                >
                </div>
                <!-- loading indicator -->
                <div
                    v-if="loading"
                    class="h-64 w-full bg-gray-200 grid items-center text-center rounded-md markdown p-3 overflow-auto"
                >
                    Loading...
                </div>

                <div class="flex justify-between mt-2">
                    <!-- notation for markdown -->
                    <p class="text-xs text-grey-800 dark:text-grey-600/50 mobile:hidden"> * You may use Markdown with <a href="https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax" target="_blank" rel="noreferrer noopener" class="text-blue-500 underline hover:text-blue-600 duration-150 transition-all"> GitHub-flavored </a> code blocks. </p>
                    <!-- toggler markdown markdown preview -->
                    <button
                        @click="markdownPreviewEnabled = !markdownPreviewEnabled"
                        class="text-xs text-blue-500"
                    >
                        {{ markdownPreviewEnabled ? 'Turn off' : 'Turn on' }} markdown preview
                    </button>
                </div>
            </div>
            <!-- btns or smth -->
            <div class="pb-5">
                <slot name="footer" />
            </div>
        </div>
    </div>
</template>

