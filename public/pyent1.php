<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookShare - Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="../pics/logo.png" type="image/x-icon">
    <link rel="stylesheet" href=".css/bootstrap.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://cdn.pixabay.com/vimeo/328940567/payment-24120.mp4?width=1280') center/cover no-repeat;
            filter: blur(5px);
            z-index: -1;
            opacity: 0.15;
        }

        .booking-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 800px;
            padding: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .booking-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.2);
        }

        /* Custom scrollbar styling */
        .booking-container::-webkit-scrollbar {
            width: 8px;
        }

        .booking-container::-webkit-scrollbar-track {
            background: rgba(241, 241, 241, 0.3);
            border-radius: 4px;
        }

        .booking-container::-webkit-scrollbar-thumb {
            background: rgba(33, 62, 94, 0.5);
            border-radius: 4px;
        }

        .booking-container::-webkit-scrollbar-thumb:hover {
            background: rgba(33, 62, 94, 0.7);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #213e5e;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .step {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 15px;
            background: #e4e6eb;
            color: #65676b;
            transition: all 0.3s ease;
        }

        .step.completed,
        .step.active {
            background: #213e5e;
            color: white;
            box-shadow: 0 4px 12px rgba(33, 62, 94, 0.3);
        }

        .step-line {
            width: 50px;
            height: 3px;
            background: #e4e6eb;
            border-radius: 2px;
        }

        .step-line.completed {
            background: #213e5e;
        }

        .main-title {
            font-size: 26px;
            color: #1c1e21;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .subtitle {
            color: #65676b;
            font-size: 16px;
        }

        .content {
            padding: 20px 0;
        }



        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #1c1e21;
        }

        .summary-item {
            color: #65676b;
            margin-bottom: 5px;
        }

        .summary-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e4e6eb;
            font-weight: bold;
            color: #1c1e21;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #1c1e21;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #dddfe2;
            border-radius: 8px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #213e5e;
            box-shadow: 0 0 0 3px rgba(41, 100, 178, 0.2);
            background: white;
        }




        .btn-pay {
            background: #213e5e;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            flex: 1;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(33, 62, 94, 0.25);
        }

        .btn-pay:hover {
            background: #1a3149;
            box-shadow: 0 6px 16px rgba(33, 62, 94, 0.35);
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #e4e6eb;
            color: #1c1e21;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #d8dadf;
            transform: translateY(-2px);
        }

        .secure-info {
            text-align: center;
            color: #65676b;
            font-size: 13px;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .booking-container {
                margin: 10px;
                padding: 20px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <div class="header">
            <div class="logo">Bookshare</div>
            <div class="progress-steps">
                <div class="step completed" style="background-color: red;">1</div>
                <div class="step-line completed" style="background-color: red;"></div>
                <div class="step completed" style="background-color: red;">2</div>
                <div class="step-line completed" style="background-color: red;"></div>
                <div class="step active" style="background-color: red;">3</div>
            </div>
            <h1 class="main-title">Détails de Paiement</h1>
            <p class="subtitle">Finalisez votre achat en fournissant vos informations de paiement</p>
        </div>
        <div class="content">
            <!-- Affichage des informations du livre -->
            <div class="booking-summary">
                <hr>
                <div class="summary-title" style="text-align: center; font-size: 20px; color:#213e5e;">Informations du
                    livre</div> <br><br>
                <div style="display:flex; gap:15px; align-items:flex-start;">
                    <div style="flex:1;">

                        <div id="details">
                            <!-- hena data li jat mn news b localStorage -->
                            <!-- hena data li jat mn news b localStorage -->
                            <!-- hena data li jat mn news b localStorage -->
                            <!-- hena data li jat mn news b localStorage -->
                            <!-- hena data li jat mn news b localStorage -->
                        </div>


                        <button type="button" id="confirmBtn" class="btn-pay" onclick="confirmPurchase()"
                            style="width:100%; margin-top:10px;">
                            <span id="loadingSpinner" class="loading-spinner"></span>
                            <span id="confirmBtnText">Confirmer l'achat en ligne</span>
                        </button><br><br>
                        <button type="button" id="payBtn" class="btn-pay" onclick="processPayment()"
                            style="width:100%;">
                            <span id="loadingSpinner" class="loading-spinner"></span>
                            <span id="payBtnText"><a href="../public/news.php"
                                    style="color:#ffff; text-decoration:none; font-weight:bold;  ">
                                    Annuler l'achat en ligne
                            </span>
                        </button>

                        <script>

                            // Récupération du livre depuis le localStorage
                            const livre = JSON.parse(localStorage.getItem("livreSelectionne"));

                            if (livre) {
                                document.getElementById("details").innerHTML = `
                                             
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                              <img src="${livre.img}" alt="${livre.titre}" style=" box-shadow:black; margin-left:50px; margin-top:-45px; border-radius:9px;" width="200">
                                                            </div>
                                                            <div class="col-md-6">
                                                             <h3 style="color:red; font-weight:bold; font-family:arial-sanssirif;">${livre.titre}</h3>
                                                             <p><strong>Auteur :</strong> ${livre.auteur}</p>
                                                             <p><strong>Prix :</strong> ${livre.prix}</p>
                                                             <p><strong>Description :</strong> ${livre.synopsis}</p>
                                                             <div class="container">
                                                            </div>
                                                        </div>
                                                   </div>
                                             `;
                                 }

                            function confirmPurchase() {
                                const btn = document.getElementById('confirmBtn');
                                const spinner = document.getElementById('loadingSpinner');
                                const text = document.getElementById('confirmBtnText');

                                spinner.style.display = 'inline-block';
                                text.textContent = 'Att...';
                                btn.disabled = true;

                                let progress = 0;
                                const interval = setInterval(() => {
                                    progress += 10;
                                    btn.style.background = `linear-gradient(to right, green ${progress}%, #213e5e ${progress}%)`;
                                    if (progress >= 100) {
                                        clearInterval(interval);
                                        window.location.href = 'pyent.php';
                                    }
                                }, 100);
                            }
                        </script>
                    </div>
                </div>
            </div>





            <div class="secure-info">
                <i class="fas fa-lock"></i> Paiement sécurisé - vos informations sont protégées
            </div>
        </div>
    </div>
   

    <script>
        function cancel() {
            if (confirm('Êtes-vous sûr de vouloir annuler la réservation de votre livre ?')) {
                window.location.href = 'index.php';
            }
        }

        function processPayment() {
            document.getElementById('loadingSpinner').style.display = 'inline-block';
            document.getElementById('payBtnText').textContent = 'Traitement...';
            document.getElementById('payBtn').disabled = true;

            setTimeout(() => {
                alert('Paiement réussi ! Votre livre est réservé.');
                window.location.href = 'index.php';
            }, 2000);
        }

        window.addEventListener('load', () => {
            document.querySelector('.booking-container').style.opacity = '0';
            document.querySelector('.booking-container').style.transform = 'translateY(30px)';

            setTimeout(() => {
                document.querySelector('.booking-container').style.transition = 'all 0.6s ease';
                document.querySelector('.booking-container').style.opacity = '1';
                document.querySelector('.booking-container').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
    <script src="../js/bootstrap.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>