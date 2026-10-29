<?php

namespace App\Database\Eloquent\Builders;

use App\Models\GuruMapel;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;

/**
 * Alias kolom gaya Laravel (id_siswa, tp_id, …) serta filter guru_mapel_id tanpa FK fisik pada skema tertentu.
 */
class NilaiTpBuilder extends Builder
{
    protected function mapLegacyColumn(string|Expression $column): string|Expression
    {
        if (! is_string($column)) {
            return $column;
        }

        return match ($column) {
            'id_siswa' => 'id_siswa',
            'tp_id' => 'id_tujuan_pembelajaran',
            'mapel_id' => 'id_mapel',
            'id' => 'id_nilai_tp',
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

        if (is_string($column) && $column === 'guru_mapel_id') {
            [$value, $operator] = $this->query->prepareValueAndOperator(
                $value,
                $operator,
                func_num_args() === 2
            );

            if (strtolower((string) $operator) !== '=') {
                return $this->whereRaw('1 = 0', [], $boolean);
            }

            return $this->where(static function (\Illuminate\Database\Eloquent\Builder $q) use ($value) {
                $assignment = GuruMapel::find($value);
                if (! $assignment) {
                    return $q->whereRaw('1 = 0');
                }

                /** @see \App\Models\NilaiTp::guruMapel() — filter setara kombinasi mapel + kelas siswa */
                return $q->where($q->getModel()->qualifyColumn('id_mapel'), $assignment->getAttribute('id_mapel'))
                    ->whereHas('siswa', static function ($sq) use ($assignment) {
                        $sq->where('id_kelas', $assignment->getAttribute('id_kelas'));
                    });
            }, null, null, $boolean);
        }

        if (is_string($column)) {
            $column = $this->mapLegacyColumn($column);
        }

        return parent::where($column, $operator, $value, $boolean);
    }
}
