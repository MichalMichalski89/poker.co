// ===== Venue Management Modals & Scores =====

// Track last focused Manage Venue button to restore focus when modal closes
let lastFocusedButton = null;

/** ========== Manage Venue Modal Handling ========== **/

// Open Manage Venue modal
$(document).on("click", ".manage-venue-btn", function () {
  lastFocusedButton = $(this);

  const venueId = $(this).data("venueid");
  const venueName = $(this).data("venuename");

  $("#manageVenueModal").data("venueid", venueId);
  $("#manageVenueModalLabel").text("Manage " + venueName);

  $("#venueGamesList").html("<p>Loading games...</p>");
  $("#venuePlayersList").html("<p>Loading players...</p>");
  $("#venueScoresList").html("<p>Loading scores...</p>");

  $("#gamesSeasonSelect").html('<option value="active" selected>Active Season</option>');
  $("#scoresFilterSelect").html('<option value="last" selected>Last Game</option><option value="this_season">This Season</option>');

  $("#manageVenueModal").modal({
    backdrop: 'static',
    keyboard: true,
    focus: false
  });
});

// Load Manage Venue content on show
$("#manageVenueModal").on("shown.bs.modal", function () {
  const venueId = $(this).data("venueid");

  $.get("../assets/php/get_seasons.php", function (data) {
    $("#gamesSeasonSelect").append(data);
  });

  loadVenueGames(venueId, "active");
  loadVenuePlayers(venueId);
  loadGamesToScore(venueId, "last");

  $("#manageVenueModalLabel").trigger("focus");
});

// Restore focus when Manage Venue modal hides
$("#manageVenueModal").on("hide.bs.modal", function () {
  if (lastFocusedButton) {
    setTimeout(() => {
      lastFocusedButton.trigger("focus");
    }, 10);
  }
});

// Clear Manage Venue modal data when hidden
$("#manageVenueModal").on("hidden.bs.modal", function () {
  $(this).removeData("venueid");
  $("#venueGamesList, #venuePlayersList, #venueScoresList").empty();
});

/** ========== Filter Controls ========== **/

$(document).on("change", "#gamesSeasonSelect", function () {
  const venueId = $("#manageVenueModal").data("venueid");
  const seasonId = $(this).val();
  loadVenueGames(venueId, seasonId);
});

$(document).on("change", "#scoresFilterSelect", function () {
  const venueId = $("#manageVenueModal").data("venueid");
  const filter = $(this).val();
  loadGamesToScore(venueId, filter);
});

/** ========== Data Loaders ========== **/

function loadVenueGames(venueId, seasonId) {
  $.get("../assets/php/get_venue_games.php", { venue_id: venueId, season_id: seasonId }, function (data) {
    $("#venueGamesList").html(data);
  });
}

function loadVenuePlayers(venueId) {
  $.get("../assets/php/get_venue_players.php", { venue_id: venueId }, function (data) {
    $("#venuePlayersList").html(data);
  });
}

function loadGamesToScore(venueId, filter) {
  $.get("../assets/php/get_games_to_score.php", { venue_id: venueId, filter: filter }, function (data) {
    $("#venueScoresList").html(data);
  });
}

function loadGamePlayers(gameId) {
  $.get("../assets/php/get_game_players.php", { game_id: gameId }, function (data) {
    const parsed = JSON.parse(data);
    $("#availablePlayersList").html(parsed.available);
    $("#attendingPlayersList").html(parsed.attending);
    initializeSortableLists();
  });
}

/** ========== Add Scores Modal ========== **/

// Open Add Scores Modal cleanly after Manage Venue modal hides
$(document).on("click", ".add-scores-btn, .edit-scores-btn", function () {
  const gameId = $(this).data("gameid");
  const gameDate = $(this).data("date");
  const venueName = $(this).data("venuename");

  $("#addScoresModal").data("gameid", gameId);
  $("#addScoresModalLabel").text(`Add points for the league game at ${venueName} (${gameDate})`);

  $("#availablePlayersList").html("<li>Loading...</li>");
  $("#attendingPlayersList").empty();

  loadGamePlayers(gameId);

  // Wait for Manage Venue modal to hide first before showing Add Scores modal
  $("#manageVenueModal").on("hidden.bs.modal", function () {
    $("#addScoresModal").modal("show");
    $(this).off("hidden.bs.modal"); // remove this one-time listener after trigger
  });

  $("#manageVenueModal").modal("hide");
});

// Return to Manage Venue modal when Add Scores modal hides
$("#addScoresModal").on("hidden.bs.modal", function () {
  $("#manageVenueModal").modal("show");
});

/** ========== Sortable Lists ========== **/

function initializeSortableLists() {
  const availableList = document.getElementById("availablePlayersList");
  const attendingList = document.getElementById("attendingPlayersList");

  if (availableList && attendingList) {
    Sortable.create(availableList, {
      group: "players",
      animation: 150,
      handle: ".move-handle"
    });

    Sortable.create(attendingList, {
      group: "players",
      animation: 150,
      handle: ".move-handle"
    });
  }
}

/** ========== Save Scores ========== **/

$("#saveScoresBtn").on("click", function () {
  const gameId = $("#addScoresModal").data("gameid");
  const playerOrder = [];

  $("#attendingPlayersList li").each(function (index) {
    playerOrder.push({
      user_id: $(this).data("userid"),
      position: index + 1
    });
  });

  $.post("../assets/php/save_game_scores.php", {
    game_id: gameId,
    scores: playerOrder
  }, function (response) {
    alert(response.message);
    $("#addScoresModal").modal("hide");
  }, 'json');
});
