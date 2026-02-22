<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body>
    <div class="flex min-h-screen">

        <?php include("./includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">

            <?php include("./includes/header.php"); ?>

            <main>
            </main>

        </div>
    </div>
</body>