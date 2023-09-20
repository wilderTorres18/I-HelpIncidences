<template>
    <div>
        <Head :title="__(title)" />
        <!-- Start Hero -->
        <section class="relative z-10 overflow-hidden bg-primary pt-[120px] pb-[100px] md:pt-[130px] lg:pt-[160px]">
            <div class="container">
                <div class="-mx-4 flex flex-wrap items-center">
                    <div class="w-full px-4">
                        <div class="text-center">
                            <h1 class="text-4xl font-semibold text-white">{{ kb.title }}</h1>
                            <ul class="list-none mt-6">
                                <li class="inline-block font-semibold mx-5"> <span class=" block">Time :</span> <span class="block">{{ getReadingTime(kb.details) }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div><!--end grid-->
            </div><!--end container-->

            <div>
                <span class="absolute top-0 left-0 z-[-1]">
                    <img src="/landing/images/header/shape-1.svg" alt="" />
                </span>
                <span class="absolute top-0 right-0 z-[-1]">
                    <img src="/landing/images/header/shape-2.svg" alt="" />
                </span>
            </div>

        </section><!--end section-->
        <!-- End Hero -->

        <!-- Start Section-->
        <section class="relative md:py-24 py-16">
            <div class="container">
                <div class="grid md:grid-cols-12 grid-cols-1 gap-[30px]">
                    <div class="lg:col-span-8 md:col-span-6">
                        <div class="p-6 rounded-md">
                            <div class="post-details html" v-html="sanitizeHtml(kb.details)"></div>
                        </div>

                    </div>

                    <div class="lg:col-span-4 md:col-span-6">
                        <div class="sticky top-20">

                            <div v-if="random_kb.length">
                                <h5 class="text-lg font-semibold bg-gray-50 dark:bg-slate-800 shadow dark:shadow-gray-800 rounded-md p-2 text-center mt-8">Random</h5>
                                <div class="flex items-center mt-8" v-for="kb in random_kb" :key="kb.id">
                                    <div class="ml-3">
                                        <a href="" class="font-semibold hover:text-indigo-600">{{ kb.title }}</a>
                                        <p v-if="kb.name" class="text-sm text-slate-400">{{ kb.name }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div><!--end grid-->
            </div><!--end container-->

            <subscribe />
        </section><!--end section-->
        <!-- End -->
    </div>
</template>
<script>
import Layout from '@/Shared/Landing/Layout'
import Icon from '@/Shared/Icon'
import {Head} from '@inertiajs/inertia-vue3'
import Subscribe from '@/Shared/Landing/Subscribe'
import sanitizeHtml from "sanitize-html";
export default {
    layout: Layout,
    props: {
        kb: Object,
        random_kb: Array,
        types: Array,
    },
    components: {
        Icon,
        Head,
        Subscribe,
    },
    data(){
        return {
            title: this.kb.title,
            author_name: ''
        }
    },
    methods: {
        getReadingTime(text){
            const wpm = 225;
            const words = text.trim().split(/\s+/).length;
            const time = Math.ceil(words / wpm);
            return time+' minute read';
        },
        sanitizeHtml : sanitizeHtml
    },
    created() {

    }
}
</script>
