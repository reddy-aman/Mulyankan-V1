<x-app-layout>
    <style>
        .Courses-card {
            margin-top: 0;
        }

        /* Optional: Adjust for smaller screens */
        @media (max-width: 500px) {
            .Courses-card {
                padding-top: 20% !important;
            }
        }

        /* Optional: Adjust for smaller screens */
        @media (max-width: 3600px) {
            .Courses-card {
                padding-top: 6%;
            }
        }
    </style>


    <div class="p-4 sm:ml-64 Courses-card">

        <!-- success message -->
        @if (Session::has('success'))
            <div id="toast-success"
                class="flex items-center w-full max-w-100 p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                    </svg>
                    <span class="sr-only">Check icon</span>
                </div>
                <span class="ml-3 font-medium text-green-700">{{ session('success') }}</span>
            </div>

            <script>
                // Set timeout for auto-dismiss in seconds
                setTimeout(function () {
                    document.getElementById('toast-success').style.display = 'none';
                }, 5000); // 3000 milliseconds = 3 seconds
            </script>
        @endif



        <!-- Your Courses start -->

        <div class="bg-white p-4  rounded-lg">
            <h3 class="ml-2 mt-3 mb-5 font-semibold text-xl text-gray-800 leading-tight">Your Courses</h3>
            @if($Course->isEmpty())
                <div class="flex justify-center items-center">
                    <div class="flex flex-col items-center">
                        <p class=" mt-3 mb-3 font-semibold  text-gray-800 leading-tight">You do not have any Courses yet
                        </p>
                        <i class="fa fa-trash fa-2x text-red-700" aria-hidden="true"></i>
                        <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                            class=" cursor-pointer mt-3 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-1 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Create Course
                        </a>
                    </div>
                </div>

            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                    @foreach ($Course as $course)
                        <div
                            class="max-w-full sm:max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <p
                                class=" bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2 py-2 font-semibold w-100  rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                                <i class="fa fa-book mr-1" aria-hidden="true"></i> {{$course->term}} <span
                                    class="ml-1">{{$course->year}}</span>
                            <p>
                            <h5 class="mb-2 mt-4 text-1xl font-bold tracking-tight text-blue-800 dark:text-white">
                                <i class="fa fa-book mr-1" aria-hidden="true"></i>
                                <a href="{{ route('courses.show', ['id' => $course->id]) }}"
                                    class="hover:underline text-blue-500">
                                    {{ $course->course_name }}
                                </a>
                            </h5>
                            <p class="mb-2 text-base text-justify font-semibold leading-relaxed text-gray-900 ">
                                <i class="fa fa-info-circle mr-1" aria-hidden="true"></i>
                                Course Description
                                <hr class="border-t-2 border-gray-300 mb-3">
                            </p>
                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                                <span class="short-text text-justify ">
                                    {{ Str::limit($course->course_description, 55) }}
                                </span>
                                <a href="javascript:void(0);" data-modal-target="default-modal"
                                    data-modal-toggle="default-modal" class="read-more text-blue-500">Read More</a>
                            </p>
                            <a href="#"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                0 View Assignments
                                <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Main modal -->
            @if(empty($course) || !isset($course->course_name))

            @else
                <div id="default-modal" tabindex="-1" aria-hidden="true"
                    class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex justify-center items-center w-full h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-2xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    <i class="fa fa-book mr-2" aria-hidden="true"></i> {{ $course->course_name }}
                                </h3>
                                <button type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-hide="default-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-4 md:p-5 space-y-4 modal-body-content">
                                <p class="text-base text-justify font-semibold leading-relaxed text-blue-800">
                                    <i class="fa fa-info-circle mr-2" aria-hidden="true"></i>
                                    Course Description
                                </p>
                                <p class="text-base text-justify leading-relaxed text-gray-500 dark:text-gray-400">
                                    {{ $course->course_description }}</p>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <button data-modal-hide="default-modal"
                                    class="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800">
                                    <span
                                        class="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                                        Decline
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const modal = document.getElementById("default-modal");
                    const readMoreLinks = document.querySelectorAll(".read-more");

                    readMoreLinks.forEach(link => {
                        link.addEventListener("click", function () {
                            // Show the modal
                            modal.classList.remove("hidden");
                        });
                    });

                    // Close modal functionality
                    document.querySelectorAll("[data-modal-hide]").forEach(button => {
                        button.addEventListener("click", () => {
                            modal.classList.add("hidden");
                        });
                    });
                });
            </script>

        </div>
    </div>

    <!-- Your Courses end  -->

    <!-- Create your Course modal start -->

    <div id="crud-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
        style="padding-top:60px;">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 ">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fa fa-plus-circle m-1" aria-hidden="true"></i> Create your Course
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form method="POST" action="{{ route('courses.store') }}" class="p-4 md:p-5">
                    @csrf
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <!-- Course Number -->
                        <div class="col-span-2 sm:col-span-1">
                            <label for="course_number"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course Number <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="course_number" id="course_number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="e.g. AI201" required>
                            @error('course_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Course Name -->
                        <div class="col-span-2 sm:col-span-1">
                            <label for="course_name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="course_name" id="course_name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="e.g. Introduction to AI" required>
                        </div>

                        <!-- Course Description -->
                        <div class="col-span-2">
                            <label for="course_description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course Description
                                <span class="text-red-600">*</span></label>
                            <textarea id="course_description" name="course_description" rows="4"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write course description here" required></textarea>
                        </div>

                        <!-- Term -->
                        <div class="col-span-2 sm:col-span-1">
                            <label for="term"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Term</label>
                            <select id="term" name="term"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected>Select Term</option>
                                @foreach ($terms as $term)
                                    <option value="{{$term}}">{{$term}}</option>
                                @endforeach                            </select>
                        </div>

                        <!-- Year -->
                        <div class="col-span-2 sm:col-span-1">
                            <label for="year"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year</label>
                            <select id="year" name="year"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected>Select Year</option>
                                @foreach ($years as $year)
                                    <option value="{{$year}}">{{$year}}</option>
                                @endforeach                         </select>
                        </div>

                        <!-- Department -->
                        <div class="col-span-2">
                            <label for="department"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
                            <select id="department" name="department"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected>Select Department</option>
                                <option selected>Select Year</option>
                                @foreach ($departments as $department)
                                    <option value="{{$department}}">{{$department}}</option>
                                @endforeach                         </select>
                        </div>

                        <!-- Entry Code (Checkbox) -->
                        <div class="col-span-2">
                            <label for="entry_code"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entry Code</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="entry_code" name="entry_code" value="1"
                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    {{ old('entry_code') ? 'checked' : '' }}>
                                <label for="entry_code"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Allow students to
                                    enroll via course entry code</label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-span-2 mt-4">
                            <button type="submit"
                                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Create Course
                            </button>

                            <button data-modal-toggle="crud-modal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                Decline
                            </button>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Create your Course modal end -->

    <!-- Entry Code modal start -->

    <div id="Entry-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        <i class="fa fa-plus-circle m-1" aria-hidden="true"></i> Add Course via Entry Code
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="Entry-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium me-2 p-2 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                        <i class="fa fa-info-circle mr-1" aria-hidden="true"></i> Use your instructor-provided entry
                        code to enroll in the course.
                    </span>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course
                            Entry Code <span class="text-red-600">*</span> </label>
                        <input type="text" name="name" id="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Enter your six-character course entry code" required="">
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Enroll
                        Course</button>
                    <button data-modal-hide="Entry-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
                </div>
            </div>
        </div>
    </div>

    <!-- bottom nav start -->

    <div data-dial-init class="fixed right-6 bottom-6 group">
        <div id="speed-dial-menu-dropdown-square"
            class="flex flex-col justify-end hidden py-1 mb-4 space-y-2 bg-white border border-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
            <ul class="text-sm text-gray-500 dark:text-gray-300">
                <li>
                    <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                        class=" cursor-pointer flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                        <i class="fa fa-plus-circle fa-2x" style="color:green;" aria-hidden="true"></i>
                        <span class=" mb-1 ml-3 font-semibold text-xl text-gray-800"> Create Course </span>
                    </a>
                </li>
                <li>
                    <a data-modal-target="Entry-modal" data-modal-toggle="Entry-modal"
                        class=" cursor-pointer flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                        <i class="fa fa-book fa-2x" style="color:green;" aria-hidden="true"></i>
                        <span class=" mb-1 ml-3 font-semibold text-xl text-gray-800"> Enroll in Course </span>
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" data-dial-toggle="speed-dial-menu-dropdown-square"
            aria-controls="speed-dial-menu-dropdown-square" aria-expanded="false"
            class="flex items-center justify-center ml-auto text-white bg-blue-700 rounded-lg w-50 h-10 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800">
            <svg class="w-5 h-5 ml-3 transition-transform group-hover:rotate-45" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 1v16M1 9h16" />
            </svg>
            <span class="mr-4 ml-3 font-semibold">Create</span>
        </button>
    </div>
    </div>

    <!-- bottom nav end -->
</x-app-layout>