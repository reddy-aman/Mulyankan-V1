<style>
/* Center the logo for all screen sizes */
.responsive-logo-container {
  display: flex; /* Enable flexbox layout */
  justify-content: center; /* Center horizontally */
  align-items: center; /* Optional: Center vertically */
  margin-bottom: 20px;;

}

.responsive-logo {
  max-width: 100%; /* Make the logo responsive */
  height: auto; /* Maintain aspect ratio */
  width: 65px; /* Set a base width for larger screens */
}

/* Optional: Adjust for smaller screens */
@media (max-width: 500px) {
  .responsive-logo {
    width: 55px; /* Adjust width for smaller screens */
    margin-bottom: 20px;;
  }
}
</style>

<div class="responsive-logo-container">
  <img src="{{ asset('images/IITB.png') }}" alt="IITB Logo" class="responsive-logo">
</div>
