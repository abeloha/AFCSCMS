<html>
    <head>

    </head>

    
    <body>
        <form method="GET">
            <input type="text" name="code" />
            <input type="submit" />
        </form>

        <hr>
        <?php
            if(isset($_GET['code'])){
                echo code_to_user_id($_GET['code']);
            }
        ?>
    </body>
</html>