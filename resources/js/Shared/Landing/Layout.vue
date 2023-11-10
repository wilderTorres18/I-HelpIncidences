<template>

    <div class="text-base text-black dark:text-white dark:bg-slate-900 layout_landing" :class="current_mode" :dir="$page.props.dir">
        <!-- Start Navbar -->
        <top-nav />
        <!-- End Navbar -->

        <!--success message -->
        <flash-messages />
        <!--success message -->

        <!--main content-->
        <slot />
        <!--main content-->
        <!-- footer -->
        <footer-section :footer="footer" />
        <!-- footer -->

        <!-- Back To Top Start -->
        <a id="back-to-top" @click="topFunction" href="javascript:void(0)" class="back-to-top flex fixed hidden bottom-5 right-5 left-auto z-[999] h-10 w-10 items-center justify-center rounded-md bg-primary text-white shadow-md transition duration-300 ease-in-out hover:bg-dark">
            <span class="mt-[6px] h-3 w-3 rotate-45 border-t border-l border-white"></span>
        </a>
        <!-- Back To Top End -->


        <!-- Public Chat -->
        <div class="chat_public" v-if="!!this.enable_option && this.enable_option.chat">
            <div v-if="!!open_chat && !!current_chat" class="chat__box">
                <div class="flex justify-center items-center ">
                    <div class="w-80 h-96 bg-white rounded shadow-2xl">
                        <nav class="w-full bg-gray-900 rounded-tr rounded-tl flex justify-between items-center">
                            <div class="flex justify-center items-center w-full"> <Logo class="block mx-auto w-full max-w-xs fill-white" height="40" /> </div>
                            <div class="flex items-center"> <i class="mdi mdi-video text-gray-300 mr-4"></i> <i class="mdi mdi-phone text-gray-300 mr-2"></i> <i class="mdi mdi-dots-vertical text-gray-300 mr-2"></i> </div>
                        </nav>
                        <div class="overflow-auto px-1 py-1 reverse__order" style="height: 19rem;" id="journal-scroll">

                            <div v-if="current_chat.messages && current_chat.messages.length" v-for="(message, index) in current_chat.messages.slice().reverse()" :key="index" class="flex items-end pr-4 mt-4" v-bind:class="{ 'pt-3 pl-10 justify-end': !!message.contact_id }">
                                <img v-if="message.user_id && current_chat.participant.user" src="/images/svg/profile.svg" class="rounded-full shadow-xl object-cover w-12 h-12" />

                                <span v-if="message.contact_id" class="text-gray-400 pr-1 f-time pb-1">{{ moment(message.updated_at).format('h:mma') }}</span>
                                <span class="flex h-auto text-gray-200 text-md font-normal rounded-sm px-1 p-1 items-end agent_text" v-bind:class="{ 'ml-1 bg-gray-900 dark:bg-slate-800': !!message.user_id, 'bg-green-900': !!message.contact_id }">
                                        {{ message.message }}
                                    </span>
                                <span v-if="message.user_id" class="text-gray-400 pl-1 f-time pb-1">{{ moment(message.updated_at).format('h:mma') }}</span>
                            </div>

                        </div>
                        <div class="flex justify-between items-center p-1 bg-white chat_b_footer">
                            <div class="relative t__area">
                                <!--                            <i class="mdi mdi-emoticon-excited-outline absolute top-1 left-1 text-gray-400" style="font-size: 17px !important;font-weight: bold;"></i>-->
                                <input v-model="message" @keydown.enter.exact.prevent="sendMessage" type="text" class=" pl-3 pr-3 py-2 focus:outline-none h-12 border-gray-100"
                                       placeholder="Type and press [enter].." id="typemsg">
                                <i class="mdi mdi-paperclip absolute right-8 top-1 transform -rotate-45 text-gray-400"></i>
                            </div>
                            <div class="w-8 h-8 rounded-full text-center items-center flex justify-center">
                                <button class="w-8 h-8 rounded-full text-center items-center flex justify-center focus:outline-none bg-gray-700 text-white p-2" @click="sendMessage">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 252.07 169.78" fill="currentcolor"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><polygon points="0 0 0 69.91 142.84 84.89 0 99.88 0 169.78 252.07 84.89 0 0"/></g></g></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="!!open_chat && !current_chat" class="init_chat">
                <div class="flex justify-center items-center ">
                    <div class="w-80 h-auto bg-white rounded shadow-2xl">
                        <nav class="w-full bg-gray-900 rounded-t flex flex-col items-center pt-3">
                            <Logo class="block mx-auto w-full max-w-xs fill-white" height="40" />
                            <p class="text-md font-medium text-gray-300 text-center p-3 mb-3 t_h_text">Let us know who you are, and let's get talking.</p>
                        </nav>
                        <div class="overflow-auto">

                            <div class="flex items-center">
                                <div class="w-full">
                                    <form class="bg-white rounded px-8 pt-6 pb-8">
                                        <div class="flex justify-center mb-4">
                                            <div class="flex flex-col pr-2">
                                                <label class="block text-gray-700 text-xs font-bold mb-2" for="firstName">
                                                    Nombres completos
                                                </label>
                                                <input v-model="init_user.firstName" class=" appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" id="firstName" type="text" placeholder="First name">
                                            </div>
                                            <div class="flex flex-col pl-2">
                                                <label class="block text-gray-700 text-xs font-bold mb-2" for="lastName">
                                                    Apellidos completos
                                                </label>
                                                <input v-model="init_user.lastName" class=" appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" id="lastName" type="text" placeholder="Last name">
                                            </div>
                                        </div>
                                        <div class="mb-6">
                                            <label class="block text-gray-700 text-xs font-bold mb-2" for="emailAddress">
                                                Email Addresss
                                            </label>
                                            <input v-model="init_user.email" class=" appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" id="emailAddress" type="text" placeholder="Email address">
                                        </div>
                                        <div class="flex items-center">
                                            <button class="flex text-gray-300 bg-gray-900 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline btn_start_chat" type="button" @click="startChat">
                                                Start Chat
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <button class="chat_bubble " @click="toggleChat">
                <span v-if="unread_count" class="notification_badge">1</span>
                <span class="chat__icn hover:scale-125 duration-300" v-if="!open_chat">
                    <img src="/images/svg/chat-logo-v2.svg" alt="Live Chat" />
                </span>
                <span class="chat__close hover:scale-125 duration-300" v-if="!!open_chat">
                    <img src="/images/svg/close.svg" alt="Close Chat" />
                </span>
                <span class="bottom_text">let's talk</span>
            </button>
        </div>
        <!-- Public Chat -->

        <span class="disabled_button"></span>


    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import TopNav from '@/Shared/Landing/TopNav'
import FooterSection from '@/Shared/Landing/FooterSection'
import Switcher from '@/Shared/Landing/Switcher'
import { Link } from '@inertiajs/inertia-vue3'
import FlashMessages from '@/Shared/FlashMessages'
import moment from 'moment'

export default {
    components: {
        Icon,
        Logo,
        Link,
        TopNav,
        FooterSection,
        Switcher,
        FlashMessages,
    },
    props: {
        title: String,
        footer: Object,
    },
    data() {
        return{
            current_mode: 'light',
            current_dir: 'ltr',
            current_chat: null,
            open_chat: false,
            unread_count: 0,
            chat_id: 0,
            message: null,
            enable_option: {},
            init_user:{
                firstName: '',
                lastName: '',
                email: '',
            },
        }
    },
    mounted() {
        if(localStorage.getItem('chat_id')){
            this.chat_id = localStorage.getItem('chat_id')
            this.axios.get(this.route('chat.conversation', this.chat_id)).then((res)=>{
                if(res.data && Object.keys(res.data).length){
                    this.current_chat = res.data
                    this.watchMessage()
                }
            })
        }
    },
    methods:{
        toggleChat(){
            this.open_chat = !this.open_chat
        },
        startChat(){
            if(!!this.init_user.email && !!this.init_user.firstName && !!this.init_user.lastName){
                this.axios.post(this.route('chat.init'), this.init_user).then((response) => {
                    if(!!response.data && !!response.data.contact_id){
                        this.current_chat = response.data
                        this.chat_id = this.current_chat.id
                        localStorage.setItem('chat_id', this.chat_id)
                        this.watchMessage()
                    }
                    // this.$emit('Chat initialize!')
                }).catch((error) => {
                    console.log(error)
                    // alert('Please setup pusher correctly to complete the chat setup.');
                })
            }
        },
        sendMessage(){
            var vm = this;
            const messageData = {
                message: this.message,
                contact_id: this.current_chat.contact_id,
                conversation_id: this.current_chat.id
            }
            this.message = ''
            this.axios.post(this.route('chat.send_message'), messageData).then((response) => {
                if(response.data){
                    vm.current_chat.messages.push(response.data)
                }
                // this.$emit('message sent!')
            }).catch((error) => {
                console.log(error)
            })
        },
        watchMessage(){
            var vm = this
            window.Echo.channel(`chat.${vm.chat_id}`).listen('NewPublicChatMessage', function (e){
                console.log(e)
                if(typeof e.chatMessage === 'object'){
                    vm.current_chat.messages.push(e.chatMessage)
                }
            })
        },
        scrollFunction() {
            const mybutton = document.getElementById("back-to-top");
            if(mybutton!=null){
                if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
                    mybutton.classList.add("block");
                    mybutton.classList.remove("hidden");
                } else {
                    mybutton.classList.add("hidden");
                    mybutton.classList.remove("block");
                }
            }
        },
        topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        },
        switchMode(){
            this.current_mode = this.current_mode === 'light' ? 'dark' : 'light'
            localStorage.setItem('current_mode', this.current_mode)
            this.changeTheme();
        },
        changeTheme(){
            const htmlTag = document.getElementsByTagName("html")[0]
            htmlTag.className = this.current_mode;
        },
        changeDir(){
            const htmlTag = document.getElementsByTagName("html")[0]
            htmlTag.dir = this.current_dir;
        }
    },
    updated() {

    },
    created() {
        this.moment = moment
        const vm = this;
        window.onscroll = function () {
            vm.scrollFunction();
        };
        if(localStorage.getItem('current_dir')){
            this.current_dir = localStorage.getItem('current_dir')
            this.changeDir();
        }
        if(this.$page.props.enable_options){
            let options = JSON.parse(this.$page.props.enable_options.value)
            options.forEach(option=>{
                this.enable_option[option.slug] = !!option.value
            })
        }
    }
}
</script>
