<?php

$products = json_decode(file_get_contents('http://localhost:3000/products'), true);


if ($products === null || !is_array($products)) {
    $products = [];
    echo "Kunde inte hämta produkter eller produkterna är inte i rätt format.";
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Startsida - Utvalda Produkter</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<h1>Utvalda Produkter</h1>

<div class="product-container" id="featured-products">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p>Pris: <?php echo htmlspecialchars($product['price']); ?> SEK</p>
            <?php if (!empty($product['image'])): ?>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
            <?php else: ?>
                <p><em>Ingen bild tillgänglig</em></p>
            <?php endif; ?>
            <form method="get" action="edit-product.php" class="edit-form">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <button type="submit">Redigera</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<script>
    document.getElementById('add-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const newProduct = {
            name: document.getElementById('new-name').value,
            price: parseFloat(document.getElementById('new-price').value),
            image: document.getElementById('new-image').value || ''
        };

        fetch('http://localhost:3000/products', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(newProduct)
        })
        .then(response => response.json())
        .then(() => {
            alert('Ny produkt tillagd!');
            location.reload();
        })
        .catch(() => alert('Det gick inte att lägga till produkten.'));
    });
</script>

<div class="action-buttons">
    <a href="add-product.php" class="add-product-button">Lägg till produkt</a>
    <a href="export.php?format=xml" class="export-button">Exportera till XML</a>
    <a href="export.php?format=csv" class="export-button">Exportera till CSV</a>
</div>

</body>
</html>
