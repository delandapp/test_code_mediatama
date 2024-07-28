@extends('menu.user.layouts.master')
@section('content')
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            @include('menu.user.components.header-lihat')
            <div class="p-5">
                @include('menu.user.components.skelaton-video')

                <div class="hidden gap-1" id="videoContainer">
                    <div class="col-span-12">
                        <video class="w-full h-96 bg-cover bg-center rounded-md" src="" controls id="video">
                            <source src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="col-span-12 flex justify-between mt-5 items-center">
                        <div id="timerContainer" class="text-2xl font-semibold text-gray-700">Waktu Tersisa: <span
                                id="timer"></span></div>
                        <div class="flex gap-3 items-center">
                            <button id="btnSelesai"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900"><i
                                    class="fa-solid fa-share-from-square mr-3"></i> Selesai</button>
                            <div class="py-4">
                                <a class="inline-flex items-center like-button" href="#" data-id="1">
                                    <i class="fa-regular fa-heart mr-1 text-2xl" id="icon"></i>
                                    <span class="text-lg font-bold like-count">0</span>
                                </a>
                            </div>
                            <div class="py-4">
                                <a class="inline-flex items-center dislike-button" href="#" data-id="1">
                                    <span class="mr-1">
                                        <i class="fa-regular fa-face-tired mr-1 text-2xl" id="icon"></i>
                                    </span>
                                    <span class="text-lg font-bold dislike-count">0</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 flex justify-between mt-5 flex-col">
                        <p class="font-bold text-gray-500 dark:text-gray-400">Description Video</p>
                        <p class="text-gray-500 dark:text-gray-400" id="description"></p>
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
        $(document).ready(function() {
            const userId = {{ auth()->user() ? auth()->user()->id : null }}
            const videoContainer = $('#komentarContainer');
            getData();
            async function getData() {
                setLoadingState();
                let url = '/user-video/show/{{ $id }}';
                let videoId = {{ $id }}
                axios.get(url)
                    .then(response => {
                        const videoData = response.data.data;
                        setDataState(videoData);
                    })
                    .catch(error => {
                        console.log(error)
                        error.response.status === 404 ? window.location.href = '/user-video' : console.log(
                            error)
                    });
                axios.get(`/komentar/get_data/${videoId}`).then(response => {
                    setKomentarState(response.data.data);
                }).catch(error => {
                    console.log(error)
                });
                axios.get(`/user-video/like/get_data/${videoId}`).then(response => {
                    setLikeState(response.data.data);
                }).catch(error => {
                    console.log(error)
                });
                axios.get(`/user-video/dislike/get_data/${videoId}`).then(response => {
                    setDislikeState(response.data.data);
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
                if (timeRemaining > 0) {
                    startTimer(timeRemaining);
                } else {
                    window.location.href = '/user-video';
                    axios.post('/user-video/clear-cache/' + '{{ $id }}')
                }
            }

            function setKomentarState(komentar) {
                $('#loadingKomentar').addClass('hidden').removeClass('flex');
                $('#komentarContainer').empty();
                $('#komentarContainer').removeClass('hidden').addClass('grid');
                displayKomentar(komentar);
            }

            function setLikeState(like) {
                $('.like-count').text(like.total_like);
                if (like.liked != 1 && like.dislaked == 1) {
                    $('.like-button').css('pointer-events', 'none').addClass('disabled');
                    $('.like-button #icon').attr('data-prefix', 'far');
                } else if (like.liked == 1 && like.dislaked != 1) {
                    $('.like-button').css('pointer-events', 'auto').addClass('text-red-600');
                    $('.like-button #icon').attr('data-prefix', 'fas');
                } else {
                    $('.like-button').css('pointer-events', 'auto').removeClass('text-red-600');
                    $('.like-button #icon').attr('data-prefix', 'far');
                }
            }

            function setDislikeState(dislike) {
                $('.dislike-count').text(dislike.total_dislike);
                if (dislike.disliked != 1 && dislike.liked == 1) {
                    $('.dislike-button').css('pointer-events', 'none').addClass('disabled');
                    $('.dislike-button #icon').attr('data-prefix', 'far');
                } else if (dislike.disliked == 1 && dislike.liked != 1) {
                    $('.dislike-button').css('pointer-events', 'auto').addClass('text-red-600');
                    $('.dislike-button #icon').attr('data-prefix', 'fas');
                } else {
                    $('.dislike-button').css('pointer-events', 'auto').removeClass('text-red-600');
                    $('.dislike-button #icon').attr('data-prefix', 'far');
                }
            }

            function displayKomentar(komentar) {
                komentar.forEach(komentar => {
                    const komentarUserId = komentar.userId;
                    const komentarElementString = buatElemenKomentar(komentar);
                    const komentarElement = $(komentarElementString);
                    const buttonElement = $(buatTombolDelete(komentar.id));
                    komentarElement.data('komentar', komentar);
                    if (userId == komentarUserId) {
                        komentarElement.append(
                            buttonElement
                        )
                        komentarElement.addClass(
                            'komentar hover:cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 ')
                    }
                    komentarElement.appendTo(komentarContainer);
                });
            };

            function buatElemenKomentar(komentar) {
                const komentarUserName = komentar.name;
                const komentarUserEmail = komentar.email;
                const komentarText = komentar.komentar;
                return `
<div class="flex w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700" id="komentar-${komentar.id}">
<figure class="w-full px-4 py-2 rounded overflow-hidden">
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
        <p class="text-2xl font-semibold text-gray-900 dark:text-white komentar-text">"${komentarText}"</p>
    </blockquote>
    <figcaption class="flex items-center mt-6 space-x-3 rtl:space-x-reverse">
        <img class="w-6 h-6 rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png" alt="profile picture">
        <div class="flex items-center divide-x-2 rtl:divide-x-reverse divide-gray-300 dark:divide-gray-700">
            <cite class="pe-3 font-medium text-gray-900 dark:text-white">${komentarUserName}</cite>
            <cite class="ps-3 text-sm text-gray-500 dark:text-gray-400">${komentarUserEmail}</cite>
        </div>
    </figcaption>
</figure>
</div>
`;
            }

            function buatTombolDelete(komentarId) {
                return `
<button class="z-50 inline-flex items-center justify-center w-20 px-4 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900" href="/komentar/delete/${komentarId}" id="btnKomentarDelete">
<i class="fa-solid fa-trash text-white"></i>
</button>`
            }

            $('#btnKomentar').on('click', function(event) {
                event.preventDefault();
                var videoId = {{ $id }};
                var komentarText = $('#komentar').val();
                if (komentarText == '') {
                    Toast.fire({
                        icon: 'error',
                        title: 'Komentar Tidak Boleh Kosong!'
                    });
                    return;
                }
                addKomentar(videoId, komentarText);
                $('#komentar').val('');
            });

            $('#btnSelesai').on('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: "Apakah Kamu yakin?",
                    text: "Menghentikan menonton video sekarang!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, aku menghentikan!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/user-video/clear-cache/' + '{{ $id }}');
                        Swal.fire({
                            title: "Video Berhasil Di Hentikan!",
                            text: "Video yang kamu request akan di hapus dari sistem!",
                            icon: "success",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/user-video';
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#btnKomentarDelete', function(event) {
                event.preventDefault();
                event.stopPropagation();
                var komentarId = $(this).attr('href').split('/').pop();
                Swal.fire({
                    title: "Apakah Kamu yakin?",
                    text: "Menghapus komentar di video sekarang!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, aku menghapus komentar!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteKomentar(komentarId);
                        $(`#komentar-${komentarId}`).remove();
                    }
                });
            })

            $(document).on('click', '.komentar', function(event) {
                const komentarId = $(this).attr('id').split('-')[1];
                showSweetAlertEditKomentar(komentarId);
            });

            $(document).on('click', '.like-button', function(event) {
                event.preventDefault();
                const button = $(this);
                const icon = button.find('#icon');
                let likeCount = parseInt(button.find('.like-count').text());
                if (icon.data('prefix') === 'fas') {
                    icon.attr('data-prefix', 'far');
                    button.removeClass('text-red-600');
                    button.find('.like-count').text(likeCount > 0 ? likeCount - 1 : 0);
                    $('.dislike-button').removeClass('disabled').css('pointer-events', 'auto');
                } else {
                    icon.attr('data-prefix', 'fas');;
                    button.addClass('text-red-600');
                    button.find('.like-count').text(likeCount + 1);
                    $('.dislike-button').addClass('disabled').css('pointer-events', 'none');
                }
                const videoId = {{ $id }};
                const userId = {{ auth()->user() ? auth()->user()->id : null }}
                handleLike(videoId, userId);
            });

            $(document).on('click', '.dislike-button', function(event) {
                event.preventDefault();
                const button = $(this);
                const id = button.data('id');
                const icon = button.find('#icon');
                let dislikeCount = parseInt(button.find('.dislike-count').text());
                if (icon.data('prefix') === 'fas') {
                    icon.attr('data-prefix', 'far');
                    button.removeClass('text-red-600');
                    button.find('.dislike-count').text(dislikeCount > 0 ? dislikeCount - 1 :
                        0);
                    $('.like-button').removeClass('disabled').css('pointer-events', 'auto');
                } else {
                    icon.attr('data-prefix', 'fas');;
                    button.addClass('text-red-600');
                    button.find('.dislike-count').text(dislikeCount + 1);
                    $('.like-button').addClass('disabled').css('pointer-events', 'none');
                }
                button.toggleClass('disliked');
                const videoId = {{ $id }};
                const userId = {{ auth()->user() ? auth()->user()->id : null }}
                handleDislike(videoId, userId);
            });

            async function showSweetAlertEditKomentar(komentarId) {
                try {
                    const response = await axios.get(`/komentar/show/${komentarId}`);
                    const komentarText = response.data
                        .data.komentar;
                    const {
                        value: text
                    } = await Swal.fire({
                        input: "textarea",
                        inputLabel: "Edit Komentar",
                        inputValue: komentarText,
                        inputPlaceholder: "Type your message here...",
                        inputAttributes: {
                            "aria-label": "Type your message here"
                        },
                        showCancelButton: true
                    });
                    if (text) {
                        await axios.post(`/komentar/update/${komentarId}`, {
                            komentar: text
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Komentar berhasil diperbarui'
                        });
                        $(`#komentar-${komentarId} .komentar-text`).text(text);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memperbarui komentar'
                    });
                }
            }

            async function deleteKomentar(komentarId) {
                axios.post('/komentar/delete/' + komentarId).then(function(response) {
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
            };

            async function handleLike(videoId, userId) {
                try {
                    await axios.post(`/user-video/like/`, {
                        'materi_id': videoId,
                        'user_id': userId
                    }).then(function(response) {
                        const status = response.data.data.status;
                        Toast.fire({
                            icon: status ? 'success' : 'error',
                            title: status ? 'Video Berhasil Di Like' : 'Video Tidak Dilike',
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

            async function handleDislike(videoId, userId) {
                try {
                    await axios.post(`/user-video/dislike/`, {
                        'materi_id': videoId,
                        'user_id': userId
                    }).then(function(response) {
                        const status = response.data.data.status;
                        Toast.fire({
                            icon: status ? 'success' : 'error',
                            title: status ? 'Video Berhasil Di Dislike' :
                                'Video Tidak Didislike',
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


            window.Echo.channel('laravel_database_approve-channel')
                .listen('.doneadmin-user-event', (e) => {
                    if (userId == e.message) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Video anda di hentikan oleh admin!",
                            footer: 'Why do I have this issue?',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false

                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/user-video';
                            }
                        });
                    }
                });
            window.Echo.channel('laravel_database_komentar-channel')
                .listen('.komentar-create-event', (e) => {
                    const newKomentar = e.message;
                    const komentarElementString = buatElemenKomentar(newKomentar);
                    const buttonDelete = buatTombolDelete(newKomentar.id);
                    const komentarElement = $(komentarElementString);
                    if (userId == newKomentar.userId) {
                        komentarElement.append(buttonDelete);
                        komentarElement.addClass(
                            'komentar hover:cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 ')
                    }
                    $('#komentarContainer').prepend(komentarElement);
                })
                .listen('.komentar-delete-event', (e) => {
                    const deleteKomentar = e.message;
                    const komentarElement = $('#komentar-' + deleteKomentar.id);
                    komentarElement.remove();
                })

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
        })
    </script>
@endsection
