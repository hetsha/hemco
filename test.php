<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List Modal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-dialog {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            max-width: 50vw;
            width: 100%;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            transform: translateX(100%);
            transition: transform 0.4s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: translateX(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background-color: #007bff;
            color: white;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            max-height: calc(100vh - 60px);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: white;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease-in-out forwards;
        }

        .list-group-item:nth-child(1) { animation-delay: 0.2s; }
        .list-group-item:nth-child(2) { animation-delay: 0.4s; }
        .list-group-item:nth-child(3) { animation-delay: 0.6s; }

        .list-group-item img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 5px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .modal-dialog {
                position: fixed;
                bottom: 0;
                width: 100%;
                height: 80vh;
                max-width: none;
                border-radius: 15px 15px 0 0;
                transform: translateY(100%);
            }

            .modal.show .modal-dialog {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <button onclick="openModal()">Open Modal</button>

    <div class="modal" id="productModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h5>Product List</h5>
                <button class="close-btn" onclick="closeModal()">✖</button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <img src="https://via.placeholder.com/50" alt="Product 1">
                        <div>
                            <h6>Product 1</h6>
                            <p>- High quality material</p>
                            <p>- Affordable price</p>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <img src="https://via.placeholder.com/50" alt="Product 2">
                        <div>
                            <h6>Product 2</h6>
                            <p>- Stylish design</p>
                            <p>- Durable and long-lasting</p>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <img src="https://via.placeholder.com/50" alt="Product 3">
                        <div>
                            <h6>Product 3</h6>
                            <p>- Lightweight and comfortable</p>
                            <p>- Available in multiple colors</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("productModal").classList.add("show");
        }

        function closeModal() {
            document.getElementById("productModal").classList.remove("show");
        }
    </script>

</body>
</html>
