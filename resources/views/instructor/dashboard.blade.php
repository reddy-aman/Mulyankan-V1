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
<div class="p-4 rounded-lg bg-white">
    <div class="p-4">
        <h1 class="font-semibold text-2xl text-blue-800">Welcome to Mulyankan </h1>
        <p class="text-lg mt-2"> Hi,<span class="ml-1 text-green-700 font-semibold"> {{ Auth::user()->name }} </span> Your Role is {{ Auth::user()->roles->pluck('name')->implode(',') }}
        <a  href="{{route('instructor.create-courses')}}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <i class="fa fa-eye mr-2" aria-hidden="true"></i> 
        View Course 
        </a>
    </p>
      </div>
    </div>
    </div>
</x-app-layout>
