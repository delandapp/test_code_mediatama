@extends('menu.user.layouts.master')

@section('content')
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            @include('menu.user.components.header-video')
            <div class="p-5">
                @include('menu.user.components.skelaton-video')
                @include('menu.user.components.404-video')
                <div id="videoContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

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

        async function filterVideos(query) {
            setLoadingState();
            videoContainer.empty();
            try {
                const response = await axios.get('user-video/get_filter/' + query);
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

        $(document).on('click', '.simpan-button', function(event) {
            event.preventDefault();
            const button = $(this);
            if (button.attr('fill') === 'none') {
                button.attr('fill', 'currentColor');
            } else {
                button.attr('fill', 'none');
            }
            console.log(button.attr('fill'));
            const videoId = button.data('video-id');
            const userId = {{ auth()->user() ? auth()->user()->id : null }}
            handleLike(videoId, userId);
        });

        $('#filterAll').on('click', function() {
            searchVideos('');
        });

        $('#filterLike').on('click', function() {
            filterVideos('like');
        });

        $('#filterDislike').on('click', function() {
            filterVideos('dislike');
        });

        $('#filterDisimpan').on('click', function() {
            filterVideos('save');
        });

        async function handleLike(videoId, userId) {
            try {
                await axios.post(`/user-video/simpan/`, {
                    'materi_id': videoId,
                    'user_id': userId
                }).then(function(response) {
                    const status = response.data.data.status;
                    Toast.fire({
                        icon: status ? 'success' : 'error',
                        title: status ? 'Video Disimpan' : 'Video Tidak Disimpan',
                    });
                }).catch(function(error) {
                    Toast.fire({
                        icon: 'error',
                        title: response.data.message
                    });
                });
            } catch (error) {
                console.error('Error liking:', error);
            }
        }


        function displayThumbnails(videoData) {
            videoData.forEach(video => {
                const thumbnailUrl = video.thumbnail;
                const videoTitle = video.title;
                const videoLike = video.jumlah_like;
                const videoDislike = video.jumlah_dislike;
                const videoKomentar = video.jumlah_komentar;
                const videoSimpan = video.simpan;

                const thumbnailElementString = `
<div class="p-4 bg-white border border-gray-200 overflow-hidden rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 relative" id="${video.id}">
    <span
    class="absolute inset-x-0 bottom-0 h-2 bg-gradient-to-r from-green-300 via-blue-500 to-purple-600"
  ></span>
    <a href="#">
        <img class="h-56 rounded-md object-cover" src="${thumbnailUrl}" alt="" />
    </a>
    <div class="mt-6 flex items-center gap-8 text-xs">
      <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
        <svg class="w-6 h-6 text-indigo-700 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M4 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h1v2a1 1 0 0 0 1.707.707L9.414 13H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4Z" clip-rule="evenodd"/>
  <path fill-rule="evenodd" d="M8.023 17.215c.033-.03.066-.062.098-.094L10.243 15H15a3 3 0 0 0 3-3V8h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1v2a1 1 0 0 1-1.707.707L14.586 18H9a1 1 0 0 1-.977-.785Z" clip-rule="evenodd"/>
</svg>


        <div class="mt-1.5 sm:mt-0">
          <p class="text-gray-500">Komentar</p>

          <p class="font-medium">${videoKomentar} komentar</p>
        </div>
      </div>

      <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
        <svg class="w-6 h-6 text-indigo-700 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M8.97 14.316H5.004c-.322 0-.64-.08-.925-.232a2.022 2.022 0 0 1-.717-.645 2.108 2.108 0 0 1-.242-1.883l2.36-7.201C5.769 3.54 5.96 3 7.365 3c2.072 0 4.276.678 6.156 1.256.473.145.925.284 1.35.404h.114v9.862a25.485 25.485 0 0 0-4.238 5.514c-.197.376-.516.67-.901.83a1.74 1.74 0 0 1-1.21.048 1.79 1.79 0 0 1-.96-.757 1.867 1.867 0 0 1-.269-1.211l1.562-4.63ZM19.822 14H17V6a2 2 0 1 1 4 0v6.823c0 .65-.527 1.177-1.177 1.177Z" clip-rule="evenodd"/>
</svg>


        <div class="mt-1.5 sm:mt-0">
          <p class="text-gray-500">Dislike</p>

          <p class="font-medium">${videoDislike} dislike</p>
        </div>
      </div>

      <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
        <svg class="w-6 h-6 text-indigo-700 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z"/>
</svg>


        <div class="mt-1.5 sm:mt-0">
          <p class="text-gray-500">Like</p>

          <p class="font-medium">${videoLike} likes</p>
        </div>
      </div>
    </div>
    <div class="px-1 py-5">
        <a href="#">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">${videoTitle}</h5>
        </a>
        <div class="flex items-center justify-between mt-8">
            <a href="${video.url}" id="btnUser" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                ${video.access}
                 <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                </svg>
            </a>
            <svg class="w-6 h-6 text-gray-800 dark:text-white simpan-button" data-video-id="${video.id}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="${video.simpan ? 'currentColor' : 'none'}" viewBox="0 0 24 24" id="icon">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 21-5-4-5 4V3.889a.92.92 0 0 1 .244-.629.808.808 0 0 1 .59-.26h8.333a.81.81 0 0 1 .589.26.92.92 0 0 1 .244.63V21Z"/>
</svg>

        </div>
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
