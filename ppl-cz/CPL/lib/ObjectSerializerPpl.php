<?php
namespace PPLCZCPL;

class ObjectSerializerPpl extends ObjectSerializer {
    public static function toQueryValue(
        $value,
        string $paramName,
        string $openApiType = 'string',
        string $style = 'form',
        bool $explode = true,
        bool $required = true
    ): array
    {
        if (
            empty($value)
            && ($value !== false || $openApiType !== 'boolean')
        ) {
            if ($required) {
                switch($openApiType) {
                    case "integer":
                        return  ["{$paramName}" => '0'];
                    case "boolean":
                        return  ["{$paramName}" => 'false'];
                    default:
                        return ["{$paramName}" => ''];
                }

            } else {
                return [];
            }
        }
        return parent::toQueryValue($value, $paramName, $openApiType, $style, $explode, $required);
    }
}