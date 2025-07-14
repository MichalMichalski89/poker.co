// ===== Venue Management Modals & Scores =====

let lastFocusedButton = null;
let availablePlayerCache = {};
let pointsConfig = {
  POINTS_TABLE: {},
  PARTICIPATION_POINTS: 0
};

// Load points config from PHP
function loadPointsConfig() {
  return $.getJSON("../assets/php/get_points_config.php", function (data) {
    pointsConfig = data;
  });
}

// Venue Modals
$(document).on('click', '.edit-venue-btn', function () {
  const button = $(this);
  $('#editVenueModal input[name="id"]').val(button.data("id"));
  $('#editVenueModal input[name="name"]').val(button.data("name"));
  $('#editVenueModal input[name="location"]').val(button.data("location"));
  $('#editVenueModal input[name="marker_color"]').val(button.data("marker_color"));
  $('#editVenueModal input[name="latitude"]').val(button.data("latitude"));
  $('#editVenueModal input[name="longitude"]').val(button.data("longitude"));
  $('#editVenueModal select[name="region_id"]').val(button.data("region_id"));
  $('#editVenueModal').modal('show');
});

$(document).on("click", ".manage-venue-btn", function () {
  lastFocusedButton = $(this);
  const venueId = $(this).data("venueid");
  const venueName = $(this).data("venuename");

  $("#manageVenueModal").data("venueid", venueId);
  $("#manageVenueModalLabel").text("Manage " + venueName);

  $("#venueGamesList, #venuePlayersList, #venueScoresList").html("<p>Loading…</p>");
  $("#gamesSeasonSelect").html('<option value="active" selected>Active Season</option>');
  $("#scoresFilterSelect").html('<option value="last" selected>Last Game</option><option value="this_season">This Season</option>');
  $("#manageVenueModal").modal({ backdrop: 'static', keyboard: false, focus: true });
});

$("#manageVenueModal").on("shown.bs.modal", function () {
  const venueId = $(this).data("venueid");
  $.get("../assets/php/get_seasons.php", data => $("#gamesSeasonSelect").append(data));
  loadVenueGames(venueId, "active");
  loadVenuePlayers(venueId);
  loadGamesToScore(venueId, "last");
});

$("#manageVenueModal").on("hidden.bs.modal", function () {
  $(this).removeData("venueid");
  $("#venueGamesList, #venuePlayersList, #venueScoresList").empty();
  if (lastFocusedButton) setTimeout(() => lastFocusedButton.trigger("focus"), 10);
});

$(document).on("change", "#gamesSeasonSelect", function () {
  const venueId = $("#manageVenueModal").data("venueid");
  loadVenueGames(venueId, $(this).val());
});

$(document).on("change", "#scoresFilterSelect", function () {
  const venueId = $("#manageVenueModal").data("venueid");
  loadGamesToScore(venueId, $(this).val());
});

// Load venue games, players, scores
function loadVenueGames(venueId, seasonId) {
  $.get("../assets/php/get_venue_games.php", { venue_id: venueId, season_id: seasonId }, data => {
    $("#venueGamesList").html(data);
  });
}

function loadVenuePlayers(venueId) {
  $.get("../assets/php/get_venue_players.php", { venue_id: venueId }, data => {
    $("#venuePlayersList").html(data);
  });
}

function loadGamesToScore(venueId, filter) {
  $.get("../assets/php/get_games_to_score.php", { venue_id: venueId, filter: filter }, data => {
    $("#venueScoresList").html(data);
  });
}

// Load game players into scoring modal
function loadGamePlayers(gameId) {
  $.get("../assets/php/get_game_players.php", { game_id: gameId }, function (data) {
    const parsed = JSON.parse(data);

    $("#availablePlayersList").html(parsed.available);
    $("#attendingPlayersList").html(parsed.attending);

    availablePlayerCache = {};
    $("#availablePlayersList .player-item").each(function () {
      const userId = $(this).data("userid");
      availablePlayerCache[userId] = $(this).prop("outerHTML");
    });

    $("#attendingPlayersList .player-item").each(function () {
      const userId = $(this).data("userid");
      if (!availablePlayerCache[userId]) {
        const cleanCopy = $(this).clone();
        cleanCopy.find(".position-label, .points-value").remove();
        availablePlayerCache[userId] = cleanCopy.prop("outerHTML");
      }
    });

    initializeSortableLists();
    updatePlayerPositions();
  });
}

// Open scores modal
$(document).on("click", ".add-scores-btn, .edit-scores-btn", function () {
  const gameId    = $(this).data("gameid");
  const gameDate  = $(this).data("date");
  const gameType  = $(this).data("type");
  const venueName = $(this).data("venuename");

  console.log("Opening scores modal for gameId:", gameId);

  $("#addScoresModal").data("gameid", gameId);
  $("#addScoresModalLabel").text(`Add points for ${gameType} at ${venueName} (${gameDate})`);
  $("#availablePlayersList").html("<li>Loading players…</li>");
  $("#attendingPlayersList").empty();

  loadPointsConfig().then(() => {
    loadGamePlayers(gameId);
    $("#addScoresModal").modal("show");
  });
});

// Update positions & points
function updatePlayerPositions() {
  const attendingList = document.getElementById("attendingPlayersList");
  if (!attendingList) return;

  attendingList.querySelectorAll(".position-label, .points-value").forEach(el => el.remove());

  attendingList.querySelectorAll(".player-item").forEach((item, index) => {
    const badge = document.createElement("span");
    badge.className = "position-label badge bg-primary me-2";
    badge.textContent = index + 1;
    item.insertBefore(badge, item.firstChild);

    const points = calculatePoints(index + 1);
    const pointsSpan = document.createElement("span");
    pointsSpan.className = "ms-2 points-value text-muted small";
    pointsSpan.textContent = `${points} pts`;
    item.appendChild(pointsSpan);
  });
}

// Calculate points
function calculatePoints(position) {
  if (!pointsConfig || !pointsConfig.POINTS_TABLE) return 0;
  return pointsConfig.POINTS_TABLE[position] ?? pointsConfig.PARTICIPATION_POINTS;
}

// Drag & Drop
function initializeSortableLists() {
  const availableList = document.getElementById("availablePlayersList");
  const attendingList = document.getElementById("attendingPlayersList");

  if (availableList && attendingList) {
    [availableList, attendingList].forEach(list => {
      Sortable.create(list, {
        group: "players",
        animation: 150,
        onEnd: (evt) => {
          const movedItem = evt.item;
          const userId = $(movedItem).data("userid");
          if (evt.to.id === "availablePlayersList" && availablePlayerCache[userId]) {
            $(movedItem).replaceWith(availablePlayerCache[userId]);
          }
          updatePlayerPositions();
        }
      });
    });
  }
}

// Save scores to DB
$("#saveScoresBtn").on("click", function () {
  const gameId  = $("#addScoresModal").data("gameid");
  const venueId = $("#manageVenueModal").data("venueid");  // <- reliably from manageVenueModal

  console.log("Saving scores for gameId:", gameId, "venueId:", venueId);

  if (!venueId) {
    showToast("Error: Venue not found when saving scores.", 'danger');
    return;
  }

  const playerOrder = [];
  $("#attendingPlayersList li").each(function (index) {
    const userId = $(this).data("userid");
    playerOrder.push({ user_id: userId, position: index + 1 });
  });

  const $btn = $(this);
  $btn.prop("disabled", true).text("Saving...");

  $.post("../assets/php/save_game_scores.php", {
    game_id: gameId,
    scores: playerOrder
  }, function (response) {
    console.log("Save response:", response);

    if (response.success) {
      loadVenueGames(venueId, $("#gamesSeasonSelect").val());
      loadVenuePlayers(venueId);
      loadGamesToScore(venueId, $("#scoresFilterSelect").val());
    }

    $("#addScoresModal").modal("hide");
    showToast(response.message, response.success ? 'success' : 'danger');
    $btn.prop("disabled", false).text("💾 Save Scores");
  }, 'json');
});

// Toast notification
function showToast(message, type = 'info') {
  const toastId = 'dynamicToast';
  $("#" + toastId).remove();
  $("body").append(`
    <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>`);
  new bootstrap.Toast(document.getElementById(toastId)).show();
}

// Load points config on page load
loadPointsConfig();
