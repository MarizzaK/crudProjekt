<?php

$format = isset($_GET['format']) ? $_GET['format'] : null;

if (!$format || !in_array($format, ['xml', 'csv'])) {
    die("Ogiltigt format. Använd 'xml' eller 'csv'.");
}


$products = json_decode(file_get_contents('http://localhost:3000/products'), true);
if (!$products) {
    die("Kunde inte hämta produkter från API:et.");
}

if ($format === 'xml') {

    $xml = new SimpleXMLElement('<products/>');
    foreach ($products as $product) {
        $productNode = $xml->addChild('product');
        $productNode->addChild('id', $product['id']);
        $productNode->addChild('name', htmlspecialchars($product['name']));
        $productNode->addChild('price', $product['price']);
        $productNode->addChild('image', htmlspecialchars($product['image']));
    }


    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="products.xml"');
    echo $xml->asXML();
} elseif ($format === 'csv') {

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="products.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID', 'Name', 'Price', 'Image']);
    foreach ($products as $product) {
        fputcsv($output, [$product['id'], $product['name'], $product['price'], $product['image']]);
    }
    fclose($output);
}

exit;
?>
