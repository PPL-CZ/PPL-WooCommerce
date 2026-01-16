<?php
namespace PPLCZ\Admin\RestController;

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Admin\Errors;
use PPLCZ\Admin\RestResponse\RestResponse400;
use PPLCZ\Data\LogData;
use PPLCZ\Data\LogDataStore;
use PPLCZ\Model\Model\ErrorLogModel;
use PPLCZ\Model\Model\NewCollectionModel;
use PPLCZ\Model\Model\SendErrorLogModel;

class LogV1RestController extends PPLRestController
{
    protected $namespace = "ppl-cz/v1";

    protected $base = "logs";

    public function register_routes()
    {
        register_rest_route($this->namespace, "/" . $this->base , [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_log"],
            "permission_callback" => [$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/error", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "error"],
            "permission_callback" => [$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/send", [
            "methods" => \WP_REST_Server::CREATABLE,
            "callback" => [$this, "send_log"],
            "permission_callback" => [$this, "check_permission"],
        ]);
        
        register_rest_route($this->namespace, "/" . $this->base .  "/(?P<id>\d+)" , [
            "methods" => \WP_REST_Server::DELETABLE,
            "callback" => [$this, "delete_error"],
            "permission_callback" => [$this, "check_permission"],
        ]);
    }

    public function error()
    {
        throw new \Exception("Error");
    }

    public function delete_error(\WP_REST_Request $request)
    {
        $id = $request->get_param("id");

        $data = new LogData($id);
        if ($data->get_id()) {
            $hash = $data->get_errorhash();
            $data->delete(true);

            if ($hash) {
                $this->clear_log_hash($hash);
            }
        }

        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }

    /**
     * Remove error hash from WordPress options
     *
     * @param string $hash Error hash to remove
     * @return void
     */
    protected function clear_log_hash($hash)
    {
        $hashes = get_option(pplcz_create_name("error_log_hashes"), '');
        $count = get_option(pplcz_create_name("error_log"), 0);

        $hashArray = array_filter(array_filter(
            preg_split("/(\r?\n)+/", $hashes),
            function($h) use ($hash) {
                return trim($h) !== $hash;
            }
        ));

        update_option(
            pplcz_create_name("error_log_hashes"),
            join("\n", $hashArray)
        );

        $newCount = max(0, intval($count) - 1);
        update_option(pplcz_create_name("error_log"), $newCount);
    }

    public function get_log (\WP_REST_Request $request)
    {
        $product_ids = [];
        $order_ids = [];

        if ($request->get_param("product_ids"))
            $product_ids =array_map('intval', explode(',', $request->get_param('product_ids')));
        if ($request->get_param('order_ids'))
            $order_ids =array_map('intval', explode(',', $request->get_param('order_ids')));

        $logModel = pplcz_denormalize(new ErrorLogModel(), ErrorLogModel::class, ["product_ids" => $product_ids, 'order_ids' => $order_ids ]);
        $logModel = pplcz_normalize($logModel);
        $respose = new \WP_REST_Response();
        $respose->header("Content-Type", "application/json");
        $respose->set_data($logModel);
        return $respose;
    }

    public function  send_log(\WP_REST_Request $request)
    {

        $params = $request->get_json_params();

        /**
         * @var SendErrorLogModel $inputError
         */
        $inputError = pplcz_denormalize($params, SendErrorLogModel::class);

        $errors = new Errors();

        pplcz_validate($inputError, $errors, "");
        if ($errors->errors)
            return new RestResponse400($errors);

        $message = $inputError->getMessage();
        $mail = $inputError->getMail();
        $message  = "Kontakt: " . $mail . "\n\n" . $message;
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $mail = null;
        }

        $info = $inputError->getInfo();
        $logErrors = join("\n\n", array_map(function ($msg) {
            return $msg->getTrace();
        },$inputError->getErrors()));

        $textMessage = $message . "\n" . $info;
        $htmlMessage ="<p>" . str_replace("\n", "<br>",$message) . "</p><p>" . str_replace("\n", "<br>", $info) . "</p>";

        $headers = [
            "Content-Type" => 'text/html;charset=UTF-8',
        ];

        if ($mail)
            $headers['Reply-To'] = $mail;

        add_filter('wp_mail_content_type', function() {
            return 'multipart/alternative';
        });

        add_action('phpmailer_init', function($phpmailer) use ($textMessage, $logErrors, $inputError) {
            $phpmailer->AltBody = $textMessage;
            $phpmailer->addStringAttachment($logErrors, "zprava_a_logy.txt", "base64", "text/plain");
            if ($inputError->getGlobalParcelSetting())
                $phpmailer->addStringAttachment(wp_json_encode(pplcz_normalize($inputError->getGlobalParcelSetting()), JSON_PRETTY_PRINT), "globalni_nastaveni_parcel.json", "base64", "application/json");
            if ($inputError->getCategorySetting())
                $phpmailer->addStringAttachment(wp_json_encode(pplcz_normalize($inputError->getCategorySetting()), JSON_PRETTY_PRINT), "nastaveni_kategorii.json", "base64", "application/json");
            if ($inputError->getProductsSetting())
                $phpmailer->addStringAttachment(wp_json_encode(pplcz_normalize($inputError->getProductsSetting()), JSON_PRETTY_PRINT), "nastaveni_produktu.json", "base64", "application/json");
            if ($inputError->getShipmentsSetting())
                $phpmailer->addStringAttachment(wp_json_encode(pplcz_normalize($inputError->getShipmentsSetting()), JSON_PRETTY_PRINT), "nastaveni_dopravy.json", "base64", "application/json");
            if ($inputError->getOrders())
                $phpmailer->addStringAttachment(wp_json_encode(pplcz_normalize($inputError->getOrders()), JSON_PRETTY_PRINT), "orders.json", "base64", "application/json");
        });

        if (!wp_mail("cisteam@ppl.cz", "WooCommerce plugin - nahlášení problému", $htmlMessage, $headers))
        {
            $wp_error = new \WP_Error();
            $wp_error->errors["email"] = ["Cannot send mail to cisteam@ppl.cz"];

            return new RestResponse400($wp_error);
        }

        

        $response = new \WP_REST_Response();
        $response->set_status(204);

        return $response;
    }
}