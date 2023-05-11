<div
    x-data="{
        notifications: [],
        @if (flash()->message)
        init() {
            this.add({ id: 1, detail: { content: @js(flash()->message), type: '{{ flash()->level }}' } })
        },
       @endif
        add(e) {
            this.notifications.push({
                id: e.timeStamp,
                type: e.detail.type || 'success',
                content: e.detail.content,
            })
        },
        remove(notification) {
            this.notifications = this.notifications.filter(i => i.id !== notification.id)
        },
    }"
    @notify.window="add($event)"
    class="z-50 fixed top-0 right-0 pr-4 pb-4 max-w-sm w-full flex flex-col space-y-4 sm:justify-start"
    role="status"
    aria-live="polite"
>
    <!-- Notification -->
    <template x-for="notification in notifications" :key="notification.id">
        <div
            x-data="{
                show: false,
                timer: null,
                init() {
                    this.$nextTick(() => this.show = true)

                    this.startTimeout();
                },
                transitionOut() {
                    this.show = false

                    setTimeout(() => this.remove(this.notification), 500)
                },
                startTimeout() {
                    this.timer = setTimeout(() => this.transitionOut(), notification.type === 'error' ? 20000 : 3000)
                    this.$refs.countdown.classList.remove('alert-countdown--paused');
                },
                stopTimeout() {
                    if (! this.timer) return;
                    clearTimeout(this.timer);
                    this.$refs.countdown.classList.add('alert-countdown--paused');
                }
            }"
            x-show="show"
            x-on:mouseenter="stopTimeout()"
            x-on:mouseleave="startTimeout()"
            x-transition.duration.500ms
        >
            <div :class="'flex alert alert-flash alert-' + notification.type">
                <!-- Text -->
                <div class="w-0 flex-1">
                    <p x-text="notification.content" class="text-sm leading-5 font-medium"></p>
                </div>

                <!-- Remove button -->
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="transitionOut()" type="button" class="inline-flex opacity-50">
                        <svg aria-hidden class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close notification</span>
                    </button>
                </div>
                <div x-ref="countdown" class="alert-countdown" style="--alert-duration: 3s"></div>
            </div>
        </div>
    </template>
</div>
