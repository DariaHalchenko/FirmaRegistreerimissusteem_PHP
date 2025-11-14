<?php
$file = "tootajad.json";
$data = json_decode(file_get_contents($file), true);

// Andmete otsing
$otsi_nimi = isset($_GET['otsi_nimi']) ? strtolower($_GET['otsi_nimi']) : '';
$otsi_data = isset($_GET['otsi_data']) ? strtolower($_GET['otsi_data']) : '';
$otsi_amet = isset($_GET['otsi_amet']) ? strtolower($_GET['otsi_amet']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Firma töötajate registreerimissüsteem</title>
</head>
<body>
<header>
    <h1>Töötajate nimekiri</h1>
</header>
<!-- Andmete otsing -->
<h2>Töötajate otsing</h2>
<form method="get" action="">
    <label for="otsi_nimi">Otsi nimi:</label>
    <input type="text" id="otsi_nimi" name="otsi_nimi" value="<?= htmlspecialchars($otsi_nimi) ?>">
    <label for="otsi_data">Otsi kuupäeva järgi:</label>
    <input type="text" id="otsi_data" name="otsi_data" value="<?= htmlspecialchars($otsi_data) ?>">
    <label for="otsi_amet">Otsi ameti järgi:</label>
    <input type="text" id="otsi_amet" name="otsi_amet" value="<?= htmlspecialchars($otsi_amet) ?>">
    <input type="submit" value="Otsi">
</form>
<!-- Tulemuste tabel -->
<table>
    <tr>
        <th>Nimi</th>
        <th>Isikukood</th>
        <th>Amet</th>
        <th>Tunnitasu</th>
        <th>Kuupäev</th>
        <th>Sissepääs</th>
        <th>Väljapääs</th>
    </tr>
    <?php
    $leitud = false;
    $tootajad = isset($data["Tootaja"]) ? $data["Tootaja"] : [];
    foreach($tootajad as $tootaja):
        $r = $tootaja["@attributes"];
        $igapaev = $tootaja["Igapaev"];
        if (isset($igapaev["@attributes"])) {
            $igapaev = [$igapaev];
        }

        foreach($igapaev as $paev):
            $a = $paev["Aeg"]["@attributes"];

            // Filter otsingu järgi
            $sobib_nimi = empty($otsi_nimi) || strpos(strtolower($r['nimi']), $otsi_nimi) !== false;
            $sobib_data = empty($otsi_data) || strpos(strtolower($paev['@attributes']['kuupaev']), $otsi_data) !== false;
            $sobib_amet = empty($otsi_amet) || strpos(strtolower($r['amet']), $otsi_amet) !== false;
            if(!$sobib_nimi || !$sobib_data || !$sobib_amet) continue;
            $leitud = true;
            ?>
            <tr>
                <td><?= $r["nimi"] ?></td>
                <td><?= $r["isikukood"] ?></td>
                <td><?= $r["amet"] ?></td>
                <td><?= $r["tunnitasu"] ?></td>
                <td><?= $paev["@attributes"]["kuupaev"] ?></td>
                <td><?= $a["sissenemine"] ?></td>
                <td><?= $a["valjumine"] ?></td>
            </tr>
        <?php endforeach;
    endforeach; ?>
</table>

<?php
if(!$leitud){
    echo "<p style='color:red;'>Tulemusi ei leitud.</p>";
}
?>
<footer>
    <p>Daria Halchenko &copy; 2025</p>
</footer>
</body>
</html>
