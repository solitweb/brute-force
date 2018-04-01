<?php

error_reporting(0);
ini_set('display_errors', 0);

$errors = [];

require __DIR__ . '/../vendor/autoload.php';

// Don't cache this page
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 1 Jan 1970 00:00:00 GMT");

$config = require __DIR__ . '/../config.php';
$bf = new BruteForce($config['recaptcha_secretkey'], $config['allowed_dir']);
$lang = new Localization($config['language_dir'], $config['fallback_locale']);

// If form is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bf->validate($_POST['g-recaptcha-response']) === true) {
        exit(header("Location: " . urldecode($bf->getRedirectToUrl()) . "\n"));
    } else {
        $errors[] = "You are a bot! Go away!";
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getLocale(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $config['title']; ?></title>
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
    <div class="container h-100">
        <div class="row h-100 py-4 align-items-center justify-content-center">
            <div class="col-md-6 m-auto">

                <ul class="nav bg-light rounded mb-3 justify-content-end">
                    <li class="nav-item dropdown">
                        <button class="nav-link btn btn-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-globe"></i>
                            <span><?php echo $lang->getLocaleNative($lang->getLocale()); ?></span>
                        </button>
                        <div class="dropdown-menu">
                            <?php foreach ($lang->getAllLocales() as $locale) : ?>
                                <a class="dropdown-item <?php echo ($locale == $lang->getLocale()) ? 'active': ''; ?>" href="<?php echo $lang->buildQueryString($locale); ?>">
                                    <?php echo $lang->getLocaleNative($locale); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul><!-- /.nav -->

                <div class="card mb-3">
                    <div class="card-body">

                        <?php foreach ($errors as $error) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endforeach; ?>

                        <p class="lead text-dark">
                            <span>
                                <?php echo $lang->translate('This page is an additional security for the login page of your website or application. Before you can sign in, it\'s important that you show that you are a human being. You can do this by confirming the reCAPTCHA below.'); ?>
                            </span>
                            <a data-toggle="collapse" href="#info" role="button" aria-expanded="false" aria-controls="info">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </p>

                        <p class="text-muted font-italic collapse" id="info">
                            <?php echo $lang->translate('Due to the ongoing bruteforce attempts on login pages, we have placed this page to protect your website or application from bruteforce attackers. A bruteforce attack means that the attacker will try every possible username and password combination to get access to the backend of your website or applicatie. We have placed this page to limit the bruteforce attacks.'); ?>
                        </p>

                        <form name="validate" method="POST">
                            <div id="recaptcha">
                                <div class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="recaptchaSuccessful"></div>
                                <script src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang->getLocale(); ?>"></script>
                            </div>
                            <input type="hidden" name="redirect" value="<?php echo addslashes(htmlspecialchars($bf->getRedirectToUrl())); ?>">
                        </form>

                    </div>
                </div>
                <!-- /.card -->

                <p class="text-center text-muted small">
                    &copy; <?php echo date('Y'); ?> <a href="<?php echo strip_tags($config['company_url']); ?>" target="_blank"><?php echo strip_tags($config['company_name']); ?></a>. <?php echo $lang->translate('All rights reserved.'); ?>
                </p>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="/assets/js/app.js" type="text/javascript"></script>
    <script type="text/javascript">
        // Callback after successful submit.
        var recaptchaSuccessful = function() {
            // Wait for the recapcha animation.
            setTimeout(function() {
                // Submit the form.
                document.validate.submit();
            }, 300);
        };
    </script>
</body>
</html>