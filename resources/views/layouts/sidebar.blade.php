<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-0 transition-transform -translate-x-full
          bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">

    <!-- Wrapper to hold both scrollable content and the fixed bottom section -->
    <div class="flex flex-col h-full">
        <!-- Scrollable container for logo and menu items -->
        <div class="flex flex-col flex-grow px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            {{-- Logo and Mulyankan brand (optional) --}}
            <a href="{{ route('instructor.create-courses') }}" class="flex items-center px-0 pb-4 mt-6">
                <div class="flex items-center">
                    <x-application-logo />
                    <span class="ml-3 text-2xl font-semibold dark:text-white">
                        Mulyankan
                    </span>
                </div>
            </a>
            {{-- Main menu items --}}
            <ul class="space-y-2 font-medium ">
                <!-- Your existing menu logic remains unchanged -->
                @if (auth()->user()->getRoleNames()->contains('Instructor'))
                    @php
                        $currentRoute = Route::currentRouteName();
                        $lastOpenedCourse = session()->has('last_opened_course') ? session('last_opened_course') : null;
                    @endphp

                    @if (
                        !$lastOpenedCourse ||
                            !(Str::startsWith($currentRoute, 'courses.') || Str::startsWith($currentRoute, 'assignments.')))
                        <div class="px-4 py-2">
                            <h2 class="text-2xl font-extrabold text-black tracking-wide">
                                Welcome to <span class="text-3xl">MULYANKAN</span>
                            </h2>
                            <p class="text-sm mt-2 text-black leading-relaxed">
                                A next-gen <span class="font-semibold">grading software</span> built for seamless
                                <span class="italic">course management</span>, efficient <span class="italic">assignment
                                    tracking</span>,
                                and smooth <span class="italic">student evaluations</span>.
                            </p>
                        </div>
                    @endif

                    @if ($lastOpenedCourse && (Str::startsWith($currentRoute, 'courses.') || Str::startsWith($currentRoute, 'assignments.')))
                        <!-- <li>
                         <a href="{{ route('instructor.create-courses') }}"
                            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            <span class="ms-3">Courses</span>
                         </a>
                       </li> -->
                        @php
                            $course = \App\Models\Course::find($lastOpenedCourse);
                        @endphp
                        @if ($course)
                            <li class="mb-6">
                                <div>
                                    <div style="padding-left: 1%;">
                                        <p class="text-gray-700 mt-1 text-lg flex items-end space-x-2">
                                            <strong
                                                class="text-2xl text-black font-extrabold">{{ $course->course_number }}</strong>
                                        </p>
                                        <div style="padding-left: 2%;">
                                            <p>
                                                <strong class="text-normal">{{ $course->course_name }}</strong>
                                            </p>
                                            <span class="text-gray-1000 text-sm">{{ $course->term }}
                                                {{ $course->year }}</span>
                                        </div>
                                    </div>
                                    <div class="md:text-right"></div>
                                </div>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('courses.show', $lastOpenedCourse) }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <i class="fa fa-tasks" aria-hidden="true"></i>
                                <span class="ms-3">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assignments.index', $lastOpenedCourse) }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <i class="fa fa-file-text" aria-hidden="true"></i>
                                <span class="ms-3">Assignments</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('courses.roster', $lastOpenedCourse) }}"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <i class="fa fa-users" aria-hidden="true"></i>
                                <span class="ms-3">Roster</span>
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <i class="fa fa-clock-o fa-lg" aria-hidden="true"></i>
                                <span class="ms-3">Extensions</span>
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <i class="fa fa-cog fa-lg" aria-hidden="true"></i>
                                <span class="ms-3">Course Settings</span>
                            </a>
                        </li>
                    @endif

                @elseif (auth()->user()->getRoleNames()->contains('Student'))
                        <div class="px-4 py-2">
                            <h2 class="text-2xl font-extrabold text-black tracking-wide">
                                Welcome to <span class="text-3xl">MULYANKAN</span>
                            </h2>
                            <p class="text-sm mt-2 text-black leading-relaxed">
                                A next-gen <span class="font-semibold">grading software</span> built for seamless
                                <span class="italic">course management</span>, efficient <span class="italic">assignment
                                    tracking</span>,
                                and smooth <span class="italic">student evaluations</span>.
                            </p>
                        </div>
                @endif
            </ul>
        </div>

        <!-- User Info Section moved to the bottom of the sidebar -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-2 bg-white dark:bg-gray-800 relative">
            <!-- Clickable Section -->
            <button type="button"
                class="flex items-center justify-between space-x-2 w-full text-left focus:outline-none"
                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                <div class="flex items-center space-x-2">
                    <!-- User Icon -->
                    <i class="fa fa-user-circle text-2xl text-gray-600 dark:text-gray-300"></i>

                    <!-- User Info (Role + Name) -->
                    <div>
                        <!-- Role in bold on the first line -->
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">
                            {{ Auth::user()->roles->pluck('name')->implode(',') }}
                        </p>

                        <!-- Name -->IIT-B
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>

                <!-- Dropdown Arrow -->
                <i class="fa fa-chevron-up text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Dropdown menu -->
            <div class="z-50 hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600"
                id="dropdown-user">
                <!-- Arrow Indicator -->
                <div
                    class="absolute top-[-6px] right-4 w-3 h-3 rotate-45 bg-white dark:bg-gray-700 border-l border-t border-gray-200 dark:border-gray-600">
                </div>

                <ul class="py-1" role="none">
                    <li>
                        <a href="#"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 
                          dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                            role="menuitem">
                            <i class="fa fa-user mr-2"></i> Edit Account
                        </a>
                    </li>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <li>
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 
                              dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                role="menuitem" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fa fa-power-off mr-2"></i> Log Out
                            </a>
                        </li>
                    </form>
                </ul>
            </div>
        </div>

    </div>
</aside>
