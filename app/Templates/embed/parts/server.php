<div @click="switchServer(link)" class="flex px-4 py-3 rounded-md"
    :class="serverId == link.id ? 'pointer-events-none bg-accent-500 text-accent-50-50' : 'bg-primary-800 text-primary-200 hover:bg-primary-800/75 cursor-pointer'">
    <svg class="hidden sm:block" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
        <path x-cloak x-show="serverId == link.id" fill="currentColor"
            d="m2.394 13.742 4.743 3.62 7.616-8.704-1.506-1.316-6.384 7.296-3.257-2.486zm19.359-5.084-1.506-1.316-6.369 7.279-.753-.602-1.25 1.562 2.247 1.798z">
        </path>
        <path x-cloak x-show="serverId != link.id" fill="currentColor" d="M7 6v12l10-6z"></path>
    </svg>
    <div class="sm:ml-2">
        <p class="md:text-lg font-semibold" x-text="link.name"></p>
        <small :class="serverId == link.id ? 'text-primary-100' : 'text-primary-300'" x-text="link.quality"></small>
    </div>
</div>