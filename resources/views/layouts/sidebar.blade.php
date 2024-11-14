{{-- side bar start --}}

<!-- <link rel="stylesheet" href="/mulyankan/font-awesome-4.7.0/css/font-awesome.min.css"> -->

<aside id="logo-sidebar"
   class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
   aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
        
         @if(auth()->user()->getRoleNames()->contains('Instructor')) 
            <li>
               <a href="{{route('instructor.dashboard')}}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <i class="fa fa-tasks" aria-hidden="true"></i>
                  <span class="ms-3">Dashboard</span>
               </a>
            </li>
            <li>
               <a href="{{route('instructor.create-courses')}}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <i class="fa fa-book" aria-hidden="true"></i>
                  <span class="ms-3">Courses</span>
               </a>
            </li>
            <li>
               <a href="#"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <i class="fa fa-file-text" aria-hidden="true"></i>
                  <span class="ms-3">Assignments</span>
               </a>
            </li>
            <li>
               <a href="#"
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
         @elseif(auth()->user()->getRoleNames()->contains('Student'))
            <li>
               <a href="{{route('student.dashboard')}}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <i class="fa fa-tasks" aria-hidden="true"></i>
                  <span class="ms-3">Student Dashboard</span>
               </a>
            </li>
            <li>
               <a href="#"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <i class="fa fa-book" aria-hidden="true"></i>
                  <span class="ms-3">My Courses</span>
               </a>
            </li>
         @elseif(auth()->user()->getRoleNames()->contains('TA'))
            <li>
               <a href="{{route('ta.dashboard')}}"
                  class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                  <i class="fa fa-tasks" aria-hidden="true"></i>
                  <span class="ms-3">TA Dashboard</span>
               </a>
            </li>
         @endif
      </ul>
   </div>
</aside>



{{-- side bar close --}}
