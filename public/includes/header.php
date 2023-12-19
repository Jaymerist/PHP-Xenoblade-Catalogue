<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?php echo $title; ?> | Xeno Collection
        </title>
        <!-- BS Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <!-- BS Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/styles.css">
    </head>

    <body class="d-flex flex-column min-vh-100">
        <header class="text-center">
            <nav class="py-2 bg-dark border-bottom">
                <div class="container d-flex flex-wrap">
                    <ul class="nav me-auto">
                        <!-- CRUD Buttons -->
                        <<?php if (isset($_SESSION['username'])) : ?>

                        <a href="admin.php" class="btn btn-dark">Admin</a>

                        <!-- We'll also give them the option to log out. -->
                        <a href="logout.php" class="btn btn-dark">Log Out</a>

                        <?php else: ?>
                        <!-- If the user is NOT logged in, we'll give them the login button. -->
                        <a href="login.php" class="btn btn-primary">Log In</a>

                        <?php endif; ?>
                    </ul>
                    <ul class="nav">
                        <li class="nav-item"><a href="filter.php" class="nav-link link-light link-body-emphasis px-2">Filters  |</a></li>
                        <li class="nav-item"><a href="search.php" class="nav-link link-light link-body-emphasis px-2">  Advanced Search</a></li>
                    </ul>
                </div>
            </nav>
            <section class="py-3 mb-4 border-bottom">
                <div class="container d-flex flex-wrap justify-content-center">
                    <a href="index.php"
                        class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto link-body-emphasis text-decoration-none">
                        <svg class="bi me-2" width="40" height="32">
                            <use xlink:href="#bootstrap"></use>
                        </svg>
                        <h1 class="fs-4 fw-light"></i>Mihiri's Xeno Collection</h1>
                        </a>

                        <!-- If you choose to do the 'quick search' as your challenge, include the widget here. -->
                    <form action="search-view.php" method="GET" class="col-12 col-lg-auto mb-3 mb-lg-0" role="search">
                        <!-- This is an input type of search, so the user has to hit 'enter' or 'return' to submit the form. A more user-friendly thing to do would be to also offer a submit button beside it. -->
                        <div class="input-group">
                            <input type="search" class="form-control" aria-label="Search" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </section>
        </header>