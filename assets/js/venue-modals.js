// Attach event listener to dynamically loaded edit buttons
$(document).on('click', '.edit-venue-btn', function () {
    // Retrieve venue data from button data attributes
    const button = $(this);
    const venueId = button.data('id');
    const name = button.data('name');
    const location = button.data('location');
    const markerColor = button.data('marker_color');
    const latitude = button.data('latitude');
    const longitude = button.data('longitude');
    const regionId = button.data('region_id');

    // Populate the edit form fields with these values
    $('#editVenueModal input[name="id"]').val(venueId);
    $('#editVenueModal input[name="name"]').val(name);
    $('#editVenueModal input[name="location"]').val(location);
    $('#editVenueModal input[name="marker_color"]').val(markerColor);
    $('#editVenueModal input[name="latitude"]').val(latitude);
    $('#editVenueModal input[name="longitude"]').val(longitude);
    $('#editVenueModal select[name="region_id"]').val(regionId); // important for the select

    $('#editVenueModal').modal('show');
});



// Tournament Director's Manage Venue Modal handler
$(document).on("click", ".manage-venue-btn", function () {
    const venueId = $(this).data("venueid");
    const venueName = $(this).data("venuename");

    console.log("Venue ID: ", venueId);
    console.log("Venue Name: ", venueName);

    $("#manageVenueModalLabel").text("Manage " + venueName);
    $("#venuePlayersList").html("<p>Loading players…</p>");

    // Fetch players for the selected venue
    $.get("../assets/php/get_venue_players.php", { venue_id: venueId }, function (data) {
      $("#venuePlayersList").html(data);

      // Show the modal after content loads
      $("#manageVenueModal").modal("show");
    });
});
