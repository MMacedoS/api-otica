<?php

namespace App\Transformers\Traits;

trait TransformerTrait
{
    public function transformKeys(array $data, $keys): array
    {
        $map = $keys;
        $result = [];

        foreach ($data as $key => $value) {
            $newKey = $map[$key] ?? $key;
            if (is_array($value)) {
                $result[$newKey] = $this->transformKeys($value, $keys);
                continue;
            }

            $result[$newKey] = $value;
        }

        return $result;
    }
}
