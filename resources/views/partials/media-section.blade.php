<section class="bg-gray-900 py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-white mb-4">Watch & Connect</h2>
            <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                Catch the trailer, wishlist on Steam, watch development live on Twitch, and join our amazing community!
            </p>
        </div>

{{--        <div class="mb-16">--}}
{{--            <div class="max-w-4xl mx-auto">--}}
{{--                <div class="bg-gray-800 rounded-lg p-6 mb-8">--}}
{{--                    <h3 class="text-2xl font-semibold text-white mb-4 flex items-center">--}}
{{--                        <i class="fab fa-twitch text-purple-500 mr-3"></i> --}}
{{--                        Live Development Stream--}}
{{--                    </h3>--}}
{{--                    <p class="text-gray-400 mb-6">Join the stream and chat while I develop SurfsUp live!</p>--}}
{{--                    <div class="aspect-video w-full rounded-lg overflow-hidden bg-gray-900">--}}
{{--                        <iframe--}}
{{--                            src="https://player.twitch.tv/?channel=bearlikelion&parent={{ request()->getHost() }}"--}}
{{--                            frameborder="0"--}}
{{--                            allowfullscreen="true"--}}
{{--                            scrolling="no"--}}
{{--                            height="100%"--}}
{{--                            width="100%"--}}
{{--                            class="w-full h-full">--}}
{{--                        </iframe>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="grid lg:grid-cols-2 gap-8 max-w-7xl mx-auto">
            <div class="space-y-8">
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-2xl font-semibold text-white mb-4 flex items-center">
                        <i class="fab fa-youtube text-red-500 mr-3"></i>
                        Official Trailer
                    </h3>
                    <div class="aspect-video w-full rounded-lg overflow-hidden bg-gray-900">
                        <iframe
                            class="w-full h-full"
                            src="https://www.youtube.com/embed/j2XA7omfhUc"
                            title="SurfsUp Trailer"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-2xl font-semibold text-white mb-4 flex items-center">
                        <i class="fab fa-steam text-gray-300 mr-3"></i>
                        Wishlist on Steam
                    </h3>
                    <iframe
                        src="https://store.steampowered.com/widget/3454830/"
                        frameborder="0"
                        width="100%"
                        height="190"
                        class="rounded-lg w-full">
                    </iframe>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-2xl font-semibold text-white mb-4 flex items-center">
                    <i class="fab fa-discord text-indigo-500 mr-3"></i>
                    Join Our Community
                </h3>
                <iframe
                    src="https://discord.com/widget?id=1243644214105997373&theme=dark"
                    width="100%"
                    height="500"
                    allowtransparency="true"
                    frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    class="rounded-lg w-full">
                </iframe>
            </div>
        </div>
    </div>
</section>
