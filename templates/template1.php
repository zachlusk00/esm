<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESM Template</title>
    <link rel="stylesheet" href="template1.css">
</head>
<body>
    <div class="nav-bar-container">
        <h1>Employee Management and Scheduling</h1>
        <!--<div class="nav-logo"></div>
        <div class="nav-header">
            <h1>Template1</h1>
        </div>
        <div class="nav-buttons"></div>-->
    </div>
    <div class="content-container">
        <div class="form-container">
            <div class="form-container-top">
                <h2>Template</h2>
            </div>
            <div class="form-container-content">

                <form id="loginForm" method="POST">
                    <label for="email">Email</label><br>
                    <input id="email" name="email" type="text" max_length="100"><br>
                    <label for="password">Password</label><br>
                    <input id="password" name="password" type="text" max_length="255">
                    <a href="templates/template1.php">Forgot your password?</a>
                </form>
                

            </div>
            <div class="form-container-bottom">
                <button type="submit" form="loginForm">Submit</button>
            </div>
        </div>
    </div>
    <div class="footer-container"></div>
</body>
</html>