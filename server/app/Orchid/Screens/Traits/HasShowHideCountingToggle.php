<?php

namespace App\Orchid\Screens\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Screen\Actions\Link;

trait HasShowHideCountingToggle
{
    public bool|null $counting = null;

    protected function isCounting() : bool
    {
        if($this->counting === null){
            $this->counting = (bool)\Request::query('_counting');
        }
        return $this->counting;
    }

    protected function getCountingToggleLink() : Link
    {
        if($this->isCounting()){
            $url = \Request::fullUrlWithoutQuery('_counting');
            $text = 'Hide Count';
        }else{
            $url = \Request::fullUrlWithQuery(['_counting' => 'true']);
            $text = 'Show Count';
        }
        return Link::make($text)->href($url)->icon('back');
    }

    protected function paging(Builder $builder, $perPage = null, $columns = ['*'], $pageName = 'page', $page = null) : Paginator
    {
        if($this->isCounting()){
            return $builder->paginate($perPage, $columns, $pageName, $page);
        }else{
            return $builder->simplePaginate($perPage, $columns, $pageName, $page);
        }
    }
}