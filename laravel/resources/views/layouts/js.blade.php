<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

@if(Request::is('finance/import'))
<script src="{{ asset('js/finance_upload.js') }}"></script>
@endif
@if(Request::is('finance/show*'))
<script src="{{ asset('js/finance_update.js') }}"></script>
@endif
@if(Request::is('finance/all*'))
<script>
  urlsearch = "{{URL::to('finance/search')}}";
</script>
<script src="{{ asset('js/finance_all.js')}}"></script>
@endif
@if(Request::is('movies/all*'))
<script>
// global javascript variables
  var urlsearch = "{{URL::to('movies/search')}}";
  var loadingImg = '{{ URL::asset('img/loading.gif') }}';
  var csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/movies_all.js')}}"></script>
@endif
@if(Request::is('movies/pending*') || Request::is('tvshows/pending*'))
<script src="{{ asset('js/movies_pending.js')}}"></script>
@endif
@if(Request::is('movies/check/picture*') || Request::is('tvshows/check/picture*'))
<script src="{{ asset('js/movies_pictures.js')}}"></script>
@endif

@if(Request::is('charge/*'))
<script src="{{ asset('js/charge.js') }}"></script>
@endif
@if(Request::is('stats/*'))
  <script>
    urlstats = "{{Request::url()}}";
  </script>
<script src="{{ asset('js/stats.js') }}"></script>
@endif
