@extends('menu.user.layouts.master')

@section('content')
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            @include('menu.user.components.header-video')
            <div class="p-5">
                @include('menu.user.components.skelaton-video')
                <div class="hidden gap-4" id="videoContainer">
                    <div class="col-span-12">
                        <video class="w-full h-96 bg-cover bg-center rounded-md" src="" controls id="video">
                            <source src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="col-span-12 flex justify-between mt-5">
                        <h1 class="text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white"
                            id="title"></h1>
                        <div id="timerContainer" class="text-2xl font-semibold text-gray-700 mt-4">Waktu Tersisa: <span
                                id="timer"></span></div>
                    </div>
                    <div class="col-span-12 flex justify-between mt-5">
                        <p class="mb-3 text-gray-500 dark:text-gray-400" id="description"></p>
                    </div>
                    <div class="col-span-12 flex justify-between mt-5">
                        <label for="default-search"
                            class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <input type="text" id="komentar"
                                class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Berkomentar yang sopan...." required />
                            <button type="submit"
                                class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                id="btnKomentar">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        getData();
        async function getData() {
            setLoadingState();
            let url = '/user-video/show/{{ $id }}'
            axios.get(url)
                .then(response => {
                    const videoData = response.data.data;
                    console.log(videoData)
                    setDataState(videoData);

                })
                .catch(error => {
                    error.response.status === 404 ? window.location.href = '/user-video' : console.log(error)
                });
        }

        function startTimer(duration) {
            let timer = duration;
            const timerElement = $('#timer');
            let hours, minutes, seconds;
            const intervalId = setInterval(function() {
                hours = Math.floor(timer / 3600000);
                minutes = Math.floor((timer % 3600000) / 60000);
                seconds = parseInt((timer % 60000) / 1000, 10);
                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                timerElement.text(`${hours}:${minutes}:${seconds}`);
                timer -= 1000;
                if (timer < 0) {
                    clearInterval(intervalId);
                    axios.post('/user-video/clear-cache/' + '{{ $id }}')
                    window.location.href = '/user-video';
                }
            }, 1000);
        }

        function displayVideo(data) {
            const video = $('#video');
            const title = $('#title');
            const description = $('#description');
            video.attr('src', `{{ Storage::disk('materi')->url('${data.video}') }}`);
            title.text(data.title);
            description.text(data.description);
        }

        function setLoadingState() {
            $('#videoContainer').addClass('hidden').removeClass('grid');
            $('#loading').removeClass('hidden').addClass('flex');
            $('#notFound').addClass('hidden').removeClass('flex');
        }

        function setDataState(videoData) {
            $('#loading').addClass('hidden').removeClass('flex');
            $('#notFound').addClass('hidden').removeClass('flex');
            $('#videoContainer').removeClass('hidden').addClass('grid');
            displayVideo(videoData);
            const expiresAt = videoData.expired_at ?
                new Date(videoData.expired_at) :
                null;
            const timeRemaining = expiresAt ? expiresAt - new Date() : 0;
            console.log(timeRemaining)
            if (timeRemaining > 0) {
                startTimer(timeRemaining);
            } else {
                window.location.href = '/user-video';
            }
        }
    </script>
@endsection
