<?php

namespace App\Orchid\Screens\Tools;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Es\RawHits;
use App\Models\Song;
use App\Orchid\Selection\MakeSelection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ElasticSearchInspectorScreen extends Screen
{
    protected Collection $records;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $index = $request->input('filter.indices', '');
        $query = $request->input('filter.query', '');
        if ($index && $query) {
            /** @var Model $model
             */
            $model = $this->handleModel($index);
            $results = $model::search($query)->get();
            try {
                if (!$results->count()) {
                    Alert::error('There is no results');
                }
                $elasticHits = RawHits::from($model::search($query)->raw()['hits'])->hits;
                $results->map(function ($result, $key) use ($elasticHits) {
                    $result->score = $elasticHits[$key]->_score;
                });
                $this->records = $results;
                return [
                    "records" => $results,
                ];
            } catch (\Exception $e) {
                Alert::error($e->getMessage());
            }
        }
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Elasticsearch Query Inspector Screen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $searched = request()->has('filter.indices') &&
                request()->has('filter.query') ;
        $cols = [];
        if($searched && $this->records->count() > 0) {
            foreach (array_keys($this->records->first()->getAttributes()) as $key) {
                $col = TD::make($key);
                if ($key === 'created_at' || $key === 'updated_at')
                    $col->asComponent(DateTimeSplit::class);
                if (str_contains($key, 'path'))
                    continue;
                $cols[] = $col;
            }
        }
        return [
            MakeSelection::fields([
                Select::make('indices')
                    ->options($this->getIndices())
                    ->title('Indices'),
                TextArea::make('query')
                    ->title('Search query'),
            ]),
            Layout::table('records', $cols)->canSee($searched && $this->records->count() > 0)
        ];
    }


    protected function getIndices()
    {
        $indices = array_keys(config('elasticsearch.indices.mappings', []));
        return array_combine($indices, $indices);
    }

    protected function handleModel($pattern)
    {
        return match ($pattern) {
            config('scout.prefix') . 'songs' => Song::class,
            config('scout.prefix') . 'artists' => Artist::class,
            config('scout.prefix') . 'albums' => Album::class,
            default => throw new \Exception("No index"),
        };
    }
}
