@extends('menu.user.layouts.master')

@section('content')
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            @include('menu.user.components.header-video')
            <div class="p-5">
                @include('menu.user.components.skelaton-video')
                @include('menu.user.components.404-video')
                <div id="videoContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('scripts')
    <script type="module">
        const videoContainer = $('#videoContainer');
        searchVideos('');

        function setLoadingState() {
            $('#loading').removeClass('hidden').addClass('flex');
            $('#notFound').addClass('hidden').removeClass('flex');
            $('#details').addClass('hidden').removeClass('grid');
        }

        function setNotFoundState() {
            $('#loading').addClass('hidden').removeClass('flex');
            $('#details').addClass('hidden').removeClass('grid');
            $('#notFound').removeClass('hidden').addClass('flex');
        }

        function setDataState(videoData) {
            $('#loading').addClass('hidden').removeClass('flex');
            $('#notFound').addClass('hidden').removeClass('flex');
            $('#details').removeClass('hidden').addClass('grid');
            displayThumbnails(videoData);
        }

        async function getRequest(url, params) {
            try {
                if (url == '') {
                    return
                }
                const response = await axios.get('user-video/' + url, {
                    params: {
                        id_user: params.id_user,
                        id_materi: params.id_video
                    }
                });
            } catch (error) {
                setNotFoundState();
            }
        }

        async function searchVideos(query) {
            setLoadingState();
            videoContainer.empty();
            if (query === '') {
                try {
                    const response = await axios.get('/user-video/get_data');
                    setDataState(response.data.data);
                    return;
                } catch (error) {
                    setNotFoundState();
                    return
                }
            }


            try {
                const response = await axios.get('user-video/get_data/' + query);
                setDataState(response.data.data);
                if (Object.keys(response.data.data).length === 0) {
                    setNotFoundState();
                }
            } catch (error) {
                setNotFoundState();
            }
        }

        const debounce = (func, delay) => {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        };

        window.Echo.channel('laravel_database_approve-channel')
            .listen('.approve-user-event', (e) => {
                const newData = e.message;
                const userId = {{ auth()->user() ? auth()->user()->id : null }}
                if (newData.user_id == userId) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Request Kamu Di Approve Oleh Admin'
                    })
                    const thumbnailToUpdate = $(`#${newData.video_material_id}`);
                    if (thumbnailToUpdate.length > 0) {
                        const videoData = thumbnailToUpdate.data('video');
                        videoData.access = 'Cek Video';
                        videoData.url = '/user-video/lihat/' + videoData.id;
                        thumbnailToUpdate.remove();
                        displayThumbnails([videoData]);
                    }
                }
            })
            .listen('.cancel-user-event', (e) => {
                const newData = e.message;
                const userId = {{ auth()->user() ? auth()->user()->id : null }}
                if (newData.user_id == userId) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Request Kamu Di Cancel Oleh Admin'
                    })
                    const thumbnailToUpdate = $(`#${newData.video_material_id}`);
                    if (thumbnailToUpdate.length > 0) {
                        const videoData = thumbnailToUpdate.data('video');
                        videoData.access = 'Pending';
                        videoData.url = '';
                        thumbnailToUpdate.remove();
                        displayThumbnails([videoData]);
                    }
                }
            })
            .listen('.reject-user-event', (e) => {
                const newData = e.message;
                const userId = {{ auth()->user() ? auth()->user()->id : null }}
                if (newData.user_id == userId) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Request Kamu Di Tolak Oleh Admin'
                    })
                    const thumbnailToUpdate = $(`#${newData.video_material_id}`);
                    if (thumbnailToUpdate.length > 0) {
                        const videoData = thumbnailToUpdate.data('video');
                        videoData.access = 'Minta Request';
                        videoData.url = 'user-video/request?id_user=' + {{ auth()->user()->id }} + '&id_video=' +
                            videoData.id;
                        thumbnailToUpdate.remove();
                        displayThumbnails([videoData]);
                    }
                }
            })
            .listen('.request-user-event', (e) => {
                const newData = e.message;
                const userId = {{ auth()->user() ? auth()->user()->id : null }}
                if (newData.user_id == userId) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Request Kamu Di Approve Oleh Admin'
                    })
                    const thumbnailToUpdate = $(`#${newData.video_material_id}`);
                    if (thumbnailToUpdate.length > 0) {
                        const videoData = thumbnailToUpdate.data('video');
                        videoData.access = 'Pending';
                        videoData.url = '/user-video/lihat/' + videoData.id;
                        thumbnailToUpdate.remove();
                        displayThumbnails([videoData]);
                    }
                }
            });

        window.Echo.channel('laravel_database_materi-channel')
            .listen('.materi-create-event', (e) => {
                searchVideos('');
            })
            .listen('.materi-edit-event', (e) => {
                searchVideos('');
            })
            .listen('.materi-delete-event', (e) => {
                searchVideos('');
            });

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

        const debouncedSearch = debounce(function() {
            searchVideos(this.value);
        }, 300);
        $('#default-search').on('input', debouncedSearch);
        $(document).on('click', '#btnUser', function(event) {
            event.preventDefault();
            let url = $(this).attr('href').split('/').pop();
            let params = getQueryParams($(this).attr('href'));
            if ($(this).attr('href').split('/')[2] == 'lihat') {
                window.location = $(this).attr('href');
                return;
            }
            $(this).attr('href', '');
            getRequest(url, params);
        });


        function getQueryParams(url) {
            const params = {};
            const queryString = url.split("?")[1];

            if (queryString) {
                queryString.split("&").forEach(param => {
                    const [key, value] = param.split("=");
                    params[key] = value;
                });
            }

            return params;
        };


        function displayThumbnails(videoData) {
            videoData.forEach(video => {
                const thumbnailUrl = video.thumbnail;
                const videoTitle = video.title;

                const thumbnailElementString = `
<div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700" id="${video.id}">
    <a href="#">
        <img class="rounded-t-lg" src="${thumbnailUrl}" alt="" />
    </a>
    <div class="p-5">
        <a href="#">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">${videoTitle}</h5>
        </a>
        <a href="${video.url}" id="btnUser" class="inline-flex mt-5 items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            ${video.access}
             <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
        </a>
    </div>
</div>
`;

                const thumbnailElement = $(thumbnailElementString);
                thumbnailElement.data('video', video);
                videoContainer.append(thumbnailElement);
            });
        }
    </script>
@endsection
