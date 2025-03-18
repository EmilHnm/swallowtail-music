<?php

namespace App\Orchid\Selection;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\BaseHttpEloquentFilter;
use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Selection;

class MakeSelection
{
    public static function fields($fields = []): Selection
    {
        $filters = [];
        foreach ($fields as $field) {
            $filters[] = self::makeFilterFromField($field);
        }
        return new DynamicSelection($filters);
    }

    protected static function makeFilterFromField(Field $field)
    {
        return new class($field->get('name'), $field) extends BaseHttpEloquentFilter {
            public Field $_field;

            public function __construct(string $column, Field $field)
            {
                parent::__construct($column);
                $this->_field = $field;
                $this->_field->set('name', 'filter['.$field->get('name').']');
            }

            public function run(Builder $builder): Builder
            {
                return $builder;
            }

            public function display(): iterable
            {
                return [
                    $this->_field->value($this->request->input('filter.' . $this->column)),
                ];
            }
        };
    }

}