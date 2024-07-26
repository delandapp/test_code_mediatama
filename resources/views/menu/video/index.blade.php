@extends('menu.video.layouts.master')

@section('content')
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            @include('menu.video.components.header-table')
            <table id="videoTable" class="display table-auto w-full" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Title</th>
                        <th>Video</th>
                        <th>Thumbnail</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
        @include('menu.video.components.modal')
    </div>
@endsection
@section('scripts')
    <script type="module">
        $(document).ready(function() {
            const $previewImage = $(".preview-image");
            const $previewVideo = $(".preview-video");
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
                        targets: [1],
                        orderable: true,
                        className: "dark:text-gray-300"
                    },
                    {
                        targets: [2],
                        orderable: true,
                        className: "dark:text-gray-300"
                    },
                    {
                        targets: [3],
                        orderable: true,
                        className: "dark:text-gray-300"
                    },
                    {
                        targets: [4],
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
                if (fileVideo == undefined) {
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
                }
                const formData = {};
                $(this).serializeArray().forEach(function(item) {
                    formData[item.name] = item.value;
                });
                if (file == undefined && $previewImage.hasClass('flex')) {
                    formData.thumbnailImage = "keep";
                } else if ($previewImage.hasClass('hidden')) {
                    formData.thumbnailImage = "hapus";
                } else {
                    formData.thumbnailImage = file;
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
                    $('#Modal').toggleClass('hidden').toggleClass('flex');
                    $('#form input').removeClass('border-red-500');
                    $('#form .error-message').text('');
                }).catch(error => {
                    const errors = error.response.data.errors;
                    for (const field in errors) {
                        $(`input[name="${field}"] , select[name="${field}"] , textarea[name="${field}"]`)
                            .addClass(
                                'border-red-500').next('.error-message').text(errors[field][
                                0
                            ]);
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
                $('#form').attr('href', '/user/edit/' + $(this).attr('href').split('/').pop());
                $('#modalLabel').text('Edit User');
                $('#loadingModal').toggleClass('hidden').toggleClass('flex');
                var userId = $(this).attr('href').split('/').pop();
                axios.get('/user/' + userId)
                    .then(response => {
                        var userData = response.data.data;
                        $('#name').val(userData.name);
                        $('#email').val(userData.email);
                        $('#Modal').toggleClass('hidden').toggleClass('flex');
                        $('#loadingModal').toggleClass('hidden').toggleClass('flex');
                    })
                    .catch(error => {
                        console.error(error);
                        $('#loadingModal').toggleClass('hidden').toggleClass('flex');
                    });
            });

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

            window.Echo.channel('laravel_database_user-create-channel')
                .listen('.user-create-event', (e) => {
                    table.draw();
                });
        });
    </script>
@endsection
