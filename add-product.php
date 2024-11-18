<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProduct = [
        'name' => $_POST['name'],
        'price' => $_POST['price'],
        'image' => $_POST['image'],
    ];

    $ch = curl_init("http://localhost:3000/products");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newProduct));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
    ]);
    $response = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 201) {
        header("Location: index.php");
        exit;
    } else {
        echo "<p>Det gick inte att lägga till produkten. Försök igen.</p>";
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lägg till produkt</title>
    <link rel="stylesheet" href="index.css"> <!-- Använd samma CSS som index.php -->
</head>
<body>
    <div class="product-container" id="add-product">
        <h1>Lägg till ny produkt</h1>
        <form method="POST" class="edit-form">
            <label for="name">Namn:</label>
            <input type="text" id="name" name="name" placeholder="Produktnamn" required><br><br>

            <label for="price">Pris:</label>
            <input type="number" id="price" name="price" placeholder="Pris" step="0.01" required><br><br>

            <label for="image">Bild URL:</label>
            <input type="text" id="image" name="image" placeholder="Bild URL"><br><br>

            <button type="submit">Lägg till produkt</button>
        </form>
    </div>
</body>
</html>
