<?php

namespace App\Database\Eloquent\Builders;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;

/**
 * Memetakan nama kolom gaya Laravel (guru_id, mapel_id, …) ke skema DB siakad-nilai (id_guru, id_mapel, …)
 * tanpa mengubah semua controller sekaligus.
 */
class GuruMapelBuilder extends Builder
{
    protected function mapColumn(string|Expression $column): string|Expression
    {
        if (! is_string($column)) {
            return $column;
        }

        return match ($column) {
            'guru_id' => 'id_guru',
            'mapel_id' => 'id_mapel',
            'id_kelas' => 'id_kelas',
            'id_tahun_ajaran' => 'id_tahun_ajaran',
            'id' => 'id_guru_mapel',
            default => $column,
        };
    }

    /**
     * @param  (\Closure(static): mixed)|array|string|\Illuminate\Contracts\Database\Query\Expression  $column
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            $mapped = [];
            foreach ($column as $key => $val) {
                $mapped[is_string($key) ? $this->mapColumn($key) : $key] = $val;
            }

            return parent::where($mapped, null, null, $boolean);
        }

        if (is_string($column)) {
            $column = $this->mapColumn($column);
        }

        return parent::where($column, $operator, $value, $boolean);
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        if (is_string($column)) {
            $column = $this->mapColumn($column);
        }

        return parent::whereIn($column, $values, $boolean, $not);
    }

    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        if (is_string($columns)) {
            $columns = $this->mapColumn($columns);
        }

        return parent::whereNull($columns, $boolean, $not);
    }

    public function whereNotNull($columns, $boolean = 'and')
    {
        if (is_string($columns)) {
            $columns = $this->mapColumn($columns);
        }

        return parent::whereNotNull($columns, $boolean);
    }
}
