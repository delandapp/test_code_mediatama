@extends('menu.auth.layouts.master')

@section('content')
    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div>
                    <img src="https://mediatamasolo.com/assets/images/logos/main.jpg" alt="logo" class="w-mx-auto">
                </div>
                <div class="mt-12 flex flex-col items-center">
                    <div class="w-full flex-1 mt-8">

                        <form class="w-full" id="loginForm">
                            <label for="email" class="text-sm font-bold text-gray-700 px-2">Email</label>
                            <input
                                class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-2 mb-2"
                                name="email" type="email" required placeholder="Email" />
                            <div class="error-message text-red-500 text-[14px] mb-1 ml-4"></div>
                            <label for="password" class="text-sm font-bold text-gray-700 px-2">Password</label>
                            <input
                                class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-2 mb-2"
                                type="password" required placeholder="Password" name="password" />

                            <div class="error-message text-red-500 text-[14px] mb-1 ml-4"></div>
                            <button
                                class="mt-7 tracking-wide font-semibold bg-green-400 text-white-500 w-full py-4 rounded-lg hover:bg-green-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <p id="login-btn">Login</p>

                                <div class="hidden items-center justify-center" id="loading-spinner">
                                    <div role="status">
                                        <svg aria-hidden="true"
                                            class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-[#FF7F3E]"
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

                            </button>
                            <p class="mt-6 text-xs text-gray-600 text-center">
                                I agree to abide by Cartesian Kinetics
                                <a href="#" class="border-b border-gray-500 border-dotted">
                                    Terms of Service
                                </a>
                                and its
                                <a href="#" class="border-b border-gray-500 border-dotted">
                                    Privacy Policy
                                </a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="flex-1 bg-green-100 text-center hidden lg:flex">
                <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat">
                    <img src="{{ asset('assets/image/ilustrasi-login.png') }}" class="m-auto" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        $(document).ready(function() {
            $('#loginForm').submit(function(event) {
                event.preventDefault();
                $('#login-btn, #loading-spinner').toggleClass('hidden');

                const formData = $(this).serialize();
                $('.error-message').empty(); // Clear error messages
                $('.input-field').removeClass('error'); // Clear error styling

                axios.post('/auth/', formData)
                    .then(response => {
                        if (response.data.message === 'success') {
                            window.location.href = '/';
                        }
                    })
                    .catch(error => {
                        const errors = error.response.data.errors; // Ambil objek error

                        for (const field in errors) {
                            $(`input[name="${field}"]`).addClass(
                                'error'); // Tambahkan class error pada input
                            $(`input[name="${field}"]`).next('.error-message').text(errors[field][
                                0
                            ]); // Tampilkan pesan error
                        }

                        Toast.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan saat login.' // Atau pesan umum lainnya
                        });
                    }).finally(() => {
                        $('#login-btn, #loading-spinner').toggleClass('hidden');
                    });
            });
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
        })
    </script>
@overwrite
