<?php
require '../includes/db.php';
session_start();
$created_by = $_SESSION['admin_id'] ?? 1;

// Fetch Kingdoms
$kingdoms = $pdo->query("SELECT id, name FROM kingdoms ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Species Statuses
$statuses = $pdo->query("SELECT id, label FROM species_statuses ORDER BY FIELD(label, 'extinct', 'endangered', 'vulnerable', 'least concern')")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Animal</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h2>Add New Animal</h2>

<form action="insert_animal.php" method="POST" enctype="multipart/form-data">
  <label>Common Name:</label>
  <input type="text" name="common_name" required><br>

  <label>Scientific Name:</label>
  <input type="text" name="scientific_name" required><br>

  <label>Species Conservation Status:</label>
  <select name="species_status_id" required>
    <option value="">Select Status</option>
    <?php foreach ($statuses as $status): ?>
      <option value="<?= $status['id'] ?>"><?= htmlspecialchars($status['label']) ?></option>
    <?php endforeach; ?>
  </select><br>

  <label>Kingdom:</label>
  <select name="kingdom_id" id="kingdom" required>
    <option value="">Select Kingdom</option>
    <?php foreach ($kingdoms as $row): ?>
      <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
    <?php endforeach; ?>
  </select><br>

  <label>Phylum:</label>
  <select name="phylum_id" id="phylum" required></select><br>

  <label>Class:</label>
  <select name="class_id" id="class_tax" required></select><br>

  <label>Order:</label>
  <select name="order_id" id="order" required></select><br>

  <label>Family:</label>
  <select name="family_id" id="family" required></select><br>

  <label>Genus:</label>
  <select name="genus_id" id="genus" required></select><br>

  <label>Species:</label>
  <select name="species_id" id="species" required></select><br>

  <label>Population Estimate:</label>
  <input type="text" name="population_estimate"><br>

  <label>Average Weight (kg):</label>
  <input type="number" step="0.01" name="avg_weight_kg"><br>

  <label>Average Length (cm):</label>
  <input type="number" step="0.01" name="avg_length_cm"><br>

  <label>Appearance:</label>
  <textarea name="appearance"></textarea><br>

  <label>Main Photo:</label>
  <input type="file" name="main_photo"><br><br>

  <!-- Additional Data Tabs -->
  <fieldset>
    <legend>Animal Habits</legend>
    <label>Diet:</label><br>
    <textarea name="diet"></textarea><br>
    <label>Mating Habits:</label><br>
    <textarea name="mating_habits"></textarea><br>
    <label>Behavior:</label><br>
    <textarea name="behavior"></textarea><br>
    <label>Habitat:</label><br>
    <textarea name="habitat"></textarea><br>
  </fieldset>
  <br>

  <fieldset>
    <legend>Geography</legend>
    <label>Continent(s):</label><input type="text" name="continent"><br>
    <label>Subcontinent(s):</label><input type="text" name="subcontinent"><br>
    <label>Country(s):</label><input type="text" name="country"><br>
    <label>Realm:</label><input type="text" name="realm"><br>
    <label>Biome:</label><input type="text" name="biome"><br>
  </fieldset>
  <br>
  <!-- Life Data -->
<fieldset>
  <legend>Life Data</legend>
  <label>Lifespan (years):</label>
  <input type="number" step="0.1" name="lifespan_years"><br>

  <label>Gestation Period (days):</label>
  <input type="number" name="gestation_period_days"><br>

  <label>Average Litter Size:</label>
  <input type="number" step="0.1" name="litter_size_avg"><br>

  <label>Age of Maturity (years):</label>
  <input type="number" step="0.1" name="maturity_age_years"><br>
</fieldset>
<br>

<!-- Human Interaction -->
<fieldset>
  <legend>Human Interaction</legend>
  <label>Threats:</label><br>
  <textarea name="threats"></textarea><br>

  <label>Conservation Efforts:</label><br>
  <textarea name="conservation_efforts"></textarea><br>
</fieldset>
<br>

<!-- Defense Mechanisms -->
<fieldset>
  <legend>Defense Mechanisms</legend>
  <label>Defense Mechanisms:</label><br>
  <textarea name="defense_mechanisms"></textarea><br>

  <label>Notable Adaptations:</label><br>
  <textarea name="notable_adaptations"></textarea><br>
</fieldset>
<br>

<!-- Health Risks -->
<fieldset>
  <legend>Health Risks</legend>
  <label>Common Diseases:</label><br>
  <textarea name="common_diseases"></textarea><br>

  <label>Known Parasites:</label><br>
  <textarea name="known_parasites"></textarea><br>

  <label>Zoonotic Potential:</label>
  <select name="zoonotic_potential">
    <option value="0">No</option>
    <option value="1">Yes</option>
  </select><br>
</fieldset>
<br>

<!-- Fun Facts -->
<fieldset>
  <legend>Fun Facts</legend>
  <label>Interesting Fact 1:</label><br>
  <textarea name="facts[]"></textarea><br>

  <label>Interesting Fact 2:</label><br>
  <textarea name="facts[]"></textarea><br>

  <label>Interesting Fact 3:</label><br>
  <textarea name="facts[]"></textarea><br>
</fieldset>


  <fieldset>
    <legend>Additional Photos</legend>
    <label>Photo 1:</label><input type="file" name="photos[]"><input type="text" name="captions[]" placeholder="Caption"><br>
    <label>Photo 2:</label><input type="file" name="photos[]"><input type="text" name="captions[]" placeholder="Caption"><br>
    <label>Photo 3:</label><input type="file" name="photos[]"><input type="text" name="captions[]" placeholder="Caption"><br>
    <label>Photo 4:</label><input type="file" name="photos[]"><input type="text" name="captions[]" placeholder="Caption"><br>
    <label>Photo 5:</label><input type="file" name="photos[]"><input type="text" name="captions[]" placeholder="Caption"><br>
  </fieldset>

  <br><input type="submit" value="Add Animal">
</form>

<script>
$(document).ready(function () {
  const levels = ['kingdom', 'phylum', 'class_tax', 'order', 'family', 'genus', 'species'];

  function loadNext(level, parentId) {
    if (!parentId) {
      $('#' + level).html('<option value="">Select</option>');
      return;
    }

    $.ajax({
      type: 'POST',
      url: 'load_taxonomy.php',
      data: { level: level === 'class_tax' ? 'class' : level, parent_id: parentId },
      success: function (data) {
        const options = JSON.parse(data);
        let html = '<option value="">Select</option>';
        options.forEach(item => {
          html += `<option value="${item.id}">${item.name}</option>`;
        });
        $('#' + level).html(html);
        clearLower(level);
      },
      error: function () {
        alert('Failed to fetch ' + level);
      }
    });
  }

  function clearLower(current) {
    const index = levels.indexOf(current);
    for (let i = index + 1; i < levels.length; i++) {
      $('#' + levels[i]).html('<option value="">Select</option>');
    }
  }

  $('#kingdom').change(function () {
    loadNext('phylum', $(this).val());
  });

  $('#phylum').change(function () {
    loadNext('class_tax', $(this).val());
  });

  $('#class_tax').change(function () {
    loadNext('order', $(this).val());
  });

  $('#order').change(function () {
    loadNext('family', $(this).val());
  });

  $('#family').change(function () {
    loadNext('genus', $(this).val());
  });

  $('#genus').change(function () {
    loadNext('species', $(this).val());
  });
});
</script>

</body>
</html>
