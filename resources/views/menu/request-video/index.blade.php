@extends('menu.request-video.layouts.master');
@push('css')
    <style>
        .drop-area.active {
            border-color: #2563eb;
        }

        div.dt-container {
            width: 100% !important;
        }

        th,
        td {
            white-space: nowrap;
        }

        th.custom,
        td.custom {
            white-space: normal !important;
        }

        div.dataTables_wrapper {
            margin: 0 auto;
        }

        .active-tab {
            border-bottom-color: #9333ea;
            color: #9333ea;
        }

        .dark .active-tab {
            border-bottom-color: #a855f7;
            color: #a855f7
        }

        #tab-content>div {
            transition: margin-left 0.5s ease-in-out;
        }
    </style>
@endpush
@section('content')
    <div class="p-5">
        <div class="relative shadow-md sm:rounded-lg">
            @include('menu.request-video.components.header-table')
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center w-full" id="default-styled-tab">
                    <li class="flex-shrink-1 flex-grow">
                        <button class="inline-block w-full p-4 border-b-2 rounded-t-lg active-tab" id="outstanding-styled-tab"
                            type="button">Request User</button>
                    </li>
                    @canany(['approve-video', 'cancel-video'])
                        <li class="flex-shrink-1 flex-grow">
                            <button class="inline-block p-4 w-full border-b-2 rounded-t-lg" id="proses-styled-tab"
                                type="button">Approve Request User</button>
                        </li>
                    @endcanany
                </ul>
            </div>
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" id="tab-content">
                    <div class="flex w-full p-4 rounded-lg bg-gray-50 dark:bg-gray-800 flex-grow" id="styled-outstanding">
                        @include('menu.request-video.components.approve')
                    </div>
                    @canany(['approve-video', 'cancel-video'])
                        <div class="flex w-full p-4 rounded-lg bg-gray-50 dark:bg-gray-800 flex-grow" id="styled-proses">
                            @include('menu.request-video.components.pending')
                        </div>
                    @endcanany
                </div>
            </div>
        </div>
        <div id="loadingModal" tabindex="1" data-modal-backdrop="static"
            class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="modal-backdrop fixed inset-0 bg-[#000000] opacity-75"></div>
            <div class="relative w-full max-w-2xl max-h-full flex justify-center">
                <div class="items-center justify-center flex" id="loading-spinner">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-[#FF7F3E]"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        @include('menu.request-video.components.modal')
        @include('menu.request-video.components.modal-approve')
    </div>
@endsection

@section('scripts')
    <script type="module">
        $(document).ready(function() {
            let table = new DataTable('#videoTable', {
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ordering": true,
                "ajax": {
                    "url": "{{ url('video/get_video') }}",
                    "type": 'GET',
                    "data": {},
                    "dataSrc": function(json) {
                        return json.data;
                    }
                },
                "columnDefs": [{
                        targets: [0],
                        searchable: false,
                        orderable: false,
                        className: 'dark:text-gray-300',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        targets: [1, 3, 4],
                        orderable: false,
                        className: "dark:text-gray-300"
                    },
                    {
                        targets: [2],
                        orderable: true,
                        className: "dark:text-gray-300"
                    },
                    {
                        targets: [5],
                        orderable: false,
                        className: "dark:text-gray-300 p-4 space-x-2 whitespace-nowrap"
                    }
                ]
            });

            const debounce = (func, delay) => {
                let timeoutId;
                return function(...args) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => func.apply(this, args), delay);
                };
            };

            const debouncedSearch = debounce(function() {
                table.search(this.value).draw();
            }, 300);


            $('#video-search').on('input', debouncedSearch);

            $(document).on('submit', '#form', function(event) {
                event.preventDefault();
                const file = $('#image').prop('files')[0];
                const fileVideo = $('#video').prop('files')[0];
                const formData = {};
                $(this).serializeArray().forEach(function(item) {
                    formData[item.name] = item.value;
                });
                if (file == undefined && $previewImage.hasClass('flex')) {
                    formData.thumbnailImage = "keep";
                } else if ($previewImage.hasClass('hidden')) {
                    formData.thumbnailImage = "hapus";
                } else {
                    formData.thumbnail = file;
                }
                if (fileVideo == undefined && $previewVideo.hasClass('flex')) {
                    formData.videoMateri = "keep";
                } else if ($previewVideo.hasClass('hidden')) {
                    $(`input[name="video"]`)
                        .next(".error-message")
                        .text(
                            "File video wajib di isi"
                        )
                        .addClass("flex")
                        .removeClass("hidden");
                    $(".drop-area-video")
                        .removeClass("active border-gray-300 border-green-500")
                        .addClass("border-red-500");
                    return
                } else {
                    formData.video = fileVideo;
                }

                let url = $(this).attr('href');
                $('#ModalButtonText, #loading-button').toggleClass('hidden');

                axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    table.draw();
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message
                    })
                    $(this)[0].reset();
                    removePreview();
                    removePreviewVideo();
                    $('#Modal').toggleClass('hidden').toggleClass('flex');
                    $('#form input').removeClass('border-red-500');
                    $('#form .error-message').text('');
                }).catch(error => {
                    const errors = error.response.data.errors;
                    for (const field in errors) {
                        console.log(field);
                        if (field == 'video' || field == 'thumbnail') {
                            $(`input[name=${field}]`)
                                .next(".error-message")
                                .text(
                                    errors[field][0]
                                )
                                .addClass("flex")
                                .removeClass("hidden");
                            $(field == 'thumbnail' ? ".drop-area" : ".drop-area-video")
                                .removeClass("active border-gray-300 border-green-500")
                                .addClass("border-red-500");
                        } else {
                            $(`input[name="${field}"]`)
                                .addClass(
                                    'border-red-500').next('.error-message').text(errors[field][
                                    0
                                ]);
                        }
                    }

                    Toast.fire({
                        icon: 'error',
                        title: error.response.data.message
                    });
                }).finally(() => {
                    $('#ModalButtonText , #loading-button').toggleClass('hidden');
                })

            })

            $(document).on('click', '#editModalBtn', function(event) {
                event.preventDefault();
                $('#form').attr('href', '/video/edit/' + $(this).attr('href').split('/').pop());
                $('#modalLabel').text('Edit Materi');
                $('#loadingModal').toggleClass('hidden').toggleClass('flex');
                var videoId = $(this).attr('href').split('/').pop();
                editVideo(videoId);
            });

            $(document).on('click', '#deleteModalBtn', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: "Apakah Kamu yakin?",
                    text: "Menghapus data ini!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let videoId = $(this).attr('href').split('/').pop();
                        axios.delete('/video/' + videoId).then(response => {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Data berhasil di delete.",
                                icon: "success"
                            });
                            table.draw(true);
                        }).catch(error => {
                            Toast.fire({
                                icon: 'error',
                                title: error.response.data.message
                            })
                        })
                    }
                });
            })

            async function editVideo(videoId) {
                try {
                    const response = await axios.get('/video/' + videoId);
                    videoData = response.data.data;
                    var videoData = response.data.data;
                    let urlImage = "{{ Storage::disk('materi')->url('') }}" + videoData
                        .thumbnail;
                    let urlVideo = "{{ Storage::disk('materi')->url('') }}" + videoData
                        .video;
                    $('#title').val(videoData.title);
                    $imageLabel.addClass('hidden').removeClass('flex');
                    $(`input[name="thumbnail"]`).next('.error-message').text('').addClass('hidden');
                    $('.drop-area').removeClass('border-red-500').addClass('border-green-500');
                    $previewImage.css('background-image',
                        `url("${urlImage}")`
                    );
                    $previewImage.removeClass('hidden').addClass('flex');
                    $dropArea.removeClass('active border-green-500').removeClass('border-red-500').addClass(
                        'border-green-500');
                    $previewContainer.removeClass('hidden');
                    $previewContainer.addClass('flex');
                    $imageLabelVideo.addClass('hidden').removeClass('flex');
                    $(`input[name="thumbnail"]`).next('.error-message').text('').addClass('hidden');
                    $previewVideo.attr('src',
                        urlVideo
                    );
                    $previewVideo.removeClass('hidden').addClass('flex');
                    $dropAreaVideo.removeClass('active').removeClass('border-red-500').addClass(
                        'border-green-500');
                    $previewContainerVideo.removeClass('hidden');
                    $previewContainerVideo.addClass('flex');
                } catch (error) {
                    console.error(error);
                } finally {
                    $('#Modal').toggleClass('hidden').toggleClass('flex');
                    $('#loadingModal').toggleClass('hidden').toggleClass('flex');
                }
            }

            $(document).on('click', '#tambahModalBtn', function(event) {
                event.preventDefault();
                $('#form').attr('href', '/video/tambah');
                $('#modalLabel').text('Tambah Materi');
                $('#Modal').toggleClass('hidden').toggleClass('flex');
            });

            $(document).on('click', '#closeuserModal', function(event) {
                $('#Modal').toggleClass('hidden').toggleClass('flex');
                $('#form')[0].reset();
                $('#form input').removeClass('border-red-500');
                $('#form .error-message').text('');
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

            window.Echo.channel('laravel_database_materi-channel')
                .listen('.materi-create-event', (e) => {
                    const newData = e.message;
                    table.row.add(newData, 0).draw(false);
                })
                .listen('.materi-edit-event', (e) => {
                    const editData = e.message;
                    const rowIndex = table
                        .rows()
                        .data()
                        .toArray()
                        .findIndex(row => row[1] === editData[1]);

                    console.log(rowIndex);

                    if (rowIndex !== -1) {
                        table.row(rowIndex).data(editData).draw(
                            false);
                    } else {
                        console.error("Row not found!");
                    }
                })
                .listen('.materi-delete-event', (e) => {
                    const deleteData = e.message;
                    const rowIndex = table
                        .rows()
                        .data()
                        .toArray()
                        .findIndex(row => row[1] === deleteData[1]);

                    console.log(rowIndex);

                    if (rowIndex !== -1) {
                        table.row(rowIndex).remove().draw(false);
                    } else {
                        console.error("Row not found!");
                    }
                });

            const $dropAreaVideo = $(".drop-area-video");
            const $fileInputVideo = $("#video");
            const $imageLabelVideo = $("#video-label");
            const $previewContainerVideo = $(".preview-video-container");
            const $previewVideo = $(".preview-video");
            const $closeButtonVideo = $(".close-button-video");
            const $fileNameVideo = $(".file-name-video");
            const allowedTypesVideo = ["video/mp4", "video/webm", "video/ogg"];
            const maxSizeVideo = 20 * 1024 * 1024; // 20 MB

            $dropAreaVideo.on("dragover", (event) => {
                event.preventDefault();
                $dropAreaVideo.addClass("active");
            });

            $dropAreaVideo.on("dragleave", () => {
                $dropAreaVideo.removeClass("active");
            });

            $dropAreaVideo.on("drop", (event) => {
                event.preventDefault();
                const file = event.originalEvent.dataTransfer.files[0];
                showPreviewVideo(file);
                showFileNameVideo(file);
            });

            $fileInputVideo.on("change", () => {
                const file = $fileInputVideo[0].files[0];
                showPreviewVideo(file);
                showFileNameVideo(file);
            });

            $closeButtonVideo.on("click", (event) => {
                event.preventDefault();
                removePreviewVideo();
            });

            function removePreviewVideo() {
                $fileInputVideo.val("");
                $previewVideo.find("source").attr("src", "");
                $previewVideo[0].load();
                $fileNameVideo.text("");
                $previewVideo.addClass("hidden");
                $previewContainerVideo.addClass("hidden");
                $previewVideo.removeClass("flex");
                $dropAreaVideo
                    .removeClass("border-green-500 border-red-500")
                    .addClass("border-gray-300");
                $imageLabelVideo.removeClass("hidden").addClass("flex");
            }

            function showPreviewVideo(file) {
                if (!allowedTypesVideo.includes(file.type) || file.size > maxSizeVideo) {
                    $(`input[name="video"]`)
                        .next(".error-message")
                        .text(
                            "File harus berupa berformat MP4, WEBP atau OGG dan ukuran maksimal 20 MB"
                        )
                        .addClass("flex")
                        .removeClass("hidden");
                    $(".drop-area-video")
                        .removeClass("active border-gray-300 border-green-500")
                        .addClass("border-red-500");
                    $fileInputVideo.val("");
                    $previewVideo.removeClass("flex").addClass("hidden");
                    $previewContainerVideo.addClass("hidden");
                    $previewVideo.find("source").attr("src", "");
                    $imageLabelVideo.addClass("flex").removeClass("hidden");
                    return;
                }

                $(`input[name="video"]`)
                    .next(".error-message")
                    .text("")
                    .addClass("hidden");
                $(".drop-area-video")
                    .removeClass("border-red-500")
                    .addClass("border-green-500");

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    $imageLabelVideo.addClass("hidden").removeClass("flex");
                    const videoElement = $previewVideo[0];
                    videoElement.src = reader.result;
                    videoElement.load();
                    video
                    $previewVideo.removeClass("hidden");
                    $dropAreaVideo.removeClass("active");
                    $previewContainerVideo.removeClass("hidden");
                    $previewContainerVideo.addClass("flex");
                };
            }

            function showFileNameVideo(file) {
                $fileNameVideo.text(file.name);
                $fileNameVideo.show();
            }

            const $dropArea = $(".drop-area");
            const $fileInput = $("#image");
            const $imageLabel = $("#image-label");
            const $previewContainer = $(".preview-container");
            const $previewImage = $(".preview-image");
            const $closeButton = $(".close-button");
            const $fileName = $(".file-name");
            const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            const maxSize = 10 * 1024 * 1024;

            $dropArea.on("dragover", (event) => {
                event.preventDefault();
                $dropArea.addClass("active");
            });

            $dropArea.on("dragleave", () => {
                $dropArea.removeClass("active");
            });

            $dropArea.on("drop", (event) => {
                event.preventDefault();
                const file = event.originalEvent.dataTransfer.files[0];
                showPreview(file);
                showFileName(file);
            });

            $fileInput.on("change", () => {
                const file = $fileInput[0].files[0];
                showPreview(file);
                showFileName(file);
            });

            $closeButton.on("click", (event) => {
                event.preventDefault();
                removePreview();
            });

            function removePreview() {
                $fileInput.val("");
                $previewImage.css("background-image", "");
                $fileName.text("");
                $previewImage.addClass("hidden");
                $previewContainer.addClass("hidden");
                $previewImage.removeClass("flex");
                $dropArea
                    .removeClass("border-green-500 border-red-500")
                    .addClass("border-gray-300");
                $imageLabel.removeClass("hidden").addClass("flex");
            }

            function showPreview(file) {
                if (!allowedTypes.includes(file.type) || file.size > maxSize) {
                    $(`input[name="thumbnail"]`)
                        .next(".error-message")
                        .text(
                            "File harus berupa berformat JPEG, PNG, atau GIF dan ukuran maksimal 10 MB"
                        )
                        .addClass("flex")
                        .removeClass("hidden");
                    $(".drop-area")
                        .removeClass("active border-gray-300 border-green-500")
                        .addClass("border-red-500");
                    $fileInput.val("");
                    $previewImage.removeClass("flex").addClass("hidden");
                    $previewContainer.addClass("hidden");
                    $previewImage.css("background-image", "");
                    $imageLabel.addClass("flex").removeClass("hidden");
                    return;
                }

                if (file.type.startsWith("image/")) {
                    $(`input[name="thumbnail"]`)
                        .next(".error-message")
                        .text("")
                        .addClass("hidden");
                    $(".drop-area")
                        .removeClass("border-red-500")
                        .addClass("border-green-500");
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = () => {
                        $imageLabel.addClass("hidden").removeClass("flex");
                        $previewImage.css("background-image", `url(${reader.result})`);
                        $previewImage.removeClass("hidden");
                        $dropArea.removeClass("active");
                        $previewContainer.removeClass("hidden");
                        $previewContainer.addClass("flex");
                    };
                }
            }

            function showFileName(file) {
                $fileName.text(file.name);
                $fileName.show();
            }
        });
    </script>
@endsection
