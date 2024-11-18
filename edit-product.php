<?php

$productId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$productId) {
    die("Ingen produkt-id angiven.");
}

$product = null;
try {

    $productJson = file_get_contents("http://localhost:3000/products/$productId");
    $product = json_decode($productJson, true);
} catch (Exception $e) {
    die("Produkten kunde inte hämtas: " . $e->getMessage());
}

if (!$product) {
    die("Produkten kunde inte hittas.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedProduct = [
        'name' => $_POST['name'],
        'price' => $_POST['price'],
        'image' => $_POST['image'],
    ];

    $ch = curl_init("http://localhost:3000/products/$productId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatedProduct));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
    ]);
    $response = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
        header("Location: index.php");
        exit;
    } else {
        echo "<p>Det gick inte att uppdatera produkten. Försök igen.</p>";
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera produkt</title>
    <link rel="stylesheet" href="index.css"> 
</head>
<body>
    <div class="product-container" id="edit-product">
        <div class="product-card large">
            <h1>Redigera produkt</h1>
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p>Pris: <?php echo htmlspecialchars($product['price']); ?> SEK</p>
            <?php if (!empty($product['image'])): ?>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="200">
            <?php else: ?>
                <p><em>Ingen bild tillgänglig</em></p>
            <?php endif; ?>
        </div>

        <form method="POST" class="edit-form">
            <label for="name">Namn:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

            <label for="price">Pris:</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required><br><br>

            <label for="image">Bild URL:</label>
            <input type="text" id="image" name="image" value="<?= htmlspecialchars($product['image']) ?>"><br><br>

            <button type="submit">Spara ändringar</button>
        </form>
    </div>
</body>
</html>
