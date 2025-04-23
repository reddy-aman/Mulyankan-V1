import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// Now you can use $ in your file...

var priInstructorEmail = "{{ $pri_instructor_email }}";

// Global function to update a counter
function updateCounter(selector, singular, plural, delta) {
    var $span = $(selector);
    var count = parseInt($span.data('count')) || parseInt($span.text()) || 0;
    count = Math.max(0, count + delta);
    $span.data('count', count);
    $span.text(count + (count === 1 ? ' ' + singular : ' ' + plural));
}

$(document).ready(function () {
    // Add User AJAX
    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.addUserUrl,
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    $("#successMessage").text(response.message)
                        .removeClass("hidden")
                        .prependTo("#mainContent")
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();

                    var name = $('#name').val();
                    var email = $('#email').val();
                    var roleVal = $('input[name=role]:checked').val();
                    var roleText = (roleVal == 1) ? 'Student' : (roleVal == 2) ? 'Instructor' : 'TA';

                    var newRow = `<tr id="user-row-${response.userId}" class="border border-gray-200 bg-white hover:bg-gray-50">
                        <td class="border border-gray-200 px-6 py-3">${name}</td>
                        <td class="border border-gray-200 px-6 py-3">${email}</td>
                        <td class="border border-gray-200 px-6 py-3">${roleText}</td>
                        <td class="border border-gray-200 px-6 py-3">0</td>
                        <td class="border border-gray-200 px-6 py-3 text-center">
                            <i class="fa fa-pencil text-gray-500 cursor-pointer hover:text-gray-700"></i>
                        </td>
                        <td class="border border-gray-200 px-6 py-3 text-center">
                            <i class="fa fa-times text-red-500 cursor-pointer hover:text-red-700"></i>
                        </td>
                    </tr>`;

                    $('#userTableBody').append(newRow);

                    if (roleText === 'Student') {
                        updateCounter('#studentCount', 'Student', 'Students', 1);
                    } else if (roleText === 'Instructor') {
                        updateCounter('#instructorCount', 'Instructor', 'Instructors', 1);
                    } else if (roleText === 'TA') {
                        updateCounter('#taCount', 'TA', 'TAs', 1);
                    }

                    $('#addUserForm')[0].reset();
                    window.dispatchEvent(new CustomEvent('close-modal'));
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    });

    // CSV Upload AJAX
    $('#csvUploadForm').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: window.uploadCSVUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    $('#csvUploadForm')[0].reset();
                    window.dispatchEvent(new CustomEvent('close-modal'));
                    if (response.redirect)
                        window.location.href = response.redirect;
                } else {
                    alert("Some errors occurred: " + response.errors.join("\n"));
                }
            },
            error: function (xhr) {
                let errorMessage = "Failed to upload CSV!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(", ");
                }
                alert(errorMessage);
            }
        });
    });

    // Edit User AJAX
    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let userEmail = $(this).find('input[name="old_email"]').val();
        console.log("user email:", userEmail);
        let encodedEmail = encodeURIComponent(userEmail);
        $.ajax({
            url: window.editUserUrl.replace('__EMAIL__', encodedEmail),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content },
            success: function (response) {
                if (response.success) {
                    window.location.href = window.rosterShowUrl;
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr) {
                let errorMessage = "Failed to update user!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(", ");
                }
                alert(errorMessage);
            }
        });
    });

    // Delete User AJAX
    $('.delete-user-button').on('click', function (e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to delete this user?")) {
            return;
        }
        let userEmail = $(this).data('user-email');
        let $userRow = $("tr[data-user-email='" + userEmail + "']");
        let roleText = $userRow.find('.user-role').text().trim();
        let encodedEmail = encodeURIComponent(userEmail);
        $.ajax({
            url: window.deleteUserUrl.replace('__EMAIL__', encodedEmail), 
            type: "DELETE",
            headers: { 
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content 
            },
            success: function (response) {
                if (response.success) {
                    $userRow.remove();
                    location.reload();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr) {
                let errorMessage = "Failed to delete user!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(", ");
                }
                alert(errorMessage);
            }
        });
    });

    $('#sendNotificationBtn').on('click', function (e) {
        e.preventDefault();

        if (!confirm("Are you sure you want to send the enrollment notification?")) {
            return;
        }

        $.ajax({
            url: window.sendNotificationUrl,
            type: "POST", 
            headers: { 
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content 
            },
            success: function (response) {
                console.log("AJAX Success Response:", response); 
            
                if (response.success) {
                    $("#successMessage").text(response.message)
                        .removeClass("hidden")
                        .prependTo("#mainContent")
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();
                } else {
                    alert("Something went wrong!");
                }
            },
            error: function () {
                alert("Error while sending notification.");
            }
        });
    });
});

// Simple client-side search filter
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector('input[name="search"]');
    const tableRows = document.querySelectorAll('#userTableBody .roster-item');

    if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const term = this.value.toLowerCase();
        tableRows.forEach(function (row) {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(term) ? '' : 'none';
        });
    });
    }
});

