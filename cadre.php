<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo TITLE ?> | Tribu-Terre</title>

    <!-- Meta tags essentiels -->
    <meta property="og:title" content="<?php echo TITLE ?>">
    <meta property="og:image" content="/resources/webp/imgLogoMini.webp">
    <meta
            property="og:description"
            content="Tribu-Terre, Association des Étudiants en Sciences de l'Université d'Orléans."
    >
    <meta
            name="description"
            content="Tribu-Terre, Association des Étudiants en Sciences de l'Université d'Orléans."
    >
    <meta property="og:url" content="https://bde-tribu-terre.fr/">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Meta tags recommandés -->
    <meta property="og:site_name" content="BDE Tribu-Terre">
    <meta name="twitter:image:alt" content="Logo de Tribu-Terre">

    <!-- Meta tags recommandés -->
    <!-- <meta property="fb:app_id" content="your_app_id"> <- Il faut un token pour avoir l'ID de la page -->
    <meta name="twitter:site" content="@tributerre45">

    <!-- Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    <!-- Feuille de style générale -->
    <link rel="stylesheet" type="text/css" href="/style.min.css">

    <?php if (defined("STYLE")): ?>
        <!-- Feuille de style du gabarit -->
        <link rel="stylesheet" type="text/css" href="<?= STYLE ?>">
    <?php endif; ?>

    <!-- Fonctions Javascript -->
    <script src="/script.min.js"></script>

    <?php if (defined("SCRIPT")): ?>
        <!-- Script du gabarit -->
        <link rel="stylesheet" type="text/css" href="<?= SCRIPT ?>">
    <?php endif; ?>
</head>
<body>
<div class="page-complete">
    <header>
        <div class="jumbotron">
            <div class="container text-center">
                <a href="/">
                    <img
                            class="logo-jumbotron"
                            src="/resources/webp/imgLogoMini.webp"
                            alt="Logo"
                            <?= $_SERVER["REQUEST_URI"] == '/' ? 'style="height: 300px"' : ''; ?>
                    >
                </a>
                <p class="texte-jumbotron">Association des Étudiants en Sciences de l'Université d'Orléans</p>
            </div>
        </div>

        <nav class="navbar navbar-expand-sm py-0">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigationHeader">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-collapse collapse" id="navigationHeader">
                <ul class="nav navbar-nav">
                    <li class="nav-item<?= $_SERVER["REQUEST_URI"] == '/' ? ' active' : ''; ?>">
                        <a class="nav-link" href="/">
                            <i class="bi bi-house-door-fill" style="font-size: larger"></i>
                            <span class="alterneur-mini">Accueil</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-grid-fill" style="font-size: larger"></i>
                            À propos <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/' ? ' active' : ''; ?>" href="/association/">
                                    Tribu-Terre
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/adherer/' ? ' active' : ''; ?>" href="/association/adherer/">
                                    Adhérer
                                </a>
                            </li>
                            <li role="separator" class="divider">
                            </li>
                            <li>
                                <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/federations/' ? ' active' : ''; ?>" href="/association/federations/">
                                    Fédérations
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/partenaires/' ? ' active' : ''; ?>" href="/association/partenaires/">
                                    Partenaires
                                </a>
                            </li>
                            <li role="separator" class="divider">
                            </li>
                            <li>
                                <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/statuts/' ? ' active' : ''; ?>" href="/association/statuts/">
                                    Statuts
                                </a>
                            </li>
                            <li role="separator" class="divider">
                            </li>
                            <li>
                                <a class="dropdown-item<?= $_SERVER["REQUEST_URI"] == '/association/contact/' ? ' active' : ''; ?>" href="/association/contact/">
                                    Contact
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item<?= $_SERVER["REQUEST_URI"] == '/journaux/' ? ' active' : ''; ?>">
                        <a class="nav-link" href="/journaux/">
                            <i class="bi bi-newspaper" style="font-size: larger"></i>
                            Journaux
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.podcastics.com/podcast/tribu-sciences_1/" target="_blank">
                            <i class="bi bi-mic-fill" style="font-size: larger"></i>
                            Podcasts
                        </a>
                    </li>
                    <li class="nav-item<?= $_SERVER["REQUEST_URI"] == '/trouver-une-salle/' ? ' active' : ''; ?>">
                        <a class="nav-link" href="/trouver-une-salle/">
                            <i class="bi bi-geo-fill" style="font-size: larger"></i>
                            Trouver une salle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/discord/">
                            <i class="bi bi-discord" style="font-size: larger"></i>
                            Serveurs Discord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.instagram.com/tribu.terre/">
                            <i class="bi bi-instagram" style="font-size: larger"></i>
                            <span class="alterneur-mini">Instagram</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.facebook.com/bdeTribuTerre/">
                            <i class="bi bi-facebook" style="font-size: larger"></i>
                            <span class="alterneur-mini">Facebook</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://twitter.com/Tributerre45/">
                            <i class="bi bi-twitter" style="font-size: larger"></i>
                            <span class="alterneur-mini">Twitter</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <div<?= empty($GLOBALS['messages']) || GABARIT == 'erreur.php' ? ' style="display: none"' : '' ?>>
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <div class="well">
                            <h3 class="text-center">Message(s)</h3>
                            <hr>
                            <ul class="text-left">
                                <?php foreach ($GLOBALS['messages'] as $arrMessage): ?>
                                    <li><?= $arrMessage[0] ?> : <?= $arrMessage[1] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <?php require GABARIT; ?>
    </main>
    <footer>
        <div class="container-fluid text-center texte-footer">
            <p>Tribu-Terre est une association étudiante apartisane et asyndicale à but non lucratif, régie par la loi du 1er juillet 1901.</p>
            <p><a href="/mentions-legales/">Mentions légales</a></p>
            <p>Tribu-Terre <?= date("Y") ?> | 1A Rue de la Férollerie, 45071, Orléans Cedex 2</p>
            <p><small>Développé avec ❤️ par Anaël BARODINE | <a href="https://github.com/bde-tribu-terre/site-web-general" target="_blank">Version <?= VERSION_SITE ?></a></small></p>
        </div>
    </footer>
</div>
</body>
</html>
