<style>

.responsive-logo {
  max-width: 100%; /* Make the logo responsive */
  height: auto; /* Maintain aspect ratio */
  width: 50px; /* Set a base width for larger screens */
}

/* Optional: Adjust for smaller screens */
@media (max-width: 500px) {
  .responsive-logo {
    width: 35px; /* Adjust width for smaller screens */
   
  }
}

</style>

<div class="responsive-logo">
  <img src="{{ asset('images/IITB.png') }}" alt="IITB Logo" class="responsive-logo">
</div>