<x-app-layout>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md px-4 py-6">
            <nav>
                <!-- Sidebar content (if any) -->
            </nav>
        </aside>

        <!-- Main content container -->
        <div class="flex-1 flex flex-col p-6 bg-gray-50">
            <!-- Page Header -->
            <div class="bg-white shadow-md px-6 py-4 rounded-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Courses</h1>
            </div>

            <!-- Course Cards / Empty State -->
            <div class="flex-1 overflow-auto mt-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    @if ($Course->isEmpty())
                        <!-- Empty state when no courses exist -->
                        <div class="flex justify-center items-center">
                            <div class="flex flex-col items-center">
                                <p class="mt-3 mb-3 font-semibold text-gray-800 leading-tight">
                                    You do not have any Courses yet
                                </p>
                                <i class="fa fa-trash fa-2x text-red-700" aria-hidden="true"></i>
                                <a data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                   class="cursor-pointer mt-3 focus:outline-none text-white bg-blue-600 hover:bg-blue-700 
                                          focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-1 mb-2">
                                    Create Course
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Course Cards Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($Course as $course)
                                <div
                                    onclick="window.location='{{ route('courses.show', ['id' => $course->id]) }}';"
                                    class="cursor-pointer w-full p-4 sm:p-6 bg-white border border-gray-200 
                                           rounded-lg shadow hover:bg-gray-100 m-6"
                                >
                                    <!-- Term + Year -->
                                    <p class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-2 rounded border border-blue-400">
                                        <i class="fa fa-book mr-1" aria-hidden="true"></i>
                                        {{ $course->term }}
                                        <span class="ml-1">{{ $course->year }}</span>
                                    </p>

                                    <!-- Course Name -->
                                    <h5 class="mb-2 mt-4 text-xl font-bold tracking-tight text-blue-800">
                                        <i class="fa fa-book mr-1" aria-hidden="true"></i>
                                        {{ $course->course_name }}
                                    </h5>

                                    <!-- Description Heading -->
                                    <p class="mb-2 text-base text-justify font-semibold leading-relaxed text-gray-900">
                                        <i class="fa fa-info-circle mr-1" aria-hidden="true"></i>
                                        Course Description
                                        <hr class="border-t-2 border-gray-300 mb-3">
                                    </p>

                                    <!-- Short Description and Read More -->
                                    <p class="mb-3 font-normal text-gray-700">
                                        <span class="short-text text-justify">
                                            {{ Str::limit($course->course_description, 55) }}
                                        </span>
                                        <br>
                                        <a href="javascript:void(0);"
                                           data-modal-target="default-modal"
                                           data-modal-toggle="default-modal"
                                           class="read-more text-blue-500"
                                           onclick="event.stopPropagation()">
                                            Read More
                                        </a>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
