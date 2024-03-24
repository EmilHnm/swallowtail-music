<?php

namespace App\Orchid\Screens\Traits;

use App\Orchid\Layouts\DumpModal;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

/**
 * @Example
 * $this->getDumpModal(),
 * Layout::table('documents', [
 *                       TD::make('id')
 *                            ->render(fn(Document $doc) => $doc->id . "<br/>" .
 *                              self::getDumpModelToggle(Document::class, $doc->id, $doc->code)
 *                                         )
 *                            ->sort(),
 */
trait HasDumpModelModal
{
    public function asyncGetModel(Request $request)
    {
        $model = $request->get('model');
        $id = $request->get('id');
        $with = $request->get('with');
        if ($with) {
            $with = explode(",", $with);
            return [
                'model' => app($model)->withoutGlobalScopes()->with($with)->find($id)?->toArray(),
            ];
        } else {
            return [
                'model' => app($model)->withoutGlobalScopes()->find($id)?->toArray(),
            ];
        }
    }

    protected function getDumpModal($modal = 'dump_modal', $target = 'model')
    {
        return (new DumpModal($modal, [], $target))
            ->size('modal-xl')
            ->async('asyncGetModel')
            ->withoutApplyButton()
            ->type('modal-info')
            ->title('Dump Model');
    }

    public static function getDumpModelToggle($class, $id, $text = '</>', $additions = [])
    {
        $text = $text ?: $id;
        return ModalToggle::make($text)
            ->modal('dump_modal')
            ->async('asyncGetModel')
            ->class('btn text-primary')
            ->asyncParameters(['model' => $class, 'id' => (string)$id] + $additions);
    }
}
