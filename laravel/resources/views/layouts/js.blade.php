<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>


@if(Request::is('admin/finance/import'))
<script src="{{ asset('js/finance_upload.js') }}"></script>
@endif
@if(Request::is('admin/finance/show*'))
<script src="{{ asset('js/finance_update.js') }}"></script>
@endif
@if(Request::is('admin/finance/all*'))
<script>
  urlsearch = "{{URL::to('admin/finance/search')}}";
</script>
<script src="{{ asset('js/finance_all.js')}}"></script>
@endif
@if(Request::is('admin/movies/all*'))
<script>
// global javascript variables
  var urlsearch = "{{URL::to('admin/movies/search')}}";
  var loadingImg = '{{ URL::asset('img/loading.gif') }}';
  var csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/movies_all.js')}}"></script>
@endif
@if(Request::is('admin/movies/pending*') || Request::is('tvshows/pending*'))
<script src="{{ asset('js/movies_pending.js')}}"></script>
@endif
@if(Request::is('admin/movies/check/picture*') || Request::is('tvshows/check/picture*'))
<script src="{{ asset('js/movies_pictures.js')}}"></script>
@endif

@if(Request::is('admin/stats/*'))
  <script>
    urlstats = "{{Request::url()}}";
  </script>
<script src="{{ asset('js/stats.js') }}"></script>
@endif
