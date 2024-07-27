@extends('menu.request-video.layouts.master')
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

        .pending,
        .approve {
            padding: 10px 10px;
            text-transform: uppercase;
            color: #fff;
            color: #fff;
        }

        .pending {
            background-color: #180161;
        }

        .approve {
            background-color: #FF8225;
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
                        @include('menu.request-video.components.pending')
                    </div>
                    @canany(['approve-video', 'cancel-video'])
                        <div class="flex w-full p-4 rounded-lg bg-gray-50 dark:bg-gray-800 flex-grow" id="styled-proses">
                            @include('menu.request-video.components.approve')
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

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            const canActionOrder = @json(auth()->user()->canAny(['edit-order', 'hapus-order']));
            const canApproveOrder = @json(auth()->user()->canAny(['approve-order', ' cancel-order']));
            var satuan_waktu = null;
            let tabel_approve;
            let tabel_pending = initializeTable('#tabel_pending',
                "{{ url('request/get_requestvideo?status=pending') }}");;

            $('#outstanding-styled-tab').on('click', function() {
                $('#styled-outstanding').css('margin-left', '0');
                $('#styled-proses').css('margin-left', '100%');
                $(this).addClass('active-tab');
                $(this).removeClass('hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300');
                $('#proses-styled-tab').removeClass('active-tab');
                $('#proses-styled-tab').addClass(
                    'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300');
            });

            $('#proses-styled-tab').on('click', function() {
                $('#styled-outstanding').css('margin-left', '-100%');
                $('#styled-proses').css('margin-left', '0');
                $(this).addClass('active-tab');
                $(this).removeClass('hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300');
                $('#outstanding-styled-tab').removeClass('active-tab');
                $('#outstanding-styled-tab').addClass(
                    'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300');
                if (!tabel_approve) {
                    tabel_approve = initializeTable('#tabel_approve',
                        "{{ url('request/get_requestvideo?status=approved') }}");
                }
            });

            function initializeTable(tableId, url) {
                return new DataTable(tableId, {
                    "scrollX": true,
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    "ordering": true,
                    "scrollCollapse": true,
                    "fixedColumns": {
                        start: 2
                    },
                    "ajax": {
                        "url": url,
                        "type": 'GET',
                        "data": {},
                        "dataSrc": function(json) {
                            return json.data;
                        }
                    },
                    "drawCallback": function(settings) {
                        $(this).DataTable().columns.adjust();
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
                            targets: [0, 1, 2, 3, 6, 7, 8, 9],
                            orderable: false,
                            className: "dark:text-gray-300"
                        },
                        {
                            targets: [4, 5],
                            orderable: true,
                            className: "dark:text-gray-300"
                        },
                        {
                            targets: canActionOrder && canApproveOrder ? [9, 10] : [9],
                            orderable: false,
                            className: "p-4 space-x-2 whitespace-nowrap dark:text-gray-300"
                        }
                    ]
                });
            }


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
            });

            $(document).on('click', '#approveModalBtn', function(event) {
                event.preventDefault();
                $('#modalApproveLabel').text('Approve Orders');
                $('#approveBtn').attr('href', '/request/approve/' + $(this).attr('href').split('/').pop());
                $('#approveModal').toggleClass('hidden').toggleClass('flex');
                $('#time_expired').removeClass('hidden').addClass('block');
                $('#modalApproveBody').text(
                    'Anda yakin ingin mengkonfirmasi request ini?Request ini akan diproses. Pastikan Anda telah memeriksa detail request dengan benar.Konfirmasi request akan mengirimkan notifikasi ke User.'
                );
            });

            $(document).on('click', '#cancelModalBtn', function(event) {
                event.preventDefault();
                $('#modalApproveLabel').text('Cancel Orders');
                $('#approveBtn').attr('href', '/request/cancel/' + $(this).attr('href').split('/').pop());
                $('#approveModal').toggleClass('hidden').toggleClass('flex');
                $('#time_expired').addClass('hidden').removeClass('block');
                $('#modalApproveBody').text(
                    ' Anda yakin ingin membatalkan pesanan ini?Pesanan ini akan dibatalkan. Pastikan Anda telah memeriksa detail pesanan dengan benar.Pembatalan pesanan akan mengirimkan notifikasi ke LO dan menghentikan proses pengiriman.'
                );
            });

            $(document).on('click', '#closeAprroveModalBtn, #declineBtn', function(event) {
                event.preventDefault();
                $('#approveModal').toggleClass('hidden').toggleClass('flex');
                $('#time_expired').remove('hidden').removeClass('block');
                $("input").removeClass(
                            'border-red-500').val('')
                $('.error-message-time').text('')
            });

            $(document).on('click', '#approveBtn', function(event) {
                event.preventDefault();
                let waktu = $('#waktu').val();
                let cekApprove = $('#time_expired').hasClass('block')
                let url = $(this).attr('href');
                if (waktu == '' && cekApprove) {
                    $(`input[id="waktu"]`).addClass(
                        'border-red-500')
                    $('.error-message-time').text('Masukan inputan waktu anda')
                    return
                }
                if (cekApprove) {
                    url = `${url}?waktu=${waktu}`
                }
                console.log(url);
                $('#ModalButtonTextApprove , #approve-loading-button').toggleClass('hidden');
                axios.get(url)
                    .then(response => {
                        Toast.fire({
                            icon: 'success',
                            title: response.data.message
                        })
                        $('#approveModal').toggleClass('hidden').toggleClass('flex');
                    })
                    .catch(error => {
                        Toast.fire({
                            icon: 'error',
                            title: error.response.data.message
                        })
                    })
                    .finally(() => {
                        $('#ModalButtonTextApprove , #approve-loading-button').toggleClass('hidden');
                        $('#time_expired').remove('hidden').remove('block');
                        $("input").removeClass(
                            'border-red-500').val('')
                        $('.error-message-time').text('')
                    })
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

            window.Echo.channel('laravel_database_requestvideo-channel')
                .listen('.requestvideo-create-event', (e) => {
                    const newData = e.message;
                    tabel_pending.row.add(newData, 0).draw(false);
                })
                .listen('.requestvideo-edit-event', (e) => {
                    const editData = e.message;
                    const rowIndex = tabel_pending.rows().data().toArray().findIndex(row => {
                        return row[1].includes(editData[1]);
                    });

                    if (rowIndex !== -1) {
                        tabel_pending.row(rowIndex).data(editData).draw(false);
                    }
                })
                .listen('.requestvideo-delete-event', (e) => {
                    const deleteData = e.message;
                    const rowIndex = tabel_pending.rows().data().toArray().findIndex(row => {
                        return row[1].includes(deleteData[1]);
                    });

                    if (rowIndex !== -1) {
                        tabel_pending.row(rowIndex).remove().draw(false);
                    }
                })
                .listen('.requestvideo-approve-event', (e) => {
                    const approveData = e.message;
                    if (tabel_pending && tabel_pending
                        .rows) {
                        const rowIndex = tabel_pending.rows().data().toArray().findIndex(row => {
                            return row && row[1] && row[1].includes(approveData[1]);
                        });

                        if (rowIndex !== -1) {
                            tabel_pending.row(rowIndex).remove().draw(false);

                            if (tabel_approve && tabel_approve
                                .rows) {
                                tabel_approve.row.add(approveData, 0).draw(true);
                            }
                        }
                    }
                })
                .listen('.requestvideo-cancel-event', (e) => {
                    const cancelData = e.message;
                    if (tabel_approve && tabel_approve.rows) {
                        const rowIndex = tabel_approve.rows().data().toArray().findIndex(row => {
                            return row && row[1] && row[1].includes(cancelData[1]);
                        });

                        if (rowIndex !== -1) {
                            tabel_approve.row(rowIndex).remove().draw(false);
                        }
                    }
                    tabel_pending.row.add(cancelData, 0).draw(true);
                })
        });
    </script>
@endpush
