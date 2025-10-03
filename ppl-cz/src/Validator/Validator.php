<?php
namespace PPLCZ\Validator;
defined("WPINC") or die();
class Validator extends ModelValidator {

    public function validate($model, $errors, $path = "")
    {
            foreach (ModelValidator::$validators as $key => $validator) {
                if (is_string($validator)) {
                    $validator = new $validator();
                    ModelValidator::$validators[$key] = $validator;
                }
                /**
                 * @var ModelValidator $validator
                 */
                if ($validator->canValidate($model))
                    $validator->validate($model, $errors, $path);
            }

    }

    public function canValidate($model)
    {
        throw new \Exception("undefined");
    }

    private static $instance;

    public static function getInstance()
    {
        return static::$instance ?? static::$instance = new Validator();
    }
}