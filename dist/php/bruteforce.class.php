<?php

class BruteForce
{
    /**
     * The allowed directory.
     *
     * @var string
     */
    private $allowed_dir;

    /**
     * The reCAPTCHA secret key.
     *
     * @var string
     */
    private $recaptcha_secretkey;

    /**
     * URL to the reCAPTCHA API. 
     *
     * @var string
     */
    private static $siteverify = 'https://www.google.com/recaptcha/api/siteverify';

    function __construct(string $recaptcha_secretkey = '', string $allowed_dir = '') {
        $this->recaptcha_secretkey = $recaptcha_secretkey ?: '';
        $this->allowed_dir = $allowed_dir ?: 'allowed';
    }

    /**
     * Validate the reCAPTCHA request.
     *
     * @param string $response
     * @return boolean
     */
    public function validate(string $response = '') : bool
    {
        $validate = false;
        if (! empty($response) && $curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, self::$siteverify);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "secret={$this->recaptcha_secretkey}&remoteip={$_SERVER['REMOTE_ADDR']}&response={$response}");
            if ($verify = curl_exec($curl)) {
                $validate = json_decode($verify);
            }
            curl_close($curl);

            if (is_object($validate) && ($validate->success === true)) {
                // reCAPTCHA was entered successfuly.
                if ($_SERVER['REMOTE_ADDR']) {
                    $IPF = "{$this->allowed_dir}/{$_SERVER['REMOTE_ADDR']}.dat";
                    if (is_dir($this->allowed_dir)) {
                        if ($handle = fopen($IPF, 'w')) {
                            fclose($handle);
                        }
                    }
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Get the redirect to URL.
     *
     * @param string $redirect_to
     * @return string
     */
    public function getRedirectToUrl(string $redirect_to = '') : string
    {
        if (empty($redirect_to)) {
            if (isset($_POST['redirect_to']) && $_POST['redirect_to']) {
                $redirect_to = $_POST['redirect_to'];
            }
            elseif(isset($_GET['redirect_to']) && $_GET['redirect_to']) {
                $redirect_to = $_GET['redirect_to'];
            }
        }

        return urlencode(filter_var($redirect_to, FILTER_SANITIZE_URL));
    }

}
