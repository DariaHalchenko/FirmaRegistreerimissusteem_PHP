<?php
$file = "tootajad.json";
$data = json_decode(file_get_contents($file), true);

// Otsing ja sorteerimine
$otsi = strtolower($_GET['otsi'] ?? '');
$sorteerimine = $_GET['sort'] ?? '';
$kuu  = $_GET['kuu'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Firma töötajate registreerimissüsteem</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Töötajate nimekiri</h1>
</header>
<!--navigeerimismenüü-->
<nav class="menu">
    <ul>
        <li><a href="tootajad.php">Töötajate nimekiri</a></li>
        <li><a href="lisamine.php">Lisa uus töötaja</a></li>
    </ul>
</nav>
<!--Otsing ja sorteerimine-->
<h2>Otsing ja funktsioonid</h2>
<form method="get" action="">
    <label for="otsi">Otsi (nimi, amet, kuupäev):</label>
    <input type="text" id="otsi" name="otsi" value="<?= htmlspecialchars($otsi) ?>" oninput="this.form.submit()">
    <label for="sorteerimine">Sorteeri:</label>
    <select name="sort" onchange="this.form.submit()">
        <option value="">—</option>
        <option value="nimi" <?= $sorteerimine=='nimi'?'selected':'' ?>>Nimi</option>
        <option value="palk" <?= $sorteerimine=='palk'?'selected':'' ?>>Palk</option>
    </select>
    <label for="kuu">Kuu:</label>
    <input type="month" name="kuu" value="<?= htmlspecialchars($kuu) ?>" onchange="this.form.submit()">
    <a href="tootajad.php" class="reset-btn">Tühista filtrid</a>
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
        <th>Tunnid</th>
        <th>Palk</th>
    </tr>
    <?php
    $tootajad = $data["Tootaja"] ?? [];
    $ridu = [];
    foreach ($tootajad as $tootaja) {
        $r = $tootaja["@attributes"];
        $igapaev = $tootaja["Igapaev"];
        if (isset($igapaev["@attributes"])) {
            $igapaev = [$igapaev];
        }

        foreach ($igapaev as $paev) {
            $paevKuu = $paev["@attributes"]["kuupaev"];
            // Filtreerimine kuude järgi
            if ($kuu !== "" && strpos($paevKuu, $kuu) !== 0) continue;

            $aeg = $paev["Aeg"]["@attributes"];
            // Palgaarvestus
            list($h1,$m1) = explode(':', $aeg["sissenemine"]); // h1 - kell, m1 - minut
            list($h2,$m2) = explode(':', $aeg["valjumine"]);
            $tooaeg = (($h2*60+$m2) - ($h1*60+$m1)) / 60;

            $tunni_kaupa  = floatval(str_replace(['€',','], ['','.'], $r['tunnitasu']));

            $palk = round($tooaeg * $tunni_kaupa, 2);
            // Filter otsingu järgi
            $otsing_rida = strtolower($r["nimi"] . " " . $r["amet"] . " " . $paevKuu);
            if ($otsi && !str_contains($otsing_rida, $otsi)) continue;
            // Lisame massiivi stringid
            $ridu[] = [
                'nimi' => $r["nimi"],
                'isikukood' => $r["isikukood"],
                'amet' => $r["amet"],
                'tunnitasu' => $r["tunnitasu"],
                'kuupaev' => $paevKuu,
                'sis' => $aeg["sissenemine"],
                'val' => $aeg["valjumine"],
                'tunnid' => $tooaeg,
                'palk' => $palk
            ];
        }
    }
    // Sorteerimine tähestikulises järjekorras ja kasvavas järjekorras
    if ($sorteerimine === "nimi") {
        usort($ridu, fn($a,$b) => strcmp($a["nimi"], $b["nimi"]));
    }
    else if ($sorteerimine === "palk") {
        usort($ridu, fn($a,$b) => $a["palk"] <=> $b["palk"]);
    }
    if (empty($ridu)) {
        echo "<tr><td colspan='9' style='color:red;'>Tulemusi ei leitud.</td></tr>";
    }
    else {
        foreach ($ridu as $r) {
            echo "<tr>
            <td>{$r['nimi']}</td>
            <td>{$r['isikukood']}</td>
            <td>{$r['amet']}</td>
            <td>{$r['tunnitasu']}</td>
            <td>{$r['kuupaev']}</td>
            <td>{$r['sis']}</td>
            <td>{$r['val']}</td>
            <td>".number_format($r['tunnid'],2)."</td>
            <td>{$r['palk']} €</td>
        </tr>";
        }
    }
    ?>
</table>
<footer>
    <p>Daria Halchenko © 2025</p>
</footer>
</body>
</html>
