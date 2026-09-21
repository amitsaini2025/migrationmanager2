<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;

class ClientMatterBuilder extends Builder
{
    /**
     * @param  mixed  $columns
     */
    public function select($columns = ['*'])
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        $this->query->select($this->remapVisaTypeIdColumns($columns));

        return $this;
    }

    /**
     * @param  mixed  $column
     */
    public function addSelect($column)
    {
        $columns = is_array($column) ? $column : func_get_args();

        $this->query->addSelect($this->remapVisaTypeIdColumns($columns));

        return $this;
    }

    /**
     * @param  (\Closure(static): mixed)|string|array|Expression  $column
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $args = func_get_args();
        if (isset($args[0]) && is_string($args[0])) {
            $args[0] = $this->remapVisaTypeIdColumn($args[0]);
        }

        return parent::where(...$args);
    }

    /**
     * @param  array<int|string, mixed>  $columns
     * @return array<int|string, mixed>
     */
    private function remapVisaTypeIdColumns(array $columns): array
    {
        $mapped = [];

        foreach ($columns as $as => $column) {
            $remapped = $this->remapVisaTypeIdColumn($column);
            if (is_string($as)) {
                $mapped[$as] = $remapped;
            } else {
                $mapped[] = $remapped;
            }
        }

        return $mapped;
    }

    private function remapVisaTypeIdColumn(mixed $column): mixed
    {
        if (! is_string($column)) {
            return $column;
        }

        $normalized = str_replace(['`', '"'], '', $column);

        return match ($normalized) {
            'visa_type_id' => 'sel_matter_id',
            'client_matters.visa_type_id' => 'client_matters.sel_matter_id',
            default => $column,
        };
    }
}
