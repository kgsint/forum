<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import ForumPostCard from '@/Components/Forum/PostCard.vue'
import useCreateReply from '@/Composables/useCreateReply'
import { ref, watch, computed } from 'vue';
import Textarea from '../Textarea.vue';
import InputError from '../InputError.vue';
import axios from 'axios';
import CheckedIcon from '@/Components/Icons/CheckedIcon.vue'
import { Mentionable } from 'vue-mention';
import useMentionable from '@/Composables/useMentionable'
import useCheckAccountType from '@/Composables/useCheckAccountType'
import OptionIcon from '@/Components/Icons/OptionIcon.vue'
import useThreadPost from '@/Composables/useThreadPost';

defineOptions({
    inheritAttrs: false
})

// props
const props = defineProps({
    post: Object,
    solutionId: Number
})

const isBestAnswer = computed(() => {
    return props.solutionId === props.post.id
})

// composables
const { showReplyForm } = useCreateReply()
const { handleBestAnswer, handleSpamReport, handleDeletePost } = useThreadPost(props.post)
const { mentionableList, handleMentionSearch } = useMentionable()
const { isAdmin, isModerator } = useCheckAccountType(props.post.user)
// edit form object
const editForm = useForm({
    body: props.post.body_markdown
})
// ref for toggling edit form
const isEdit = ref(false)
const markdownPreviewEnabled = ref(false)
const markdownHtml = ref('')
const loading = ref(false) // loading indicator for markdown preview
const showOptionDialog = ref(false)
const isLiked = ref(props.post.is_liked)
const likeCount = ref(props.post.like_count)
const disableLikeButton = ref(false)

watch(markdownPreviewEnabled, (isEnabled) => {
    // markdown preview is off, do nothing
    if(! isEnabled) {
        return
    }

    loading.value = true
    // if turn on, make request to the route
    axios.post(route('markdown.preview'), {
        body: editForm.body.trim(),
    }).then(res => {
        loading.value = false
        markdownHtml.value = res.data.markdown_html
    })
})

// submit edit form request
const handleEditPost = () => {
    editForm.patch(route('posts.update', {
        thread: props.post.thread,
        post: props.post
    }), {
        onSuccess: () => {
            isEdit.value = false
        }
    })
}

// hide edit form
const hideEditForm = () => {
    isEdit.value = false
    editForm.errors.body = '' // when toggling off the form, reset validation error if any
}

// like or unlike
const handleLikeOrUnlike = (postId) => {
    // making reactive in client side
    isLiked.value = !isLiked.value
    if(isLiked.value) {
        likeCount.value = props.post.like_count + 1
    }else {
        likeCount.value = props.post.like_count
    }
    // making request to server
    disableLikeButton.value = true
    axios.post(route('posts.likes.store', { post: postId }))
        .then((res) => {
            if(res.status === 200) {
                disableLikeButton.value = false
                // update with actual like count
                likeCount.value = res.data.like_count
            }
        })
}

// click away | click outside option btn
window.addEventListener('click', (e) => {
    if(document.querySelector(`#option-btn-${props.post.id}`) && ! document.querySelector(`#option-btn-${props.post.id}`).contains(e.target) && showOptionDialog.value === true) {
        showOptionDialog.value = false
    }
})
</script>


<template>
    <article
        :id="`post-${post.id}`"
        class="relative flex bg-white space-x-2 px-2 py-4 rounded-lg shadow thread-post"
        v-bind="$attrs"
        :class="{ '!bg-blue-50 border border-blue-400': isBestAnswer }"
    >
        <!-- profile image -->
        <div class="flex-none flex lg:items-center gap-2 lg:block mb-3">
            <a href="#">
                <img :src="post.user?.avatar" class="w-[28px] h-[28px] lg:w-14 lg:h-14 rounded-xl object-cover" alt="profile image" v-if="post.user">
                <img src="https://static.thenounproject.com/png/5034901-200.png" class="w-[20px] h-[20px] lg:w-14 lg:h-14 rounded-xl" alt="default profile image" v-else>
            </a>
        </div>
        <div class="flex flex-col flex-1 space-y-3">
            <div class="flex justify-between items-start">
                <div class="flex flex-col">
                    <Link :href="route('profile.show', post.user)" class="flex items-center space-x-2 mb-1" style="color: #111; text-decoration: none;">
                        <!-- username -->
                        <h4 class="text-md font-semibold">{{ post.user?.username || '[Deleted User]' }}</h4>
                        <!-- thread owner badge -->
                        <span
                            v-if="post.thread.user.id === post.user.id"
                            class="text-[10px] font-semibold bg-gray-400 px-2 py-1 text-gray-100 rounded-2xl"
                        >
                            Thread Owner
                        </span>
                        <!-- admin or moderator badge -->
                        <span
                            v-if="isAdmin() || isModerator()"
                            class="text-[10px] font-semibold px-3 py-1 rounded-2xl uppercase"
                            :class="{ 'bg-blue-400 text-gray-50': isAdmin(), 'border border-blue-400 text-blue-400': isModerator() }"
                        >
                            {{ post.user.type }}
                        </span>
                    </Link>
                    <!-- created date -->
                    <div class="text-xs text-gray-600 leading-normal font-semibold">
                        <time :datetime="post.created_at.datetime" :title="post.created_at.datetime">{{ post.created_at.human }}</time>
                    </div>
                </div>
                <h5 v-if="isBestAnswer" class="text-blue-600 font-bold flex items-center">
                    <CheckedIcon />
                    Best Answer
                </h5>
            </div>

            <!-- description -->
            <p
                v-if="!isEdit"
                v-html="post.body"
                class="prose max-w-none text-sm text-gray-600 leading-7">
            </p>

            <!-- edit form -->
            <form @submit.prevent="handleEditPost"  v-else>
                <!-- textarea for editing post -->
                <div v-if="! markdownPreviewEnabled">
                    <Mentionable
                        :keys="['@']"
                        :items="mentionableList"
                        v-on:search="handleMentionSearch"
                    >
                        <Textarea v-model="editForm.body" rows="4" class="h-32" />
                    </Mentionable>
                    <InputError :message="editForm.errors.body" />
                </div>
                <!-- markdown preview panel -->
                <div
                    v-html="markdownHtml"
                    class="bg-gray-200 w-full h-32 mb-2 p-3 rounded-lg  markdown"
                    v-if="markdownPreviewEnabled && !loading">
                </div>

                <!-- loading indicator -->
                <div v-if="loading" class="bg-gray-200 w-full mb-2 p-3 rounded-lg  markdown text-center my-auto">
                    <span>Loading...</span>
                </div>

                <div class="space-x-3 flex justify-between items-start">
                    <button
                        type="button"
                        @click="markdownPreviewEnabled = !markdownPreviewEnabled"
                        class="text-xs text-blue-500 cursor-pointer">
                        {{ markdownPreviewEnabled ? 'Turn off' : 'Turn on' }} Markdown Preview
                    </button>
                    <div class="space-x-2">
                        <button
                            type="button"
                            @click="hideEditForm"
                            class="bg-gray-200 px-4 py-2 text-sm rounded-xl font-bold hover:bg-gray-300 transition-all duration-150">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-500 text-white px-4 py-2 text-sm rounded-xl font-bold hover:bg-blue-300 transition-all duration-150">
                            Update
                        </button>
                    </div>
                </div>
            </form>

            <!-- reply, edit and delete button -->
            <div class="flex items-center justify-between" v-if="!isEdit">
                <div class="space-x-3">
                    <button
                        @click="handleLikeOrUnlike(post.id)"
                        v-if="$page.props.auth.user"
                        class="bg-blue-200 px-4 py-2 text-sm rounded-xl font-bold hover:bg-blue-300 transition-all duration-150"
                        :class="{ 'bg-blue-400 text-gray-200': isLiked }"
                        :disabled="disableLikeButton"
                    >
                        <span v-if="isLiked">Liked ({{ likeCount ?? 0 }})</span>
                        <span v-else>Like ({{ likeCount ?? 0 }})</span>
                    </button>
                    <button
                        v-if="$page.props.auth.user"
                        @click="showReplyForm(post, false)"
                        class="bg-gray-200 px-4 py-2 text-sm rounded-xl font-bold hover:bg-gray-300 transition-all duration-150">
                        Reply
                    </button>
                </div>

                <!-- action btns -->
                <div class="space-x-3 flex items-center">
                    <button
                        v-if="$page.props.auth.user"
                        :id="`option-btn-${post.id}`"
                        @click="showOptionDialog = !showOptionDialog"
                        class="relative hover:bg-gray-300 hover:text-white rounded-full"
                    >
                        <OptionIcon />
                        <!-- option dialog -->
                        <ul
                            v-show="showOptionDialog"
                            v-if="$page.props.auth.user"
                            class="z-50 duration-500 transition-all absolute top-8 right-3 bg-gray-200 border border-gray-100 rounded-lg
                                        shadow text-sm flex flex-col min-w-[140px]"
                        >
                            <li
                                @click="handleSpamReport"
                                class="list-none bg-gray-50 px-4 py-2 cursor-pointer hover:bg-gray-200 border-b
                                    border-gray-200 flex items-center text-black hover:text-black last:border-b-0">
                                    Report as Spam
                            </li>
                            <li
                                    v-if="post.can.update"
                                    @click="isEdit = true"
                                    class="list-none bg-gray-50 px-4 py-2 cursor-pointer hover:bg-gray-200 border-b
                                        border-gray-200 last:border-b-0 text-black hover:text-black">
                                        <button>
                                            Edit
                                        </button>
                            </li>
                            <li
                                @click="handleBestAnswer(solutionId)"
                                v-if="post.thread.can.manage"
                                    class="text-xs list-none bg-gray-50 px-4 py-2 cursor-pointer hover:bg-gray-200 border-b
                                        border-gray-200 last:border-b-0 text-black hover:text-black">
                                    <button>
                                        {{ isBestAnswer ? 'Remove' : 'Mark' }} best answer
                                    </button>
                            </li>
                            <li
                                v-if="post.can.delete"
                                @click="handleDeletePost"
                                    class="list-none bg-gray-50 px-4 py-2 cursor-pointer hover:bg-gray-200 border-b text-red-500
                                        border-gray-200 last:border-b-0">
                                    <button>
                                            Delete
                                    </button>
                            </li>
                        </ul>
                    </button>

                </div>
            </div>
        </div>
    </article>

        <!-- {{ post.replies }} -->
        <ForumPostCard
            class="reply-post ml-0 md:ml-10"
            v-if="post?.replies?.length"
            v-for="reply in post.replies"
            :key="reply.id"
            :post="reply"
            :solutionId="solutionId"
        />
</template>
