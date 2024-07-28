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
                    <div class="col-span-12 mt-5">
                        <div class="px-8">
                            @include('menu.user.components.skelaton-komentar')
                        </div>
                        <div class="grid gap-4" id="komentarContainer">
                        </div>
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
        const videoContainer = $('#komentarContainer');
        getData();
        async function getData() {
            setLoadingState();
            let url = '/user-video/show/{{ $id }}'
            axios.get(url)
                .then(response => {
                    const videoData = response.data.data;
                    setDataState(videoData);
                })
                .catch(error => {
                    console.log(error)
                    error.response.status === 404 ? window.location.href = '/user-video' : console.log(error)
                });
            axios.get('/komentar/get_data').then(response => {
                setKomentarState(response.data.data);
            }).catch(error => {
                console.log(error)
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


            const expiresAt = videoData && videoData.expired_at ? new Date(videoData.expired_at) : null;
            const timeRemaining = expiresAt ? expiresAt - new Date() : 0;
            console.log(expiresAt)
            if (timeRemaining > 0) {
                startTimer(timeRemaining);
            } else {
                window.location.href = '/user-video';
            }
        }

        function setKomentarState(komentar) {
            $('#loadingKomentar').addClass('hidden').removeClass('flex');
            $('#komentarContainer').empty();
            $('#komentarContainer').removeClass('hidden').addClass('grid');
            displayKomentar(komentar);
        }

        function displayKomentar(komentar) {
            komentar.forEach(komentar => {
                const komentarUserName = komentar.name;
                const komentarUserEmail = komentar.email;
                const komentarText = komentar.komentar;

                const komentarElementString = `
<figure class="w-full px-4 py-2 rounded overflow-hidden bg-gray-100" id="komentar-${komentar.id}">
    <div class="flex items-center mb-4 text-yellow-300">
        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
        </svg>
        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
        </svg>
        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
        </svg>
        <svg class="w-5 h-5 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
        </svg>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
        </svg>
    </div>
    <blockquote>
        <p class="text-2xl font-semibold text-gray-900 dark:text-white">"${komentarText}"</p>
    </blockquote>
    <figcaption class="flex items-center mt-6 space-x-3 rtl:space-x-reverse">
        <img class="w-6 h-6 rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png" alt="profile picture">
        <div class="flex items-center divide-x-2 rtl:divide-x-reverse divide-gray-300 dark:divide-gray-700">
            <cite class="pe-3 font-medium text-gray-900 dark:text-white">${komentarUserName}</cite>
            <cite class="ps-3 text-sm text-gray-500 dark:text-gray-400">${komentarUserEmail}</cite>
        </div>
    </figcaption>
</figure>
`;
                const komentarElement = $(komentarElementString);
                komentarElement.data('komentar', komentar);
                komentarElement.appendTo(komentarContainer);
            });
        };

        $('#btnKomentar').on('click', function(event) {
            event.preventDefault();
            var videoId = {{ $id }};
            var komentarText = $('#komentar').val();
            addKomentar(videoId, komentarText);
            $('#komentar').val('');
        });

        async function addKomentar(videoId, komentarText) {
            axios.post('/komentar', {
                'materi_id': videoId,
                'komentar': komentarText
            }).then(function(response) {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message
                });
            }).catch(function(error) {
                Toast.fire({
                    icon: 'error',
                    title: error.response.data.message
                })
            });
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast',
            },
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });
    </script>
@endsection
