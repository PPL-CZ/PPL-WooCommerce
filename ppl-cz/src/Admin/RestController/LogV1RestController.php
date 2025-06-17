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
        if ($data->get_id())
            $data->delete(true);

        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }

    public function get_log ()
    {
        $logModel = pplcz_denormalize(new ErrorLogModel(), ErrorLogModel::class);
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

        add_action('phpmailer_init', function($phpmailer) use ($textMessage, $logErrors) {
            $phpmailer->AltBody = $textMessage;
            $phpmailer->addStringAttachment($logErrors, "logs.txt", "base64", "text/plain");
        });

        wp_mail("cisteam@ppl.cz", "WooCommerce plugin - nahlášení problému", $htmlMessage, $headers);

        $response = new \WP_REST_Response();
        $response->set_status(204);

        return $response;
    }
}