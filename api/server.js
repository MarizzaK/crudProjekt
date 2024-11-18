const express = require("express");
const fs = require("fs");
const path = require("path");
const app = express();

app.use(express.json());

const databasePath = path.join(__dirname, "..", "dbh.json");

function readDatabase() {
  try {
    const data = fs.readFileSync(databasePath, "utf8");
    return JSON.parse(data);
  } catch (error) {
    console.error("Fel vid läsning av databasen:", error);
    throw error;
  }
}

function writeDatabase(data) {
  try {
    fs.writeFileSync(databasePath, JSON.stringify(data, null, 2), "utf8");
  } catch (error) {
    console.error("Fel vid skrivning till databasen:", error);
    throw error;
  }
}

app.get("/products/:id", (req, res) => {
  try {
    const products = readDatabase();
    const productId = parseInt(req.params.id, 10);
    const product = products.find((p) => p.id === productId);

    if (product) {
      res.json(product);
    } else {
      res.status(404).json({ message: "Produkt ej hittad" });
    }
  } catch (error) {
    res.status(500).json({ message: "Ett fel uppstod." });
  }
});

app.get("/products", (req, res) => {
  try {
    const products = readDatabase();
    res.json(products);
  } catch (error) {
    res.status(500).json({ message: "Ett fel uppstod." });
  }
});

app.post("/products", (req, res) => {
  try {
    const products = readDatabase();
    const newProduct = req.body;
    newProduct.id =
      products.length > 0 ? Math.max(...products.map((p) => p.id)) + 1 : 1;

    products.push(newProduct);
    writeDatabase(products);

    res.status(201).json(newProduct);
  } catch (error) {
    res.status(500).json({ message: "Kunde inte lägga till produkten." });
  }
});

app.put("/products/:id", (req, res) => {
  try {
    const products = readDatabase();
    const productId = parseInt(req.params.id, 10);
    const productIndex = products.findIndex((p) => p.id === productId);

    if (productIndex !== -1) {
      products[productIndex] = { ...products[productIndex], ...req.body };
      writeDatabase(products);
      res.json(products[productIndex]);
    } else {
      res.status(404).json({ message: "Produkt ej hittad" });
    }
  } catch (error) {
    res.status(500).json({ message: "Kunde inte uppdatera produkten." });
  }
});

app.delete("/products/:id", (req, res) => {
  try {
    const products = readDatabase();
    const productId = parseInt(req.params.id, 10);
    const updatedProducts = products.filter((p) => p.id !== productId);

    if (products.length !== updatedProducts.length) {
      writeDatabase(updatedProducts);
      res.status(200).json({ message: "Produkt borttagen" });
    } else {
      res.status(404).json({ message: "Produkt ej hittad" });
    }
  } catch (error) {
    res.status(500).json({ message: "Kunde inte ta bort produkten." });
  }
});

module.exports = app;
