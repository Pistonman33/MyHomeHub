<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Scanner Code-barres</title>
  <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: system-ui, sans-serif;
      background: #f5f6fa;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }

    h1 {
      margin-top: 20px;
      font-size: 1.8em;
      color: #2c3e50;
    }

    #scanner-container {
      margin-top: 20px;
      width: 90%;
      max-width: 800px;
      background: #000;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.15);
    }

    #scanner {
      width: 100%;
      height: 680px;
    }

    #result-box {
      margin-top: 20px;
      width: 90%;
      max-width: 800px;
      background: white;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      font-size: 1.8em;
      color: #2c3e50;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
    }

    #result-box span {
      font-weight: bold;
      color: #27ae60;
    }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

<h1>📦 Scanner un Code-barres</h1>

<div id="scanner-container">
  <div id="scanner"></div>
</div>

<div id="result-box">
  Aucun code détecté pour le moment...
</div>

<script>
Quagga.init({
    inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector("#scanner"),
        constraints: {
            facingMode: "environment",
            width: { ideal: 1280 },
            height: { ideal: 720 },
            focusMode: "continuous"
        }
    },
    decoder: {
        readers: ["ean_reader", "ean_8_reader", "upc_reader", "upc_e_reader"]
    },
    locate: true
}, function(err) {
    if (err) {
        console.error(err);
        alert("Erreur caméra : " + err);
        return;
    }
    Quagga.start();
    console.log("Scanner démarré !");
});

let lastScan = 0;

Quagga.onDetected(data => {
    const code = data.codeResult.code;

    if (!code || code.length !== 13 || !/^\d+$/.test(code)) {
        return;
    }

    const now = Date.now();

    // Cooldown de 2 secondes
    if (now - lastScan < 2000) {
        return;
    }
    lastScan = now;
    
    // Indiquer visuellement qu’on envoie
    document.getElementById("result-box").innerHTML =
        "Recherche du produit pour : <b>" + code + "</b>";

    fetch("/library/api/barcode", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
        },
        body: JSON.stringify({ code })
    })
    .then(async res => {
        console.log("Réponse reçue du serveur, statut HTTP : " + res.status);

        const txt = await res.text();

        console.log("Texte brut reçu de Laravel :\n\n" + txt);

        try {
            return JSON.parse(txt);
        } catch (e) {
            console.log("Erreur JSON : " + e.message);
            throw e;
        }
    })
    .then(json => {
        console.log("JSON final :\n\n" + JSON.stringify(json, null, 2));

        if (!json.success) {
            document.getElementById("result-box").innerHTML =
                "❌ Aucun produit trouvé.";
            return;
        }

        const p = json.product;

        console.log("Produit trouvé :\n\n" + JSON.stringify(p, null, 2));

        document.getElementById("result-box").innerHTML = `
            <h3>${p.title ?? "Sans titre"}</h3>
            <p><strong>Catégorie :</strong> ${p.category ?? "Pas de catégorie"}</p>
            <p>${p.description ?? "Pas de description"}</p>

            ${p.image ? `<img src="${p.image}" style="max-width:300px; border-radius:6px;">` : ""}
        `;
    })
    .catch(err => {
        console.log("Erreur fetch : " + err);
    });
});

</script>

</body>
</html>
