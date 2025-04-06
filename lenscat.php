<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List Modal</title>
    <style>
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
            background-color: rgb(250, 249, 247);
            border-radius: 8px;
            overflow: hidden;
            max-width: 55vw;
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
            /* display: flex; */
            /* justify-content: space-between; */
            /* padding: 25px; */
            background-color: #faf9f7;
            color: black;
            display: flex;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            justify-content: space-between;
            padding: 20px 46px;
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

        svg {
            pointer-events: auto !important;
            cursor: pointer;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            flex-direction: row;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            /* justify-content: space-between; */
            height: 10vh;
            align-items: center;
            padding: 24px;
            gap: 24px;
            margin: 10px 0;
            border-radius: 5px;
            border-bottom: 1px solid #ddd;
            opacity: 0;
            cursor: pointer;
            transition: box-shadow 0.2s ease-in-out;
            box-shadow: rgba(0, 0, 66, 0.06) 0px 0px 12px;
            background-color: white;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease-in-out forwards;
        }

        .list-group-item:nth-child(1) {
            animation-delay: 0.2s;
        }

        .list-group-item:nth-child(2) {
            animation-delay: 0.4s;
        }

        .list-group-item:nth-child(3) {
            animation-delay: 0.6s;
        }

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
                top: auto;
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
<?php include('lens.php'); ?>
<body>
    <div class="modal fade" id="lenscat">
        <div class="modal-dialog">
            <div class="modal-header">
            <!-- <i class="fa fa-window-close" aria-hidden="true"></i> -->
            <!-- <button type="button" class="btn-close fa fa-window-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <svg width="1em" class="btn-close fa fa-window-close" data-bs-dismiss="modal" height="1em" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.085 1.53 8.08.529.383 8.208l.049.05 7.648 7.63 1.005-1.002-5.983-5.968h15.99v-1.42H3.101l5.983-5.967Z" fill="currentColor"></path>
                </svg>
                <h1>Select Lenscat Type</h1>
                <svg width="1em" class="btn-close fa fa-window-close" data-bs-dismiss="modal" height="1em" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="m1.064 18.593 8.133-8.114 8.13 8.114 1.007-1.006-8.133-8.113 8.133-8.111L17.327.358l-8.13 8.114L1.064.358.057 1.363l8.132 8.11-8.132 8.114 1.007 1.006Z" fill="currentColor"></path>
                </svg>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item" data-bs-toggle="modal" data-bs-target="#lens">
                    <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#lens"> -->
                        <img src="https://via.placeholder.com/50" alt="Product 1">
                        <div>
                            <h6>Product 1</h6>
                            <p>- High quality material</p>
                            <p>- Affordable price</p>
                        </div>
                        <!-- </button> -->
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>