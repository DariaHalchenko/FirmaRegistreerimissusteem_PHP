<?php
$file = "tootajad.json";
if(isset($_POST['submit'])){
    unset($_POST['submit']);
    // Lisame tunnitasule € märgi, kui seda pole
    $tunnitasu = $_POST['tunnitasu'];
    if ($tunnitasu !== "" && !str_contains($tunnitasu, "€")) {
        $tunnitasu = "€" . $tunnitasu;
    }
    // uus töötaja
    $newdata = [
        "@attributes" => [
            "nimi" => $_POST['nimi'],
            "isikukood" => $_POST['isikukood'],
            "tunnitasu" => $tunnitasu,
            "amet" => $_POST['amet']
        ],
        "Igapaev" => [
            "@attributes" => [
                "kuupaev" => $_POST['kuupaev']
            ],
            "Aeg" => [
                "@attributes" => [
                    "sissenemine" => $_POST['sissenemine'],
                    "valjumine" => $_POST['valjumine']
                ]
            ]
        ]
    ];
    // Loeme vana faili
    $data = json_decode(file_get_contents($file), true);
    // Lisame uue töötaja
    $data["Tootaja"][] = $newdata;
    // kirjutame tagasi faili
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $teade = "<p style='color:green;'>Töötaja edukalt lisatud!</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lisa töötaja</title>
</head>
<body>
<header>
    <h1>Lisa töötaja</h1>
</header>
<!--navigeerimismenüü-->
<nav class="menu">
    <ul>
        <li><a href="tootajad.php">Töötajate nimekiri</a></li>
        <li><a href="lisamine.php">Lisa uus töötaja</a></li>
    </ul>
</nav>
<h2>Uue töötaja lisamine</h2>
<?php if(isset($teade)) echo $teade; ?>
<form action="" method="post">
    <label>Nimi:</label>
    <input type="text" name="nimi" required>
    <label>Isikukood:</label>
    <input type="text" name="isikukood" required>
    <label>Tunnitasu:</label>
    <input type="text" name="tunnitasu" required>
    <label>Amet:</label>
    <input type="text" name="amet" required>
    <label>Kuupäev:</label>
    <input type="date" name="kuupaev" required>
    <label>Sissepääs:</label>
    <input type="time" name="sissenemine" required>
    <label>Väljapääs:</label>
    <input type="time" name="valjumine" required>
    <input type="submit" name="submit" value="Lisa töötaja">
</form>
</body>
</html>
