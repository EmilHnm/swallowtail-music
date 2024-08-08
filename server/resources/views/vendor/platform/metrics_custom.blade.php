@isset($metrics)
   <div class="my-4">
       <h2>
           Statistics
       </h2>
       <div class="d-flex gap-4">
           @foreach($metrics as $metric)
               <div class="bg-white p-4 rounded-2">
                 <h4>  {{$metric->title}}</h4>
                   <div class="fs-4">
                       {{ $metric->format($metric->value) }}
                   </div>

                   @if($metric->previousValue !== null)

                       <div class="mt-2 fw-semibold {{ $metric->hasChanged() ? ($metric->hasIncreased() ? 'text-success' : 'text-danger') : '' }}">

                           @if($metric->hasChanged())
                               @if($metric->hasIncreased())
                                   @lang('Up from')
                               @else
                                   @lang('Down from')
                               @endif
                           @else
                               @lang('No change from')
                           @endif

                           {{ $metric->format($metric->previousValue) }}
                       </div>

                   @endif

               </div>
           @endforeach
       </div>

   </div>
@endisset