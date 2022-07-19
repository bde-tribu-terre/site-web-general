<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo TITLE ?> | Tribu-Terre</title>

    <!-- Meta tags essentiels -->
    <meta property="og:title" content="<?php echo TITLE ?>">
    <meta property="og:image" content="/-images/imgLogoMini.png">
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

    <!-- Fonctions Javascript -->
    <script src="/script.min.js"></script>
</head>
<body>
<div class="page-complete">
    <header>
        <div class="jumbotron">
            <div class="container text-center">
                <a href="/">
                    <img
                            class="logo-jumbotron"
                            src="/resources/imgLogoMini.png"
                            alt="Logo"
                            <?php echo $_SERVER["REQUEST_URI"] == '/' ? 'style="height: 300px"' : ''; ?>
                    >
                </a>
                <p class="texte-jumbotron">Association des Étudiants en Sciences de l'Université d'Orléans</p>
            </div>
        </div>

        <nav class="navbar navbar-expand-sm py-0">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigationHeader"> <!-- C'est le petit bouton menu quand l'écran est trop petit -->
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-collapse collapse" id="navigationHeader"> <!-- Et ça c'est quand l'écran est assez grand -->
                <ul class="nav navbar-nav">
                    <li class="nav-item<?php echo $_SERVER["REQUEST_URI"] == '/' ? ' active' : ''; ?>">
                        <a class="nav-link" href="/">Accueil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">À propos <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item<?php echo $_SERVER["REQUEST_URI"] == '/association/' ? ' active' : ''; ?>" href="/association/">
                                    Tribu-Terre
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item<?php echo $_SERVER["REQUEST_URI"] == '/association/adherer/' ? ' active' : ''; ?>" href="/association/adherer/">
                                    Adhérer
                                </a>
                            </li>
                            <li role="separator" class="divider">
                            </li>
                            <li>
                                <a class="dropdown-item<?php echo $_SERVER["REQUEST_URI"] == '/association/federations/' ? ' active' : ''; ?>" href="/association/federations/">
                                    Fédérations
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item<?php echo $_SERVER["REQUEST_URI"] == '/association/partenaires/' ? ' active' : ''; ?>" href="/association/partenaires/">
                                    Partenaires
                                </a>
                            </li>
                            <li role="separator" class="divider">
                            </li>
                            <li>
                                <a class="dropdown-item<?php echo $_SERVER["REQUEST_URI"] == '/association/statuts/' ? ' active' : ''; ?>" href="/association/statuts/">
                                    Statuts
                                </a>
                            </li>
                            <li role="separator" class="divider">
                            </li>
                            <li>
                                <a class="dropdown-item<?php echo $_SERVER["REQUEST_URI"] == '/association/contact/' ? ' active' : ''; ?>" href="/association/contact/">
                                    Contact
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item<?php echo $_SERVER["REQUEST_URI"] == '/journaux/' ? ' active' : ''; ?>">
                        <a class="nav-link" href="/journaux/">
                            Journaux
                        </a>
                    </li>
                    <li class="nav-item<?php echo $_SERVER["REQUEST_URI"] == '/trouver-une-salle/' ? ' active' : ''; ?>">
                        <a class="nav-link" href="/trouver-une-salle/">
                            Trouver une salle
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
                    <li class="nav-item">
                        <a class="nav-link" href="/discord/">
                            <i class="bi bi-discord" style="font-size: larger"></i>
                            <span class="alterneur-mini">Discord</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <div<?php echo empty($GLOBALS['messages']) || GABARIT == 'erreur.php' ? ' style="display: none"' : '' ?>>
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <div class="well">
                            <h3 class="text-center">Message(s)</h3>
                            <hr>
                            <ul class="text-left">
                                <?php
                                foreach ($GLOBALS['messages'] as $arrMessage) {
                                    switch (substr($arrMessage[0], 0, 1)) {
                                        case '1':
                                            $color = '';
                                            break;
                                        case '2':
                                            $color = ' style="color: green;"';
                                            break;
                                        case '4':
                                            $color = ' style="color: orange"';
                                            break;
                                        case '5':
                                            $color = ' style="color: red"';
                                            break;
                                        case '6':
                                            $color = ' style="color: purple"';
                                            break;
                                        default:
                                            $color = ' style="color: blue"';
                                    }
                                    echo '<li' . $color . '>' . $arrMessage[0] . ' : ' . $arrMessage[1] . '</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <?php require_once GABARIT; ?>
    </main>
    <footer>
        <div class="container-fluid text-center">
            <p><strong>
                    Vous avez des questions sur Tribu-Terre ou les derniers évènements ?<br/>
                    <a href="/association/contact/">Contactez nous !</a>
                </strong></p>
            <hr>
            <div class="texte-footer">
                <p>Tribu-Terre est une association étudiante apolitique à but non lucratif, régie par la loi du 1er juillet 1901.</p>
                <p><a href="/mentions-legales/">Mentions légales</a></p>
                <p>Tribu-Terre 2021 | 1A Rue de la Férollerie, 45071, Orléans Cedex 2</p>
                <p><strong>Site Tribu-Terre version <?= VERSION_SITE ?></strong></p>
                <p><small>Développé avec ❤️ par Anaël BARODINE</small></p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
