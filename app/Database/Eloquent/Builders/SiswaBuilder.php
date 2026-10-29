<?php

namespace App\Database\Eloquent\Builders;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;

/**
 * Memetakan id_kelas (legacy) → id_kelas pada tabel siswa (skema siakad-nilai).
 */
class SiswaBuilder extends Builder
{
    protected function mapColumn(string|Expression $column): string|Expression
    {
        if (! is_string($column)) {
            return $column;
        }

        return match ($column) {
            'id_kelas' => 'id_kelas',
            'id' => 'id_siswa',
            default => $column,
        };
    }

    /**
     * @param  (\Closure(static): mixed)|array|string|\Illuminate\Contracts\Database\Query\Expression  $column
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            foreach ($column as $key => $filteredValue) {
                if (is_string($key)) {
                    $this->where($key, '=', $filteredValue, $boolean);
                }
            }

            return $this;
        }

        if (is_string($column)) {
            $column = $this->mapColumn($column);
        }

        return parent::where($column, $operator, $value, $boolean);
    }
}
